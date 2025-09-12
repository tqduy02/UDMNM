document.addEventListener('DOMContentLoaded', function(){
  var nav = document.getElementById('primaryNav');
  var backdrop = document.querySelector('.navbar-backdrop');

  if (nav) {
    nav.addEventListener('shown.bs.collapse', function(){
      document.body.classList.add('nav-open','has-fixed-navbar');
    });
    nav.addEventListener('hidden.bs.collapse', function(){
      document.body.classList.remove('nav-open');
    });
  }
  if (backdrop){
    backdrop.addEventListener('click', function(){
      var inst = bootstrap.Collapse.getInstance(nav) || new bootstrap.Collapse(nav, {toggle:false});
      inst.hide();
    });
  }
});
document.addEventListener('DOMContentLoaded', function () {
  if (typeof Swiper !== 'undefined') {
    new Swiper('.hero-swiper', {
      loop: true,
      speed: 600,
      autoplay: { delay: 4500, disableOnInteraction: false },
      // Ẩn pagination; dùng navigation custom
      navigation: {
        nextEl: '.hero-next',
        prevEl: '.hero-prev',
      }
    });
  }
});
