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

  // ===== Điều khiển dropdown theo breakpoint =====
  (function(){
    var BREAKPOINT = 992; // khớp CSS

    // Các parent link (item có submenu)
    var parents = document.querySelectorAll('.navbar-nav .menu-item-has-children > .nav-link');

    // Click trên parent
    parents.forEach(function(parentLink){
      parentLink.addEventListener('click', function(e){
        var li = parentLink.parentElement;

        if (window.innerWidth < BREAKPOINT) {
          // MOBILE: chặn điều hướng và toggle .open
          e.preventDefault();

          // Đóng các anh em đang mở (accordion nhẹ)
          var siblings = li.parentElement.querySelectorAll('.menu-item-has-children.open');
          siblings.forEach(function(sib){
            if (sib !== li) sib.classList.remove('open');
          });

          // Toggle hiện tại
          li.classList.toggle('open');
        } else {
          // DESKTOP: chặn điều hướng (CSS đã pointer-events:none)
          e.preventDefault();
        }
      });
    });

    // Khi click vào link trong submenu trên mobile: đóng menu chính + backdrop + dọn trạng thái
    var submenuLinks = document.querySelectorAll('.navbar-nav .sub-menu a');
    submenuLinks.forEach(function(a){
      a.addEventListener('click', function(){
        if (window.innerWidth < BREAKPOINT) {
          var inst = bootstrap.Collapse.getInstance(nav) || new bootstrap.Collapse(nav, { toggle: false });
          inst.hide();

          // Dọn trạng thái .open
          document.querySelectorAll('.menu-item-has-children.open').forEach(function(li){
            li.classList.remove('open');
          });
        }
      });
    });

    // Khi đổi kích thước màn hình: dọn trạng thái open để tránh “kẹt”
    window.addEventListener('resize', function(){
      if (window.innerWidth >= BREAKPOINT) {
        document.querySelectorAll('.menu-item-has-children.open').forEach(function(li){
          li.classList.remove('open');
        });
      }
    });
  })();
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
