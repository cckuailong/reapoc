<?php  if ( ! defined( 'ABSPATH' ) ) exit;

  $this_widget_widgetEmbedVideo = $thisWidget['widgetEmbedVideo'];

  $widgetEvidVideoType = $this_widget_widgetEmbedVideo['widgetEvidVideoType'];
  $widgetEvidVideoLink = $this_widget_widgetEmbedVideo['widgetEvidVideoLink'];
  $widgetEvidVideoAutoplay = $this_widget_widgetEmbedVideo['widgetEvidVideoAutoplay'];
  $widgetEvidVideoPlayerControls = $this_widget_widgetEmbedVideo['widgetEvidVideoPlayerControls'];
  $widgetEvidVideoTitle = $this_widget_widgetEmbedVideo['widgetEvidVideoTitle'];
  $widgetEvidVideoSuggested = $this_widget_widgetEmbedVideo['widgetEvidVideoSuggested'];
  $widgetEvidImageOverlay = $this_widget_widgetEmbedVideo['widgetEvidImageOverlay']; 
  $widgetEvidImageUrl = $this_widget_widgetEmbedVideo['widgetEvidImageUrl'];
  $widgetEvidImageIcon = $this_widget_widgetEmbedVideo['widgetEvidImageIcon'];
  $widgetEvidImageIconColor = $this_widget_widgetEmbedVideo['widgetEvidImageIconColor'];
  if ($widgetEvidImageIcon == 'block') {
    $widgetFALoadScripts = true;
  }
  
  $thumbImageScript = '';
  $widgetEvidPlayerId = 'POPB_player'.(rand(500,1000)*2)*rand(10,500);

  $widgetEvidVideoAutoplayPrev = $widgetEvidVideoAutoplay;
  $triggerClick = '';
  if ($widgetEvidVideoAutoplayPrev == 'true') {
    $triggerClick = 'jQuery("#thumbImage_'.$widgetEvidPlayerId.'").trigger("click");';
  }

  if ($widgetEvidVideoAutoplay == 'true') {
      $widgetEvidVideoAutoplay = 1;
    }else{
      $widgetEvidVideoAutoplay = 0;
    }

    if ($widgetEvidVideoPlayerControls == 'true') {
      $widgetEvidVideoPlayerControls = 1;
    }else{
      $widgetEvidVideoPlayerControls = 0;
    }

    if ($widgetEvidVideoSuggested == 'true') {
      $widgetEvidVideoSuggested = 1;
    }else{
      $widgetEvidVideoSuggested = 0;
    }

    if ($widgetEvidVideoTitle == 'true') {
      $widgetEvidVideoTitle = 1;
    }else{
      $widgetEvidVideoTitle = 0;
    }

  if ($widgetEvidVideoType == 'youtube') {


    if ($widgetEvidImageOverlay == 'true') {
      $widgetEvidVideoAutoplay = 1;
    }


       $YtregExp = "/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/";
       $YTurlMatch = preg_match($YtregExp, $widgetEvidVideoLink,$YTurlMatches);
       if($YTurlMatch == 1){
        $widgetEvidVideoLink =  $YTurlMatches[7];
       }else{
        $widgetEvidVideoLink = 'false';
       }

    $videoIframeURL = 'https://www.youtube.com/embed/'.$widgetEvidVideoLink.'?autoplay='.$widgetEvidVideoAutoplay.'&amp;rel='.$widgetEvidVideoSuggested.'&amp;showinfo='.$widgetEvidVideoTitle.'&amp;controls='.$widgetEvidVideoPlayerControls;

    if ($widgetEvidImageOverlay == 'true' ) {
      $thumbnailVidIframe = "<iframe style='position: absolute; top: 0; left: 0; width: 100%; height: 100%;' src='".$videoIframeURL."' frameborder='0' allowfullscreen></iframe> ";

      $thumbnailVidIframe = str_replace("'", '"', $thumbnailVidIframe);

      $thumbImageScript =

        'jQuery("#thumbImage_'.$widgetEvidPlayerId.'").click(function(){'.
          'jQuery("#'.$widgetEvidPlayerId.'").html(\''.$thumbnailVidIframe.'\');'.
          'jQuery(this).remove();'.
        '}); '. $triggerClick

      ;
      $POPBVideoIframeContainer = 
        '<div id="'.$widgetEvidPlayerId.'" style="position: relative; padding-bottom: 56.25%;  height: 0; cursor:pointer;" >
          <div id="thumbImage_'.$widgetEvidPlayerId.'" >
            <i class="fas fa-play" style="color:'.$widgetEvidImageIconColor.'; box-shadow:2px 15px 20px '.$widgetEvidImageIconColor.'55; font-size:85px;z-index:1;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%);  -moz-transform: translate(-50%, -50%);  -ms-transform: translate(-50%, -50%);  border: 5px solid '.$widgetEvidImageIconColor.';padding: 20px 30px;border-radius: 200px; display:'.$widgetEvidImageIcon.'; box-shadow:2px 3px 10px; text-shadow:2px 3px 10px; "></i>
            <img style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"  src="'.$widgetEvidImageUrl.'">
          </div>
        </div> 
      '."\n";

    } else{

      $POPBVideoIframeContainer = '<div id="'.$widgetEvidPlayerId.'" style="position: relative; padding-bottom: 56.25%;   height: 0;" > <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src="'.$videoIframeURL.'" frameborder="0" allowfullscreen></iframe> </div>';

    }

  } else if($widgetEvidVideoType == 'vimeo'){

    if ($widgetEvidImageOverlay == 'true') {
      $widgetEvidVideoAutoplay = 1;
    }

    $embededVimeo_url_parser = '/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/';

    $embededVimeo_urlMatch = preg_match($embededVimeo_url_parser, $widgetEvidVideoLink,$VimeourlMatches);

    if ($embededVimeo_urlMatch == 1) {
      $embededVimeo_url = $VimeourlMatches[3];
    }else{
      $embededVimeo_url = 'Not Valid URL';
    }

    $videoIframeURL = 'https://player.vimeo.com/video/'.$embededVimeo_url.'?autoplay='.$widgetEvidVideoAutoplay.'&amp;rel='.$widgetEvidVideoSuggested.'&amp;title='.$widgetEvidVideoTitle;

    if ($widgetEvidImageOverlay == 'true' ) {

      $thumbnailVidIframe = "<iframe style='position: absolute; top: 0; left: 0; width: 100%; height: 100%;' src='".$videoIframeURL."' frameborder='0' allowfullscreen></iframe> ";

      $thumbnailVidIframe = str_replace("'", '"', $thumbnailVidIframe);
      $thumbImageScript =

        'jQuery("#thumbImage_'.$widgetEvidPlayerId.'").click(function(){'.
          'jQuery("#'.$widgetEvidPlayerId.'").html(\''.$thumbnailVidIframe.'\');'.
          'jQuery(this).remove();'.
        '}); '. $triggerClick 

      ;
      
      $POPBVideoIframeContainer = '<div id="'.$widgetEvidPlayerId.'" style="position: relative; padding-bottom: 56.25%;  height: 0; cursor:pointer;" >  <div id="thumbImage_'.$widgetEvidPlayerId.'" > <i class="fas fa-play" style="color:'.$widgetEvidImageIconColor.'; box-shadow:2px 15px 20px '.$widgetEvidImageIconColor.'55; font-size:85px;z-index:1;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%);  -moz-transform: translate(-50%, -50%);  -ms-transform: translate(-50%, -50%); border: 5px solid '.$widgetEvidImageIconColor.';padding: 20px 30px;border-radius: 200px; display:'.$widgetEvidImageIcon.'; box-shadow:2px 3px 10px; text-shadow:2px 3px 10px; "></i> <img style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"  src="'.$widgetEvidImageUrl.'"> </div> </div> '."\n";

    } else{

      $POPBVideoIframeContainer = '<div id="'.$widgetEvidPlayerId.'" style="position: relative; padding-bottom: 56.25%;   height: 0;" > <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src="'.$videoIframeURL.'" frameborder="0" allowfullscreen></iframe> </div>';

    }
    

  }

  array_push($POPBallWidgetsScriptsArray, $thumbImageScript);
  $widgetJQueryLoadScripts = true;

  $widgetContent = $POPBVideoIframeContainer;



?>