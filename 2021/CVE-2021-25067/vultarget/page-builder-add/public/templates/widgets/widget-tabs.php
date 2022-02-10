<?php  if ( ! defined( 'ABSPATH' ) ) exit;

  $this_widget = $thisWidget['widgetTabs'];



  $tabItems = $this_widget['tabItems'];
  $tabIcon = $this_widget['tabIcon'];
  $tabsettings = $this_widget['tabSettings'];
  $tabTitle = $this_widget['tabTitle'];
  $tabTitleTypography = $this_widget['tabTitle']['typography'];
  $tabContent = $this_widget['tabContent'];
  $tabContentTypography = $this_widget['tabContent']['typography'];

  $uniquetabId = (rand(500,1000)*2)*rand(10,500);

  $allTabTitles = '';
  $allTabContent = '';

  if ($tabTitleTypography['ffam'] == '') { $tabTitleTypography['ffam'] = ' '; }


  foreach ($tabItems as $index => $val) {

    $thisTabIcon = '';
    if ($val['icon'] != '') {
      $thisTabIcon = '<i class=" '.$val['icon'].' tab_icon"></i>';
    }
    
    $linkActiveContainer = ''; $linkActiveClass = '';
    if ($index == 0) {
      $linkActiveClass = 'tab_widget_linkActive';
      $linkActiveContainer = 'tabContentActive';
    }

    $thisTabIconAfter = ''; $thisTabIconBefore = '';
    if ($tabIcon['acciPos'] == 'before') {
      $thisTabIconBefore = $thisTabIcon;
    }else{
      $thisTabIconAfter = $thisTabIcon;
    }


    $thisTabTitle =
      "<li class='tab_widget-menu tabTitle tab_widget-tab_link ".$linkActiveClass."' data-href='#tabWidget_".$index."_".$uniquetabId."' >".
        $thisTabIconBefore.
          $val['title'].
        $thisTabIconAfter.
      "</li>"
    ;


    $accContent = $val['content'];

    $thistabContent =
      '<div id="tabWidget_'.$index.'_'.$uniquetabId.'" class="tab_widget-tab tabContent '.$linkActiveContainer.'" > '.$accContent .' </div>'
    ;


    $allTabTitles = $allTabTitles . "\n" . $thisTabTitle;

    $allTabContent = $allTabContent . "\n" . $thistabContent;

  }


  $fullRenderedtabWidget = 
    "<div class='tab_widget tab_widget-tab' id='tab_widget_".$uniquetabId."' >".
      "<div style='width:100%;display:block; float:left;' >".
        "<ul class='tab_widget-links'>".
          $allTabTitles.
        "</ul>".
      "</div>".
      "<div class='tab_widget-tab-content' style='float:left;'>".
        $allTabContent.
      "</div>".
    "</div>"
  ;


  $thisTitleBorderWidth = $tabTitle['borwt'].'px ' . $tabTitle['borwr'].'px ' . $tabTitle['borwb'].'px ' . $tabTitle['borwl'].'px ';
  $thisAccordTitleStyles = 
    'background:'.$tabTitle['acctbg'].';'.
    'color:'.$tabTitle['acctc'].';'.
    'padding:'.$tabTitle['vgap'].'px '.$tabTitle['hgap'].'px;'.
    'border-style:'.$tabsettings['accocbort'].';'.
    'border-color:'.$tabsettings['accocborc'].' !important;'.
    'border-width: '.$thisTitleBorderWidth.' ;'.
    'margin: 0px 0 0 0;'
  ;

  if(1 === preg_match('~[0-9]~', $tabTitleTypography['ffam'])){
    $tabTitleTypography['ffam'] = "'".$tabTitleTypography['ffam']."'";
  }

  if(1 === preg_match('~[0-9]~', $tabContentTypography['ffam'])){
    $tabContentTypography['ffam'] = "'".$tabContentTypography['ffam']."'";
  }

  $thisAccordTitleTypoStyles =
    'font-family:'.str_replace('+', ' ', $tabTitleTypography['ffam'] ).';'.
    'font-size:'.$tabTitleTypography['fsize'].$tabTitleTypography['fsizeu'].';'.
    'font-weight:'.$tabTitleTypography['fwei'].';'.
    'text-transform:'.$tabTitleTypography['ftrans'].';'.
    'text-style:'.$tabTitleTypography['fstyl'].';'.
    'text-decoration:'.$tabTitleTypography['fdeco'].';'.
    'line-height:'.$tabTitleTypography['flinh'].'em;'.
    'letter-spacing:'.$tabTitleTypography['fletsp'].'px;'
  ;

  $thisContentBorderWidth = $tabContent['borwt'].'px ' . $tabContent['borwr'].'px ' . $tabContent['borwb'].'px ' . $tabContent['borwl'].'px ';
  $thisAccordContentStyles =
    'background:'.$tabContent['acccbg'].';'.
    'color:'.$tabContent['acccc'].';'.
    'padding:'.$tabContent['vgap'].'px '.$tabContent['hgap'].'px;'.
    'border-style:'.$tabsettings['accocbort'].';'.
    'border-color:'.$tabsettings['accocborc'].'; !important;'.
    'border-width: '.$thisContentBorderWidth.' ;'
  ;

  $thisAccordContentTypoStyles =
    'font-family:'.str_replace('+', ' ', $tabContentTypography['ffam'] ).';'.
    'font-size:'.$tabContentTypography['fsize'].$tabContentTypography['fsizeu'].';'.
    'font-weight:'.$tabContentTypography['fwei'].';'.
    'text-transform:'.$tabContentTypography['ftrans'].';'.
    'text-style:'.$tabContentTypography['fstyl'].';'.
    'text-decoration:'.$tabContentTypography['fdeco'].';'.
    'line-height:'.$tabContentTypography['flinh'].'em;'.
    'letter-spacing:'.$tabContentTypography['fletsp'].'px;'
  ;

  $tabCSS = 
    '<style>'.

      '#tab_widget_'.$uniquetabId.' .tabTitle {'.
        $thisAccordTitleStyles.
        $thisAccordTitleTypoStyles.
      '}'.

      '#tab_widget_'.$uniquetabId.' .tabContent {'.
        $thisAccordContentStyles.
        $thisAccordContentTypoStyles.
      '}'.

      '#tab_widget_'.$uniquetabId.' .ui-tab-header-active {'.
        'color: '.$tabTitle['acctc'].';'.
        'background: '.$tabTitle['acctabg'].';'.
      '}'.

      '#tab_widget_'.$uniquetabId.' i {'.
        'padding: 0px '.$tabIcon['acciGap'].'px;'.
      '}'.

      '#tab_widget_'.$uniquetabId.'  .tab_widget_linkActive { '.
        'color: '.$tabTitle['acctac'].' !important;'.
        'background: '.$tabTitle['acctabg'].' !important;'.
      '}'.

      '#tab_widget_'.$uniquetabId.' .tabContent  {'.
        'display:none;'.
      '}'.
      '#tab_widget_'.$uniquetabId.' .tabContentActive  {'.
        'display:block !important;'.
      '}'.

      '.tab_widget  .ui-icon { display:none; }'.
      '.tab_widget  .acw_iconOpen { display:none; }'.
      "
      	.tab_widget ul{
      		padding:0px;
      		margin:0px;
      	}
      	.tab_widget_link {
		  text-decoration:none;
		  color:inherit;
		}
		.tab_widget_link:hover{
		  color:inherit;
		}
		.tab_widget_link:hover{
		  color:inherit;
		}

		.tab_widget-tabs, .tab_widget-tab-content {
		  width: 100%;
		  position: relative;
		  display: block;
		}

		.tab_widget-links li{
		  display: inline-block;
		  float:left;
		  cursor: pointer;
		  list-style:none;
		}

		.tabContent {
		  display:none;
		  width: 100%;
		  position: relative;
		  display: block;
		}
	  ".

    '</style>'
  ;


  $tabScript = 
    
      '(function($){'.
        '$(document).ready(function() {'.

          "jQuery('#tab_widget_".$uniquetabId." .tab_widget-tab_link').on('click', function(e)  {".
              "var currentAttrValue = jQuery(this).attr('data-href');".
              
              "jQuery('#tab_widget_".$uniquetabId." .tab_widget-tab-content ' + currentAttrValue).show().siblings().hide();".
              
              "jQuery('#tab_widget_".$uniquetabId." .tabContentActive ').removeClass('tabContentActive');".
              "jQuery('#tab_widget_".$uniquetabId." .tab_widget-tab-content ' + currentAttrValue).addClass('tabContentActive');".

              "jQuery(this).addClass('tab_widget_linkActive').siblings().removeClass('tab_widget_linkActive');".
       
              "e.preventDefault();".
          "});".

        '});'.

      '})(jQuery);'.
      
    " ";


  $tabTabletStylesScript =
  	"#tab_widget_$uniquetabId .tabTitle {
  		font-size: ".$tabTitleTypography['fsizeT'].$tabTitleTypography['fsizeuT']." !important;
  		line-height: ".$tabTitleTypography['flinhT']."em !important;
  		letter-spacing: ".$tabTitleTypography['fletspT']."px !important;
  	}

  	#tab_widget_$uniquetabId .tabContent {
  		font-size: ".$tabContentTypography['fsizeT'].$tabContentTypography['fsizeuT']." !important;
  		line-height: ".$tabContentTypography['flinhT']."em !important;
  		letter-spacing: ".$tabContentTypography['fletspT']."px !important;
  	} "
   ;

  $tabMobileStylesScript =
  	"#tab_widget_$uniquetabId .tabTitle {
  		font-size: ".$tabTitleTypography['fsizeM'].$tabTitleTypography['fsizeuM']." !important;
  		line-height: ".$tabTitleTypography['flinhM']."em !important;
  		letter-spacing: ".$tabTitleTypography['fletspM']."px !important;
  	}

  	#tab_widget_$uniquetabId .tabContent {
  		font-size: ".$tabContentTypography['fsizeM'].$tabContentTypography['fsizeuM']." !important;
  		line-height: ".$tabContentTypography['flinhM']."em !important;
  		letter-spacing: ".$tabContentTypography['fletspM']."px !important;
  	} "
  ;


  array_push($POPBNallRowStylesResponsiveTablet, $tabTabletStylesScript);
  array_push($POPBNallRowStylesResponsiveMobile, $tabMobileStylesScript);

  array_push($thisColFontsToLoad, $tabTitleTypography['ffam']);
  array_push($thisColFontsToLoad, $tabContentTypography['ffam']);

  array_push($POPBallWidgetsScriptsArray, $tabScript);

  $widgetJQueryLoadScripts = true;
  $widgetFALoadScripts = true;

  $widgetContent = $fullRenderedtabWidget . $tabCSS;