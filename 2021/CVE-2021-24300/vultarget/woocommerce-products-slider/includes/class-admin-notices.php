<?php
if ( ! defined('ABSPATH')) exit; // if direct access 

class class_wcps_notices{

    public function __construct(){
        //add_action('admin_notices', array( $this, 'woocommerce_plugin_missing' ));
        //add_action('admin_notices', array( $this, 'data_upgrade' ));

    }



    public function data_upgrade(){

        if (!is_plugin_active('woocommerce-products-slider/woocommerce-products-slider.php')) return;

        //delete_option('wcps_plugin_info');

        $wcps_plugin_info = get_option('wcps_plugin_info');
        $wcps_upgrade = isset($wcps_plugin_info['wcps_upgrade']) ? $wcps_plugin_info['wcps_upgrade'] : '';


        $actionurl = admin_url().'edit.php?post_type=wcps&page=upgrade_status';
        $actionurl = wp_nonce_url( $actionurl,  'wcps_upgrade' );

        $nonce = isset($_REQUEST['_wpnonce']) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';

        if ( wp_verify_nonce( $nonce, 'wcps_upgrade' )  ){
            $wcps_plugin_info['wcps_upgrade'] = 'processing';
            update_option('wcps_plugin_info', $wcps_plugin_info);
            wp_schedule_event(time(), '1minute', 'wcps_cron_upgrade_settings');

            return;
        }


        if(empty($wcps_upgrade)){

            ?>
            <div class="update-nag">
                <?php
                echo sprintf(__('Data migration required for <b>PickPlugins Product Slider</b> plugin, please <a class="button button-primary" href="%s">click to start</a> migration. Watch this <a target="_blank" href="https://youtu.be/kn3skEwh5t4">video</a>  first', 'woocommerce-products-slider'), $actionurl);
                ?>
            </div>
            <?php


        }

    }



    public function woocommerce_plugin_missing(){

        $active_plugins = get_option('active_plugins');

        ob_start();

        if(!in_array( 'woocommerce/woocommerce.php', (array) $active_plugins )):
            ?>
            <div class="update-nag">
                <?php
                echo sprintf(__('<a href="%s">WooCommerce</a> plugin is required to run <b>PickPlugins Product Slider</b>', 'woocommerce-products-slider'), 'https://wordpress.org/plugins/woocommerce/')
                ?>
            </div>
        <?php

//            if (is_plugin_active('woocommerce-products-slider/woocommerce-products-slider.php')) {
//                deactivate_plugins('woocommerce-products-slider/woocommerce-products-slider.php');
//                flush_rewrite_rules();
//            }

        endif;




        echo ob_get_clean();
    }


}

new class_wcps_notices();