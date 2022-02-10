<?php if ( ! defined( 'ABSPATH' ) ) exit; 

if (isset( $_SERVER['HTTP_REFERER'] )) {
  $popb_main_user_referer = get_option( 'popb_main_user_referer', false );
  if (! $popb_main_user_referer) {
    $popb_main_user_referer = update_option( 'popb_main_user_referer', $_SERVER['HTTP_REFERER'] , null );
  }
  $popb_main_user_referer = get_option( 'popb_main_user_referer', false );
}

?>
<div id="consentAskBox" style="display: none;">
  <div class="close_data_optin" style="float: right;"> <a href="#">Close</a> </div>
  <p> Would you like to help us improve PluginOps Landing Page Builder by sending anonymous usage data ? </p>
  <div class="popb_button yes_buton button button-primary">Sure, I'll help</div>
  <div style="float: right; margin: 20px;">
    <div class=" popb_button nope_buton"> <u>No, Thank you</u></div>
    <div class="button expand_collection_information">What kind of info will we collect?</div>
    <div class="expaned_info" style="display: none;">
      <h4>Information We Collect</h4>
      <ul>
          <li>WordPress Version</li>
          <li>PlguinOps Plugin Version</li>
          <li>PHP Version</li>
          <li>Locale</li>
          <li>Public Website URL</li>
          <li>Number of Landing Pages</li>
      </ul>
      <h4>Tracking</h4>
      <ul>
          <li> Basic Conversion Tracking - Facebook, Google etc. </li>
      </ul>
    </div>
  </div>
