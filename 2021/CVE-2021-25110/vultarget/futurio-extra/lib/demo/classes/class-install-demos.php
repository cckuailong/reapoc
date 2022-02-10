<?php
/**
 * Install demos page
 *
 * @package Futurio_Extra
 * @category Core
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Start Class
class FWP_Install_Demos {

    /**
     * Start things up
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_page'), 999);
    }

    /**
     * Add sub menu page for the custom CSS input
     *
     * @since 1.0.0
     */
    public function add_page() {

        $title = esc_html__('Install Demos', 'futurio-extra');

        add_submenu_page(
                'themes.php',
                esc_html__('Install Demos', 'futurio-extra'),
                $title,
                'manage_options',
                'futurio-panel-install-demos',
                array($this, 'create_admin_page')
        );
    }

    /**
     * Settings page output
     *
     * @since 1.0.0
     */
    public function create_admin_page() {

        // Theme branding
        $brand = 'Futurio';
        ?>

        <div class="fwp-demo-wrap wrap">

            <h2><?php echo esc_attr($brand); ?> - <?php esc_attr_e('Install Demos', 'futurio-extra'); ?></h2>
            <p>
                <?php esc_html_e('Thank you for using our theme. You can import our demo sites or set up the website from scratch.', 'futurio-extra') ?>
                <a href="<?php echo esc_url(admin_url('themes.php?page=futurio')); ?>" class="button action-btn">
                    <?php esc_html_e('Futurio Options', 'futurio-extra') ?>
                </a>
            </p>
            <div class="theme-browser rendered">

                <?php
                // Vars
                $demos = FuturioWP_Demos::get_demos_data();
                $categories = FuturioWP_Demos::get_demo_all_categories($demos);
                ?>

                <?php if (!empty($categories)) : ?>
                    <div class="fwp-header-bar">
                        <nav class="fwp-navigation">
                            <ul>
                                <li class="active"><a href="#all" class="fwp-navigation-link"><?php esc_html_e('All', 'futurio-extra'); ?></a></li>
                                <?php foreach ($categories as $key => $name) : ?>
                                    <li><a href="#<?php echo esc_attr($key); ?>" class="fwp-navigation-link"><?php echo esc_html($name); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                        <div clas="fwp-search">
                            <input type="text" class="fwp-search-input" name="fwp-search" value="" placeholder="<?php esc_html_e('Search demos...', 'futurio-extra'); ?>">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="themes wp-clearfix">

                    <?php
                    // Loop through all demos
                    foreach ($demos as $demo => $key) {

                        // Vars
                        $item_categories = FuturioWP_Demos::get_demo_item_categories($key);
                        $title = str_replace('demo', '', $demo);
                        $title = str_replace('-', ' ', $title);
                        $pro = $key['required_plugins'];
                        ?>

                        <div class="theme-wrap" data-categories="<?php echo esc_attr($item_categories); ?>" data-name="<?php echo esc_attr(strtolower($demo)); ?>">

                            <div class="theme fwp-open-popup" data-demo-id="<?php echo esc_attr($demo); ?>">

                                <div class="theme-screenshot">
                                    <img src="https://futuriodemos.com/wp-content/uploads/demos/<?php echo esc_attr($demo); ?>.jpg" />

                                    <div class="demo-import-loader preview-all preview-all-<?php echo esc_attr($demo); ?>"></div>

                                    <div class="demo-import-loader preview-icon preview-<?php echo esc_attr($demo); ?>"><i class="custom-loader"></i></div>
                                    <?php if (isset($pro['premium']) && !empty($pro['premium'])) { ?>
                                        <div class="pro-badge">
                                            <?php esc_html_e('PRO', 'futurio-extra'); ?>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="theme-id-container">

                                    <h2 class="theme-name" id="<?php echo esc_attr($demo); ?>"><span><?php echo ucwords($title); ?></span></h2>

                                    <div class="theme-actions">
                                        <a class="button button-primary" href="https://futuriodemos.com/<?php echo esc_attr($demo); ?>" target="_blank"><?php _e('Live Preview', 'futurio-extra'); ?></a>
                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                </div>

            </div>

        </div>

        <?php
    }

}

new FWP_Install_Demos();
