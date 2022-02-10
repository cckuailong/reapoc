<?php if ( ! defined( 'ABSPATH' ) ) exit; 
  $this_widget_testimonial = $thisWidget['widgetTCarousel'];
  $tCarOps = $this_widget_testimonial['tCarOps'];
  $tCarSlides = $this_widget_testimonial['tCarSlides'];
  $tDesignOps = $this_widget_testimonial['tDesignOps'];

  $iconHTML ='';
  if ($tDesignOps['tcis'] !='' && $tDesignOps['tcis'] != '0') {
    $iconHTML = '<i class="fas fa-quote-left" style="border:2px solid '.$tDesignOps['tcic'].'; padding:15px; font-size:'.$tDesignOps['tcis'].'px; color:'.$tDesignOps['tcic'].'; text-align:center; margin:5px 0 5px 0; border-radius:'.$tDesignOps['tcir'].'; "></i>';
    $widgetFALoadScripts = true;
  }

  $widgetPostsSliderExternalScripts = true;
  $widgetOwlLoadScripts = true;
  $widgetJQueryLoadScripts = true;

  $pbCarouselAllSlides = '';
  foreach ($tCarSlides as $index => $val) {

    $pbSliderPrevSlides = $pbCarouselAllSlides;

    if ( !isset($val['tcia']) ) { $val['tcia'] = ''; }
    if ( !isset($val['tcit']) ) { $val['tcit'] = ''; }

    if ($val['tci'] != '') {
      $imgHTMLCenter = '<img src="'.$val['tci'].'" style="width:'.$tDesignOps['tcisi'].'%;  border-radius:'.$tDesignOps['tcir'].';"  alt="'.$val['tcia'].'" title="'.$val['tcit'].'"  />';
      $imgArea = 'visible';
    } else{
      $imgHTMLCenter = '';
      $imgArea = 'none';
    }


    $authorName = '<p class="tesAName"> '.$val['tcn'].' </p>';
    $authorinfo =  '<p class="tesAJob" >'.$val['tcj'].'</p>';
    $testimonialText = '<p class="tesAComment">'.$val['tct'].'</p>';

    if ($val['tcl'] != '') {
      $authorinfo = '<a href='.$val['tcl'].' target="_blank">'.$authorinfo.'</a>';
    }

    if ($tDesignOps['tcca'] == 'center') {

      $testimonialCardHTML = '<div style="text-align:center; padding:3% 1% 3% 1%;"> '.$iconHTML.' <br> <br>   '.$imgHTMLCenter.' '.$testimonialText.' <b>'.$authorName.'</b> '.$authorinfo.'</div>';

    } else if ($tDesignOps['tcca'] == 'left'){

      $testimonialCardHTML = 
        '<div style="padding:3% 1% 3% 1%; text-align:center; display: inline-flex; align-items: center;">

          <div style="width:30%; display:inline-block; text-align:center; display:'.$imgArea.'; float:left; ">'.$imgHTMLCenter.' </div>
          <div style="width:69%; display:inline-block; text-align:left;">'.$testimonialText.' <b>'.$authorName.'</b> '.$authorinfo.'</div>

        </div>'
      ;

    } else if ($tDesignOps['tcca'] == 'right'){

      $testimonialCardHTML = 
        '<div style="padding:3% 1% 3% 1%; text-align:center; display: inline-flex; align-items: center;">

          <div style="width:69%; display:inline-block; text-align:left; margin-left:2%; ">'.$testimonialText.' <b>'.$authorName.'</b> '.$authorinfo.' </div>
          <div style="width:28%; display:inline-block; text-align:center; display:'.$imgArea.'; ">'.$imgHTMLCenter.' </div>

        </div>';

    } else{
      $testimonialCardHTML = '<div style="text-align:center; padding:3% 1% 3% 1%;"> '.$iconHTML.' <br> <br>   '.$imgHTMLCenter.' '.$testimonialText.' <b>'.$authorName.'</b> '.$authorinfo.'</div>';
    }

    $pbSliderThisSlide = "<div class='carouselSingleSlide'> ".$testimonialCardHTML." </div>";
    $pbCarouselAllSlides = $pbSliderPrevSlides . "\n".  $pbSliderThisSlide;

  }

  $pbTCarouselUniqueCode =  (rand(500,1000)*2)*rand(10,500);
  $pbTCarouselUniqueId = 'pb_testimonialCarousel_' .$pbTCarouselUniqueCode;
  $pbCarouselScript = "

    (function($) {

      $(document).ready(function(){ 

        jQuery('#".$pbTCarouselUniqueId."').owlCarousel({items:".$tCarOps['tNSlides'].",   singleItem: false,  autoPlay : ".$tCarOps['tCarAutoplay'].",   stopOnHover : true,   navigation: ".$tCarOps['tCarNav']." ,    paginationSpeed : ".$tCarOps['tCarDelay']."00,   goToFirstSpeed : ".$tCarOps['tCarDelay']."00,    autoHeight : true,    slideSpeed : ".$tCarOps['tCarDelay']."000,   transitionStyle: '".$tCarOps['tCarSlideTransition']."',    pagination : ".$tCarOps['tCarPagination'].",   paginationNumbers: false,   navigationText : ['<span class=\"dashicons dashicons-arrow-left-alt2\" > </span>', '<span class=\"dashicons dashicons-arrow-right-alt2\" > </span>'], theme: 'pbOwl-theme', baseClass: 'pbOwl-carousel' ,  });

        });  

    })(jQuery); 

  ";

  $pbCarStyles = '<style>  '.
    '#'.$pbTCarouselUniqueId.' .tesAName { color:'.$tDesignOps['tcntc'].'; font-size:'.$tDesignOps['tcnts'].'px; font-family:'.str_replace('+', ' ', $tDesignOps['tcntf']).'; }'.
    '#'.$pbTCarouselUniqueId.' .tesAJob { color:'.$tDesignOps['tcntc'].'; font-size: calc(3 - '.$tDesignOps['tcnts'].'px); font-family:'.str_replace('+', ' ', $tDesignOps['tcntf']).'; }'.
    '#'.$pbTCarouselUniqueId.' .tesAComment { color:'.$tDesignOps['tcttc'].'; font-size:'.$tDesignOps['tctts'].'px ; font-family:'.str_replace('+', ' ', $tDesignOps['tcttf']).'; }'.
  '</style>';

  array_push($thisColFontsToLoad, $tDesignOps['tcntf']);
  array_push($thisColFontsToLoad, $tDesignOps['tcttf']);

  $thisWidgetResponsiveWidgetStylesTablet = ' '.
    '#'.$pbTCarouselUniqueId.' .tesAName { font-size:'.$tDesignOps['tcntst'].'px;  }'.
    '#'.$pbTCarouselUniqueId.' .tesAJob { font-size: calc(3 - '.$tDesignOps['tcntst'].'px);  }'.
    '#'.$pbTCarouselUniqueId.' .tesAComment {  font-size:'.$tDesignOps['tcttst'].'px ; }'.
    '#'.$pbTCarouselUniqueId.' i { font-size:'.$tDesignOps['tcist'].'px ; }';

  $thisWidgetResponsiveWidgetStylesMobile = ' '.
    '#'.$pbTCarouselUniqueId.' .tesAName { font-size:'.$tDesignOps['tcntsm'].'px;  }'.
    '#'.$pbTCarouselUniqueId.' .tesAJob { font-size: calc(3 - '.$tDesignOps['tcntsm'].'px);  }'.
    '#'.$pbTCarouselUniqueId.' .tesAComment {  font-size:'.$tDesignOps['tcttsm'].'px ; }'.
    '#'.$pbTCarouselUniqueId.' i { font-size:'.$tDesignOps['tcism'].'px ; }';


  array_push($POPBNallRowStylesResponsiveTablet, $thisWidgetResponsiveWidgetStylesTablet);
    
  array_push($POPBNallRowStylesResponsiveMobile, $thisWidgetResponsiveWidgetStylesMobile);

  array_push($POPBallWidgetsScriptsArray, $pbCarouselScript);

  $pbCarouselSlidesWrapper = '<div  id='.$pbTCarouselUniqueId.' class="pbOwl-carousel">' .$pbCarouselAllSlides.'</div>'. "\n" . $pbCarStyles."\n";

  $widgetContent = $pbCarouselSlidesWrapper;
?>