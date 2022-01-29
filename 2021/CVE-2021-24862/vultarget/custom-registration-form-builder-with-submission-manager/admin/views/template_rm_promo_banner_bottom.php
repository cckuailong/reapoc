<?php
if (!defined('WPINC')) {
    die('Closed');
}
  if(isset($rm_promo_banner_title))
      $title = $rm_promo_banner_title;
  else
      $title = __('Upgrade and expand the power of','custom-registration-form-builder-with-submission-manager');
?>
<div class="rm-upgrade-note-gold">        
        <div class="rm-banner-title"><?php echo $title; ?><img src="<?php echo RM_IMG_URL.'logo.png'?>"> </div>
        <div class="rm-banner-box"><a href="<?php echo RM_Utilities::comparison_page_link(); ?>" target="_blank"><img src="<?php echo RM_IMG_URL.'premium-logo.png'?>"></a>
        </div>
</div>

