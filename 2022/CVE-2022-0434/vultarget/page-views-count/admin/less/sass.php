<?php

namespace A3Rev\PageViewsCount\FrameWork {

// File Security Check
if (!defined('ABSPATH'))
    exit;

class Less_Sass
{
    public $plugin_name   = A3_PVC_KEY;
    public $css_file_name = 'pvc';
    public $plugin_folder = A3_PVC_FOLDER;
    public $plugin_dir    = A3_PVC_DIR;
    
    /*-----------------------------------------------------------------------------------*/
    /* Constructor */
    /*-----------------------------------------------------------------------------------*/
    public function __construct()
    {
		add_action( $this->plugin_name.'_after_settings_save_reset', array ($this, 'plugin_build_sass') );
		add_action( 'wp_enqueue_scripts', array ($this, 'apply_style_css_fontend') , 11 );
    }

    public function register_dynamic_style_file()
    {
        $_upload_dir = wp_upload_dir();
        if ( file_exists( $_upload_dir['basedir'] . '/sass/' . $this->css_file_name . '.min.css' ) ) {
            wp_register_style( 'a3' . $this->css_file_name, str_replace( array('http:','https:'), '', $_upload_dir['baseurl'] ) . '/sass/' . $this->css_file_name . '.min.css', array(), $this->get_css_file_version() );

            return true;
        }

        return false;
    }

	public function apply_style_css_fontend()
	{
		if ( $this->register_dynamic_style_file() ) {
            wp_enqueue_style( 'a3' . $this->css_file_name );
        }
	}

	public function plugin_build_sass()
    {
		$sass = $this->sass_content_data();
		$this->plugin_compile_less_mincss( $sass );
        $this->set_css_file_version();
	}
    public function custom_filesystem_method( $method = '') {
        return 'direct';
    }

	public function plugin_compile_less_mincss( $sass, $css_file_name = '' )
    {
        // just filter when compile less file
        add_filter( 'filesystem_method', array( $this, 'custom_filesystem_method' ) );

        $form_url = wp_nonce_url( esc_url( add_query_arg( 'compile-sass', 'true' ) ), 'compile-sass' );

        if ( ! function_exists( 'request_filesystem_credentials' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        if ( false === ( $creds = request_filesystem_credentials( $form_url, '', false, false, null ) ) ) {
            return true;
        }

        if ( ! WP_Filesystem( $creds ) ) {
            // our credentials were no good, ask the user for them again
            request_filesystem_credentials( $form_url, '', true );
            return true;
        }

        global $wp_filesystem;

        $_upload_dir = wp_upload_dir();
        if (! $wp_filesystem->is_dir($_upload_dir['basedir'] . '/sass')) {
            $wp_filesystem->mkdir($_upload_dir['basedir'] . '/sass', 0755);
        }

		if ( trim( $css_file_name ) == '' ) $css_file_name = $this->css_file_name;

        if ( $css_file_name == '' )
            return;

		if ( $this->plugin_folder == '' )
            return;

        $filename = $css_file_name;

        if (!file_exists($_upload_dir['basedir'] . '/sass/' . $filename . '.less')) {
            $wp_filesystem->put_contents($_upload_dir['basedir'] . '/sass/' . $filename . '.less', '', 0644 );
            $wp_filesystem->put_contents($_upload_dir['basedir'] . '/sass/' . $filename . '.css', '', 0644);
            $wp_filesystem->put_contents($_upload_dir['basedir'] . '/sass/' . $filename . '.min.css', '', 0644);
        }

        $mixins = $this->css_file_name . '_mixins';
        if( !file_exists( $_upload_dir['basedir'].'/sass/'.$mixins.'.less' ) ){
            $mixinsless = $this->plugin_dir.'/admin/less/assets/css/mixins.less';
            $a3rev_mixins_less = $_upload_dir['basedir'].'/sass/'.$mixins.'.less';
            $wp_filesystem->copy($mixinsless, $a3rev_mixins_less, true );
        }

        $sass_data = '';

        if ($sass != '') {

            $sass_data = '@import "'.$mixins.'.less";' . "\n";

            $sass_data .= $sass;

            $sass_data = str_replace(':;', ': transparent;', $sass_data);
            $sass_data = str_replace(': ;', ': transparent;', $sass_data);
            $sass_data = str_replace(': !important', ': transparent !important', $sass_data);
            $sass_data = str_replace(':px', ':0px', $sass_data);
            $sass_data = str_replace(': px', ': 0px', $sass_data);

            $less_file = $_upload_dir['basedir'] . '/sass/' . $filename . '.less';
            if (is_writable($less_file)) {

                if (!class_exists('Compile_Less_Sass'))
                    include( dirname( __FILE__ ) . '/compile_less_sass_class.php');
                $wp_filesystem->put_contents($less_file, $sass_data, 0644);
                $css_file     = $_upload_dir['basedir'] . '/sass/' . $filename . '.css';
                $css_min_file = $_upload_dir['basedir'] . '/sass/' . $filename . '.min.css';
                $compile      = new \Compile_Less_Sass;
                $compile->compileLessFile($less_file, $css_file, $css_min_file);
            }
        }
    }

    public function sass_content_data()
    {
		do_action($this->plugin_name . '_get_all_settings');

        ob_start();
		include( $this->plugin_dir. '/includes/customized_style.php' );
		$sass = ob_get_clean();
		$sass = str_replace( '<style>', '', str_replace( '</style>', '', $sass ) );
		$sass = str_replace( '<style type="text/css">', '', str_replace( '</style>', '', $sass ) );

        // Start Less
        $sass_ext = '';

        $sass_ext = apply_filters( $this->plugin_name.'_build_sass', $sass_ext );

        if ($sass_ext != '')
            $sass .= "\n" . $sass_ext;

        return $sass;
    }

    public function set_css_file_version( $css_file_name = '' ) {
        if ( trim( $css_file_name ) == '' ) {
            $css_file_name = $this->css_file_name;
        }

        if ( $css_file_name == '' ) {
            return false;
        }

        update_option( $css_file_name . '_style_version', time() );
    }

    public function get_css_file_version( $css_file_name = '' ) {
        if ( trim( $css_file_name ) == '' ) {
            $css_file_name = $this->css_file_name;
        }

        if ( $css_file_name == '' ) {
            return false;
        }

        $version_number = get_option( $css_file_name . '_style_version', time() );

        return $version_number;
    }
}

}

