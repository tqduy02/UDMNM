
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
