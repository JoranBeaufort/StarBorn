
    // For fixed top bar
       $(window).scroll(function(){
        if($(window).scrollTop() >100 /*or $(window).height()*/){
            $(".navbar-fixed-top").addClass('past-main');  
            $("footer").addClass('past-main');  
        }
    else{    	
      $(".navbar-fixed-top").removeClass('past-main');
      $("footer").removeClass('past-main');
      }
    });

