<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php 

  if (is_plugin_active( 'page-builder-add-templates-pack-one/page-builder-add-templates-pack-one.php' )  || is_plugin_active('PluginOps-Extensions-Pack/extension-pack.php') ) {
      $templatesOneExtLink = '<button class="ext_cta_installed">'.__( 'Installed', 'page-builder-add' ).'  </button>';
  }else{
    $templatesOneExtLink = '<a href="https://pluginops.com/page-builder/?ref=extensionsPage"> <button class="ext_cta"> '.__( 'Get All Features', 'page-builder-add' ).'</button> </a>';
  }


  if (is_plugin_active( 'page-builder-add-mailchimp-extension/page-builder-add-mailchimp-extension.php' )  || is_plugin_active('PluginOps-Extensions-Pack/extension-pack.php') ) {
      $mailchimpExtLink = '<button class="ext_cta_installed">'.__( 'Installed', 'page-builder-add' ).'  </button>';
  }else{
    $mailchimpExtLink = '<a href="https://pluginops.com/page-builder/?ref=extensionsPageIntegrations"> <button class="ext_cta"> '.__( 'Get All Features', 'page-builder-add' ).'</button> </a>';
  }


?>

<div id="ulpb_dash_container">
  <h2 style="font-size:20px; font-weight: normal;"><?php _e( 'Landing Page Builder Premium Features', 'page-builder-add' ); ?>  </h2>

      <div id="tab1" class="tab active" style="background: #f1f1f1; padding: 30px;">
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/1.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> Premium Templates Pack </a> </h3>
          <p>Get beautiful Premium Templates & blocks to speed up your design process. Build your landing page funnels faster & better.</p>

          <?php echo $templatesOneExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container" style="background: #7289f2;"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/4.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> Global Rows</a> </h3>
          <p>Save & Reuse same row on multiple pages and make changes without having to edit each page. <br><br> </p>
          
          <?php echo $templatesOneExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/6.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> Database Extension </a> </h3>
          <p>With database extension you can save the user data from your forms in database which can be viewed & exported to be used with other services.</p>
          
          <?php echo $templatesOneExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/2.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> Export & Duplicate</a> </h3>
          <p>Export & Duplicate your pages and reuse them on multiple sites or same site, , Easy one click export & import.</p>
          
          <?php echo $templatesOneExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/13.png' ?>"> </div>
              <h3> <a href="https://pluginops.com/page-builder?ref='AbTesting'"> A/B Testing </a> </h3>
              <p>With A/B testing test different variants of Landing Pages to find out what and where it converts the most. A highly effective tool to increase leads.</p>
              <?php echo $templatesOneExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/3.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> MailChimp </a> </h3>
          <p>MailChimp Extension allows you to send your subscribe form and form builder submissions directly to your mailchimp account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>

        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/18.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> Zapier </a> </h3>
          <p>Zapier Extension allows you to send your form builder submissions directly to your account and integrate with thousands of services.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>

        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/5.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> Embed Anywhere </a> </h3>
          <p>Embed Anywhere Extension lets you place your templates/pages anywhere with just a shortcode. <br><br> </p>
          
          <?php echo $templatesOneExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/7.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> GetResponse Integration </a> </h3>
          <p>GetResponse Extension allows you to send your subscribe form and form builder submissions directly to your GetResponse account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/9.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> ActiveCampaign Integration </a> </h3>
          <p>Active Campaign Extension allows you to send your subscribe form and form builder submissions directly to your ActiveCampaign account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/8.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> CampaignMonitor Integration </a> </h3>
          <p>Campaign Monitor Extension allows you to send your subscribe form and form builder submissions directly to your CampaignMonitor account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/10.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> Aweber Integration </a> </h3>
          <p>Aweber Extension allows you to send your subscribe form and form builder submissions directly to your Aweber account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/11.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> Drip Integration </a> </h3>
          <p>Drip Extension allows you to send your subscribe form and form builder submissions directly to your Drip account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/12.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> ConvertKit Integration </a> </h3>
          <p>ConvertKit Extension allows you to send your subscribe form and form builder submissions directly to your ConvertKit account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/14.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> MarketHero Integration </a> </h3>
          <p>MarketHero Extension allows you to send your subscribe form and form builder submissions directly to your MarketHero account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/15.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> MailPoet Integration </a> </h3>
          <p>MailPoet Extension allows you to send your subscribe form and form builder submissions directly to your MailPoet Plugin & account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/16.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> SendInBlue Integration </a> </h3>
          <p>SendInBlue Extension allows you to send your subscribe form and form builder submissions directly to your SendInBlue account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/17.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> ConstantContact Integration </a> </h3>
          <p>ConstantContact Extension allows you to send your subscribe form and form builder submissions directly to your ConstantContact account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
        <div class="pb_ext-card">
          <div class="pb_extImg_container"> <img src="<?php echo ULPB_PLUGIN_URL.'/images/extension-icons/17.png' ?>"> </div>
          <h3> <a href="https://pluginops.com/page-builder/?ref=extensionsPage"> MailRelay Integration </a> </h3>
          <p>MailRelay Extension allows you to send your subscribe form and form builder submissions directly to your MailRelay account.</p>
          
          <?php echo $mailchimpExtLink; ?>
        </div>
      </div>
      
<style type="text/css">

  .pb_ext-card{
    display: inline-block;
    max-width:23%;
    min-width: 300px;
    max-height:550px;
    background: #fff;
    border:1px solid #ddd;
    text-align: center;
    margin-right: 1.2%;
    margin-bottom: 60px;
    padding-bottom: 30px;
  }

  .pb_ext-card a {
    text-decoration: none;
  }

  .pb_ext-card .ext_cta{
    border: none;
    padding: 2% 4% 2% 4%;
    font-size: 15px;
    color: #fff;
    background: #FF9800;
    cursor: pointer;
    margin: 1% 0 .5% 0;
    border-radius: 5px;
    font-weight: 500;
    letter-spacing: 2px;
  }

  .pb_ext-card .ext_cta:hover{
    background: #ffb445;
  }

  .pb_ext-card img {
    max-width:40% !important;
  }

  .pb_ext-card p {
    margin: 5px;
  }

  .pb_extImg_container {
    width: 100%;
    background: rgba(109, 150, 255, 1);
  }
  .ext_cta_installed{
    border: 2px solid #FF9800;
    padding: 10px 30px 10px 30px;
    font-size: 17px;
    color: #FF9800;
    background: #ffffff;
    cursor: pointer;
    margin: 10px 0 5px 0;
    border-radius: 5px;
    font-weight: 500;
    letter-spacing: 3px;
  }
  body{
    background: #F3F6F8 !important;
  }
</style>

<script type="text/javascript">
    jQuery('.tabs .tab-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
 
        // Show/Hide Tabs
        jQuery('.tabs ' + currentAttrValue).show().siblings().hide();
 
        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
 
        e.preventDefault();
    });
</script>