</div>
<div id="ulpb_dash_container">
  <h2 style="font-size:20px; font-weight: normal;"> <?php _e( 'Page Builder Dashboard', 'page-builder-add' ); ?>  </h2>

  <div class="pluginops-tabs">
    <ul class="pluginops-tab-links">
        <li class="pluginops-active"><a href="#tab1" class="pluginops-tab_link"> <?php _e( 'Welcome', 'page-builder-add' ); ?> </a></li>
        <li><a href="#tab2" class="pluginops-tab_link"> <?php _e( 'Video Tutorials', 'page-builder-add' ); ?>  </a></li>
        <!--<li><a href="#tabUpdates" class="pluginops-tab_link">Update Log</a></li> -->
    </ul>

    <div class="pluginops-tab-content" style="min-height: 930px;">
      <div id="tab1" class="pluginops-tab pluginops-active" > 
        <h2> <?php _e( 'Welcome to Page Builder by PluginOps', 'page-builder-add' ); ?>  </h2>
        <p> <?php _e( 'Thank you for choosing the Page Builder plugin and welcome to the community. Find some useful information below and learn how to create beautiful pages in minutes.', 'page-builder-add' ); ?>  </p>
        <br>
        <h3> <?php _e( 'Getting Started - Build Your First Standalone Landing Page', 'page-builder-add' ); ?> </h3>
        <br>
        <a href="<?php echo admin_url('post-new.php?post_type=ulpb_post'); ?>" target="_blank" style="font-size:14px; font-weight: bold;"><?php _e( 'Page Builder - Add New Landing Page', 'page-builder-add' ); ?></a>
        <p> <?php _e( 'Ready to start creating pages ? Jump into the page builder by clicking the Add new Page button under the Page builder menu.', 'page-builder-add' ); ?> </p>
        <br>
        <br>
        <div style="float: left; width: 60%;">
        <h3><?php _e( 'Or Build a Simple Page With your Theme\'s Header & Footer ', 'page-builder-add' ); ?> </h3>
        <br>
        <a href="<?php echo admin_url('post-new.php?post_type=page'); ?>" target="_blank" style="font-size:14px; font-weight: bold;"><?php _e( 'Pages - Add New Page', 'page-builder-add' ); ?> </a>
        <p><?php _e( 'Add new Page and jump into the page builder by clicking the Switch to Page Builder tab.', 'page-builder-add' ); ?> </p>
        <br>
        </div>
        <br>
        <div style="float: left; width: 100%;">
          <hr>
          <br>
          <h2><?php _e( 'User Guide', 'page-builder-add' ); ?> </h2>
          <br>
          <h3><?php _e( 'PluginOps Page Builder - Getting Started Usage Guide', 'page-builder-add' ); ?> </h3>
          <a style="font-size:16px;" href="https://pluginops.com/pluginops-landing-page-builder-getting-started/" target="_blank"> <?php _e( 'PluginOps Page Builder - Getting Started', 'page-builder-add' ); ?>  </a>
          <br><br>
          <h3><?php _e( 'PluginOps Page Builder - Documentation', 'page-builder-add' ); ?> </h3>
          <a style="font-size:16px;"  href="https://pluginops.com/docs/home" target="_blank"> <?php _e( 'PluginOps Page Builder - Docs Home', 'page-builder-add' ); ?>  </a>
          <br><br>
          <h3><?php _e( 'Page not found error - Fix', 'page-builder-add' ); ?> </h3>
          <a style="font-size:16px;"  href="http://pluginops.com/fix-404-page-not-found-error-wordpress/" target="_blank"><?php _e( 'How to fix page not found error.', 'page-builder-add' ); ?> </a>
          <br><br><br>
          <a target="_blank" href="https://pluginops.com/fix-404-page-not-found-error-wordpress/" style="font-size:14px; font-weight: bold; background: #e3e3e3; padding: 5px 10px; line-height: 2em;"> If you face page not found error then Please reset your permalink structure to fix page not found error. </a>
          <br><br><br>
        </div>
      </div>
      <div id="tab2" class="pluginops-tab" style="background: #F3F6F8;">
        

        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/gUreU3ZDbVc" frameborder="0" allowfullscreen></iframe>
          <h3>How To Build a Lead Magnet Landing page</h3>
        </div>
        
        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/uExfYto3nV0" frameborder="0" allowfullscreen></iframe>
          <h3>How To Add Forms with Form Builder widget</h3>
        </div>

        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/GLMMsKp2kpY" frameborder="0" allowfullscreen></iframe>
          <h3> How to View Form Submissions </h3>
        </div>

        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/eeDsOS8uzco" frameborder="0" allowfullscreen></iframe>
          <h3> How to add custom fonts in landing pages </h3>
        </div>

        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/stqAH4VNhuc" frameborder="0" allowfullscreen></iframe>
          <h3> How to integrate with MailChimp </h3>
        </div>

        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/Xwqh-M288Bo" frameborder="0" allowfullscreen></iframe>
          <h3> How to use anchor widget </h3>
        </div>
        
        
        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/GHivNsOBngg" frameborder="0" allowfullscreen></iframe>
          <h3>How to change landing page URL keyword.</h3>
        </div>

        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/a0yb1Ce2ac8" frameborder="0" allowfullscreen></iframe>
          <h3>How To Add Pricing Table</h3>
        </div>

        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/5oRCB-7dZkY" frameborder="0" allowfullscreen></iframe>
          <h3>What is Margin & Padding</h3>
        </div>
        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/3rK4jL3oTRs" frameborder="0" allowfullscreen></iframe>
          <h3> Design A Landing Page</h3>
        </div>
        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/6W42KjrxM58" frameborder="0" allowfullscreen></iframe>
          <h3> Fix WordPress Page Not Found Error </h3>
        </div>
        <div class="video-card">
          <iframe width="460" height="255" src="https://www.youtube.com/embed/39oK8mFVMnA" frameborder="0" allowfullscreen></iframe>
          <h3> How To Create Full Page Slider </h3>
        </div>
      </div>
      <div id="tabUpdates" class="pluginops-tab">
        <h3>V. 1.5.4</h3> 
        <li>Whole New UI With Live Changes Preview</li>
        <li>Added WooCommerce Widget</li>
        <br>
        <br>
        <hr>
        <br>
        <br>
        <hr>
      </div>
    </div>
  </div>
</div>

