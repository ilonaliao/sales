function Lightbox (root) {
  var node = {},
      nums = {};
  node.root = $(root);
  node.itm = node.root.find('.itm');
  node.scene = $('.lightbox-scene').eq(0);
  prepare();
  
  node.itm.on('click', showLightbox);
  $(window).on('resize', closeLightbox);
  node.scene.on('click', closeLightbox);
  node.close.on('click', closeLightbox);
  node.main.on('click', function (evt) {evt.stopPropagation()});

  function prepare () {
    node.main = $('<div class="lightbox-main">').appendTo(node.scene);
    node.con = $('<div class="lightbox-con">').appendTo(node.main);
    node.img = $('<img>').appendTo(node.con);
    node.close = $('<button type="button" class="lightbox-close" title="close">').appendTo(node.main);
  } 

  function calc() {
    var ww = $(window).width();
        wh = $(window).height();
    nums.winRatio = ww / wh;
    nums.maxWidth = ww * 0.8;
    nums.maxHeight = wh * 0.8;
    node.main.css({
      maxWidth: nums.maxWidth,
      maxHeight: nums.maxHeight
    });
  }

  
  function closeLightbox (evt) {
    evt.stopPropagation();
    node.scene.fadeOut(300, function () {
      node.con.attr('style', '');
      node.img.attr('style', '');
    });
  }
  
  function showLightbox (evt) {
    evt.stopPropagation();
    evt.preventDefault();
    var origin = $(this).attr('href');
    calc(); 
    node.img.attr('src', origin);
    node.scene.fadeIn(); 
  }

  node.img.on('load', function () {
    nums.imgWidth = node.img.prop('naturalWidth');
    nums.imgHeight = node.img.prop('naturalHeight');

    var w = (nums.imgWidth > nums.maxWidth)?nums.maxWidth:nums.imgWidth;
        h = (nums.imgHeight > nums.maxHeight)?nums.maxHeight:nums.imgHeight;
        r = nums.imgWidth / nums.imgHeight; 

    nums.setWidth = (r > nums.winRatio)?(w - 20):((h - 20) * r);
    nums.setHeight = (r > nums.winRatio)?((w - 20) / r):(h - 20);

    node.con.css({
      width: nums.setWidth,
      height: nums.setHeight 
    })
    node.main.css({
      marginTop: -(nums.setHeight+20)/2,
      marginLeft: -(nums.setWidth+20)/2
    })
    node.img.css('opacity', 1);
  });
}

new Lightbox('.albums');
