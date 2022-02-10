<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Settings UI
 * 
 */

global $wpdb;
$all_options = [];
?>

<div class="wrap publishpress-caps-manage publishpress-caps-settings pressshack-admin-wrapper">
    <h1><?php printf(__('Capabilities Settings', 'capsman-enhanced'), '<a href="admin.php?page=pp-capabilities">', '</a>'); ?></h1>

    <form class="basic-settings" method="post" action="">
        <?php wp_nonce_field('pp-capabilities-settings'); ?>

        <br />

        <?php do_action('pp-capabilities-settings-ui');?>

        <?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION')) :?>
        <h3><?php _e('Related Permissions Plugins', 'capsman-enhanced');?></h3>
        <ul>
            <?php $_url = "plugin-install.php?tab=plugin-information&plugin=publishpress&TB_iframe=true&width=640&height=678";
            $url = ( is_multisite() ) ? network_admin_url($_url) : admin_url($_url);
            ?>
            <li><a class="thickbox" href="<?php echo $url;?>"><?php _e('PublishPress', 'capsman-enhanced');?></a></li>

            <?php $_url = "plugin-install.php?tab=plugin-information&plugin=publishpress-authors&TB_iframe=true&width=640&height=678";
            $url = ( is_multisite() ) ? network_admin_url($_url) : admin_url($_url);
            ?>
            <li><a class="thickbox" href="<?php echo $url;?>"><?php _e('PublishPress Authors', 'capsman-enhanced');?></a></li>
            </li>
            
            <?php $_url = "plugin-install.php?tab=plugin-information&plugin=press-permit-core&TB_iframe=true&width=640&height=678";
            $url = ( is_multisite() ) ? network_admin_url($_url) : admin_url($_url);
            ?>
            <li><a class="thickbox" href="<?php echo $url;?>"><?php _e('PublishPress Permissions', 'capsman-enhanced');?></a></li>
            </li>
            
            <?php $_url = "plugin-install.php?tab=plugin-information&plugin=revisionary&TB_iframe=true&width=640&height=678";
            $url = ( is_multisite() ) ? network_admin_url($_url) : admin_url($_url);
            ?>
            <li><a class="thickbox" href="<?php echo $url;?>"><?php _e('PublishPress Revisions', 'capsman-enhanced');?></a></li>

            <li class="publishpress-contact"><a href="https://publishpress.com/contact" target="_blank"><?php _e('Help / Contact Form', 'capsman-enhanced');?></a></li>
        </ul>
        <?php endif;?>
        
        <?php
        echo "<input type='hidden' name='all_options' value='" . implode(',', $all_options) . "' />";
        ?>

        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'capsman-enhanced');?>">
    </form>

	<?php if (!defined('PUBLISHPRESS_CAPS_PRO_VERSION') || get_option('cme_display_branding')) {
		cme_publishpressFooter();
	}
	?>
</div>