<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Futurio_Extra_Dashboard {
    static $_instance;
    public $title;
    public $config;
    public $current_tab = '';
    public $url = ''; // current page url

    static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
            self::$_instance->url = admin_url( 'admin.php' );
	          self::$_instance->url = add_query_arg( array( 'page' => 'futurio-extra' ), self::$_instance->url );

            self::$_instance->title = esc_html__( 'Futurio Options', 'futurio-extra' );
            add_action( 'admin_menu', array( self::$_instance, 'add_menu' ), 5 );
            add_action( 'admin_enqueue_scripts', array(  self::$_instance, 'scripts' ) );
            add_action( 'futurio/dashboard/main', array(  self::$_instance, 'box_links' ), 10 );
            
            add_action( 'futurio/dashboard/main', array(  self::$_instance, 'pro_notify' ), 14 );
            add_action( 'futurio/dashboard/main', array(  self::$_instance, 'pro_modules_box' ), 15 );
            
            add_action( 'futurio/dashboard/sidebar', array(  self::$_instance, 'box_plugins' ), 10 );
            add_action( 'futurio/dashboard/sidebar', array(  self::$_instance, 'box_recommend_plugins' ), 20 );
            add_action( 'futurio/dashboard/sidebar', array(  self::$_instance, 'box_community' ), 25 );
            add_action( 'admin_bar_menu', array(  self::$_instance, 'admin_bar_button' ), 100 );
            // Tabs
            // add_action( 'futurio/dashboard/tab/changelog', array( self::$_instance, 'tab_changelog' ) );

        }
        return self::$_instance;
    }

    function add_url_args( $args = array() ){
	    return add_query_arg( $args, self::$_instance->url );
    }

    function add_menu(){
        add_theme_page(
            $this->title,
            $this->title,
            'manage_options',
            'futurio',
            array( $this, 'page' )
        );
    }
    function admin_bar_button($wp_admin_bar){
      if (current_user_can('manage_options')) {  
        $args = array(
            'id' => $this->title,
            'title' => 'Futurio Theme',
            'href' => admin_url( 'themes.php?page=futurio' ),
            'meta' => array(
              'class' => 'futurio-admin'
            )
        );
        $wp_admin_bar->add_node($args);
      }
    }

	/**
     * Register scripts
     *
	 * @param $id
	 */
    function scripts($id)
    {
        wp_enqueue_style( 'futurio-extra-notice', plugin_dir_url( __FILE__ ) . 'css/notice.css' );
        if ( $id != 'appearance_page_futurio' && $id != 'themes.php' ) {
            return;
        }
        wp_enqueue_style('futurio-admin', plugin_dir_url( __FILE__ ) . '/css/dashboard.css', false, '');
        if ( $id != 'themes' ) {
            wp_enqueue_style('plugin-install');
            wp_enqueue_script('plugin-install');
            wp_enqueue_script('updates');
            add_thickbox();
        }
    }

    function setup(){
        $theme = wp_get_theme();
        $this->config = array(
            'name' => $theme->get('Name'),
            'theme_uri' => $theme->get('ThemeURI'),
            'desc' => $theme->get('Description'),
            'author' => $theme->get('Author'),
            'author_uri' => $theme->get('AuthorURI'),
            'version' => $theme->get('Version'),
        );

        $this->current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';
    }

    function page(){
        $this->setup();
        $this->page_header();
        echo '<div class="wrap">';
        $cb = apply_filters( 'futurio/dashboard/content_cb',  false );
        if ( ! is_callable( $cb ) ) {
            $cb = array( $this, 'page_inner' );
        }

        if ( is_callable( $cb ) ) {
            call_user_func_array( $cb, array( $this ) );
        }

        echo '</div>';
    }

    public function page_header(){
        ?>
        <div class="cd-header">
            <div class="cd-row">
                <div class="cd-header-inner">
                    <a href="https://futuriowp.com" target="_blank" class="cd-branding">
                        <img src="<?php echo esc_url( get_template_directory_uri() ) .'/img/futurio-logo.png'; ?>" alt="<?php esc_attr_e( 'logo', 'futurio-extra' ); ?>">
                    </a>
                    
                    
                </div>
            </div>
        </div>
        <?php
    }

    function tab_changelog(){
	    global $wp_filesystem;
	    WP_Filesystem();
	    $file = get_template_directory().'/changelog.txt';
	    if ( file_exists( $file ) ) {
		    $file_contents = $wp_filesystem->get_contents( $file );
	    }
        ?>
        <p>
            <a class="button button-secondary" href="<?php echo esc_url( $this->url ); ?>"><?php _e( 'Back', 'futurio-extra' ); ?></a>
        </p>

        <?php
	    do_action( 'futurio/dashboard/changelog/before' );
        ?>
        <div class="cd-box theme-changelog">
            <div class="cd-box-top"><?php _e( 'Changelog', 'futurio-extra' ); ?></div>
            <div class="cd-box-content">
                <pre style="width: 100%; max-height: 60vh; overflow: auto"><?php echo esc_textarea( $file_contents ); ?></pre>
            </div>
        </div>
        <?php
        do_action( 'futurio/dashboard/changelog/after' );

    }

    function box_links(){
        $url = admin_url( 'customize.php' );

        $links = array(
            array(
                'label' => __( 'Logo & Site Identity', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'section' => 'title_tagline' ) ), $url ),
            ),
            array(
                'label' => __( 'Colors & Typography', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'panel' => 'colors' ) ), $url ),
            ),
            array(
                'label' => __( 'Color presets', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'section' => 'presets_colors_section' ) ), $url ),
            ),
            array(
                'label' => __( 'Footer credits', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'section' => 'code_section' ) ), $url ),
            ),
            array(
                'label' => __( 'Global options', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'section' => 'global_section' ) ), $url ),
            ),
            array(
                'label' => __( 'Top bar options', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'section' => 'top_bar' ) ), $url ),
            ),
            array(
                'label' => __( 'Main menu options', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'section' => 'main_menu_icons' ) ), $url ),
            ),
            array(
                'label' => __( 'Posts and pages options', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'section' => 'posts_pages' ) ), $url ),
            ),
            array(
                'label' => __( 'Sidebar options', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'section' => 'main_sidebar' ) ), $url ),
            ),
            array(
                'label' => __( 'Homepage Settings', 'futurio-extra' ),
                'url' => add_query_arg( array( 'autofocus' => array( 'section' => 'static_front_page' ) ), $url ),
            )
        );

        $links = apply_filters( 'futurio/dashboard/links', $links );
        ?>
        <div class="cd-box">
            <div class="cd-box-top"><?php _e( 'Links to Customizer Settings', 'futurio-extra' ); ?></div>
            <div class="cd-box-content">
                <ul class="cd-list-flex">
                    <?php foreach( $links as $l ) { ?>
                        <li class="">
                            <a class="cd-quick-setting-link" href="<?php echo esc_url( $l['url'] ); ?>" target="_blank"><?php echo esc_html( $l['label'] ); ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <?php
    }

	/**
	 * Display documentation info
	 */
    function box_community() {
        ?>
        <div class="cd-box">
            <div class="cd-box-top"><?php esc_html_e( 'Knowledge Base', 'futurio-extra' ); ?></div>
            <div class="cd-box-content">
                <p><?php esc_html_e( 'Not sure how something works? Take a peek at the knowledge base and learn.', 'futurio-extra' ) ?></p>
                <a target="_blank" href="<?php echo esc_url( 'https://futuriowp.com/docs/futurio/' ); ?>"><?php esc_html_e( 'Visit Knowledge Base', 'futurio-extra' ); ?></a>
            </div>
        </div>
        <?php
    }

	/**
	 * Display import sites
	 */
    function box_plugins(){

        ?>
        <div class="cd-box box-plugins">
            <div class="cd-box-top"><?php esc_html_e( 'Futurio ready to import sites', 'futurio-extra' ); ?></div>
            <div class="cd-sites-thumb">
                <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ) . 'img/futurio-sites.png'; ?>">
            </div>
            <div class="cd-box-content">
                <p><?php esc_html_e( 'Import your favorite site with one click and start your project in style!', 'futurio-extra' ) ?></p>
                <p>
                  <a href="<?php echo esc_url( admin_url( 'themes.php?page=futurio-panel-install-demos' ) ); ?>" class="button action-btn view-site-library">
                    <?php esc_html_e( 'See Library', 'futurio-extra' ) ?>
                  </a>
                </p>

            </div>
        </div>
        <?php
    }

    function get_plugin_file( $plugin_slug ) {
        $installed_plugins = get_plugins();
        foreach ( ( array ) $installed_plugins as $plugin_file => $info ) {
            if ( strpos( $plugin_file, $plugin_slug.'/' ) === 0 ) {
                return $plugin_file;
            }
        }
        return false;
    }

    function box_recommend_plugins(){

        $list_plugins = array(
            'elementor',
            'contact-form-7'
        );

        $list_plugins = apply_filters( 'futurio/recommend-plugins', $list_plugins );
        $key = 'futurio_plugins_info_'. wp_hash( json_encode( $list_plugins ) );
        $plugins_info = get_transient( $key );
        if ( false === $plugins_info) {
            $plugins_info =array();
            if ( ! function_exists( 'plugins_api' ) ) {
                require_once  ABSPATH.'/wp-admin/includes/plugin-install.php';
            }
            foreach ( $list_plugins as $slug ) {
                $info = plugins_api( 'plugin_information', array( 'slug' => $slug ) );
                if ( ! is_wp_error( $info ) ){
                    $plugins_info[ $slug ] = $info;
                }
            }
            set_transient( $key, $plugins_info );
        }

        $html  = '';
        foreach ( $plugins_info as $plugin_slug => $info ) {
            $status = is_dir( WP_PLUGIN_DIR . '/' . $plugin_slug );
            $plugin_file = $this->get_plugin_file( $plugin_slug );
            if ( ! is_plugin_active( $plugin_file )  ) {
                $html .= '<div class="cd-list-item">';
                $html .= '<p class="cd-list-name">'.esc_html( $info->name ).'</p>';
                if ($status) {
                    $button_class = 'activate-now'; //
                    $button_txt = esc_html__('Activate', 'futurio-extra');
                    $url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . urlencode($plugin_file), 'activate-plugin_' . $plugin_file);
                } else {
                    $button_class = 'install-now'; //
                    $button_txt = esc_html__('Install Now', 'futurio-extra');
                    $url = wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'install-plugin',
                                'plugin' => $plugin_slug
                            ),
                            network_admin_url('update.php')
                        ),
                        'install-plugin_' . $plugin_slug
                    );
                }

                $detail_link = add_query_arg(
                    array(
                        'tab'       => 'plugin-information',
                        'plugin'    => $plugin_slug,
                        'TB_iframe' => 'true',
                        'width'     => '772',
                        'height'    => '349',
                    ),
                    network_admin_url('plugin-install.php')
                );

                $class = 'action-btn plugin-card-' . $plugin_slug;

                $html .= '<div class="rcp">';
                $html .= '<p class="' . esc_attr($class) . '"><a href="' . esc_url($url) . '" data-slug="' . esc_attr($plugin_slug) . '" class="' . esc_attr($button_class) . '">' . $button_txt . '</a></p>';
                $html .= '<a class="plugin-detail thickbox open-plugin-details-modal" href="' . esc_url($detail_link) . '">' . esc_html__('Details', 'futurio-extra') . '</a>';
                $html .= '</div>';

                $html .= '</div>';
            }
        } // end foreach

        if ( $html ) {
            ?>
            <div class="cd-box">
                <div class="cd-box-top"><?php _e('Recommend Plugins', 'futurio-extra'); ?></div>
                <div class="cd-box-content cd-list-border">
                    <?php
                        echo $html; // WPCS: XSS OK.
                    ?>
                </div>
            </div>
            <?php
        }
    }

    function pro_notify(){
    
      if ( defined('FUTURIO_PRO_CURRENT_VERSION') ) return;  // hide if PRO version activated
      ?>
      <div class="cd-box">
        <?php futurio_pro_notice_message(); ?>
      </div>
      <?php
    }

    function pro_modules_box(){
    
        if ( defined('FUTURIO_PRO_CURRENT_VERSION') ) return;  // hide if PRO version activated
        
        $modules = array(
            array(
                'name' => __( '800+ Google fonts', 'futurio-extra' ),
                'desc' => __( 'Integrates more than 800 google fonts.', 'futurio-extra' ),
                'url' => '',
            ),
            array(
                'name' => __( 'Multiple color options', 'futurio-extra' ),
                'desc' => __( 'Allows you to easily change the color or background color of almost each and every element of your site.', 'futurio-extra' ),
                'url' => '',
            ),
            array(
                'name' => __( 'Ultimate Addons for Elementor', 'futurio-extra' ),
                'desc' => __( 'A library of unique Elementor Widgets included.', 'futurio-extra' ),
                'url' => '',
            ),
            array(
                'name' => __( 'Sticky sidebar', 'futurio-extra' ),
                'desc' => __( 'Make your sidebar permanently visible while scrolling.', 'futurio-extra' ),
                'url' => '',
            ),
            array(
                'name' => __( 'Scroll To Top', 'futurio-extra' ),
                'desc' => __( 'Get a better user experience with a scroll to top button with beautiful animation.', 'futurio-extra' ),
                'url' => '',
            ),
            array(
                'name' => __( 'Infinite scroll & ajax posts loading', 'futurio-extra' ),
                'desc' => __( 'Ajax loading loads the next set of post without page reloading.', 'futurio-extra' ),
                'url' => '',
            ),
            array(
                'name' => __( 'Custom content width', 'futurio-extra' ),
                'desc' => __( 'Allows you to set your maximum width of your website content.', 'futurio-extra' ),
                'url' => '',
            ),
            array(
                'name' => __( 'Fixed menu', 'futurio-extra' ),
                'desc' => __( 'Do not like the floating menu? Does not matter. You can unstick it and keep it fixed.', 'futurio-extra' ),
                'url' => '',
            ),
            array(
                'name' => __( 'Transparent menu', 'futurio-extra' ),
                'desc' => __( 'Make your menu transparent on posts or pages.', 'futurio-extra' ),
                'url' => '',
            ),
            array(
                'name' => __( 'And much more...', 'futurio-extra' ),
                'url' => '',
            ),

	        array(
		        'name' => __( 'WooCommerce Booster', 'futurio-extra' ),
		        'desc' => __( 'Gives you creative control of style and layout options for your shop.', 'futurio-extra' ),
		        'url'  => ''
	        ),

                array(
                    'name' => __( 'Google fonts & custom colors', 'futurio-extra' ),
                    'desc' => __( 'Integrates more than 800 google fonts and allows you change the color of almost each WooCommerce element.', 'futurio-extra' ),
                    'url'  => '',
                    'sub' => true,
                ),
                array(
                    'name' => __( 'Quick View, Compare, Wishlist support', 'futurio-extra' ),
                    'desc' => __( 'Integrated support for 3 major and popular WooCommerce extensions from Yith.', 'futurio-extra' ),
                    'url'  => '',
                    'sub' => true,
                ),
                array(
                    'name' => __( 'Floating add to cart bar', 'futurio-extra' ),
                    'desc' => __( 'The floating add to cart bar ensures that your add to cart button is always visible.', 'futurio-extra' ),
                    'url'  => '',
                    'sub' => true,
                ),
                array(
                    'name' => __( 'Gallery images on shop pages', 'futurio-extra' ),
                    'desc' => __( 'Show your customers additional images from the product gallery and increase the interest of your products.', 'futurio-extra' ),
                    'url'  => '',
                    'sub' => true,
                ),
                array(
                    'name' => __( 'Popup cart', 'futurio-extra' ),
                    'desc' => __( 'Auto open lightbox popup cart when click Add to cart button. ', 'futurio-extra' ),
                    'url'  => '',
                    'sub' => true,
                ),
                array(
                    'name' => __( 'Single product ajax add to cart', 'futurio-extra' ),
                    'desc' => __( 'Customers dont have to wait for the page to refresh on single product page. Product is added in the cart without page refresh.', 'futurio-extra' ),
                    'url'  => '',
                    'sub' => true,
                ),
                array(
                    'name' => __( 'Image Flipper', 'futurio-extra' ),
                    'desc' => __( 'Feature that adds a secondary product thumbnail on product archives that is displayed when you hover over the main product image', 'futurio-extra' ),
                    'url'  => '',
                    'sub' => true,
                ),
                array(
                    'name' => __( 'And much more...', 'futurio-extra' ),
                    'url' => '',
                    'sub' => true,
                ),

        );

        ?>
        <div class="cd-box">
            <div class="cd-box-top"><?php esc_html_e( 'Futurio PRO addon', 'futurio-extra' ); ?>
                <a class="cd-upgrade" target="_blank" href="https://futuriowp.com/futurio-pro/"><?php esc_html_e( 'Upgrade Now &rarr;', 'futurio-extra' ); ?></a></div>
            <div class="cd-box-content cd-modules">
                <?php foreach( $modules as $m ) { ?>
                <div class="cd-module-item <?php echo isset( $m['sub'] ) && $m['sub'] ? 'cd-sub-module' : ''; ?>">
                    <div class="cd-module-info">
                        <div class="cd-module-name"><?php echo esc_html( $m['name'] ); ?></div>
                        <?php if ( isset( $m['desc'] ) ) { ?>
                        <div class="cd-module-desc"><?php echo esc_html( $m['desc'] ); ?></div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php
    }

    private function page_inner(){
        ?>
        <div id="plugin-filter" class="cd-row metabox-holder">
            <hr class="wp-header-end">
            <?php

            do_action( 'futurio/dashboard/start', $this );

            if ( $this->current_tab && has_action( 'futurio/dashboard/tab/'.$this->current_tab ) ){
                do_action( 'futurio/dashboard/tab/'.$this->current_tab, $this );
            } else {
	            ?>
                <div class="cd-main">
		            <?php do_action( 'futurio/dashboard/main', $this ); ?>
                </div>
                <div class="cd-sidebar">
		            <?php do_action( 'futurio/dashboard/sidebar', $this ); ?>
                </div>
	            <?php
            }

            do_action( 'futurio/dashboard/end', $this );

            ?>
        </div>
    <?php
    }

}

Futurio_Extra_Dashboard::get_instance();
