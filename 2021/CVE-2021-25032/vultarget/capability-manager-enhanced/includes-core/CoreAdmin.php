<?php
namespace PublishPress\Capabilities;

class CoreAdmin {
    function __construct() {
        add_action('admin_print_scripts', [$this, 'setUpgradeMenuLink'], 50);

        if (is_admin()) {
            $autoloadPath = PUBLISHPRESS_CAPS_ABSPATH . '/vendor/autoload.php';
			if (file_exists($autoloadPath)) {
				require_once $autoloadPath;
			}

            require_once PUBLISHPRESS_CAPS_ABSPATH . '/vendor/publishpress/wordpress-version-notices/includes.php';
    
            add_filter(\PPVersionNotices\Module\TopNotice\Module::SETTINGS_FILTER, function ($settings) {
                $settings['capabilities'] = [
                    'message' => 'You\'re using PublishPress Capabilities Free. The Pro version has more features and support. %sUpgrade to Pro%s',
                    'link'    => 'https://publishpress.com/links/capabilities-banner',
                    'screens' => [
                        ['base' => 'toplevel_page_pp-capabilities'],
                        ['base' => 'capabilities_page_pp-capabilities-roles'],
                        ['base' => 'capabilities_page_pp-capabilities-backup'],
                        ['base' => 'capabilities_page_pp-capabilities-settings'],
                    ]
                ];
    
                return $settings;
            });
        }

        add_action('pp-capabilities-admin-submenus', [$this, 'actCapabilitiesSubmenus']);

        //Editor feature metaboxes promo
        add_action('pp_capabilities_features_gutenberg_after_table_tr', [$this, 'metaboxesPromo']);
        add_action('pp_capabilities_features_classic_after_table_tr', [$this, 'metaboxesPromo']);
    }

    function setUpgradeMenuLink() {
        $url = 'https://publishpress.com/links/capabilities-menu';
        ?>
        <style type="text/css">
        #toplevel_page_pp-capabilities ul li:last-of-type a {font-weight: bold !important; color: #FEB123 !important;}
        </style>

		<script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#toplevel_page_pp-capabilities ul li:last a').attr('href', '<?php echo $url;?>').attr('target', '_blank').css('font-weight', 'bold').css('color', '#FEB123');
            });
        </script>
		<?php
    }

    function actCapabilitiesSubmenus() {
        $cap_name = (is_multisite() && is_super_admin()) ? 'read' : 'manage_capabilities';
        
        add_submenu_page('pp-capabilities',  __('Admin Menus', 'capsman-enhanced'), __('Admin Menus', 'capsman-enhanced'), $cap_name, 'pp-capabilities-admin-menus', [$this, 'AdminMenusPromo']);
        add_submenu_page('pp-capabilities',  __('Nav Menus', 'capsman-enhanced'), __('Nav Menus', 'capsman-enhanced'), $cap_name, 'pp-capabilities-nav-menus', [$this, 'NavMenusPromo']);
    }

    function AdminMenusPromo() {
        wp_enqueue_style('pp-capabilities-admin-core', plugin_dir_url(CME_FILE) . 'includes-core/admin-core.css', [], PUBLISHPRESS_CAPS_VERSION, 'all');
        include (dirname(__FILE__) . '/admin-menus-promo.php');
    }

    function NavMenusPromo() {
        wp_enqueue_style('pp-capabilities-admin-core', plugin_dir_url(CME_FILE) . 'includes-core/admin-core.css', [], PUBLISHPRESS_CAPS_VERSION, 'all');
        include (dirname(__FILE__) . '/nav-menus-promo.php');
    }

    function metaboxesPromo(){
        wp_enqueue_style('pp-capabilities-admin-core', plugin_dir_url(CME_FILE) . 'includes-core/admin-core.css', [], PUBLISHPRESS_CAPS_VERSION, 'all');
        include (dirname(__FILE__) . '/editor-features-promo.php');
    }
}
