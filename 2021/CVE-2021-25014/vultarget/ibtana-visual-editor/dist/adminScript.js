jQuery(document).ready(function($) {

  const SEARCH_DELAY = 100; // in ms
  const interval = setInterval(() => {
    if ($('.ive-product-slider-hidden').length > 0) {
      $(".ive-product-slider-hidden").each(function(i, el) {
        $(el).removeClass('ive-product-slider-hidden');

        var navtextprev = $(this).attr('data-navtextprev');
        var navtextnext = $(this).attr('data-navtextnext');
        var navbtntype = $(this).attr('data-navbtntype');

        if (navbtntype=='icon') {
          var navtextprevicon= `<i class="`+navtextprev+`"></i>`;
          var navtextnexticon= `<i class="`+ navtextnext +`"></i>`;
        }else{
          var navtextprevicon= navtextprev ;
          var navtextnexticon = navtextnext ;
        }

        var settingData = {
          nav: true,
          dots: true,
          margin: parseInt($(this).attr('data-margin')),
          stagePadding: parseInt($(this).attr('data-stagepadding')),
          rewind: ($(this).attr('data-rewind') === "true"),
          autoplay: ($(this).attr('data-autoplay') === "true"),
          autoplayTimeout: parseInt($(this).attr('data-autoplaytimeout')),
          autoplayHoverPause: ($(this).attr('data-autoplayhoverpause') === "true"),
          autoplaySpeed: parseInt($(this).attr('data-autoplayspeed')),
          navSpeed: parseInt($(this).attr('data-navspeed')),
          dotsSpeed: parseInt($(this).attr('data-dotsspeed')),
          loop: ($(this).attr('data-loop') === "true"),
          navText: [navtextprevicon, navtextnexticon],
          responsive: {
            0: {
              items: 1
            },
            425: {
              items: parseInt($(this).attr('data-responsive-mob'))
            },
            720: {
              items: parseInt($(this).attr('data-responsive-tab'))
            },
            1024: {
              items: parseInt($(this).attr('data-responsive-desk'))
            }
          }
        };
        $(this).owlCarousel(settingData);
      });
    }
  }, SEARCH_DELAY);

});
