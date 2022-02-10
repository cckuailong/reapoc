<?php
	$ICON_EDIT = WPAM_URL . "/images/icon_edit.png";
 	$ICON_DELETE = WPAM_URL . "/images/icon_delete.png";
?>

<script src="<?php echo WPAM_URL?>/js/jquery.tablednd_0_5.js" type="text/javascript"></script>
<script type="text/javascript">

	jQuery(function($) {
		/*
		$("#tabs").tabs({
			cookie: {
				  name: 'wpam_settings_tab'
			}
		});
                */
	});
</script>

<div class="wrap">

	<h2><?php _e('Settings', 'affiliates-manager') ?></h2>
	<?php if (isset($this->viewData['updateMessage'])) {?>
		<div id="updated" class="updated">
			<p><?php echo $this->viewData['updateMessage']?></p>
		</div>
	<?php }?>

<?php
require_once WPAM_BASE_DIRECTORY . "/html/widget_form_errors_panel.php";
?>

                <!--
		<div id="tabs">
			<ul>
				<li><a href="#tabs-general">General</a></li>
				<li><a href="#tabs-affiliate-registration">Affiliate Registration</a></li>
				<li><a href="#tabs-messaging">Messaging</a></li>
				<li><a href="#tabs-payment">Payment</a></li>
			</ul>
			
		</div>
                -->
                <?php
                $wpam_plugin_tabs = array(
                    'wpam-settings' => __('General', 'affiliates-manager'),
                    'wpam-settings&action=affiliate-registration' => __('Affiliate Registration', 'affiliates-manager'),
                    'wpam-settings&action=messaging' => __('Messaging', 'affiliates-manager'),
                    'wpam-settings&action=payment' => __('Payment', 'affiliates-manager'),
                    'wpam-settings&action=affiliate-pages' => __('Pages/Forms', 'affiliates-manager'),
                    'wpam-settings&action=advanced-settings' => __('Advanced Settings', 'affiliates-manager'),
                ); 

                if(isset($_GET['page'])){
                    $current = sanitize_text_field($_GET['page']);
                    if(isset($_GET['action'])){
                        $current .= "&action=".sanitize_text_field($_GET['action']);
                    }
                }
                $content = '';
                $content .= '<h2 class="nav-tab-wrapper">';
                foreach($wpam_plugin_tabs as $location => $tabname)
                {
                    if($current == $location){
                        $class = ' nav-tab-active';
                    } else{
                        $class = '';    
                    }
                    $content .= '<a class="nav-tab'.$class.'" href="?page='.$location.'">'.$tabname.'</a>';
                }
                $content .= '</h2>';
                echo $content;
                ?>
                <div id="poststuff"><div id="post-body">
                <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<input type="hidden" name="wpam_submit_settings" value="1" />
                <?php
                if(isset($_GET['action']))
                { 
                     switch ($_GET['action'])
                     {
                          case 'affiliate-registration':
                              ?>
                              <input type="hidden" name="AffRegSettings" value="1" />
                              <?php wp_nonce_field('aff_reg_settings_save'); ?>
                              <?php
                              require_once "settings_registration.php";
                              break;
                          case 'messaging':
                              ?>
                              <input type="hidden" name="AffMsgSettings" value="1" />
                              <?php wp_nonce_field('aff_msg_settings_save'); ?>
                              <?php
                              require_once "settings_messaging.php";
                              break;
                          case 'payment':
                              ?>
                              <input type="hidden" name="AffPaymentSettings" value="1" />
                              <?php wp_nonce_field('aff_payment_settings_save'); ?>
                              <?php
                              require_once "settings_payment.php";
                              break;
                          case 'affiliate-pages':
                              ?>
                              <input type="hidden" name="AffPagesSettings" value="1" />
                              <?php wp_nonce_field('aff_pages_settings_save'); ?>
                              <?php
                              require_once "settings_aff_pages.php";
                              break;
                          case 'advanced-settings':
                              ?>
                              <input type="hidden" name="AffAdvancedSettings" value="1" />
                              <?php wp_nonce_field('aff_advanced_settings_save'); ?>
                              <?php
                              require_once "settings_advanced.php";
                              break;
                     }
                }
                else
                {
                    ?>
                    <input type="hidden" name="AffGeneralSettings" value="1" />
                    <?php wp_nonce_field('aff_general_settings_save'); ?>
                    <?php
                    require_once "settings_general.php";
                }
                ?>
		<div style="margin: 10px;">
			<input class="button-primary" type="submit" name="btnSubmit" id="btnSubmit" value="<?php _e('Save Settings', 'affiliates-manager');?>" />
		</div>



	</form>
        </div></div>
</div>