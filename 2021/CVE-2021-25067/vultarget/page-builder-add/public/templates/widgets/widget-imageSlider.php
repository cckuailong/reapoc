<?php
if ( ! defined( 'ABSPATH' ) ) exit; 


$widget_imageSlider = $thisWidget['widgetImageSlider'];
$pbSliderImagesURL = $widget_imageSlider['pbSliderImagesURL'];
$pbSliderContent = $widget_imageSlider['pbSliderContent'];
$pbSliderAuto = $widget_imageSlider['pbSliderAuto'];
$pbSliderDelay = $widget_imageSlider['pbSliderDelay'];
$pbSliderPager = $widget_imageSlider['pbSliderPager'];
$pbSliderNav = $widget_imageSlider['pbSliderNav'];
$pbSliderRandom = $widget_imageSlider['pbSliderRandom'];
$pbSliderPause = $widget_imageSlider['pbSliderPause'];
  

  if (!isset($widget_imageSlider['pbSliderHeight']) ) {
    $pbSliderHeight = '400';
    $pbSliderHeightUnit = 'px';
  }else{
    $pbSliderHeight = $widget_imageSlider['pbSliderHeight'];
    $pbSliderHeightUnit = $widget_imageSlider['pbSliderHeightUnit'];
  }
  if (!isset($widget_imageSlider['pbSliderContent']) ) {
    $contentSlider = false;
  }else{
    $contentSlider = true;
  }

  $pbImageSliderUniqueId = (rand(500,1000)*2)*rand(10,500);
  $pbImageSliderUniqueId = "popb_Slider_"."$pbImageSliderUniqueId";

  $pbSliderContainer =  "<div class='rslides_container' style='min-height:100px;'> <ul class='rslides' id='".$pbImageSliderUniqueId."'>";
  $pbSliderAllSlides = '';

  if ( isset($pbSliderContent[0]['imageSlideUrl']) ) {
    $pbSliderImagesURL = $pbSliderContent;
  }

  foreach ($pbSliderImagesURL as $index => $val) {

      $slideImageUrl = $val;
      if (isset($val['imageSlideUrl'])) {
        $slideImageUrl = $val['imageSlideUrl'];
      }

	    $pbSliderPrevSlides = $pbSliderAllSlides;
	    

	    if ($contentSlider == false) {$imageSlideContent = ''; }
	    else{

	      $pbSlideContent = $pbSliderContent[$index];
	      $imageSlideHeading = '';  $imageSlideDesc = ''; $imageSlideButton = '';
	      if ($pbSlideContent['imageSlideHeading'] != '') {
	        $imageSlideHeading = "<h2>".$pbSlideContent['imageSlideHeading']."</h2>";
	      }

	      if ($pbSlideContent['imageSlideDesc'] != '') {
	        $imageSlideDesc = "<p>". $pbSlideContent['imageSlideDesc'] ."</p>";
	      }

	      if ($pbSlideContent['imageSlideButtonText'] != '') {
	        $imageSlideButton = "<a href=".$pbSlideContent['imageSlideButtonURL']." target='_blank'> <button>".$pbSlideContent['imageSlideButtonText']."</button> </a>";

	      }

      $thisSlideContentHidden = '';
      if ($imageSlideHeading == '' && $imageSlideDesc == '' && $imageSlideButton == '') {
        $thisSlideContentHidden = 'display:none !important;';
      }
	      
	      $imageSlideContent = "<div class='popb_slide_content' style='$thisSlideContentHidden'>".$imageSlideHeading." ".$imageSlideDesc."  ".$imageSlideButton."   </div>";
	    }
	    

	    $pbSliderThisSlide = "<li> <div class='popb_slideContainer' style='background:url(".$slideImageUrl."); width: 100%;height:".$pbSliderHeight.$pbSliderHeightUnit.";background-size: cover; background-repeat: no-repeat;background-position: center;'> ".$imageSlideContent."  </div> </li>";

      $pbSliderThisSlideAutoHeight = "<li> <div class='popb_slideContainer' style='width: 100%;height:".$pbSliderHeight.$pbSliderHeightUnit.";'>
        <img src='$val' style='max-width:100%; height:auto;'>
       ".$imageSlideContent."  </div> </li>";


	    $pbSliderAllSlides = $pbSliderPrevSlides .  $pbSliderThisSlide;
  };

  $pbSliderContainerClose = "</ul> </div>";

  $pbSliderScript = "

    (function($){
      $(document).ready(function() {

        jQuery('#".$pbImageSliderUniqueId."').responsiveSlides({
          auto:  ".$pbSliderAuto.",
          speed: 500,
          timeout:  ".$pbSliderDelay.",
          pager:  ".$pbSliderPager.",
          nav:  ".$pbSliderNav.",
          random:  ".$pbSliderRandom.",
          pause:  ".$pbSliderPause.",
          namespace: 'pb-centeredSlider',
        });

      });

    })(jQuery);

    

  ";

  if ($contentSlider == false){ 
    $pbSliderStyling = '';
   }else{

    $slideHeadingStyles = $widget_imageSlider['slideHeadingStyles'];
    $slideDescStyles = $widget_imageSlider['slideDescStyles'];
    $slideButtonStyles = $widget_imageSlider['slideButtonStyles'];
    $pbSliderContentBgColor = $widget_imageSlider['pbSliderContentBgColor'];

    $slideHeadingBold = ''; $slideHeadingItalic = ''; $slideHeadingUnderlined = '';
    if ($slideHeadingStyles['slideHeadingBold'] == true) { $slideHeadingBold = 'bold'; }
    if ($slideHeadingStyles['slideHeadingItalic'] == true) { $slideHeadingItalic = 'italic'; }
    if ($slideHeadingStyles['slideHeadingUnderlined'] == true) { $slideHeadingUnderlined = 'underline'; }


    if (isset($slideHeadingStyles['slideHeadingFontFamily']) ) {
      $slideHeadingFontFamily = str_replace('+', ' ', $slideHeadingStyles['slideHeadingFontFamily']);
      array_push($thisColFontsToLoad, $slideHeadingStyles['slideHeadingFontFamily']);
    } else{
      $slideHeadingFontFamily = ' none';
    }

    if (isset($slideDescStyles['slideDescFontFamily']) ) {
      $slideDescFontFamily = str_replace('+', ' ', $slideDescStyles['slideDescFontFamily']);
      array_push($thisColFontsToLoad, $slideDescStyles['slideDescFontFamily']);

    } else{
      $slideDescFontFamily = ' none';
    }

    if (isset($slideButtonStyles['slideButtonBtnFontFamily']) ) {
      $slideButtonBtnFontFamily = str_replace('+', ' ', $slideButtonStyles['slideButtonBtnFontFamily']);
      array_push($thisColFontsToLoad, $slideButtonStyles['slideButtonBtnFontFamily']);
    } else{
      $slideButtonBtnFontFamily = ' none';
    }

    $pbSliderHeadingStyles = ''
    .'color:'.$slideHeadingStyles['slideHeadingColor'].';'
    .'font-size:'.$slideHeadingStyles['slideHeadingSize'].'px;'
    .' letter-spacing:'.$slideHeadingStyles['slideHeadingLetterSpacing'].'px;'
    .' line-height:'.$slideHeadingStyles['slideHeadingLineHeight'].'px;'
    .' font-family:'.$slideHeadingFontFamily.';'
    .' font-weight:'.$slideHeadingBold.';'
    .' font-style:'.$slideHeadingItalic.';'
    .'  text-decoration:'.$slideHeadingUnderlined.';';


    $slideDescBold = ''; $slideDescItalic = ''; $slideDescUnderlined = '';
    if ($slideDescStyles['slideDescBold'] == true) { $slideDescBold = 'bold'; }
    if ($slideDescStyles['slideDescItalic'] == true) { $slideDescItalic = 'italic'; }
    if ($slideDescStyles['slideDescUnderlined'] == true) { $slideDescUnderlined = 'underline'; }

    $pbSliderDescStyles = ''
    .'color:'.$slideDescStyles['slideDescColor'].';'
    .'font-size:'.$slideDescStyles['slideDescSize'].'px;'
    .' letter-spacing:'.$slideDescStyles['slideDescLetterSpacing'].'px;'
    .' line-height:'.$slideDescStyles['slideDescLineHeight'].'px;'
    .' font-family:'.$slideDescFontFamily.';'
    .' font-weight:'.$slideDescBold.';'
    .' font-style:'.$slideDescItalic.';'
    .'  text-decoration:'.$slideDescUnderlined.';';

    $pbSliderBtnStyles = ''
      .'padding:'.$slideButtonStyles['slideButtonBtnHeight'].'px 5px;'
      .'width:'.$slideButtonStyles['slideButtonBtnWidth'].'px;'
      .'background:'.$slideButtonStyles['slideButtonBtnBgColor'].';'
      .'background-color:'.$slideButtonStyles['slideButtonBtnBgColor'].';'
      .'color:'.$slideButtonStyles['slideButtonBtnColor'].';'
      .'font-size:'.$slideButtonStyles['slideButtonBtnFontSize'].'px;'
      .'font-family:'.$slideButtonBtnFontFamily.';'
      .'letter-spacing:'.$slideButtonStyles['slideButtonBtnFontLetterSpacing'].';px'
      .'border-width:'.$slideButtonStyles['slideButtonBtnBorderWidth'].';px'
      .'border-color:'.$slideButtonStyles['slideButtonBtnBorderColor'].';'
      .'border-radius:'.$slideButtonStyles['slideButtonBtnBorderRadius'].'px;'
      .'border-style:solid;';



    if ( !isset($widget_imageSlider['slideContentWidth']) || $widget_imageSlider['slideContentWidth'] == '' ) { $widget_imageSlider['slideContentWidth'] = '40';  }

    if ( !isset($widget_imageSlider['slideContentWUnit']) || $widget_imageSlider['slideContentWUnit'] == '' ) { $widget_imageSlider['slideContentWUnit'] = '%';  }
    
    if ( !isset($widget_imageSlider['slideContentAlignH']) || $widget_imageSlider['slideContentAlignH'] == '' ) { $widget_imageSlider['slideContentAlignH'] = 'center';  }

    if ( !isset($widget_imageSlider['slideContentAlignV']) || $widget_imageSlider['slideContentAlignV'] == '' ) { $widget_imageSlider['slideContentAlignV'] = 'middle';  }

    if ( !isset($widget_imageSlider['slideContentAlign']) || $widget_imageSlider['slideContentAlign'] == '' ) { $widget_imageSlider['slideContentAlign'] = 'center';  }

    $slideContentAlignVMargin = '';

    if ($widget_imageSlider['slideContentAlignV'] == 'middle') {
      $slideContentAlignV = '50%';
    }

    if ($widget_imageSlider['slideContentAlignV'] == 'top') {
      $slideContentAlignV = '0%';
    }
    if ($widget_imageSlider['slideContentAlignV'] == 'bottom') {
      $slideContentAlignV = '0%';
      $slideContentAlignVMargin = 'bottom:0%; top:inherit;';
    }

    $slideContentAlignHMargin = '';
    if ($widget_imageSlider['slideContentAlignH'] == 'center') {
      $slideContentAlignH = '50%';
      $slideContentAlignHMargin = 'margin:0 auto;';
    }
    
    if ($widget_imageSlider['slideContentAlignH'] == 'left') {
      $slideContentAlignH = '0%';
    }

    if ($widget_imageSlider['slideContentAlignH'] == 'right') {
      $slideContentAlignH = '0%';
      $slideContentAlignHMargin = 'left:inherit; right: 0;';
    }

    $slideContentAlignmentCss =
      'position: absolute;'.
      'top: '.$slideContentAlignV.';'.
      'left: '.$slideContentAlignH.';'.
      'transform: translate(-'.$slideContentAlignH.', -'.$slideContentAlignV.');'.
      '-ms-transform: translate(-'.$slideContentAlignH.', -'.$slideContentAlignV.');'.
      '-webkit-transform: translate(-'.$slideContentAlignH.', -'.$slideContentAlignV.');'.
      $slideContentAlignHMargin . $slideContentAlignVMargin
    ;


    $pbSliderStyling = 
    '<style>
      #'.$pbImageSliderUniqueId.' .popb_slide_content{ 
        background:'.$pbSliderContentBgColor.';
        padding:3% 6%;
        width:'.$widget_imageSlider['slideContentWidth'].$widget_imageSlider['slideContentWUnit'].';
        text-align:'.$widget_imageSlider['slideContentAlign'].';
        '.$slideContentAlignmentCss.'
      } '."\n"
      .'#'.$pbImageSliderUniqueId.' .popb_slide_content h2{ '.$pbSliderHeadingStyles.'  } '."\n"
      .'#'.$pbImageSliderUniqueId.' .popb_slide_content p{ '.$pbSliderDescStyles.'  }'
      .'#'.$pbImageSliderUniqueId.' .popb_slide_content button{ '.$pbSliderBtnStyles.'  
      } '."\n"
    .'</style>';

  }

  if (isset($widget_imageSlider['pbSliderHeightTablet'])) {
    
    $pbSliderHeadingStylesTablet = ' '
      .'font-size:'.$slideHeadingStyles['slideHeadingSizeTablet'].'px;'
      .' letter-spacing:'.$slideHeadingStyles['slideHeadingLetterSpacingTablet'].'px;'
      .' line-height:'.$slideHeadingStyles['slideHeadingLineHeightTablet'].'px;' ;

    $pbSliderDescStylesTablet = ''
      .'font-size:'.$slideDescStyles['slideDescSizeTablet'].'px;'
      .' letter-spacing:'.$slideDescStyles['slideDescLetterSpacingTablet'].'px;'
      .' line-height:'.$slideDescStyles['slideDescLineHeightTablet'].'px;';

    $pbSliderBtnStylesTablet = ''
        .'padding:'.$slideButtonStyles['slideButtonBtnHeightTablet'].'px 5px;'
        .'width:'.$slideButtonStyles['slideButtonBtnWidthTablet'].'px;'
        .'font-size:'.$slideButtonStyles['slideButtonBtnFontSizeTablet'].'px;'
        .'letter-spacing:'.$slideButtonStyles['slideButtonBtnFontLetterSpacingTablet'].';px';

    $pbSliderHeadingStylesMobile = ''
      .'font-size:'.$slideHeadingStyles['slideHeadingSizeMobile'].'px;'
      .' letter-spacing:'.$slideHeadingStyles['slideHeadingLetterSpacingMobile'].'px;'
      .' line-height:'.$slideHeadingStyles['slideHeadingLineHeightMobile'].'px;';

    $pbSliderDescStylesMobile = ''
      .'font-size:'.$slideDescStyles['slideDescSizeMobile'].'px;'
      .' letter-spacing:'.$slideDescStyles['slideDescLetterSpacingMobile'].'px;'
      .' line-height:'.$slideDescStyles['slideDescLineHeightMobile'].'px;';

    $pbSliderBtnStylesMobile = ''
      .'padding:'.$slideButtonStyles['slideButtonBtnHeightMobile'].'px 5px;'
      .'width:'.$slideButtonStyles['slideButtonBtnWidthMobile'].'px;'
      .'font-size:'.$slideButtonStyles['slideButtonBtnFontSizeMobile'].'px;'
      .'letter-spacing:'.$slideButtonStyles['slideButtonBtnFontLetterSpacingMobile'].';px';

    $thisWidgetResponsiveWidgetStylesTablet = ' '
      . '#'.$pbImageSliderUniqueId.'{ height:'.$widget_imageSlider['pbSliderHeightTablet'].$widget_imageSlider['pbSliderHeightUnitTablet'].'; }'."\n"
      . '#'.$pbImageSliderUniqueId.' .popb_slide_content h2{ '.$pbSliderHeadingStylesTablet.'  } '."\n"
      . '#'.$pbImageSliderUniqueId.' .popb_slide_content p{ '.$pbSliderDescStylesTablet.'  }'
      . '#'.$pbImageSliderUniqueId.' .popb_slide_content button{ '.$pbSliderBtnStylesTablet.'  } '."\n";

    $thisWidgetResponsiveWidgetStylesMobile = ' '
      . '#'.$pbImageSliderUniqueId.'{ height:'.$widget_imageSlider['pbSliderHeightMobile'].$widget_imageSlider['pbSliderHeightUnitMobile'].'; }'."\n"
      . '#'.$pbImageSliderUniqueId.' .popb_slide_content h2{ '.$pbSliderHeadingStylesMobile.'  } '."\n"
      . '#'.$pbImageSliderUniqueId.' .popb_slide_content p{ '.$pbSliderDescStylesMobile.'  }'
      . '#'.$pbImageSliderUniqueId.' .popb_slide_content button{ '.$pbSliderBtnStylesMobile.'  } '."\n";


    array_push($POPBNallRowStylesResponsiveTablet, $thisWidgetResponsiveWidgetStylesTablet);
    
    array_push($POPBNallRowStylesResponsiveMobile, $thisWidgetResponsiveWidgetStylesMobile);
  }


  $slideContentAlignVMargin = '';

  if (!isset($widget_imageSlider['slideContentAlignVT'])) {
    $widget_imageSlider['slideContentAlignVT'] = '';
  }

  if (!isset($widget_imageSlider['slideContentAlignHT'])) {
    $widget_imageSlider['slideContentAlignHT'] = '';
  }

  if (!isset($widget_imageSlider['slideContentWidthT'])) {
    $widget_imageSlider['slideContentWidthT'] = '';
  }

  if (!isset($widget_imageSlider['slideContentWUnitT'])) {
    $widget_imageSlider['slideContentWUnitT'] = '';
  }

  if (!isset($widget_imageSlider['slideContentAlignT'])) {
    $widget_imageSlider['slideContentAlignT'] = '';
  }

  if (!isset($widget_imageSlider['slideContentAlignVM'])) {
    $widget_imageSlider['slideContentAlignVM'] = '';
  }

  if (!isset($widget_imageSlider['slideContentAlignHM'])) {
    $widget_imageSlider['slideContentAlignHM'] = '';
  }

  if (!isset($widget_imageSlider['slideContentWidthM'])) {
    $widget_imageSlider['slideContentWidthM'] = '';
  }

  if (!isset($widget_imageSlider['slideContentWUnitM'])) {
    $widget_imageSlider['slideContentWUnitM'] = '';
  }

  if (!isset($widget_imageSlider['slideContentAlignM'])) {
    $widget_imageSlider['slideContentAlignM'] = '';
  }



  if ($widget_imageSlider['slideContentAlignVT'] == 'middle') {
    $slideContentAlignV = '50%';
  }

  if ($widget_imageSlider['slideContentAlignVT'] == 'top') {
    $slideContentAlignV = '0%';
  }
  if ($widget_imageSlider['slideContentAlignVT'] == 'bottom') {
    $slideContentAlignV = '0%';
    $slideContentAlignVMargin = 'bottom:0% !important; top:inherit !important;';
  }

    $slideContentAlignHMargin = '';
  if ($widget_imageSlider['slideContentAlignHT'] == 'center') {
    $slideContentAlignH = '50%';
    $slideContentAlignHMargin = 'margin:0 auto;';
  }
    
  if ($widget_imageSlider['slideContentAlignHT'] == 'left') {
    $slideContentAlignH = '0%';
  }

  if ($widget_imageSlider['slideContentAlignHT'] == 'right') {
    $slideContentAlignH = '0%';
    $slideContentAlignHMargin = 'left:inherit !important; right: 0 !important;';
  }

  $slideContentAlignmentCssTablet =
    '#'.$pbImageSliderUniqueId.' .popb_slide_content{'.
      'width: '.$widget_imageSlider['slideContentWidthT'].$widget_imageSlider['slideContentWUnitT'].' !important;'.
      'text-align: '.$widget_imageSlider['slideContentAlignT'].' !important;'.
      'top: '.$slideContentAlignV.' !important;'.
      'left: '.$slideContentAlignH.' !important;'.
      'transform: translate(-'.$slideContentAlignH.', -'.$slideContentAlignV.') !important;'.
      '-ms-transform: translate(-'.$slideContentAlignH.', -'.$slideContentAlignV.') !important;'.
      '-webkit-transform: translate(-'.$slideContentAlignH.', -'.$slideContentAlignV.') !important;'.
      $slideContentAlignHMargin . $slideContentAlignVMargin .
    '}'
  ;

  array_push($POPBNallRowStylesResponsiveTablet, $slideContentAlignmentCssTablet);

  $slideContentAlignVMargin = '';

  if ($widget_imageSlider['slideContentAlignVM'] == 'middle') {
    $slideContentAlignV = '50%';
  }

  if ($widget_imageSlider['slideContentAlignVM'] == 'top') {
    $slideContentAlignV = '0%';
  }
  if ($widget_imageSlider['slideContentAlignVM'] == 'bottom') {
    $slideContentAlignV = '0%';
    $slideContentAlignVMargin = 'bottom:0% !important; top:inherit !important;';
  }

    $slideContentAlignHMargin = '';
  if ($widget_imageSlider['slideContentAlignHM'] == 'center') {
    $slideContentAlignH = '50%';
    $slideContentAlignHMargin = 'margin:0 auto;';
  }
    
  if ($widget_imageSlider['slideContentAlignHM'] == 'left') {
    $slideContentAlignH = '0%';
  }

  if ($widget_imageSlider['slideContentAlignHM'] == 'right') {
    $slideContentAlignH = '0%';
    $slideContentAlignHMargin = 'left:inherit !important; right: 0 !important;';
  }

  $slideContentAlignmentCssMobile =
    '#'.$pbImageSliderUniqueId.' .popb_slide_content{'.
      'width: '.$widget_imageSlider['slideContentWidthM'].$widget_imageSlider['slideContentWUnitM'].' !important;'.
      'text-align: '.$widget_imageSlider['slideContentAlignM'].' !important;'.
      'top: '.$slideContentAlignV.' !important;'.
      'left: '.$slideContentAlignH.' !important;'.
      'transform: translate(-'.$slideContentAlignH.', -'.$slideContentAlignV.') !important;'.
      '-ms-transform: translate(-'.$slideContentAlignH.', -'.$slideContentAlignV.') !important;'.
      '-webkit-transform: translate(-'.$slideContentAlignH.', -'.$slideContentAlignV.') !important;'.
      $slideContentAlignHMargin . $slideContentAlignVMargin .
    '}'
  ;
    
  array_push($POPBNallRowStylesResponsiveMobile, $slideContentAlignmentCssMobile);
    

  $widgetSliderLoadScripts = true;
  $widgetJQueryLoadScripts = true;
  array_push($POPBallWidgetsScriptsArray, $pbSliderScript);
  
  $widgetContent = $pbSliderContainer  .  $pbSliderAllSlides  .   $pbSliderContainerClose . $pbSliderStyling;

?>