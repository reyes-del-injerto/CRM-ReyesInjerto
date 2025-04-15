$(document).ready(function ($) {
  // Variables declarations
  var $wrapper = $(".main-wrapper");
  var $pageWrapper = $(".page-wrapper");
  var $slimScrolls = $(".slimscroll");
  var $sidebarOverlay = $(".sidebar-overlay");

  // Sidebar
  var Sidemenu = function () {
    this.$menuItem = $("#sidebar-menu a");
  };

  function init() {
    var $this = Sidemenu;
    $("#sidebar-menu a").on("click", function (e) {
      if ($(this).parent().hasClass("submenu")) {
        e.preventDefault();
      }
      if (!$(this).hasClass("subdrop")) {
        $("ul", $(this).parents("ul:first")).slideUp(350);
        $("a", $(this).parents("ul:first")).removeClass("subdrop");
        $(this).next("ul").slideDown(350);
        $(this).addClass("subdrop");
      } else if ($(this).hasClass("subdrop")) {
        $(this).removeClass("subdrop");
        $(this).next("ul").slideUp(350);
      }
    });
    $("#sidebar-menu ul li.submenu a.active")
      .parents("li:last")
      .children("a:first")
      .addClass("active")
      .trigger("click");
  }
  // Sidebar Initiate
  init();

  // Sidebar overlay
  function sidebar_overlay($target) {
    if ($target.length) {
      $target.toggleClass("opened");
      $sidebarOverlay.toggleClass("opened");
      $("html").toggleClass("menu-opened");
      $sidebarOverlay.attr("data-reff", "#" + $target[0].id);
    }
  }

  // Mobile menu sidebar overlay
  $(document).on("click", "#mobile_btn", function () {
    var $target = $($(this).attr("href"));
    sidebar_overlay($target);
    $wrapper.toggleClass("slide-nav");
    $("#chat_sidebar").removeClass("opened");
    return false;
  });

  // Chat sidebar overlay
  $(document).on("click", "#task_chat", function () {
    var $target = $($(this).attr("href"));
    console.log($target);
    sidebar_overlay($target);
    return false;
  });

  // Sidebar overlay reset
  $sidebarOverlay.on("click", function () {
    var $target = $($(this).attr("data-reff"));
    if ($target.length) {
      $target.removeClass("opened");
      $("html").removeClass("menu-opened");
      $(this).removeClass("opened");
      $wrapper.removeClass("slide-nav");
    }
    return false;
  });

  // Floating Label
  if ($(".floating").length > 0) {
    $(".floating")
      .on("focus blur", function (e) {
        $(this)
          .parents(".form-focus")
          .toggleClass("focused", e.type === "focus" || this.value.length > 0);
      })
      .trigger("blur");
  }

  // Right Sidebar Scroll
  if ($("#msg_list").length > 0) {
    $("#msg_list").slimscroll({
      height: "100%",
      color: "#878787",
      disableFadeOut: true,
      borderRadius: 0,
      size: "4px",
      alwaysVisible: false,
      touchScrollStep: 100,
    });
    var msgHeight = $(window).height() - 124;
    $("#msg_list").height(msgHeight);
    $(".msg-sidebar .slimScrollDiv").height(msgHeight);
    $(window).resize(function () {
      var msgrHeight = $(window).height() - 124;
      $("#msg_list").height(msgrHeight);
      $(".msg-sidebar .slimScrollDiv").height(msgrHeight);
    });
  }

  // Left Sidebar Scroll
  if ($slimScrolls.length > 0) {
    $slimScrolls.slimScroll({
      height: "auto",
      width: "100%",
      position: "right",
      size: "7px",
      color: "#ccc",
      wheelStep: 10,
      touchScrollStep: 100,
    });
    var wHeight = $(window).height() - 60;
    $slimScrolls.height(wHeight);
    $(".sidebar .slimScrollDiv").height(wHeight);
    $(window).resize(function () {
      var rHeight = $(window).height() - 60;
      $slimScrolls.height(rHeight);
      $(".sidebar .slimScrollDiv").height(rHeight);
    });
  }

  // Page wrapper height
  var pHeight = $(window).height();
  $pageWrapper.css("min-height", pHeight);
  $(window).resize(function () {
    var prHeight = $(window).height();
    $pageWrapper.css("min-height", prHeight);
  });

  // Datetimepicker
  if ($(".datetimepicker").length > 0) {
    $(".datetimepicker").datetimepicker({
      format: "DD/MM/YYYY",
      icons: {
        up: "fas fa-angle-up",
        down: "fas fa-angle-down",
        next: "fas fa-angle-right",
        previous: "fas fa-angle-left",
      },
    });
  }

  // Time
  if ($(".datetimepicker-time").length > 0) {
    $(function () {
      $(".datetimepicker-time").datetimepicker({
        format: "LT",
        icons: {
          up: "fas fa-angle-up",
          down: "fas fa-angle-down",
          next: "fas fa-angle-right",
          previous: "fas fa-angle-left",
        },
      });
    });
  }

  // Bootstrap Tooltip
  if ($('[data-toggle="tooltip"]').length > 0) {
    $('[data-toggle="tooltip"]').tooltip();
  }

  // Mobile Menu
  $(document).on("click", "#open_msg_box", function () {
    $wrapper.toggleClass("open-msg-box");
    return false;
  });

  //Small Sidebar
  if (screen.width >= 992) {
    $(document).on("click", "#toggle_btn", function () {
      if ($("body").hasClass("mini-sidebar")) {
        $(".header-left").fadeIn();
        $("body").removeClass("mini-sidebar");
        $(".subdrop + ul").slideDown();
      } else {
        $(".header-left").fadeOut();
        $("body").addClass("mini-sidebar");
        $(".subdrop + ul").slideUp();
      }
      return false;
    });
  }
  //! MOUSEOVER SIDEBAR
  $(document).on("mouseover", function (e) {
    e.stopPropagation();
    if ($("body").hasClass("mini-sidebar") && $("#toggle_btn").is(":visible")) {
      var targ = $(e.target).closest(".sidebar").length;
      if (targ) {
        $("body").addClass("expand-menu");
        $(".subdrop + ul").slideDown();
      } else {
        $("body").removeClass("expand-menu");
        $(".subdrop + ul").slideUp();
      }
      return false;
    }
  });

  $(document).on("click", ".logo-hide-btn", function () {
    $(this).parent().hide();
  });

  $(".app-listing .selectBox").on("click", function () {
    $(this).parent().find("#checkBoxes").fadeToggle();
    $(this).parent().parent().siblings().find("#checkBoxes").fadeOut();
  });
  $(".invoices-main-form .selectBox").on("click", function () {
    $(this).parent().find("#checkBoxes-one").fadeToggle();
    $(this).parent().parent().siblings().find("#checkBoxes-one").fadeOut();
  });
});

function showSweetAlert(
  title,
  text,
  icon,
  timer,
  timerProgressBar,
  showConfirmButton
) {
  return Swal.fire({
    title: title || "Error",
    text: text || "Contacta a administración",
    icon: icon || "error",
    timer: timer || 2500,
    timerProgressBar: timerProgressBar || true,
    showConfirmButton: showConfirmButton || false,
  });
}
