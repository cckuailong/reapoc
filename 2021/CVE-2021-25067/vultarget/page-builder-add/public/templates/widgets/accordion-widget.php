<?php  if ( ! defined( 'ABSPATH' ) ) exit;

  $this_widget = $thisWidget['widgetAccordion'];

  $accordionItems = $this_widget['accordionItems'];
  $accordionIcon = $this_widget['accordionIcon'];
  $accordionSettings = $this_widget['accordionSettings'];
  $accordionTitle = $this_widget['accordionTitle'];
  $accordionTitleTypography = $this_widget['accordionTitle']['typography'];
  $accordionContent = $this_widget['accordionContent'];
  $accordionContentTypography = $this_widget['accordionContent']['typography'];

  $uniqueAccordionId = (rand(500,1000)*2)*rand(10,500);

  $allAccordionItems = '';

  if ($accordionSettings['accoActive'] == 'true') {
    $isAccordionActive = '';
    $accordionActiveClass = 'acwTitleActive';
  }else{
    $isAccordionActive = 'active:false,';
    $accordionActiveClass = '';
  }

  if ($accordionTitleTypography['ffam'] == '') { $accordionTitleTypography['ffam'] = ' '; }

  $accordionIconClosed = '<i class=" '.$accordionIcon['acciClosed'].' acw_iconClosed"></i>';
  $accordionIconOpen = '<i class=" '.$accordionIcon['acciOpen'].' acw_iconOpen"></i>';

  foreach ($accordionItems as $index => $val) {

    if ($index == 0) {
      $accordionActiveClass = $accordionActiveClass;
    }else{
      $accordionActiveClass = ' ';
    }

  	$thisAccordTitle =
      '<h4 class="accordionTitle '.$accordionActiveClass.'">'.
        $accordionIconClosed.
        $accordionIconOpen.
        $val['accoTitle'].
      '</h4>'
    ;

    $thisAccordContent =
      '<div class="accordionContent"> '.$val['accContent'].' </div>'
    ;

    $thisAccordItem = $thisAccordTitle . "\n" . $thisAccordContent;

    $allAccordionItems = $allAccordionItems . "\n" . $thisAccordItem;

  }


  $fullRenderedAccordionWidget = 
    "<div id='accordion_widget_".$uniqueAccordionId."' class='accordion_widget' style=''>".
      $allAccordionItems.
    "</div>"
  ;


  $thisTitleBorderWidth = $accordionTitle['borwt'].'px ' . $accordionTitle['borwr'].'px ' . $accordionTitle['borwb'].'px ' . $accordionTitle['borwl'].'px ';
  $thisAccordTitleStyles = 
    'background:'.$accordionTitle['acctbg'].';'.
    'color:'.$accordionTitle['acctc'].';'.
    'padding:'.$accordionTitle['vgap'].'px '.$accordionTitle['hgap'].'px;'.
    'border-style:'.$accordionSettings['accocbort'].';'.
    'border-color:'.$accordionSettings['accocborc'].' !important;'.
    'border-width: '.$thisTitleBorderWidth.' ;'.
    'margin: 0px 0 0 0;'
  ;


  if(1 === preg_match('~[0-9]~', $accordionTitleTypography['ffam'])){
    $accordionTitleTypography['ffam'] = "'".$accordionTitleTypography['ffam']."'";
  }

  if(1 === preg_match('~[0-9]~', $accordionContentTypography['ffam'])){
    $accordionContentTypography['ffam'] = "'".$accordionContentTypography['ffam']."'";
  }

  $thisAccordTitleTypoStyles =
    'font-family:'. str_replace('+', ' ', $accordionTitleTypography['ffam'] ) .';'.
    'font-size:'.$accordionTitleTypography['fsize'].$accordionTitleTypography['fsizeu'].';'.
    'font-weight:'.$accordionTitleTypography['fwei'].';'.
    'text-transform:'.$accordionTitleTypography['ftrans'].';'.
    'text-style:'.$accordionTitleTypography['fstyl'].';'.
    'text-decoration:'.$accordionTitleTypography['fdeco'].';'.
    'line-height:'.$accordionTitleTypography['flinh'].'em;'.
    'letter-spacing:'.$accordionTitleTypography['fletsp'].'px;'
  ;

  $thisContentBorderWidth = $accordionContent['borwt'].'px ' . $accordionContent['borwr'].'px ' . $accordionContent['borwb'].'px ' . $accordionContent['borwl'].'px ';
  $thisAccordContentStyles =
    'background:'.$accordionContent['acccbg'].';'.
    'color:'.$accordionContent['acccc'].';'.
    'padding:'.$accordionContent['vgap'].'px '.$accordionContent['hgap'].'px;'.
    'border-style:'.$accordionSettings['accocbort'].';'.
    'border-color:'.$accordionSettings['accocborc'].'; !important;'.
    'border-width: '.$thisContentBorderWidth.' ;'
  ;


  $thisAccordContentTypoStyles =
    'font-family:'. str_replace('+', ' ', $accordionContentTypography['ffam'] ) .';'.
    'font-size:'.$accordionContentTypography['fsize'].$accordionContentTypography['fsizeu'].';'.
    'font-weight:'.$accordionContentTypography['fwei'].';'.
    'text-transform:'.$accordionContentTypography['ftrans'].';'.
    'text-style:'.$accordionContentTypography['fstyl'].';'.
    'text-decoration:'.$accordionContentTypography['fdeco'].';'.
    'line-height:'.$accordionContentTypography['flinh'].'em;'.
    'letter-spacing:'.$accordionContentTypography['fletsp'].'px;'
  ;

  $accordionCSS = 
    '<style>'.

      '#accordion_widget_'.$uniqueAccordionId.' .accordionTitle {'.
        $thisAccordTitleStyles.
        $thisAccordTitleTypoStyles.
      '}'.

      '#accordion_widget_'.$uniqueAccordionId.' .accordionContent {'.
        $thisAccordContentStyles.
        $thisAccordContentTypoStyles.
      '}'.

      '#accordion_widget_'.$uniqueAccordionId.' .ui-accordion-header-active {'.
        'color: '.$accordionTitle['acctc'].';'.
        'background: '.$accordionTitle['acctabg'].';'.
      '}'.

      '#accordion_widget_'.$uniqueAccordionId.' i {'.
        'padding: 0px '.$accordionIcon['acciGap'].'px;'.
        'color: '.$accordionIcon['acciColor'].';'.
        'float:'.$accordionIcon['acciAlign'].';'.
      '}'.

      '#accordion_widget_'.$uniqueAccordionId.'  .acwIconActive { '.
        'color: '.$accordionIcon['acciAColor'].' !important; '.
      '}'.

      '#accordion_widget_'.$uniqueAccordionId.'  .acwTitleActive { '.
        'color: '.$accordionTitle['acctac'].' !important;'.
        'background: '.$accordionTitle['acctabg'].' !important;'.
      '}'.

      '.accordion_widget  .ui-icon { display:none;}'.
      '.accordion_widget  .acw_iconOpen { display:none;}'.


    '</style>'
  ;


  $accordionScript = 

      '(function($){'.
        '$(document).ready(function() {'.

          'jQuery( "#accordion_widget_'.$uniqueAccordionId.'" ).accordion({'.

            'collapsible: true,'.
            'heightStyle: "'.$accordionSettings['accoHeight'].'",'.
            $isAccordionActive.
            'beforeActivate: function(event,ui){'.

              'var oldHeader = ui.oldHeader[0];'.
              'var newHeader = ui.newHeader[0];'.
              "jQuery(oldHeader).children('.acw_iconClosed').css('display','inline-block');".
              "jQuery(oldHeader).children('.acw_iconOpen').css('display','none');".

              "jQuery(newHeader).children('.acw_iconClosed').css('display','none');".
              "jQuery(newHeader).children('.acw_iconOpen').css('display','inline-block');".

              "jQuery('.acwIconActive').removeClass('acwIconActive');". 
              "jQuery(newHeader).children('.acw_iconOpen').addClass('acwIconActive');".

              "jQuery('.acwTitleActive').removeClass('acwTitleActive');". 
              "jQuery(newHeader).addClass('acwTitleActive');".

            '},'.

          '});'.

        '});'.

      '})(jQuery);'
      

  ;


  $accordionTabletStylesScript =
  	"#accordion_widget_$uniqueAccordionId .accordionTitle {
  		font-size: ".$accordionTitleTypography['fsizeT'].$accordionTitleTypography['fsizeuT']." !important;
  		line-height: ".$accordionTitleTypography['flinhT']."em !important;
  		letter-spacing: ".$accordionTitleTypography['fletspT']."px !important;
  	}

  	#accordion_widget_$uniqueAccordionId .accordionContent {
  		font-size: ".$accordionContentTypography['fsizeT'].$accordionContentTypography['fsizeuT']." !important;
  		line-height: ".$accordionContentTypography['flinhT']."em !important;
  		letter-spacing: ".$accordionContentTypography['fletspT']."px !important;
  	} "
   ;

  $accordionMobileStylesScript =
  	"#accordion_widget_$uniqueAccordionId .accordionTitle {
  		font-size: ".$accordionTitleTypography['fsizeM'].$accordionTitleTypography['fsizeuM']." !important;
  		line-height: ".$accordionTitleTypography['flinhM']."em !important;
  		letter-spacing: ".$accordionTitleTypography['fletspM']."px !important;
  	}

  	#accordion_widget_$uniqueAccordionId .accordionContent {
  		font-size: ".$accordionContentTypography['fsizeM'].$accordionContentTypography['fsizeuM']." !important;
  		line-height: ".$accordionContentTypography['flinhM']."em !important;
  		letter-spacing: ".$accordionContentTypography['fletspM']."px !important;
  	} "
  ;


  array_push($POPBNallRowStylesResponsiveTablet, $accordionTabletStylesScript);
  array_push($POPBNallRowStylesResponsiveMobile, $accordionMobileStylesScript);

  array_push($POPBallWidgetsScriptsArray, $accordionScript);

  array_push($thisColFontsToLoad, $accordionTitleTypography['ffam']);
  array_push($thisColFontsToLoad, $accordionContentTypography['ffam']);
    
  $widgetJQueryLoadScripts = true;
  $widgetFALoadScripts = true;
  
  $widgetContent = $fullRenderedAccordionWidget . $accordionCSS;

?>