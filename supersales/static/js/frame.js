function xx (t) {console.log(t)};

function NavActive () {
  var node = {};
  var pos = {};
  node.nav = $('#nav');
  node.itms = node.nav.find('a');
  node.active = node.nav.find('.active'); 
  node.effect = node.nav.find('.hover');
  setPos(node.active); 
  
  node.itms.on('mouseenter', function () {
    setPos($(this));
  })
  node.nav.on('mouseleave', function () {
    node.effect.css('top', '50px');
  })

  function setPos (target) {
    pos.left = target.position().left;
    pos.width = target.outerWidth(true);
    node.effect.css({
      top: 0,
      left: pos.left,
      width: pos.width
    });
  }
}

new NavActive();
