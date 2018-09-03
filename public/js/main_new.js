  // $(window).on('load', function () {
  //   $("main,.add-button,.teleport,.logo-text").css("opacity","0")
    
  // });
$(document).ready(function(){
  $("header").animate({"top": "0","height":"90px" }, "slow" );
  $(".animet").animate({"width": "52px","height": "52px" }, 2000 );

  function Time(){
    $(".animet").removeClass("active");
    $("main,footer,.add-button,.teleport").animate({"opacity":"1" }, 1000 );
    $("header").css("box-shadow","0px 0px 7.6px 0.4px rgba(0, 0, 0, 0.06), 0px 12px 30px 1.45px rgba(0, 0, 0, 0.08)");
    $(".logo-text").fadeIn("slow","linear")
  }setTimeout(Time, 2000);
  
  $(".click-ther").click(function(){
    $(".click-ther>.icon-arrow-down").toggleClass("active");
    $(".drop-select-menu").toggleClass("active");
  });

  $(document).on('click', function(e) {
    if (!$(e.target).closest(".drop-select-menu,.click-ther").length) {
      $('.drop-select-menu').removeClass("active");
      $(".click-ther>.icon-arrow-down").removeClass("active");
      
    }
    if(!$(e.target).closest(".btn-white").length){
      $(".btn-white").removeClass("toggle");
      $(".popup-menu").fadeOut();
    }
    e.stopPropagation();
  });

  $(".drop-menu-punkt").click(function(){
    var textOne = $(this).text();
    $(".click-ther p").text(textOne);
    $(".drop-select-menu").removeClass("active");
    $(".click-ther>.icon-arrow-down").removeClass("active");
  });

  $(".btn-pink").click(function(){
    $(this).children("i").toggleClass("active");
    $(this).parent().parent().nextAll(".hide-show").slideToggle(300);
  });

  $(".more-info").click(function(){
    $(this).parent().children().toggleClass("active");
    $(this).children(".btn-text-blue").children(".icon-arrow-down").toggleClass("active");
    if ($(".product-inform").hasClass("active")) {
      $(".blurring").css("display" ,"none");
    } else {
      $(".blurring").css("display" ,"block");
    }
    if ($(".document-wrapper").hasClass("active")) {
      $(".document-wrapper>.blurring").css("display" ,"none");
    } else {
      $(".document-wrapper>.blurring").css("display" ,"block");
    }
  });

  $(".open-more-button").click(function(){
    $(this).children("i").toggleClass("active");
    $(this).parent().nextAll(".hide-info").slideToggle(300);
    $(this).parent().nextAll(".hide-info").css("display","flex");
  });

  $(".read-more").click(function(){
    $(this).toggleClass("active");
    $(".popup-over").fadeToggle().animate({ top:'0px',opacity:"1"}, 300);
  });
  
  $("#drop-down").click(function(){
    $(this).children(".drop-menu.wrapper").toggleClass("active");
  });

     var x, i, j, selElmnt, a, b, c;
    /*look for any elements with the class "custom-select":*/
    x = document.getElementsByClassName("custom-select");
    for (i = 0; i < x.length; i++) {
      selElmnt = x[i].getElementsByTagName("select")[0];
      /*for each element, create a new DIV that will act as the selected item:*/
      a = document.createElement("DIV");
      a.setAttribute("class", "select-selected");
      a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
      x[i].appendChild(a);
      /*for each element, create a new DIV that will contain the option list:*/
      b = document.createElement("DIV");
      b.setAttribute("class", "select-items select-hide");
      for (j = 1; j < selElmnt.length; j++) {
        /*for each option in the original select element,
        create a new DIV that will act as an option item:*/
        c = document.createElement("DIV");
        c.innerHTML = selElmnt.options[j].innerHTML;
        c.addEventListener("click", function(e) {
            /*when an item is clicked, update the original select box,
            and the selected item:*/
            var i, s, h;
            s = this.parentNode.parentNode.getElementsByTagName("select")[0];
            h = this.parentNode.previousSibling;
            for (i = 0; i < s.length; i++) {
              if (s.options[i].innerHTML == this.innerHTML) {
                s.selectedIndex = i;
                h.innerHTML = this.innerHTML;
                break;
              }
            }
            h.click();
        });
        b.appendChild(c);
      }
      x[i].appendChild(b);
      a.addEventListener("click", function(e) {
          /*when the select box is clicked, close any other select boxes,
          and open/close the current select box:*/
          e.stopPropagation();
          closeAllSelect(this);
          this.nextSibling.classList.toggle("select-hide");
          this.classList.toggle("select-arrow-active");
        });
    }

    function closeAllSelect(elmnt) {
      /*a function that will close all select boxes in the document,
      except the current select box:*/
      var x, y, i, arrNo = [];
      x = document.getElementsByClassName("select-items");
      y = document.getElementsByClassName("select-selected");
      for (i = 0; i < y.length; i++) {
        if (elmnt == y[i]) {
          arrNo.push(i)
        } else {
          y[i].classList.remove("select-arrow-active");
        }
      }
      for (i = 0; i < x.length; i++) {
        if (arrNo.indexOf(i)) {
          x[i].classList.add("select-hide");
        }
      }
    }

    /*if the user clicks anywhere outside the select box,
    then close all select boxes:*/
    document.addEventListener("click", closeAllSelect);

    $(".check-drop-title").click(function(){
      $(this).parent().children(".check-drop-down").slideToggle(200);
    });

    $( function() {
      $( "#slider-range-min" ).slider({
        range: "min",
        value: 37,
        min: 1,
        max: 2000000,
        slide: function( event, ui ) {
          $( "#minimym" ).val( ui.value );
        }
      });

      $( "#slider-range-max" ).slider({
        range: "min",
        value: 37,
        min: 1,
        max: 2000000,
        slide: function( event, ui ) {
          $( "#maximym" ).val(  ui.value );
        }
      });

      $( "#amount" ).val($( "#slider-range-min" ).slider( "value" ) );
      $( "#amount" ).val($( "#slider-range-max" ).slider( "value" ) );
    } );


    $(".chenge-time-active").click(function(){
      if ($(".chenge-time-active").hasClass("active")) {
        $(".chenge-time-active").removeClass("active");
        $(this).addClass("active");
      }
    });

    $(".change_item").not(":first").hide();

    $(".chenge").click(function() {
        $(".chenge").removeClass("active").eq($(this).index()).addClass("active");
        $(".change_item").hide().eq($(this).index()).show()
    }).eq(0).addClass("active");

  $('.owl-carousel').owlCarousel({
    loop:false,
    nav:true,
    center:false,
    navText : ["",""],
    dots:false,
    touchDrag:true,
    items: 1,
    responsiveClass:true,
    responsive:{
            0:{
              items:1.5

            },
            425:{
                items:2
            },
            768:{
                items:3
            }
        }
    });

    $(".close").click(function(){
      $(this).parent().parent().parent().parent().hide(400);
    });

    $(".close-warning").click(function(){
      $(this).parent().fadeToggle();
      $(".add-button > .btn-white").removeClass("toggle");
    });

    $('#hamburger-icon').click(function() {
      $('#hamburger-icon').toggleClass('active');
      return false;
    });

    $("#hamburger-icon").click( function(){
        $(".mob-menu").animate({ width: "toggle" }, "fast", "linear");
    });

    $(".add-button > .btn-white").on("click", function(e) {
        e.stopPropagation();

        if ($(window).width() >= 768) {
            if ($(this).hasClass("toggle")) {
                $(this).removeClass("toggle");
                $(".popup-menu").fadeOut(300);
            } else {
                $(this).addClass("toggle");
                $(".popup-menu").fadeIn(300);
            }
        }

        if ( $(window).width() < 768 ) {
            if( $(this).hasClass("toggle") ){
                $(this).removeClass("toggle");
                $(".popup-over-menu").fadeOut(300);
            } else {
                $(this).addClass("toggle");
                $(".popup-over-menu").fadeIn(300);
            }
        }
    });

    btnAppend();
});

function btnAppend() {
    if ( $(window).width() <= 767 ) {
        $(".popup-menu").appendTo(".popup-over-menu");
    }
    if ( $(window).width() > 767 ) {
        $(".popup-menu").appendTo(".add-button .btn-white");
    }
}

$(window).resize(function() {
    var i = window.innerWidth;

    window.setTimeout("btnAppend()",20);

    if ( i <= 675 ) {
        $(".inform-link").appendTo(".mob-menu");
    } else {
        $(".inform-link").appendTo(".teleport");
    }

    if ( i <= 675) {
        $(".add-button>.btn-blue").appendTo(".mob-menu > .listing-title>.right");
    } else {
        $(".right>.btn-blue").appendTo(".add-button");
    }

    if ( i <= 768) {
        $(".popup").appendTo(".popup-over");
    } else {
        $(".popup").appendTo(".none");
    }
}).resize();