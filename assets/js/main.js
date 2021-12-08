"use strict";
$(document).ready(function(){
  
  $("select").selectric();
  $(".slick").slick({
      // normal options...
      infinite: false,
      slidesToShow: 5,
      // the magic
      responsive: [        
      {
        breakpoint: 1400,
        settings: {
          slidesToShow: 4            
        }
      }, 
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
        }
      }, 
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
        }
      },
      {
        breakpoint: 576,
        settings: {
          slidesToShow: 1,
        }
      }
      ]
    });


  /*toggle filters functionality on phone*/
  !function(){


    if($(".sort.go").is(":visible")) {
     $(".sort-dropdown").hide()
     $(".sort-dropdown").removeClass("d-none d-md-flex");

     $(".sort").click(function(){
      if($(this).hasClass("active")){
        $(this).removeClass("active");
        $(".sort-dropdown").slideUp();
      }
      else {
        $(this).addClass("active");
        $(".sort-dropdown").slideDown();
      }
    });
   }

   

 }();

 /*end*/
});
