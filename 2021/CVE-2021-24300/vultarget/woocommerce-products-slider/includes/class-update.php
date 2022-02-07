<?php



if ( ! defined('ABSPATH')) exit;  // if direct access 	

if( ! class_exists( 'class_wcps_udpate' ) ) {
    class class_wcps_udpate
    {


        public function __construct()
        {

            //add_action( 'admin_notices', array( $this, 'wcps_plugin_version_upgrade_notice' ) );
            add_action('wcps_action_install', array($this, 'wcps_update_2_1_13'));

        }


        function wcps_plugin_version_upgrade_notice()
        {
            $wcps_upgrade = get_option('wcps_upgrade');


            $html = '';

            if ($wcps_upgrade == 'done') {

            } else {
                $html .= '<div class="update-nag">';
                $html .= 'Data update required for <b>Woocommerce Products Slider</b> latest version. <span id="wcps-upgrade">Click to update</span>';
                $html .= '</div>';
            }


            echo $html;
        }


        public function wcps_update_2_1_13()
        {


            $wp_query = new WP_Query(
                array(
                    'post_type' => 'wcps',
                    'post_status' => 'publish',
                    'post_per_page' => -1,

                ));


            if ($wp_query->have_posts()) :
                while ($wp_query->have_posts()) : $wp_query->the_post();

                    $wcps_themes = get_post_meta(get_the_ID(), 'wcps_themes', true);

                    if ($wcps_themes == 'theme12') {

                        $wcps_themes = 'halfthumbright';
                    } elseif ($wcps_themes == 'theme11') {

                        $wcps_themes = 'halfthumbleft';
                    } elseif ($wcps_themes == 'theme10') {

                        $wcps_themes = 'contentbottom';
                    } elseif ($wcps_themes == 'theme9') {

                        $wcps_themes = 'contentinright';
                    } elseif ($wcps_themes == 'theme8') {

                        $wcps_themes = 'contentinleft';
                    } elseif ($wcps_themes == 'theme7') {

                        $wcps_themes = 'contentinbottom';
                    } elseif ($wcps_themes == 'theme6') {

                        $wcps_themes = 'zoomin';
                    } elseif ($wcps_themes == 'theme5') {

                        $wcps_themes = 'flat';
                    } elseif ($wcps_themes == 'theme4') {

                        $wcps_themes = 'flat';
                    } elseif ($wcps_themes == 'theme3') {

                        $wcps_themes = 'zoomin';
                    } elseif ($wcps_themes == 'theme2') {

                        $wcps_themes = 'flat';
                    } elseif ($wcps_themes == 'theme1') {

                        $wcps_themes = 'flat';
                    } else {
                        $wcps_themes = $wcps_themes;
                    }


                    update_post_meta(get_the_ID(), 'wcps_themes', $wcps_themes);


                endwhile;
                wp_reset_query();


            endif;


        }


    }
}
new class_wcps_udpate();