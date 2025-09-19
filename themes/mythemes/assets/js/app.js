// assets/js/app.js
(function () {
  "use strict";

  var BREAKPOINT = 992; // khớp CSS
  var body = document.body;
  var nav = document.getElementById("primaryNav");
  var backdrop = document.querySelector(".navbar-backdrop");

  // ===== Helpers
  function isMobile() { return window.innerWidth < BREAKPOINT; }
  function collapseInstance() {
    if (!nav || typeof bootstrap === "undefined" || !bootstrap.Collapse) return null;
    return bootstrap.Collapse.getInstance(nav) || new bootstrap.Collapse(nav, { toggle: false });
  }
  function closeOffcanvas() {
    var inst = collapseInstance();
    if (inst) inst.hide();
    body.classList.remove("nav-open");
    if (backdrop) {
      backdrop.style.opacity = "0";
      backdrop.style.visibility = "hidden";
    }
    document.querySelectorAll(".menu-item-has-children.open").forEach(function (li) {
      li.classList.remove("open");
    });
  }
  function syncFixedNavbarFlag() {
    body.classList.toggle("has-fixed-navbar", isMobile());
  }

  document.addEventListener("DOMContentLoaded", function () {
    // trạng thái ban đầu
    syncFixedNavbarFlag();

    if (nav) {
      nav.addEventListener("shown.bs.collapse", function () {
        body.classList.add("nav-open");
        syncFixedNavbarFlag();
        if (backdrop) { backdrop.style.opacity = "1"; backdrop.style.visibility = "visible"; }
      });

      nav.addEventListener("hidden.bs.collapse", function () {
        body.classList.remove("nav-open");
        if (backdrop) { backdrop.style.opacity = "0"; backdrop.style.visibility = "hidden"; }
        if (!isMobile()) body.classList.remove("has-fixed-navbar");
      });
    }

    // Backdrop click -> đóng
    if (backdrop) {
      backdrop.addEventListener("click", function () { closeOffcanvas(); });
    }

    // Nút X đóng menu (mobile)
    document.addEventListener("click", function(e){
      var closeBtn = e.target.closest(".btn-close-menu");
      if (closeBtn) { closeOffcanvas(); }
    });

    // Submenu: behavior theo breakpoint
    (function () {
      var parents = document.querySelectorAll(".navbar-nav .menu-item-has-children > .nav-link");
      parents.forEach(function (parentLink) {
        parentLink.addEventListener("click", function (e) {
          var li = parentLink.parentElement;
          if (isMobile()) {
            e.preventDefault();
            // đóng anh em
            var siblings = li.parentElement.querySelectorAll(".menu-item-has-children.open");
            siblings.forEach(function (sib) { if (sib !== li) sib.classList.remove("open"); });
            li.classList.toggle("open");
          } else {
            // desktop: chỉ hover; muốn đi link thì bỏ preventDefault
            e.preventDefault();
          }
        });
      });

      // click link trong submenu (mobile) -> đóng panel
      var submenuLinks = document.querySelectorAll(".navbar-nav .sub-menu a");
      submenuLinks.forEach(function (a) {
        a.addEventListener("click", function () { if (isMobile()) closeOffcanvas(); });
      });
    })();

    // Resize: reset khi về desktop (fix khoảng trắng/padding)
    window.addEventListener("resize", function () {
      syncFixedNavbarFlag();
      if (!isMobile()) closeOffcanvas();
    });

    // Rút gọn nhãn ngôn ngữ (.lang-label) -> VI/EN
    (function () {
      var MAP = { vi: "VI", "vi-vn": "VI", vietnamese: "VI", en: "EN", "en-us": "EN", english: "EN" };
      var labels = document.querySelectorAll(".lang-label");
      labels.forEach(function (el) {
        var txt = (el.textContent || "").trim().toLowerCase();
        var wrapper = el.closest(".btn-lang");
        var flagImg = wrapper ? wrapper.querySelector("img") : null;
        if (flagImg && flagImg.src) {
          if (/[\W_](vi|vn)[\W_]/i.test(flagImg.src)) { el.textContent = "VI"; return; }
          if (/[\W_](en|gb|us|uk)[\W_]/i.test(flagImg.src)) { el.textContent = "EN"; return; }
        }
        if (MAP[txt]) { el.textContent = MAP[txt]; return; }
        el.textContent = (el.textContent || "").trim().slice(0, 2).toUpperCase();
      });
    })();

    // Swiper hero (nếu dùng)
    if (typeof Swiper !== "undefined") {
      new Swiper(".hero-swiper", {
        loop: true,
        speed: 600,
        autoplay: { delay: 4500, disableOnInteraction: false },
        navigation: { nextEl: ".hero-next", prevEl: ".hero-prev" }
      });
    }
  });
})();
