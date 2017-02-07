 var areaConf = {
   id:"city-catemenu",
   init_Div:"divorg",
   DataSource:"AreaRoot",
   maxChoose:"10",
   main_ContentType:"s1",
   sub_ColumnType:"22",
   mscb:"2",
   se_SearchLevel:"3",
   se_TreeLevel:"2",
   se_ShowDetail:"yes",
   lightBox:"0",
   s1TopTitle:"台灣地區",
   s1BottomTitle:"海外地區",
   s1NO:"6001000000",
   choiceNumber:"3",
   chooseItems: getChosen('area'),
 };

var indConf = {
  id:"industry-catemenu",
  init_Div:"divorg",
  DataSource:"IndustRoot",
  maxChoose:"5",
  searchBox:"1",
  se_AutoSend:"1",
  mscb:"2",
  choiceNumber:"2",
  se_ShowDetail:"yes",
  lightBox:"0",
  main_ContentType:"1",
  main_ColumnType:"2",
  se_SearchLevel:"3",
  sub_ContentType:"23",
  sub_ColumnType:"1",
  chooseItems: getChosen('indcat'),
};


var fillCity = new CateSelector('#fill-city', '#area', areaConf);
var fillIndustry = new CateSelector('#fill-industry', '#indcat', indConf);


function CateSelector (target, input, conf) {
  var node = {};
  node.target = $(target);
  node.input = $(input);
  var placeholder = node.target.data('placeholder'); 
  node.target.on('click', function () {
    obj_e104menu2011 = new E104Menu2011(conf);
  });
  /* trigger input change text and value */
  this.setState = function (state) {
    if(state !== '') {
      var noArr = state.no.split(',');
      var str = state.desc.split(',').join('、');
      node.target.find('.opts')
        .text(str) 
        .removeClass('placeholder');
      node.input.val(state.no);

      conf.chooseItems = []; 
      noArr.forEach(function(val, i){
        conf.chooseItems.push({'no': val}); 
      });
    } else {
      node.target.find('.opts')
        .text(placeholder)
        .addClass('placeholder');
      node.input.val('');
      conf.chooseItems = []; 
    }
  }
}

E104Menu2011.prototype.callBack = function(){
  var chosen = this.config.chooseItems,
      noArr = [],
      descArr = [],
      stateObj;

  if(chosen.length > 0) {
    stateObj = {};
    $.each(chosen, function(i, itm) {
      noArr.push(itm['no']);
      descArr.push(itm['des']);
    })
    stateObj.no = noArr.join(',');
    stateObj.desc = descArr.join(',');
  } else {
    stateObj = ''; 
  }

  switch(this.config.id){
    case "city-catemenu":
      fillCity.setState(stateObj);
      break;
    case "industry-catemenu":
      fillIndustry.setState(stateObj);
      break;
  }
}

function getChosen(index) {
  var search = location.search.substring(1);
  window.paramObj = search?JSON.parse('{"' + search.replace(/&/g, '","').replace(/=/g,'":"') + '"}', function(key, value) { return key===""?value:decodeURIComponent(value) }):{};
  var arr = [];

  if (paramObj[index]) {
    var data = paramObj[index].split(',');
    data.forEach(function (val) {
      arr.push(JSON.parse('{"no":' + val + '}'));
    });
    return arr;
  }
}

window.obj_e104menu2011 = new E104Menu2011(indConf);
window.obj_e104menu2011.sendBack();
window.obj_e104menu2011 = new E104Menu2011(areaConf);
window.obj_e104menu2011.sendBack();
window.obj_e104menu2011 = null;
$('[name=keyword]').val(paramObj.keyword);



function Search(){
  var node = {};
  node.root = $('#srerch-form');
  node.but = node.root.find('.send');
  // node.enter = {
  //   'keyword':'',
  //   'indcat':'6000001,600ji30002',
  //   'area':'6000003,6000004',
  // }

  node.root.on('submit',function(e){
    // e.preventDefault();
    node.enter = {
      'keyword':node.root.find('.keyword').val(),
      'indcat':node.root.find('.indcat').val(),
      'area':node.root.find('.area').val(),
    }
    if(validSpecialChar(node.enter.keyword)){
      alert('請勿輸入以下特殊符號\!():^[]{}~?%*"');
    }

    if(!validIsNumberArr(node.enter.indcat)){
      delete(node.enter.indcat);
    }
    if(!validIsNumberArr(node.enter.area)){
      delete(node.enter.area);
    }

    location.replace(nowUrl+"?"+buildQuery(node.enter));
    return false;
  });

  function buildQuery(str){
    var qureyStr = '';
    for(var key in str){
      if(str[key] != ''){
        if(qureyStr == ''){
          qureyStr = key + '=' + encodeURIComponent(str[key]);
        }else{
          qureyStr = qureyStr + '&' + key + '=' + encodeURIComponent(str[key]);
        }
      }
    }
    return qureyStr;
  }

  function validIsNumberArr(str){
    var arr = str.split(",");
    var regExp = /^[\d]+$/;
    for(var k in arr){
      if (!regExp.test(arr[k]))
        return false;
    }
    return true;
  }

  function validSpecialChar(o){
    var regExp = /[\~\!\@\#\$\%\^\&\*\+]$/;
    if (regExp.test(o))
        return true;
    else
        return false;
  }
}


  function SaveJob(){
    var node = {};
    node.trigger = $('button.save');
    savedList();
    clickButton();

    function savedList() {
      if(getCookie('ID_CK') == '') return;
      var xhr = $.ajax({
        url: '../ajax/getList',
        cache: false,
        dataType: 'json',
        type: 'GET',
      });

      xhr.success(function(o) {
        if(o.status == 200){
          $.each(o.data.list, function (i, val) {
            $('.save[data-jno=' + val + ']').addClass('active');
          })
        }
      });
    }

    function clickButton(){
      node.trigger.on('click',function(){
        if(getCookie('ID_CK') == ''){
         document.location.href=loginUrl+'/login.cfm?return_url=' + encodeURIComponent(nowUrl);
         return;
        }
        var $t = $(this);
        $t.prop('disalbed', true).hide().prev().show();
        var query = 'custno='+$t.data('cno')+'&jobno='+$t.data('jno');
        if(!$t.hasClass('active')){
          //add
          var xhr = $.ajax({
            url: '../ajax/add?'+ query,
            type:'GET',
            dataType: 'json',
          });
          xhr.success(function(o){
            $t.prop('disabled', false).show().prev().hide();
            if(o.status == 201) {
              $t.addClass('active');
            }else if (o.status == 200) {
              alert ('儲存上限已達 200 筆！');
            }else if (o.status == 500) {
              alert ('儲存失敗，請稍後再試！');
            }
          });
        } else {
          //del
          var xhr = $.ajax({
            url: '../ajax/delete?'+ query,
            type:'GET',
            dataType: 'json',
          });
          xhr.success(function(o){
            $t.prop('disabled', false).show().prev().hide();
            if(o.status == 200) {
              $t.removeClass('active');
            } else if (o.status == 500) {
              alert ('取消儲存失敗，請稍後再試！');
            }
          });
        }
      });
    }
  }

  

  function getCookie(cname) {
    var name = cname + '=';
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
  }


new Search();
new SaveJob();