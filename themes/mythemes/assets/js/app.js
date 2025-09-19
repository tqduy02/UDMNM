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
    // chỉ bật padding-top cho body ở mobile
    body.classList.toggle("has-fixed-navbar", isMobile());
  }

  // ===== Navbar collapse + backdrop
  document.addEventListener("DOMContentLoaded", function () {
    // Đồng bộ trạng thái ban đầu
    syncFixedNavbarFlag();

    if (nav) {
      nav.addEventListener("shown.bs.collapse", function () {
        body.classList.add("nav-open");
        syncFixedNavbarFlag();
        if (backdrop) {
          backdrop.style.opacity = "1";
          backdrop.style.visibility = "visible";
        }
      });

      nav.addEventListener("hidden.bs.collapse", function () {
        body.classList.remove("nav-open");
        if (backdrop) {
          backdrop.style.opacity = "0";
          backdrop.style.visibility = "hidden";
        }
        if (!isMobile()) body.classList.remove("has-fixed-navbar");
      });
    }

    // Click backdrop để đóng
    if (backdrop) {
      backdrop.addEventListener("click", function () {
        closeOffcanvas();
      });
    }

    // ===== Submenu: behavior theo breakpoint
    (function () {
      // Parent links (item có submenu)
      var parents = document.querySelectorAll(".navbar-nav .menu-item-has-children > .nav-link");

      parents.forEach(function (parentLink) {
        parentLink.addEventListener("click", function (e) {
          var li = parentLink.parentElement;

          if (isMobile()) {
            // MOBILE: accordion
            e.preventDefault();

            // Đóng các anh em đang mở
            var siblings = li.parentElement.querySelectorAll(".menu-item-has-children.open");
            siblings.forEach(function (sib) { if (sib !== li) sib.classList.remove("open"); });

            li.classList.toggle("open");
          } else {
            // DESKTOP: chỉ hover (nếu muốn parent click đi trang thì bỏ preventDefault)
            e.preventDefault();
          }
        });
      });

      // Click link trong submenu (mobile) => đóng panel
      var submenuLinks = document.querySelectorAll(".navbar-nav .sub-menu a");
      submenuLinks.forEach(function (a) {
        a.addEventListener("click", function () {
          if (isMobile()) closeOffcanvas();
        });
      });
    })();

    // ===== Resize: reset khi về desktop (fix khoảng trắng/padding)
    window.addEventListener("resize", function () {
      syncFixedNavbarFlag();
      if (!isMobile()) closeOffcanvas();
    });

    // ===== Rút gọn nhãn ngôn ngữ (nếu bạn có .lang-label): chỉ VI / EN
    (function () {
      var MAP = {
        vi: "VI", "vi-vn": "VI", vietnamese: "VI",
        en: "EN", "en-us": "EN", english: "EN"
      };
      var labels = document.querySelectorAll(".lang-label");

      labels.forEach(function (el) {
        var txt = (el.textContent || "").trim().toLowerCase();
        // ưu tiên đọc từ src cờ nếu có
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

    // ===== Swiper hero (nếu dùng)
    if (typeof Swiper !== "undefined") {
      new Swiper(".hero-swiper", {
        loop: true,
        speed: 600,
        autoplay: { delay: 4500, disableOnInteraction: false },
        navigation: { nextEl: ".hero-next", prevEl: ".hero-prev" }
      });
    }

    // ===== Page transition (menu click => slide)
    (function pageTransition() {
      // Khi load trang: áp enter animation
      body.classList.add("page-enter");
      requestAnimationFrame(function () {
        body.classList.add("page-enter-active");
      });

      // Click menu links: exit rồi chuyển trang
      var navLinks = document.querySelectorAll(".navbar-nav .nav-link");
      navLinks.forEach(function (link) {
        link.addEventListener("click", function (e) {
          var url = link.getAttribute("href");

          // Bỏ qua anchor tại chỗ, điện thoại, mailto, và target=_blank
          var isAnchor = url && url.charAt(0) === "#";
          var isExternal = url && (/^mailto:|^tel:/i).test(url);
          var isBlank = link.target === "_blank";

          if (!url || isAnchor || isExternal || isBlank) return;

          // Nếu đang ở mobile và link là parent của submenu -> đã xử lý ở trên
          var parentIsMenu = link.closest(".menu-item-has-children");
          if (parentIsMenu && isMobile()) return;

          e.preventDefault();

          // Đóng offcanvas nếu đang mở
          if (isMobile()) closeOffcanvas();

          // Exit animation
          body.classList.add("page-exit");
          requestAnimationFrame(function () {
            body.classList.add("page-exit-active");
          });

          // thời gian khớp với CSS (.6s)
          setTimeout(function () {
            window.location.href = url;
          }, 600);
        });
      });
    })();
  });
})();
