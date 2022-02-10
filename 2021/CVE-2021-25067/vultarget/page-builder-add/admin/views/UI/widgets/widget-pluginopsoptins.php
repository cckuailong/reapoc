<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pbp_form" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
  <?php
  if ( post_type_exists('pluginops_forms') ) { ?>

  <label>Select An Optin Below : </label>
  <select class="widgetOptinId" id="widgetOptinId"  data-optname="widgetOptinId" >
      <option value="Select">Choose...</option>
      <?php 
        $ULP_pluginOps_Optins = array(
          'post_type' => 'pluginops_forms',
          'orderby' => 'date',
          'post_status'   => 'any',
          'posts_per_page'    => 100,
        );
        $ULP_pluginOps_Optins_posts = get_posts( $ULP_pluginOps_Optins );
        if (!is_array($ULP_pluginOps_Optins_posts)) {
          $ULP_pluginOps_Optins_posts = array();
        }
        foreach ($ULP_pluginOps_Optins_posts as  $thisPost) {
          $currentPostId = $thisPost->ID;
          $currentPostName = get_the_title($currentPostId);
          $currentPostLink = get_permalink($currentPostId);
          echo "<option value='$currentPostId' > $currentPostName </option>";
        }
      ?>
  </select>
    
  <?php } else { ?> 
    <p style="background: #f0f0f0; color:#333; padding: 10px; max-width: 90%; font-size: 17px;">Please install the Optin Builder plugin to access PluginOps Optins and to add them in your Landing Page.<br> You can install it by clicking here : <a target="_blank" href="<?php echo admin_url('plugin-install.php?s=pluginops+&tab=search&type=term'); ?>"> Install Optin Builder</a></p>
  <?php } ?>
	
</div>