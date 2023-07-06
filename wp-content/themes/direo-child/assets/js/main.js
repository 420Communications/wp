/* eslint-disable */
(function ($) {

  var initSetup;

  $(document).ready(function () {
    // single listing
    const single_listing = $(".atbd_single_listing");
    const slWidth = single_listing.width();
    if (slWidth <= 300) {
      single_listing.addClass("rs_fix");
    }

    //menu fix
    const menuHeight = $(".menu-area").innerHeight();
    if ($(".menu-area:not(.menu--transparent)").hasClass("menu--light") || $(".menu-area:not(.menu--transparent)").hasClass("menu--dark")) {
      $(".header-breadcrumb").addClass("height-fix");
      $(".menu-area + .elementor").addClass("height-fix");
    }
    $(".height-fix").css("margin-top", menuHeight);
    if ($('.admin-bar').length !== 0) {
      $(".admin-bar .directorist-type-nav--listings-map").css("top", (menuHeight + 30));
    } else {
      $(".directorist-type-nav--listings-map").css("top", (menuHeight));
    }


    // mobile menu fix
    $(".menu-item.menu-item-has-children > a").on("click", function (e) {
      if ($(this).attr('href') === '#') {
        e.preventDefault();
      }
    });
    if ($(window).width() < 991) {
      $(".menu-item.menu-item-has-children > a").css("display", "block");
      $(".menu-item.menu-item-has-children > a").on("click", function (e) {
        e.preventDefault();
        $(this).parent(".menu-item.menu-item-has-children").toggleClass("active");
      });
    }

    //author menu dropdown
    $(".author-info .avatar").on("click", function (e) {
      $(this).siblings("ul").toggleClass("active");
      if ($(".cart_module .cart__items").length) {
        $(".cart_module .cart__items").removeClass("active");
      }
      e.stopPropagation();
    });
    //cart dropdown
    $(document).on("click", ".cart_module .cart__icon", function (e) {
      $(this).siblings(".cart__items").toggleClass("active");
      if ($(".author-info ul").length) {
        $(".author-info ul").removeClass("active");
      }
      e.stopPropagation();
    });

    $(document).on("click", function (e) {
      if (e.currentTarget !== $(".author-info .avatar")) {
        $(".author-info ul").removeClass("active");
      }
      if (e.currentTarget !== $(".cart_module .cart__icon") && e.target !== $(".cart_module .cart__items")) {
        $(".cart_module .cart__items").removeClass("active");
      }
    });

    // enable bootstrap tooltip
    $('[data-toggle="tooltip"]').tooltip();
    const rtl = direo_rtl.rtl === "true";

    // testimonial-carousel
    $(".testimonial-carousel").owlCarousel({
      items: 1,
      dots: false,
      rtl,
      nav: true,
      navText: [
        '<span class="i la la-long-arrow-left"></span>',
        '<span class="i la la-long-arrow-right"></span>',
      ],
    });

    //Listing carousel
    $(".listing-carousel").owlCarousel({
      items: 5,
      rtl,
      nav: true,
      navText: [
        '<span class="la la-long-arrow-left"></span>',
        '<span class="la la-long-arrow-right"></span>',
      ],
      dots: true,
      margin: 30,
      responsive: {
        0: {
          items: 1,
        },
        400: {
          items: 1,
        },
        575: {
          items: 2,
        },
        767: {
          items: 3,
        },
        991: {
          items: 4,
        },
        1191: {
          items: 5,
        },
      },
    });
    //Listing carousel
    $(".category-slider").owlCarousel({
      items: 6,
      rtl,
      nav: true,
      navText: [
        '<span class="la la-long-arrow-left"></span>',
        '<span class="la la-long-arrow-right"></span>',
      ],
      dots: true,
      margin: 30,
      responsive: {
        0: {
          items: 1,
        },
        400: {
          items: 1,
        },
        575: {
          items: 2,
        },
        767: {
          items: 3,
        },
        991: {
          items: 4,
        },
        1191: {
          items: 6,
        },
      },
    });


    // logo carousel
    $(".logo-carousel").owlCarousel({
      items: 5,
      nav: false,
      dots: false,
      margin: 100,
      responsive: {
        0: {
          items: 1,
          margin: 0,
        },
        400: {
          items: 2,
        },
        575: {
          items: 3,
        },
        767: {
          items: 3,
        },
        991: {
          items: 5,
        },
      },
    });

    // setting css bg image as inline in html
    $(".bg_image_holder").each(function () {
      const $this = $(this);
      let imgLink;
      if ($this.children().attr("data-lazy-src")) {
        imgLink = $this.children().attr("data-lazy-src");
      } else if ($this.children().attr("data-src")) {
        imgLink = $this.children().attr("data-src");
      } else {
        imgLink = $this.children().attr("src");
      }
      // console.log(imgLink);
      $this
        .css({
          "background-image": `url(${imgLink})`,
          opacity: "1",
        })
        .children()
        .attr("alt", imgLink);
    });

    /* FAQ Accordion */
    $("p.dac_body").hide();
    $(".dacc_single > h3 > a").on("click", function (e) {
      const $this = $(this);
      $this.parent().next().slideToggle();
      $this
        .parent()
        .parents(".dacc_single")
        .siblings(".dacc_single")
        .children("p.dac_body")
        .slideUp();
      $this.toggleClass("active");
      $this
        .parent()
        .parents(".dacc_single")
        .siblings(".dacc_single")
        .children("h3")
        .children("a")
        .removeClass("active");
      e.preventDefault();
    });

    // counter
    $(".count_up").counterUp({
      time: 1000,
    });

    /* offcanvas menu */
    const oc_menu = $(".offcanvas-menu__contents");
    $(".offcanvas-menu__user").on("click", function (e) {
      oc_menu.addClass("active");
      e.preventDefault();
    });
    $(".offcanvas-menu__close").on("click", function (e) {
      oc_menu.removeClass("active");
      e.preventDefault();
    });

    // Video Popup
    $(".video-iframe").magnificPopup({
      type: "iframe",
      iframe: {
        markup: '<div class="mfp-iframe-scaler">' +
          '<div class="mfp-close"></div>' +
          '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
          "</div>",
        patterns: {
          youtube: {
            index: "youtube.com/",
            id(url) {
              const m = url.match(/[\\?\\&]v=([^\\?\\&]+)/);
              if (!m || !m[1]) return null;
              return m[1];
            },
            src: "//www.youtube.com/embed/%id%?rel=0&autoplay=1",
          },
          vimeo: {
            index: "vimeo.com/",
            id(url) {
              const m = url.match(
                /(https?:\/\/)?(www.)?(player.)?vimeo.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/
              );
              if (!m || !m[5]) return null;
              return m[5];
            },
            src: "//player.vimeo.com/video/%id%?autoplay=1",
          },
        },
        srcAction: "iframe_src",
      },
      mainClass: "mfp-fade",
    });

    /* Search Dropdown */
    $("body").on("click", ".directorist-filter-btn", function () {
      $(this).closest(".directorist-filter-btn").toggleClass("active");
    });

    $("body").on("click", ".theme-search-dropdown .theme-search-dropdown__label", function () {
      $(this).closest(".theme-search-dropdown").toggleClass("active");
    });

    $('body').on('click', function (e) {
      if (!$(e.target).closest('.theme-search-dropdown').length) {
        $(".theme-search-dropdown").removeClass("active");
      }
    });

    // blog single page
    // Style for category, if assigned category is more than 4
    const cats = $(".post-meta li:nth-child(3) a");
    if (cats.length > 3) {
      $(".post-meta li:nth-child(3)").addClass("order-3");
    }

    // body class in `listing with map` page
    $("#listing-listings_with_map")
      .parent()
      .parents("body")
      .addClass("atbdp_listings_map_page");

    // all listing sort status
    const CurrentUrl = document.URL;
    const CurrentUrlEnd = CurrentUrl.split("/").filter(Boolean).pop();
    if ($(".view-mode .action-btn")) {
      $(".view-mode .action-btn").each(function () {
        const ThisUrl = $(this).attr("href");
        const ThisUrlEnd = ThisUrl.split("/").filter(Boolean).pop();
        if (ThisUrlEnd === CurrentUrlEnd) {
          $(this).addClass("active");
        }
      });
    }

    const acbtn = $(".view-mode .action-btn:first-child");
    if (acbtn.siblings().hasClass("active") === true) {
      acbtn.removeClass("active");
    }
    if ($(".view-mode .action-btn").hasClass("active") === true) {
      $(".view-mode .action-btn")
        .siblings()
        .removeClass("ab-grid ab-list ab-map");
    }

    $(".atbd_add_listing_wrapper label")
      .has("input")
      .append("<span class='cf-select'></span>");

    $("#signup_modal")
      .find(".container-fluid, .row, .col-md-8.offset-md-2")
      .removeClass();
    $("#signup_modal").find(".add_listing_title").remove();

    $(".recover-pass-form").hide();
    $(".recover-pass-link").on("click", function (e) {
      e.preventDefault();
      $(".recover-pass-form").slideToggle().show();
    });

    // woocommerce checkout confirm address fields collapse option
    $(".woocommerce-columns address").hide();
    $(".woocommerce-column .woocommerce-column__title").on("click", function () {
      $(this).toggleClass("active");
      $(this).next().slideToggle().show();
    });

    $("body").on("change", "#at_biz_dir-categories", function (e) {
      var clearInt = setInterval(function () {
        if ($(".atbdp-checkbox-list label .cf-select").length > 0) {
          clearInterval(clearInt);
        }
        $(".atbd_add_listing_wrapper label")
          .has("input")
          .append("<span class='cf-select'></span>");
      }, 100);
    });

    // fixing widgets select options long sentence
    const maxLength = 30;
    $(".widget select > option").text(function (i, text) {
      if (text.length > maxLength) {
        return `${text.substr(0, maxLength)}...`;
      }
    });

    // set widget social icon background from it's color property
    function rgb2hex(rgb) {
      rgb = rgb.match(
        /^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i
      );
      return rgb && rgb.length === 4 ?
        `#${`0${parseInt(rgb[1], 10).toString(16)}`.slice(-2)}${`0${parseInt(
          rgb[2],
          10
        ).toString(16)}`.slice(-2)}${`0${parseInt(rgb[3], 10).toString(
          16
        )}`.slice(-2)}` :
        "";
    }

    function hex2rgba(hex, opacity) {
      // extract the two hexadecimal digits for each color
      const patt = /^#([\da-fA-F]{2})([\da-fA-F]{2})([\da-fA-F]{2})$/;
      const matches = patt.exec(hex);
      // convert them to decimal
      const r = parseInt(matches[1], 16);
      const g = parseInt(matches[2], 16);
      const b = parseInt(matches[3], 16);
      // create rgba string
      const rgba = `rgba(${r},${g},${b},${opacity})`;
      // return rgba color
      return rgba;
    }

    const s_icon = $(".social-list li span.instagram i");
    s_icon.each(function () {
      const si_color = $(this).css("color");
      const si_color_hex = rgb2hex(si_color);
      $(this).css("background", hex2rgba(si_color_hex, 0.1));
    });

    const ci_color = $(
      "#category-style-two #directorist.atbd_wrapper .atbd_all_categories .atbd_category_single figure figcaption .cat-box .icon span"
    );
    ci_color.each(function () {
      const ci_color_value = $(this).css("color");
      const ci_color_hex = rgb2hex(ci_color_value);
      $(this).parent(".icon").css("background", hex2rgba(ci_color_hex, 0.1));
    });

    const fi_color = $(".feature-box-wrapper li .icon span");
    fi_color.each(function () {
      const fi_color_value = $(this).css("color");
      const fi_color_hex = rgb2hex(fi_color_value);
      $(this).parent(".icon").css("background", hex2rgba(fi_color_hex, 0.1));
    });

    // remove image from category style two
    $("#category-style-two .atbd_category_single figure img").remove();

    // location list sub items expander style
    $(".expander").on("click", function () {
      const txt = $(this).text() === "+" ? "-" : "+";
      $(this).text(txt);
    });

    $(".atbd_more-filter-contents > div").wrapAll("<div></div>");

    /*
     * Replace all SVG images with inline SVG
     */
    $("img.svg").each(function () {
      const $img = $(this);
      const imgID = $img.attr("id");
      const imgClass = $img.attr("class");
      const imgURL = $img.attr("src");

      $.get(
        imgURL,
        function (data) {
          // Get the SVG tag, ignore the rest
          let $svg = jQuery(data).find("svg");

          // Add replaced image's ID to the new SVG
          if (typeof imgID !== "undefined") {
            $svg = $svg.attr("id", imgID);
          }
          // Add replaced image's classes to the new SVG
          if (typeof imgClass !== "undefined") {
            $svg = $svg.attr("class", `${imgClass} replaced-svg`);
          }

          // Remove any invalid XML tags as per http://validator.w3.org
          $svg = $svg.removeAttr("xmlns:a");

          // Replace image with new SVG
          $img.replaceWith($svg);
        },
        "xml"
      );
    });

    // clear form data when dismiss the modal
    $("[data-dismiss=modal]").on("click", function (e) {
      const $t = $(this);
      const target =
        $t[0].href || $t.data("target") || $t.parents(".modal") || [];

      $(target)
        .find("input,textarea,select")
        .val("")
        .end()
        .find("input[type=checkbox], input[type=radio]")
        .prop("checked", "")
        .end();
      $('.alert').remove();
    });

    /*= ===========custom dropdown============ */
    //const atbdDropdown = document.querySelectorAll(".atbd-dropdown");

    // toggle dropdown
    /* let clickCount = 0;
      if (atbdDropdown !== null) {
      atbdDropdown.forEach(function (el) {
        el.querySelector(".atbd-dropdown-toggle").addEventListener(
          "click",
          function (e) {
            e.preventDefault();
            clickCount++;
            if (clickCount % 2 === 1) {
              document
                .querySelectorAll(".atbd-dropdown-items")
                .forEach(function (elem) {
                  elem.classList.remove("atbd-show");
                });
              el.querySelector(".atbd-dropdown-items").classList.add("atbd-show");
            } else {
              document
                .querySelectorAll(".atbd-dropdown-items")
                .forEach(function (elem) {
                  elem.classList.remove("atbd-show");
                });
            }
          }
        );
      });
    } */

    // remvoe toggle when click outside
    /* document.body.addEventListener("click", function (e) {
      if (e.target.getAttribute("data-drop-toggle") !== "atbd-toggle") {
        clickCount = 0;
        document.querySelectorAll(".atbd-dropdown-items").forEach(function (el) {
          el.classList.remove("atbd-show");
        });
      }
    }); */

    // custom select
    /* const atbdSelect = document.querySelectorAll(".atbd-drop-select");
    if (atbdSelect !== null) {
      atbdSelect.forEach(function (el) {
        el.querySelectorAll(".atbd-dropdown-item").forEach(function (item) {
          item.addEventListener("click", function (e) {
            e.preventDefault();
            el.querySelector(".atbd-dropdown-toggle").textContent =
              item.textContent;
            el.querySelectorAll(".atbd-dropdown-item").forEach(function (elm) {
              elm.classList.remove("atbd-active");
            });
            item.classList.add("atbd-active");
          });
        });
      });
    } */

    // select data-status
    /* const atbdSelectData = document.querySelectorAll(".atbd-drop-select.with-sort");
    atbdSelectData.forEach(function (el) {
      el.querySelectorAll(".atbd-dropdown-item").forEach(function (item) {
        const ds = el.querySelector(".atbd-dropdown-toggle");
        const itemds = item.getAttribute("data-status");
        item.addEventListener("click", function () {
          ds.setAttribute("data-status", `$//{itemds}`);
        //});
      //});
    //}); */

    const flatWrapper = document.querySelector(".flatpickr-calendar");
    const fAvailableTime = document.querySelector(".bdb-available-time-wrapper");
    if (flatWrapper != null && fAvailableTime != null) {
      flatWrapper.insertAdjacentElement("beforeend", fAvailableTime);
    }

    //Hide contact form
    if ($("#hide_contact_info").length) {
      document
        .querySelector("input#hide_contact_info")
        .addEventListener("change", function (e) {
          if (this.checked) {
            document.querySelector("#atbdp_zip").style.display =
              "none";
            document.querySelector("#atbdp_phone").style.display = "none";
            document.querySelector("#atbdp_phone2").style.display = "none";
            document.querySelector("#atbdp_fax").style.display = "none";
            document.querySelector("#atbdp_emails").style.display = "none";
            document.querySelector("#atbdp_webs").style.display = "none";
            document.querySelector("#atbdp_socialInFo").style.display = "none";
          } else {
            document.querySelector("#atbdp_zip").style.display =
              "block";
            document.querySelector("#atbdp_phone").style.display = "block";
            document.querySelector("#atbdp_phone2").style.display = "block";
            document.querySelector("#atbdp_fax").style.display = "block";
            document.querySelector("#atbdp_emails").style.display = "block";
            document.querySelector("#atbdp_webs").style.display = "block";
            document.querySelector("#atbdp_socialInFo").style.display = "block";
          }
        });
    }
    //Hide booking
    if ($("#hide_booking").length) {
      document
        .querySelector("#hide_booking")
        .addEventListener("change", function (e) {
          if (this.checked) {
            document.querySelector(".bdb-booking-type").style.display = "none";
            document.querySelector(".instant_booking").style.display = "none";
            document.querySelector(".bdb_service").style.display = "none";
          } else {
            document.querySelector(".bdb-booking-type").style.display = "block";
            document.querySelector(".instant_booking").style.display = "block";
            document.querySelector(".bdb_service").style.display = "block";
          }
        });
    }
    //hide map
    if ($("#hide_map").length) {
      document
        .querySelector("#hide_map")
        .addEventListener("change", function (e) {
          if (this.checked) {
            document.querySelector("#atbdp_address").style.display = "none";
            document.querySelector(".map_wrapper").style.display = "none";
          } else {
            document.querySelector("#atbdp_address").style.display = "block";
            document.querySelector(".map_wrapper").style.display = "block";
          }
        });
    }
    $(".desktop-close-icon").click(function (e) {
      e.preventDefault();
      $("body").addClass("disable-scroll");
      $("#show-sidebar").removeClass("show-sidebar");
      $(".menu-right").addClass("menu-right--index");
      $(".logo-wrapper").addClass("logo-wrapper--index");
    });
    $(".mobile-close-icon").click(function (e) {
      e.preventDefault();
      $("body").removeClass("disable-scroll");
      $(window).resize();
    });
    $(".mobile-close-icon .navbar-toggler").click(function () {
      $("#show-sidebar").addClass("show-sidebar").fadeIn(1000);
      $("body").removeClass("disable-scroll");
      $(".menu-right").removeClass("menu-right--index");
      $(".logo-wrapper").removeClass("logo-wrapper--index");
    });

    // Headroom JS
    var myElement = document.querySelector(".menu-area-sticky");
    if (myElement !== null) {
      var headroom = new Headroom(myElement);
      headroom.init();
    }

    //add class listing details
    $(".atbd_sub_title")
      .parents(".listing-info")
      .addClass("atbd_sub_title_wrapper");
    //add class listing details
    $(".logo-wrapper.site_title_tag")
      .parents(".menu-area")
      .addClass("site_title_tag--wrapper");

    //ads overflow issue
    if ($(".ads-advanced")) {
      $(".elementor-section").addClass("overflow-visible");
    }

  })

  //listing with map page loader
  $(window).on('load', function () {
    $('.atbdp_listings_map_page').removeClass('atbdp_listings_map_page_loading');

    //menu height fix
    if (document.querySelector(".atbdp_listings_map_page .directorist-type-nav--listings-map") !== null) {
      $(".height-fix").css("margin-top", 0);
      $(".menu-area + .elementor").removeClass("height-fix");
    }
  });

  $('body').on('submit', '#directorist-search-area-form', function (e) {
    var form = $(this);
    $.post(
      direo_rtl.ajaxurl, {
        action: 'direo_map_header_title',
        post_id: $('.directorist-listing-map-title').data('post-id'),
        form: form.serialize(),
      },
      function (data) {
        $('.directorist-listing-map-title').html(data);

        // convertToSelect2
        function convertToSelect2(field) {
          if (!field) {
            return;
          }
          if (!field.elm) {
            return;
          }
          if (!field.elm.length) {
            return;
          }

          const default_args = {
            allowClear: true,
            width: '100%',
            templateResult: function (data) {
              // We only really care if there is an field to pull classes from
              if (!data.field) {
                return data.text;
              }
              var $field = $(data.field);
              var $wrapper = $('<span></span>');

              $wrapper.addClass($field[0].className);
              $wrapper.text(data.text);

              return $wrapper;
            }
          };

          var args = (field.args && typeof field.args === 'object') ? Object.assign(default_args, field.args) : default_args;

          var options = field.elm.find('option');
          var placeholder = (options.length) ? options[0].innerHTML : '';

          if (placeholder.length) {
            args.placeholder = placeholder;
          }

          field.elm.select2(args)

        }

        // Init Select 2
        setTimeout(() => {
          if ($('.directorist-select select').length > 0) {
            convertToSelect2({
              elm: $('.directorist-select select')
            });
          }
        }, 1000);
      }
    );

  });

  window.addEventListener('directorist-reload-listings-map-archive', function () {
    /* Search Dropdown */
    $("body").on("click", ".directorist-filter-btn", function () {
      $(this).closest(".directorist-filter-btn").toggleClass("active");
    });

    $("body").on("click", ".theme-search-dropdown .theme-search-dropdown__label", function () {
      $(this).closest(".theme-search-dropdown").toggleClass("active");
    });

    $('body').on('click', function (e) {
      if (!$(e.target).closest('.theme-search-dropdown').length) {
        $(".theme-search-dropdown").removeClass("active");
      }
    });
  });

  $(window).on('load', initSetup);
  $(window).on('directorist-reload-listings-map-archive', initSetup);

})(jQuery)