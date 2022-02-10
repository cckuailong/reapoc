//
jQuery( document ).ready( function( $ ) {
  alert('test');
  $( ".ive-carousel-content-wrap" ).each(function( OwlHscrolls ) {


    var autoPLayy=$(this).attr('data-autoplay');
    if($(this).attr('data-nav')=="arrows")
    {
          var settingData={navigation : true, // Show next and prev buttons
          items:1,
          autoplay:autoPLayy,
          nav: true,
          dots:false};
    }

    else if($(this).attr('data-nav')=="none")
    {
          var settingData={navigation : true, // Show next and prev buttons
          items:1,
          autoplay:autoPLayy,
          nav: false,
          dots:false};
    }

    else if($(this).attr('data-nav')=="dots")
    {
          var settingData={navigation : true, // Show next and prev buttons
          items:1,
          autoplay:autoPLayy,
          nav: false,
          dots:true};
    }
    else{
      var settingData={navigation : true, // Show next and prev buttons
      items:1,
      autoplay:autoPLayy,
      nav: false,
      dots:false};
    }

     $(this).owlCarousel(settingData);
     });
});
