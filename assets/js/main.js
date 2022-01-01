"use strict";
$(document).ready(function(){

  if($("html").hasClass("homepage")) {
    $("select").selectric();
    $(".slick").slick({
      // normal options...      
      infinite: false,
      speed: 0,
      swipe: false,
      slidesToScroll: 5,
      slidesToShow: 5,
      // the magic
      responsive: [        
      {
        breakpoint: 1400,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 4            
        }
      }, 
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3
        }
      }, 
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 576,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }]
    });
  }


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

  
  //submit button pop up functionality for wikipedea - addCategory - addPost pages 
  !function(){
    var rand1, rand2;
    $("button[name='identifier'].sure").prop("disabled", false);
    $("button[name='identifier'].sure").click(function(e){ 
      if(!!$(this).closest("form")[0].reportValidity()) {  
        //show modal
        rand1 = Math.ceil(Math.random() * 5);
        rand2 = Math.ceil(Math.random() * 5);
        $("#captcha_sure_label > strong").html(rand1 + "+" + rand2 + " =");        
        $("#modal_sure").modal("show");        
        $("#modal_sure #sure_submit").prop("disabled", true);
      }        
      e.preventDefault();              
    });

    $("#captcha_sure_input").on("input", function(){
      if ( $(this).val() == (rand1 + rand2) ) {
        $(this).closest(".modal").find("#sure_submit").prop("disabled", false);
      }
      else {
        $(this).closest(".modal").find("#sure_submit").prop("disabled", true);
      }
    });

    $("#sure_submit").click(function(){
      $(this).closest(".modal").prev("form")[0].requestSubmit($("button[name='identifier']")[0]);
    });



  }();




  /*end*/
});