<style type="text/css">
  #consentAskBox {
    background: #fff;
    border-left: 4px solid #00A0D2;
    padding:7px 15px 20px 15px;
    margin-top: 20px;
    margin-bottom: 50px;
  }
  #consentAskBox p {
    font-size: 15px;
  }
  .yes_buton{
    font-size: 16px !important;
    height: auto;;
    padding: 3px 30px 3px !important;
  }
  .nope_buton, .expand_collection_information{
    float: right;
    margin-left: 10px !important;
  }

  .nope_buton{
    font-size: 13px;
    line-height: 2.15;
    min-height: 30px;
    cursor: pointer;
  }

  .pluginops-tab_link{
    text-decoration:none;
  }
  .pluginops-tabs {
    width:auto;
    display:inline-block;
  }
   
     
  .pluginops-tab-links:after {
    display:block;
    clear:both;
    content:'';
  }

  .video-card{
    display: inline-block;
    max-width:660px;
    max-height:500px;
    background: #fff;
    border:1px solid #d3d3d3;
    text-align: center;
    margin-right: 15px;
    margin-bottom: 40px;
  }

  .pluginops-tab-links li {
    margin:0px 5px;
    float:left;
    list-style:none;
  }

  .pluginops-tab-links a {
    padding:9px 20px;
    display:inline-block;
    border-radius:7px 7px 0px 0px;
    background:#7fc9fb;
    font-size:16px;
    font-weight:600;
    color:#fff;
    transition:all linear 0.15s;
  }
   
  .pluginops-tab-links a:hover {
  background:#2fa8f9;
  text-decoration:none;
  }
   
  li.pluginops-active a, li.pluginops-active a:hover {
    background:#fff;
    color:#2fa8f9;
  }
   

  .pluginops-tab-content {
    border-radius:3px;
    box-shadow:-1px 1px 1px rgba(0,0,0,0.15);
    background:#fff;
  }
   
  .pluginops-tab {
    padding: 20px 40px;
    display:none;
    min-width: 60%;
    min-height: 600px;
  }
   
  .pluginops-tab.pluginops-active {
    display:block;
  }

  body{
    background: #F3F6F8 !important;
  }

  .expaned_info{
    background: #fff;
    padding: 40px 15px;
  }

</style>

<form id="formBuilderDataListEmpty" style="display: none !important;">
  <input type="text" name="asdasd">
</form>


<script type="text/javascript">


    jQuery('.pluginops-tabs2 .pluginops-tab2-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
 
        // Show/Hide Tabs
        jQuery('.pluginops-tabs2 ' + currentAttrValue).show().siblings().hide();
 
        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
 
        e.preventDefault();
    });


    jQuery('.pluginops-tabs .pluginops-tab-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
 
        jQuery('.pluginops-tabs ' + currentAttrValue).show().siblings().hide();
 
        jQuery(this).parent('li').addClass('pluginops-active').siblings().removeClass('pluginops-active');
 
        e.preventDefault();
    });


    <?php 
    $pluginops_tracking_consent = get_option( 'pluginops_tracking_consent', false ); 
    if ($pluginops_tracking_consent == '' || !isset($pluginops_tracking_consent)) {
      $pluginops_tracking_consent = 'notSet';
    }

    $plugOps_pageBuilder_data_nonce = wp_create_nonce( 'POPB_data_nonce' );
    ?>;

    var isConsentGiven = '<?php echo $pluginops_tracking_consent; ?>';

    var request_popb_nonce = '<?php echo $plugOps_pageBuilder_data_nonce; ?>';

    if (isConsentGiven == 'notSet' || isConsentGiven == '') {
      jQuery('#consentAskBox').css('display','block');
    }else{
      //jQuery('#consentAskBox').css('display','none');
    }

    jQuery('.close_data_optin').on('click',function(){
      jQuery('#consentAskBox').css('display','none');
      e.preventDefault();
    });

    jQuery('.expand_collection_information').on('click',function(){
      jQuery('.expaned_info').css('display','block');
      e.preventDefault();
    });

    jQuery('.popb_button').on('click',function(){
      checkIfYesIsPressed = jQuery(this).hasClass('yes_buton');
      console.log(checkIfYesIsPressed);

      var insSubmit_URl = "<?php echo admin_url('admin-ajax.php?action=popb_update_data_collection_option'); ?>&request_click_action="+checkIfYesIsPressed+"&submitNonce="+request_popb_nonce;
      var result = " ";
      var form = jQuery('#formBuilderDataListEmpty');
      jQuery.ajax({
        url: insSubmit_URl,
        method: 'post',
        data: form.serialize(),
          success: function(result){
            jQuery('#consentAskBox').css('display','none');
            location.reload(); 
          }
      });

      e.preventDefault();
      return;
    });

</script>


<?php 

$pluginops_tracking_consent = get_option( 'pluginops_tracking_consent', false ); 
if ($pluginops_tracking_consent == true) {
  ?>

  <!-- Global site tag (gtag.js) - Google Ads: 979248754 -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=AW-979248754"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'AW-979248754');
  </script>
  <!-- Event snippet for Plugin Install - Landing Page Builder conversion page -->
  <script>
    gtag('event', 'conversion', {'send_to': 'AW-979248754/_KH5CJvmz6UBEPLM-NID'});
  </script>


  <?php
}

?>