(function ($) {
    "use strict";
    jQuery(document).ready(function ($) {
        //Menu On Hover
        $('body').on('mouseenter mouseleave', '.nav-item,.top-dropdown', function (e) {
            if ($(window).width() > 750) {
                var _d = $(e.target).closest('.nav-item,.top-dropdown'); _d.addClass('show');
                setTimeout(function () {
                    _d[_d.is(':hover') ? 'addClass' : 'removeClass']('show');
                }, 1);
            }
        });
         //Menu On Hover
        //  menu sticky
        $(function () {
            //caches a jQuery object containing the header element
            var header = $("#menu-area");
            $(window).scroll(function () {
                var scroll = $(window).scrollTop();

                if (scroll >= 135) {
                    header.addClass("add-sticky");
                } else {
                    header.removeClass("add-sticky");
                }
            });
        });
        //  menu sticky
        // main-slider starts //
        $('#main-slider, #text-slider').slick({
            dots: false,
            // infinite: false,
            speed: 1500,
            cssEase:'ease',
            infinite:true,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        infinite: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
        // main-slider ends //
        // product-carousel //
        $('#product-carousel, #used-products-carousel, #high-end-carousel, #wholesaler-carousel, #wholesaler-carousel').slick({
            dots: false,
            infinite: false,
            speed: 1500,
            slidesToShow: 5,
            slidesToScroll: 1,
            autoplay: false,
            autoplaySpeed: 3000,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        infinite: true
                    }
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }
            ]
        });
        // product-carousel //

    });
    // loader
    $(window).on('load', function (e) {
        $("#loading").delay(300).fadeOut("slow"); // will fade out the white DIV that covers the website.
    })
    // loader
}(jQuery));

// exzoom
// $(function(){
//     $("#exzoom").exzoom({
//         "autoPlay": false,
//     });
// });

//about page counter
$('.stat-number').each(function () {
    var size = $(this).text().split(".")[1] ? $(this).text().split(".")[1].length : 0;
    $(this).prop('Counter', 0).animate({
        Counter: $(this).text()
    }, {
        duration: 5000,

        step: function (func) {
            $(this).text(parseFloat(func).toFixed(size));
        }
    });
});
//product search
$(window).scroll(function(){
    if ($(this).scrollTop() > 135) {
        $('.srch-product').addClass('fixed');
    } else {
        $('.srch-product').removeClass('fixed');
    }
});

//----------------------//
//top-search
//product search----srch-frm2-----------
$(window).scroll(function(){
    if ($(this).scrollTop() > 135) {
        $('.srch-frm2').addClass('fixed');
    } else {
        $('.srch-frm2').removeClass('fixed');
    }
});

//product search----srch-frm1-----------
$(window).scroll(function(){
    if ($(this).scrollTop() > 135) {
        $('.srch-frm1').addClass('fixed');
    } else {
        $('.srch-frm1').removeClass('fixed');
    }
});


// menu-carousel //
$('#menu-carousel').slick({
    dots: false,
    infinite: false,
    speed: 1500,
    slidesToShow: 6,
    slidesToScroll: 1,
    autoplay:false,
    autoplaySpeed: 3000,
    responsive: [
        {
            breakpoint: 1024,
            settings: {
                slidesToShow: 6,
                slidesToScroll: 1,
                infinite: true
            }
        },
        {
            breakpoint: 767,
            settings: {
                slidesToShow: 6,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        }
    ]
});
// menu-carousel //


// home page location
$(".location-layer .location-info .button-area .btn").click(function(){
    $(".location-layer .location-info .form-group").addClass("active");
  });

  $(".location-layer .location-info .submit-location").click(function(){
    $(".location-layer .location-info .form-group").removeClass("active");
  });

//   topbar search
$("#topbar-area .logo-search ul .cart-box .cart-login.search").click(function(){
    $("#topbar-area .logo-search ul .srch-frm1").toggleClass("active");
  });


  
// password eye open close
$('.password-eye').click(function(){
    $(this).toggleClass("active");
  });
// --------login signup -----------

  $(".login-signup-sec #signin").on( "click", function() {
    $('.login-signup-sec #myModal1').modal('hide');  
});

$(".login-signup-sec #signin").on( "click", function() {
    $('.login-signup-sec #myModal2').modal('show');  
});


$(".login-signup-sec #password").on( "click", function() {
    $('.login-signup-sec #myModal1').modal('hide');  
    $('.login-signup-sec #myModal3').modal('show'); 
});

$(".login-signup-sec #myModal4").on( "click", function() {
    $('.login-signup-sec #myModal3').modal('hide'); 
    $('.login-signup-sec #myModal5').modal('show');  
});

$(".login-signup-sec #myModal6").on( "click", function() {
    $('.login-signup-sec #myModal5').modal('hide'); 
    $('.login-signup-sec #myModal7').modal('show');  
});

$(".login-signup-sec #myModal8").on( "click", function() {
    $('.login-signup-sec #myModal7').modal('hide'); 
    $('.login-signup-sec #myModal9').modal('show');  
});

$(".login-signup-sec #signup-btn").on( "click", function() {
    $('.login-signup-sec #myModal2').modal('hide'); 
    $('.login-signup-sec #myModal3').modal('show');  
});

$(".login-signup-sec #login").on( "click", function() {
    $('.login-signup-sec #myModal2').modal('hide'); 
    $('.login-signup-sec #myModal1').modal('show');  
});

// -------------menu carousel show hide-------------
$('.slide-menu h5.spctg').click(function(){
    $(".slide-menu .slidemenu-main").toggleClass("active");
  });
