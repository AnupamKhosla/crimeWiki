"use strict";
$(document).ready(function(){

  if($("html").hasClass("homepage") || $("html").hasClass("searchpage")) {
    $("select").selectric();
  }

  if($("html").hasClass("homepage")) {    
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
    $("button[name='identifier'].sure").click(function(e) { 
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
    
    $("#captcha_sure_input").keypress(function(event){         
      if(event.which == 13) {          
        $(this).closest(".modal-content").find("#sure_submit:not(:disabled)").click();  
      } 
    });

  }();
  //submit button functionality finished

  //class based general button pop up functionality for make-sure-captcha
  !function(){
    var rand1, rand2;
    $("button.make-sure").prop("disabled", false);
    $("button.make-sure").click(function(e) {       
      if(!!$(this).closest("form")[0].reportValidity()) {         
        //show modal
        rand1 = Math.ceil(Math.random() * 5);
        rand2 = Math.ceil(Math.random() * 5); 
        var $form = $(this).closest("form");    
        $(".modal_sure").find(".captcha_sure_label > strong").html(rand1 + "+" + rand2 + " =");        
        $(".modal_sure").modal("show");        
        $(".modal_sure").children(".sure_submit").prop("disabled", true);

        $(".modal_sure").find(".sure_submit").click(function(){          
          $form.submit();
          $form.closest("tr").fadeTo("slow", 0.33).css("pointer-events", "none");
          $(".modal_sure").find(".sure_submit").off("click");
        });       
      }      
      e.preventDefault();              
    });

    $(".captcha_sure_input").on("input", function(){
      if ( $(this).val() == (rand1 + rand2) ) {
        $(this).closest(".modal").find(".sure_submit").prop("disabled", false);
      }
      else {
        $(this).closest(".modal").find(".sure_submit").prop("disabled", true);
      }
    });

    $(".captcha_sure_input").keypress(function(event){         
      if(event.which == 13) {          
        $(this).closest(".modal-content").find(".sure_submit:not(:disabled)").click();  
      } 
    });

    $(".modal_sure").on("hide.bs.modal", function(){
      $(this).find(".sure_submit").prop("disabled", true);
      $(".modal_sure").find(".sure_submit").off("click");
    });

    
  }();
  //submit button functionality finished



  //dashboard page homepage content $blog_about_text $blog_month_post
  !function(){
    $(".dashboard button[name='identifier'].sure").prop("disabled", true);
    $(".dashboard-form input[type='checkbox']").change(function(){      
      if(!$(".dashboard-form input[name='check_post']").is(":checked") && !$(".dashboard-form input[name='check_about_text']").is(":checked") ) {        
        $("button[name='identifier'].sure").prop("disabled", true);
      }
      else {
        $("button[name='identifier'].sure").prop("disabled", false);
      }

      if($(".dashboard-form input[name='check_post']").is(":checked")) {
        $(".fields input").prop("disabled", false);
      }
      else {
        $(".fields input").prop("disabled", true);
      }

      if($(".dashboard-form input[name='check_about_text']").is(":checked")) {
        $(".about-text textarea").prop("disabled", false);
      }
      else {
        $(".about-text textarea").prop("disabled", true);
      }
    });
  }();
  //END: dashboard page homepage content $blog_about_text $blog_month_post 


  //edit post page  
  !function(){
    $(".choose-image").change(function(){
      $(".org-category").html(" ");
    });
  }();



  /*end*/
});
