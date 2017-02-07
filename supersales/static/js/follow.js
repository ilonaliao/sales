function RenderPages (root, itm, perPage, maxPages) {
  var node = {};
  var nums = {};

  node.root = $(root);
  node.itms = node.root.find(itm);
  node.proot = node.root.find('.page-ctrl');

  node.prev = $('<a class="page-prev">上一頁</a>');
  node.next = $('<a class="page-next">下一頁</a>');
  node.more = $('<span class="page-ellipsis">...</span>');

  init();
  
  function init () {
    nums.curPage = 1;
    nums.itmsLength = node.itms.length;
    nums.totalPages = Math.ceil(nums.itmsLength/perPage);
    nums.perPage = perPage;
    nums.maxPages = maxPages;
    
    pageAction();
  }
  
  node.proot.on('click', '.page-num', function () {
    if (nums.curPage == $(this).data('num')) return false;
    nums.curPage = $(this).data('num');
    pageAction();
  });

  node.proot.on('click', '.page-next', function () {
    if (nums.curPage >= nums.totalPages) return false;
    nums.curPage = nums.curPage + 1;
    pageAction();
  });
  node.proot.on('click', '.page-prev', function () {
    if (nums.curPage <= 1) return false;
    nums.curPage = nums.curPage - 1;
    pageAction();
  });

  function pageAction () {
    var start = nums.curPage * nums.perPage - nums.perPage; 

    node.itms.hide();
    renderPages();
    showItems(start);
  };

  function showItems (start) {
    for (var i = start; i < start + nums.perPage; i++) {
      node.itms.eq(i).show();
    }
  };

  function renderPages () {
    var cur = nums.curPage;
    var atLastPage = (cur > nums.totalPages - nums.maxPages);
    var startNum = (atLastPage)?nums.totalPages - nums.maxPages + 1:cur;
    node.proot.html('');

    // If total pages less than 1, dont render any content.
    if (nums.totalPages <= 1) return false;   

    //Start render the number pages !!
    displayPrevNext();
    node.proot.append(node.prev);
    renderNums();
    node.proot.append(node.next);
    appendEllipsis();
     
    function displayPrevNext() {
      node.prev.removeClass('disabled'); 
      node.next.removeClass('disabled'); 
      if (cur == 1) node.prev.addClass('disabled');
      if (cur == nums.totalPages) node.next.addClass('disabled');
    };

    function renderNums () {
      for (var i = startNum; i <= startNum + nums.maxPages - 1; i++) {
        var pnums =  $('<a class="page-num">').text(i).attr('data-num', i);
        if (i == cur) pnums.addClass('active');
        node.proot.append(pnums);
      }
    };

    function appendEllipsis () {
      if (nums.maxPages < nums.totalPages) {
        if (atLastPage) {
          node.prev.after(node.more);
        }else{
          node.next.before(node.more);
        }
      };
    };
  };
};

var juniorPage = new RenderPages('#junior', '.itm', 8, 5);
