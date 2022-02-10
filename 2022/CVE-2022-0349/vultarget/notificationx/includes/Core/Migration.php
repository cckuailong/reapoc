<?php

/**
 * Extension Factory
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Core;

use NotificationX\Admin\Cron;
use NotificationX\Admin\Settings;
use NotificationX\Extensions\ConvertKit\ConvertKit;
use NotificationX\Extensions\CustomNotification\CustomNotification;
use NotificationX\Extensions\CustomNotification\CustomNotificationConversions;
use NotificationX\Extensions\Envato\Envato;
use NotificationX\Extensions\ExtensionFactory;
use NotificationX\Extensions\Freemius\FreemiusConversions;
use NotificationX\Extensions\Freemius\FreemiusReviews;
use NotificationX\Extensions\Freemius\FreemiusStats;
use NotificationX\Extensions\Google_Analytics\Google_Analytics;
use NotificationX\Extensions\MailChimp\MailChimp;
use NotificationX\Extensions\WooCommerce\WooCommerce as WooCommerce;
use NotificationX\Extensions\WordPress\WPOrgReview;
use NotificationX\Extensions\WordPress\WPOrgStats;
use NotificationX\GetInstance;
use NotificationX\Types\Conversions;
use NotificationXPro\Feature\SalesFeatures;

/**
 * ExtensionFactory Class
 */
class Migration {
    /**
     * Instance of Migration
     *
     * @var Migration
     */
    use GetInstance;

    private $stats = [];

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        add_action('plugins_loaded', [$this, 'plugins_loaded']);
    }

    public function plugins_loaded() {
        // @todo check version.
        global $wpdb;
        $wpdb->show_errors();
        $this->migrate_options();
        $posts = $this->migrate_posts();
        $this->migrate_entries($posts);

        // delete options
        // @todo uncomment
        // delete_option('notificationx_data');
        // delete_option('notificationx_settings');
    }

    public function migrate_options() {
        $settings = get_option('notificationx_settings', []);
        if($settings){
            if(!empty($settings['nx_modules'])){
                $settings['modules'] = $settings['nx_modules'];
                unset($settings['nx_modules']);
            }
            $settings['is_migrated'] = true;
            Settings::get_instance()->set('settings', $settings);

            $ga_option_key = Google_Analytics::get_instance()->option_key;
            $pa_options = get_option($ga_option_key, []);
            if($pa_options){
                Settings::get_instance()->set("settings.{$ga_option_key}", $pa_options);
            }
            if(!empty($settings['ga_profile'])){
                $ga_profile = $settings['ga_profile'];
                Settings::get_instance()->set("settings.ga_profile", $ga_profile);
            }


            $convertkit_api_key = get_option( 'nxpro_convertkit_api_key', '' );
            $convertkit_api_secret = get_option( 'nxpro_convertkit_api_secret', '' );
            Settings::get_instance()->set('settings.convertkit_api_key', $convertkit_api_key);
            Settings::get_instance()->set('settings.convertkit_api_secret', $convertkit_api_secret);

        }

        // @todo move to ReportEmail.php
        $nx_daily   = get_option("nx_daily_mail_sent", false);
        $nx_weekly  = get_option("nx_weekly_mail_sent", false);
        $nx_monthly = get_option("nx_monthly_mail_sent", false);
        Settings::get_instance()->set("reporting", [
            'mail_sent' => [
                'daily'    => $nx_daily,
                'weekly'   => $nx_weekly,
                'monthly'  => $nx_monthly,
            ]
        ]);
    }

    public function migrate_posts() {
        global $wpdb;
        $posts = [];
        $post_meta = [];
        $query = "SELECT * FROM $wpdb->posts WHERE post_type = 'notificationx'"; // AND ID = $id
        $_posts = $wpdb->get_results($query, ARRAY_A);
        // $nx_ids = array_column($_posts, 'ID');

        if(empty($_posts)) return false;

        // $nx_ids = implode(", ", $nx_ids);
        // $main_query = "SELECT * FROM $wpdb->postmeta WHERE post_id IN ( $nx_ids )";
        // $_post_meta = $wpdb->get_results($main_query, ARRAY_A);

        // foreach ($_post_meta as $meta) {
        //     $pid = $meta['post_id'];
        //     $key = $meta['meta_key'];
        //     $_value = maybe_unserialize($meta['meta_value']);
        //     if ($key == '_nx_meta' && is_array($_value)) {
        //         foreach ($_value as $key => $value) {
        //             $post_meta[$pid]["_nx_meta_$key"] = $value;
        //         }
        //     } else {
        //         if( $key === "_nx_meta_impression_per_day" ) {
        //             $post_meta[$pid][$key][] = $_value;
        //         } else {
        //             $post_meta[$pid][$key] = $_value;
        //         }
        //     }
        // }
        // wp_send_json($post_meta);

        foreach ($_posts as $key => $post) {
            if ($post['post_type'] != 'notificationx' || $post['post_status'] == 'auto-draft' || $post['post_status'] == 'draft') { // && $post['post_status'] == 'trash'
                continue;
            }

            $pid = $post['ID'];

            $is_exist = PostType::get_instance()->get_col('nx_id', ['nx_id' => $pid]);

            if (empty($is_exist)) {
                try {
                    $post_meta = $this->get_normalize_meta( $pid );
                    if(!empty($post_meta)){
                        // we need to do create post first so that we can save entries.
                        $nx_id = PostType::get_instance()->insert_post([
                            'nx_id' => $post['ID'],
                            'title' => $post['post_title'],
                        ]);
                        $post = array_merge($post, $post_meta, ['nx_id' => $nx_id]);
                        $data = $this->migrate_post($post);
                        $posts[$pid] = [ 'source' => $data['source'] ];
                        $this->migrate_stats($post);
                        $post_date      = (!empty($post['post_date_gmt']) && $post['post_date_gmt'] != '0000-00-00 00:00:00' ) ? $post['post_date_gmt'] : get_gmt_from_date($post['post_date']);
                        $post_modified  = (!empty($post['post_modified_gmt']) && $post['post_modified_gmt'] != '0000-00-00 00:00:00' ) ? $post['post_modified_gmt'] : get_gmt_from_date($post['post_modified']);
                        $status = !empty($data['enabled']) ? $data['enabled'] : false;
                        if($post['post_status'] == 'trash'){
                            $status = false;
                            $data['trash'] = true;
                            // $post['post_title'] = "Trash > " . $post['post_title'];
                        }

                        PostType::get_instance()->update_post([
                            'nx_id'        => $data['nx_id'],
                            'type'         => $data['type'],
                            'source'       => $data['source'],
                            'theme'        => $data['themes'],
                            'global_queue' => !empty($data['global_queue']) ? $data['global_queue'] : false,
                            'enabled'      => $status,
                            'title'        => $post['post_title'],
                            'data'         => $data,
                            'created_at'   => $post_date,
                            'updated_at'   => $post_modified,
                        ], $nx_id);
                    }

                } catch (\Exception $e) {
                    //throw $th;
                }
            }
        }

        // delete post & meta;
        // @todo uncomment
        // $query = "DELETE FROM $wpdb->posts WHERE ID in ($nx_ids)"; // AND ID = $id
        // $wpdb->query($query);
        // $query = "DELETE FROM $wpdb->postmeta WHERE post_id in ($nx_ids)"; // AND ID = $id
        // $wpdb->query($query);

        return $posts;
    }

    public function get_normalize_meta( $id ){
        $metas = get_post_meta( $id );
        $_metas = [];
        if( ! empty( $metas ) && is_array( $metas ) ) {
            array_walk( $metas, function( $value, $key ) use ( &$_metas ) {
                $_value = maybe_unserialize( $value[0] );
                if ($key == '_nx_meta' && is_array($_value)) {
                    foreach ($_value as $_key => $value) {
                        $_metas["_nx_meta_$_key"] = $value;
                    }
                } else {
                    if( $key === "_nx_meta_impression_per_day" ) {
                        $_metas[$key][] = $_value;
                    } else {
                        $_metas[$key] = $_value;
                    }
                }
            });
        }

        return $_metas;
    }

    public function migrate_post($_post) {
        $post               = [];
        // get() function use $this->_post
        $this->_post        = $_post;
        $nx_id              = $this->get('ID');

        $post['id']         = $this->get('ID');
        $post['nx_id']      = $this->get('ID');
        $post['title']      = $this->get('post_title');
        $post['type']       = $this->get('_nx_meta_display_type');
        $post['currentTab'] = $this->get('_nx_builder_current_tab');
        $post['enabled']    = $this->get('_nx_meta_active_check');
        $post['is_migrated'] = true;

        $post['utm_campaign']            = $this->get("_nx_meta_utm_campaign");
        $post['utm_medium']              = $this->get("_nx_meta_utm_medium");
        $post['utm_source']              = $this->get("_nx_meta_utm_source");
        $post['utm_source']              = $this->get("_nx_meta_utm_source");

        // Display
        $post['show_on']         = $this->get("_nx_meta_show_on");
        $post['all_locations']   = $this->get("_nx_meta_all_locations");
        $post['show_on_display'] = $this->get("_nx_meta_show_on_display");

        $post['notification-template'] = [];

        // Customize
        $post['position']       = $this->get("_nx_meta_conversion_position");
        $post['size']           = $this->get("_nx_meta_conversion_size");
        $post['close_button']   = (bool) $this->get("_nx_meta_close_button");
        $post['hide_on_mobile'] = (bool) $this->get("_nx_meta_hide_on_mobile");
        $post['global_queue']   = (bool) $this->get("_nx_meta_global_queue_active");
        $post['delay_before']   = $this->get("_nx_meta_delay_before");
        $post['initial_delay']  = $this->get("_nx_meta_initial_delay");
        $post['auto_hide']      = $this->get("_nx_meta_auto_hide");
        $post['hide_after']     = $this->get("_nx_meta_hide_after");
        $post['display_for']    = $this->get("_nx_meta_display_for");
        $post['delay_between']  = $this->get("_nx_meta_delay_between");
        $post['display_last']   = $this->get("_nx_meta_display_last");
        $post['display_from']   = $this->get("_nx_meta_display_from");
        $post['loop']           = (bool) $this->get("_nx_meta_loop");
        $post['link_open']      = (bool) $this->get("_nx_meta_link_open");
        $post['custom_ids']     = $this->get("_nx_meta_custom_ids");
        if ($this->get("_nx_meta_sound_checkbox")) {
            $post['volume']     = $this->get("_nx_meta_volume") * 100;
        }

        // Display Tab
        $post['show_notification_image'] = $this->get("_nx_meta_show_notification_image");
        $post['show_default_image']      = (bool) $this->get("_nx_meta_show_default_image");
        $post['default_avatar']          = $this->get("_nx_meta_default_avatar");
        $post['image_url']               = $this->get("_nx_meta_image_url");


        switch ($post['type']) {
            case 'conversions':
                // Source Tab
                $post['source']                  = $this->get("_nx_meta_conversion_from");

                // Theme Tab
                $post['themes']                  = $this->get("_nx_meta_theme");
                $post['advance_edit']            = $this->get("_nx_meta_advance_edit");
                $post['bg_color']                = $this->get("_nx_meta_bg_color");
                $post['text_color']              = $this->get("_nx_meta_text_color");
                $post['border']                  = $this->get("_nx_meta_border");
                $post['border_size']             = $this->get("_nx_meta_border_size");
                $post['border_style']            = $this->get("_nx_meta_border_style");
                $post['border_color']            = $this->get("_nx_meta_border_color");
                $post['image_shape']             = $this->get("_nx_meta_image_shape");
                $post['image_position']          = $this->get("_nx_meta_image_position");
                $post['custom_image_shape']      = $this->get("_nx_meta_image_custom_shape");
                $post['first_font_size']         = $this->get("_nx_meta_first_font_size");
                $post['second_font_size']        = $this->get("_nx_meta_second_font_size");
                $post['third_font_size']         = $this->get("_nx_meta_third_font_size");

                // Content Tab
                $post['notification-template']   = $this->get("_nx_meta_woo_template_new");
                if($post['template_adv'] = $this->get("_nx_meta_woo_template_adv")){
                    $post['advanced_template']   = $this->get("_nx_meta_woo_template");
                }

                $post['combine_multiorder']      = $this->get("_nx_meta_combine_multiorder");
                $post['combine_multiorder_text'] = $this->get("_nx_meta_combine_multiorder_text");
                $post['random_order']            = $this->get("_nx_meta_random_order");

                $post['link_type']          = $this->get("_nx_meta_conversion_url");
                $post['custom_url']  = $this->get("_nx_meta_conversions_custom_url");

                $post['sound']   = $this->get("_nx_meta_conversions_sound");


                switch ($post['source']) {
                    case 'woocommerce':
                        $post['product_control']         = $this->get("_nx_meta_product_control");
                        $post['category_list']           = $this->get("_nx_meta_category_list");
                        $post['product_list']            = $this->get("_nx_meta_product_list");
                        $post['product_exclude_by']      = $this->get("_nx_meta_product_exclude_by");
                        $post['exclude_categories']      = $this->get("_nx_meta_exclude_categories");
                        $post['exclude_products']        = $this->get("_nx_meta_exclude_products");
                        break;
                    case 'edd':
                        $post['product_control']         = $this->get("_nx_meta_edd_product_control");
                        $post['category_list']           = $this->get("_nx_meta_edd_category_list");
                        $post['product_list']            = $this->get("_nx_meta_edd_product_list");
                        $post['product_exclude_by']      = $this->get("_nx_meta_edd_product_exclude_by");
                        $post['exclude_categories']      = $this->get("_nx_meta_edd_exclude_categories");
                        $post['exclude_products']        = $this->get("_nx_meta_edd_exclude_products");
                        break;
                    case 'freemius':
                        if( boolval( $post['enabled'] ) ) {
                            Cron::get_instance()->set_cron($nx_id, 'nx_freemius_interval');
                        }
                        $post['source']             = 'freemius_conversions';
                        $post['freemius_item_type'] = $this->get("_nx_meta_freemius_item_type");
                        $post['freemius_themes']    = $this->get("_nx_meta_freemius_themes");
                        $post['freemius_plugins']   = $this->get("_nx_meta_freemius_plugins");

                        $sales    = $this->get("_nx_meta_freemius_content");
                        $freemius = FreemiusConversions::get_instance();
                        if(is_array($sales)){
                            foreach ($sales as $key => $sale) {
                                $sales[$key] = [
                                    'nx_id'      => $_post['nx_id'],
                                    'source'     => $freemius->id,
                                    'entry_key'  => $sale['id'],
                                    'data'       => $sale,
                                ];
                            }
                            $freemius->update_notifications($sales);
                        }

                        break;
                    case 'zapier':
                        $post['source'] = 'zapier_conversions';
                        break;
                    case 'envato':
                        if( boolval( $post['enabled'] ) ) {
                            Cron::get_instance()->set_cron($nx_id, 'nx_envato_interval');
                        }
                        $sales  = $this->get('_nx_meta_envato_content');
                        $envato = Envato::get_instance();
                        if(is_array($sales)){
                            foreach ($sales as $key => $sale) {
                                $sales[$key] = [
                                    'nx_id'      => $_post['nx_id'],
                                    'source'     => $envato->id,
                                    'entry_key'  => $sale['id'],
                                    'data'       => $sale,
                                ];
                            }
                            $envato->update_notifications($sales);
                        }

                        break;
                    case 'custom_notification':
                        // $post['themes'] = $this->get("_nx_meta_theme");
                        $post['sound'] = $this->get("_nx_meta_custom_sound");
                        $custom_notification = CustomNotificationConversions::get_instance();
                        $post['source'] = $custom_notification->id;
                        $post['custom_contents']  = $this->get('_nx_meta_custom_contents');
                        if(is_array($post['custom_contents'])){
                            foreach($post['custom_contents'] as &$entry){
                                if((empty($entry['name']) || $entry['name'] == __('Someone', 'notificationx')) && isset($entry['first_name']) || isset($entry['last_name'])){
                                    $entry['name'] = Helper::name($entry['first_name'], $entry['last_name']);
                                }
                                if(!empty($entry['name']) && empty($entry['first_name']) && empty($entry['last_name'])){
                                    $entry['first_name'] = $entry['name'];
                                }
                            }
                        }
                        break;
                    default:
                        # code...
                        break;
                }

                break;
            case 'comments':
                // Source Tab
                $post['source']       = $this->get('_nx_meta_comments_source');
                $post['themes']        = $this->get('_nx_meta_comment_theme');
                $post['advance_edit'] = $this->get('_nx_meta_comment_advance_edit');


                // Theme Tab
                $post['bg_color']           = $this->get('_nx_meta_comment_bg_color');
                $post['text_color']         = $this->get('_nx_meta_comment_text_color');
                $post['border']             = $this->get('_nx_meta_comment_border');
                $post['border_size']        = $this->get('_nx_meta_comment_border_size');
                $post['border_style']       = $this->get('_nx_meta_comment_border_style');
                $post['border_color']       = $this->get('_nx_meta_comment_border_color');
                $post['image_shape']        = $this->get("_nx_meta_comment_image_shape");
                $post['image_position']     = $this->get("_nx_meta_comment_image_position");
                $post['custom_image_shape'] = $this->get("_nx_meta_comment_image_custom_shape");
                $post['first_font_size']    = $this->get("_nx_meta_comment_first_font_size");
                $post['second_font_size']   = $this->get("_nx_meta_comment_second_font_size");
                $post['third_font_size']    = $this->get("_nx_meta_comment_third_font_size");

                // Content Tab
                $post['notification-template'] = $this->get("_nx_meta_comments_template_new");
                if($post['template_adv'] = $this->get("_nx_meta_comments_template_adv")){
                    $post['advanced_template']  = $this->get("_nx_meta_comments_template");
                }

                $post['content_trim_length'] = $this->get('_nx_meta_content_trim_length');
                $post['link_type']           = $this->get("_nx_meta_comments_url");
                $post['custom_url']          = $this->get("_nx_meta_comments_custom_url");
                $show_avatar                 = $this->get("_nx_meta_show_avatar");
                if($show_avatar){
                    $post['show_notification_image'] = 'gravatar';
                }

                $post['sound']              = $this->get("_nx_meta_comments_sound");

                switch ($post['source']) {
                    case 'wp_comments':
                        # code...
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'reviews':
                // Source Tab
                $post['source'] = $this->get('_nx_meta_reviews_source');
                $post['themes'] = $this->get('_nx_meta_wporg_theme');
                $post['advance_edit'] = $this->get('_nx_meta_wporg_advance_edit');

                // Theme Tab
                $post['bg_color']         = $this->get('_nx_meta_wporg_bg_color');
                $post['text_color']       = $this->get('_nx_meta_wporg_text_color');
                $post['border']           = $this->get('_nx_meta_wporg_border');
                $post['border_size']      = $this->get('_nx_meta_wporg_border_size');
                $post['border_style']     = $this->get('_nx_meta_wporg_border_style');
                $post['border_color']     = $this->get('_nx_meta_wporg_border_color');
                $post['image_shape']      = $this->get("_nx_meta_wporg_image_shape");
                $post['image_position']   = $this->get("_nx_meta_wporg_image_position");
                $post['first_font_size']  = $this->get("_nx_meta_wporg_first_font_size");
                $post['second_font_size'] = $this->get("_nx_meta_wporg_second_font_size");
                $post['third_font_size']  = $this->get("_nx_meta_wporg_third_font_size");

                // Content Tab
                $post['wp_reviews_product_type'] = $this->get("_nx_meta_wp_reviews_product_type");
                $post['wp_reviews_slug']         = $this->get("_nx_meta_wp_reviews_slug");
                if ($post['themes'] == 'review_saying') {
                    $post['notification-template'] = $this->get("_nx_meta_review_saying_template_new");
                } else {
                    $post['notification-template'] = $this->get("_nx_meta_wp_reviews_template_new");
                }
                if($post['template_adv'] = $this->get("_nx_meta_wp_reviews_template_adv")){
                    $post['advanced_template']  = $this->get("_nx_meta_wp_reviews_template");
                }

                $post['content_trim_length'] = $this->get('_nx_meta_content_trim_length');
                $post['link_type']          = $this->get("_nx_meta_rs_url");
                $post['custom_url']         = $this->get("_nx_meta_rs_custom_url");

                $post['sound']              = $this->get("_nx_meta_reviews_sound");

                switch ($post['source']) {
                    case 'wp_reviews':
                        if( boolval( $post['enabled'] ) ) {
                            Cron::get_instance()->set_cron($nx_id, 'nx_wp_review_interval');
                        }

                        $plugin_data = $this->get('_nx_meta_wporg_review_content');
                        if (!empty($plugin_data['reviews'])) {
                            $reviews = $plugin_data['reviews'];
                            unset($plugin_data['reviews']);
                            $WPR = WPOrgReview::get_instance();
                            if(is_array($reviews)){
                                foreach ($reviews as $key => $review) {
                                    $reviews[$key] = [
                                        'nx_id'      => $_post['nx_id'],
                                        'source'     => $WPR->id,
                                        'entry_key'  => $review['username'],
                                        'data'       => array_merge($review, $plugin_data),
                                    ];
                                }
                                $WPR->update_notifications($reviews);
                            }
                        }

                        break;
                    case 'woo_reviews':
                        if(!empty($post['notification-template']) && is_array($post['notification-template'])){
                            foreach ($post['notification-template'] as $key => &$value) {
                                if($key == 'third_param' && $value == 'tag_plugin_name'){
                                    $value = 'tag_product_title';
                                }
                            }
                        }
                        if(!empty($post['advanced_template']) && is_array($post['advanced_template'])){
                            foreach ($post['advanced_template'] as $key => &$value) {
                                $value = str_replace('{{plugin_name}}', '{{product_title}} ', $value);
                            }
                        }
                        # code...
                        break;
                    case 'reviewx':
                        # code...
                        break;
                    case 'freemius':
                        if( boolval( $post['enabled'] ) ) {
                            Cron::get_instance()->set_cron($nx_id, 'nx_freemius_interval');
                        }
                        // Content Tab
                        $post['source']             = 'freemius_reviews';
                        $post['freemius_item_type'] = $this->get("_nx_meta_freemius_item_type");
                        $post['freemius_themes']    = $this->get("_nx_meta_freemius_themes");
                        $post['freemius_plugins']   = $this->get("_nx_meta_freemius_plugins");


                        $sales    = $this->get("_nx_meta_freemius_content");
                        $freemius = FreemiusReviews::get_instance();
                        if(is_array($sales)){
                            foreach ($sales as $key => $sale) {
                                $sales[$key] = [
                                    'nx_id'      => $_post['nx_id'],
                                    'source'     => $freemius->id,
                                    'entry_key'  => $sale['id'],
                                    'data'       => $sale,
                                ];
                            }
                            $freemius->update_notifications($sales);
                        }

                        break;
                    case 'zapier':
                        $post['source'] = 'zapier_reviews';
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'download_stats':
                // Source Tab
                $post['source']       = $this->get('_nx_meta_stats_source');
                $post['themes']       = $this->get('_nx_meta_wpstats_theme');
                $post['advance_edit'] = $this->get('_nx_meta_wpstats_advance_edit');



                // Theme Tab
                $post['bg_color']         = $this->get('_nx_meta_wpstats_bg_color');
                $post['text_color']       = $this->get('_nx_meta_wpstats_text_color');
                $post['border']           = $this->get('_nx_meta_wpstats_border');
                $post['border_size']      = $this->get('_nx_meta_wpstats_border_size');
                $post['border_style']     = $this->get('_nx_meta_wpstats_border_style');
                $post['border_color']     = $this->get('_nx_meta_wpstats_border_color');
                $post['image_position']   = $this->get("_nx_meta_wpstats_image_position");
                $post['first_font_size']  = $this->get("_nx_meta_wpstats_first_font_size");
                $post['second_font_size'] = $this->get("_nx_meta_wpstats_second_font_size");
                $post['third_font_size']  = $this->get("_nx_meta_wpstats_third_font_size");

                // Content Tab
                $post['wp_stats_product_type']              = $this->get("_nx_meta_wp_stats_product_type");
                $post['wp_stats_slug']                      = $this->get("_nx_meta_wp_stats_slug");
                //
                if ($post['themes'] == 'actively_using') {
                    $post['notification-template']              = $this->get("_nx_meta_actively_using_template_new");
                    if(!empty($post['notification-template']['third_param']) && $post['notification-template']['third_param'] == 'tag_name'){
                        $post['notification-template']['third_param'] = 'tag_plugin_theme_name';
                    }
                } else {
                    $post['notification-template']              = $this->get("_nx_meta_wp_stats_template_new");
                    if(!empty($post['notification-template']['first_param']) && $post['notification-template']['first_param'] == 'tag_name'){
                        $post['notification-template']['first_param'] = 'tag_plugin_theme_name';
                    }
                }
                if($post['template_adv'] = $this->get("_nx_meta_wp_stats_template_adv")){
                    $post['advanced_template']  = $this->get("_nx_meta_wp_stats_template");
                    if(!empty($post['advanced_template']) && is_array($post['advanced_template'])){
                        foreach ($post['advanced_template'] as $key => &$value) {
                            $value = str_replace('{{name}}', '{{plugin_theme_name}} ', $value);
                        }
                    }
                }

                $post['link_type']          = $this->get("_nx_meta_rs_url");
                if($post['link_type'] == 'product_page'){
                    $post['link_type'] = 'stats_page';
                }
                $post['custom_url']         = $this->get("_nx_meta_rs_custom_url");
                $post['sound']              = $this->get("_nx_meta_download_stats_sound");

                switch ($post['source']) {
                    case 'wp_stats':
                        if( boolval( $post['enabled'] ) ) {
                            Cron::get_instance()->set_cron($nx_id, 'nx_wp_stats_interval');
                        }
                        $plugin_data = $this->get('_nx_meta_wporg_stats_content');
                        if (!empty($plugin_data)) {
                            $WPS = WPOrgStats::get_instance();
                            $reviews = [];
                            if(is_array($plugin_data)){
                                foreach ($plugin_data as $key => $stats) {
                                    $reviews[$key] = [
                                        'nx_id'      => $_post['nx_id'],
                                        'source'     => $WPS->id,
                                        'entry_key'  => '',
                                        'data'       => $stats,
                                    ];
                                }
                                $WPS->update_notifications($reviews);
                            }
                        }
                        # code...
                        break;
                    case 'freemius':
                        if( boolval( $post['enabled'] ) ) {
                            Cron::get_instance()->set_cron($nx_id, 'nx_freemius_interval');
                        }
                        $post['source']             = 'freemius_stats';
                        $post['freemius_item_type'] = $this->get("_nx_meta_freemius_item_type");
                        $post['freemius_themes']    = $this->get("_nx_meta_freemius_themes");
                        $post['freemius_plugins']   = $this->get("_nx_meta_freemius_plugins");

                        $sales    = $this->get("_nx_meta_freemius_content");
                        $freemius = FreemiusStats::get_instance();
                        if(is_array($sales)){
                            foreach ($sales as $key => $sale) {
                                $sales[$key] = [
                                    'nx_id'      => $_post['nx_id'],
                                    'source'     => $freemius->id,
                                    'entry_key'  => $sale['id'],
                                    'data'       => $sale,
                                ];
                            }
                            $freemius->update_notifications($sales);
                        }

                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'elearning':
                // Source Tab
                $post['source'] = $this->get('_nx_meta_elearning_source');
                $post['themes'] = $this->get('_nx_meta_elearning_theme');
                $post['advance_edit'] = $this->get('_nx_meta_elearning_advance_edit');


                // Theme Tab
                $post['bg_color']           = $this->get("_nx_meta_bg_color");
                $post['text_color']         = $this->get("_nx_meta_text_color");
                $post['border']             = $this->get("_nx_meta_border");
                $post['border_size']        = $this->get("_nx_meta_border_size");
                $post['border_style']       = $this->get("_nx_meta_border_style");
                $post['border_color']       = $this->get("_nx_meta_border_color");
                $post['image_shape']        = $this->get("_nx_meta_image_shape");
                $post['image_position']     = $this->get("_nx_meta_image_position");
                $post['custom_image_shape'] = $this->get("_nx_meta_image_custom_shape");
                $post['first_font_size']    = $this->get("_nx_meta_first_font_size");
                $post['second_font_size']   = $this->get("_nx_meta_second_font_size");
                $post['third_font_size']    = $this->get("_nx_meta_third_font_size");

                // Content Tab
                // $post['elearning_template']            = $this->get("");
                $post['notification-template']        = $this->get("_nx_meta_elearning_template_new");
                if($post['template_adv'] = $this->get("_nx_meta_elearning_template_adv")){
                    $post['advanced_template']  = $this->get("_nx_meta_elearning_template");
                }
                $post['link_type']                 = $this->get("_nx_meta_elearning_url");
                if($post['link_type'] == 'product_page'){
                    $post['link_type'] = 'course_page';
                }
                $post['custom_url']          = $this->get("_nx_meta_elearning_custom_url");

                $post['sound']              = $this->get("_nx_meta_comments_sound");

                switch ($post['source']) {
                    case 'tutor':
                        $post['ld_product_control'] = $this->get("_nx_meta_tutor_product_control");
                        $post['ld_course_list']     = $this->get("_nx_meta_tutor_course_list");
                        break;
                    case 'learndash':
                        $post['ld_product_control'] = $this->get("_nx_meta_ld_product_control");
                        $post['ld_course_list']     = $this->get("_nx_meta_ld_course_list");
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'donation':
                // Source Tab
                $post['source'] = $this->get('_nx_meta_donation_source');
                $post['themes'] = $this->get('_nx_meta_donation_theme');
                $post['advance_edit'] = $this->get('_nx_meta_donation_advance_edit');

                // Theme Tab
                $post['bg_color']     = $this->get("_nx_meta_bg_color");
                $post['text_color']   = $this->get("_nx_meta_text_color");
                $post['border']       = $this->get("_nx_meta_border");
                $post['border_size']  = $this->get("_nx_meta_border_size");
                $post['border_style'] = $this->get("_nx_meta_border_style");
                $post['border_color'] = $this->get("_nx_meta_border_color");
                $post['image_shape']        = $this->get("_nx_meta_image_shape");
                $post['image_position']     = $this->get("_nx_meta_image_position");
                $post['custom_image_shape'] = $this->get("_nx_meta_image_custom_shape");
                $post['first_font_size']  = $this->get("_nx_meta_first_font_size");
                $post['second_font_size'] = $this->get("_nx_meta_second_font_size");
                $post['third_font_size']  = $this->get("_nx_meta_third_font_size");

                // Content Tab
                $post['notification-template']        = $this->get("_nx_meta_donation_template_new");
                if($post['template_adv'] = $this->get("_nx_meta_donation_template_adv")){
                    $post['advanced_template']  = $this->get("_nx_meta_donation_template");
                }
                $post['give_forms_control']           = $this->get("_nx_meta_give_forms_control");
                $post['give_form_list']               = $this->get("_nx_meta_give_form_list");

                $post['link_type']                 = $this->get("_nx_meta_donation_url");
                if($post['link_type'] == 'product_page'){
                    $post['link_type'] = 'donation_page';
                }
                $post['custom_url']          = $this->get("_nx_meta_donation_custom_url");

                $post['sound']              = $this->get("_nx_meta_comments_sound");

                switch ($post['source']) {
                    case 'give':
                        # code...
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'press_bar':
                // Source Tab
                $post['type']         = 'notification_bar';
                $post['source']       = 'press_bar';
                $post['themes']       = $this->get('_nx_meta_bar_theme');
                $post['advance_edit'] = $this->get('_nx_meta_bar_advance_edit');

                // Theme Tab
                $post['bg_color']            = $this->get('_nx_meta_bar_bg_color');
                $post['text_color']          = $this->get('_nx_meta_bar_text_color');
                $post['btn_bg']              = $this->get('_nx_meta_bar_btn_bg');
                $post['btn_text_color']      = $this->get('_nx_meta_bar_btn_text_color');
                $post['counter_bg']          = $this->get('_nx_meta_bar_counter_bg');
                $post['counter_text_color']  = $this->get('_nx_meta_bar_counter_text_color');
                $post['close_color']         = $this->get('_nx_meta_bar_close_color');
                $post['close_position']      = $this->get('_nx_meta_bar_close_position');
                $post['bar_font_size']       = $this->get('_nx_meta_bar_font_size');
                $post['press_content']       = $this->get('_nx_meta_press_content');
                $post['button_text']         = $this->get('_nx_meta_button_text');
                $post['button_url']          = $this->get('_nx_meta_button_url');
                $post['content_trim_length'] = $this->get('_nx_meta_content_trim_length');

                // Content Tab
                $post['enable_countdown']       = $this->get('_nx_meta_enable_countdown');
                $post['evergreen_timer']        = $this->get('_nx_meta_evergreen_timer');
                $post['countdown_text']         = $this->get('_nx_meta_countdown_text');
                $post['countdown_expired_text'] = $this->get('_nx_meta_countdown_expired_text');
                $post['countdown_start_date']   = $this->get('_nx_meta_countdown_start_date');
                $post['countdown_end_date']     = $this->get('_nx_meta_countdown_end_date');
                $post['time_rotation']          = $this->get('_nx_meta_time_rotation');
                $post['countdown_rand']         = time();
                $post['time_randomize']         = $this->get('_nx_meta_time_randomize');
                $post['time_randomize_between'] = $this->get('_nx_meta_time_randomize_between');
                // $post['start_time']             = $this->get('start_time');
                // $post['end_time']               = $this->get('end_time');
                $post['time_reset']             = $this->get('_nx_meta_time_reset');
                $post['close_forever']          = $this->get('_nx_meta_close_forever');
                if(empty($post['close_forever']) && !empty($this->get('_nx_meta_close_forever_2'))){
                    $post['close_forever']      = $this->get('_nx_meta_close_forever_2');
                }

                $post['position']      = $this->get('_nx_meta_pressbar_position');
                $post['sticky_bar']    = $this->get('_nx_meta_sticky_bar');
                $post['pressbar_body'] = $this->get('_nx_meta_pressbar_body');
                $post['elementor_id']  = (int) $this->get('_nx_bar_elementor_type_id');
                // if($post['nx_id'] == 4386){
                //     wp_send_json([$post, $_post]);
                // }

                break;
            case 'form':
                // Source Tab
                $post['source'] = $this->get('_nx_meta_form_source');
                $post['themes'] = $this->get('_nx_meta_form_theme');
                $post['advance_edit'] = $this->get('_nx_meta_form_advance_edit');

                // Theme Tab
                $post['bg_color']           = $this->get("_nx_meta_bg_color");
                $post['text_color']         = $this->get("_nx_meta_text_color");
                $post['border']             = $this->get("_nx_meta_border");
                $post['border_size']        = $this->get("_nx_meta_border_size");
                $post['border_style']       = $this->get("_nx_meta_border_style");
                $post['border_color']       = $this->get("_nx_meta_border_color");
                $post['image_shape']        = $this->get("_nx_meta_image_shape");
                $post['image_position']     = $this->get("_nx_meta_image_position");
                $post['custom_image_shape'] = $this->get("_nx_meta_image_custom_shape");
                $post['first_font_size']    = $this->get("_nx_meta_first_font_size");
                $post['second_font_size']   = $this->get("_nx_meta_second_font_size");
                $post['third_font_size']    = $this->get("_nx_meta_third_font_size");

                $post['sound']              = $this->get("_nx_meta_comments_sound");

                switch ($post['source']) {
                    case 'cf7':

                        // Content Tab
                        $post['form_list']             = "{$post['source']}_" . $this->get('_nx_meta_cf7_form');
                        $post['notification-template'] = $this->get('_nx_meta_form_template_new');
                        break;
                    case 'wpf':
                        // Content Tab
                        $post['form_list']             = "{$post['source']}_" . $this->get('_nx_meta_wpf_form');
                        $post['notification-template'] = $this->get('_nx_meta_wpf_template_new');
                        break;
                    case 'njf':
                        // Content Tab
                        $post['form_list']             = "{$post['source']}_" . $this->get('_nx_meta_njf_form');
                        $post['notification-template'] = $this->get('_nx_meta_njf_template_new');
                        break;
                    case 'grvf':
                        // Content Tab
                        $post['form_list']             = "{$post['source']}_" . $this->get('_nx_meta_grvf_form');
                        $post['notification-template'] = $this->get('_nx_meta_grvf_template_new');
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'email_subscription':
                // Source Tab
                $post['source'] = $this->get('_nx_meta_subscription_source');
                $post['themes'] = $this->get('_nx_meta_mailchimp_theme');
                $post['advance_edit'] = $this->get('_nx_meta_mailchimp_advance_edit');



                // Theme Tab
                $post['bg_color']         = $this->get('_nx_meta_mailchimp_bg_color');
                $post['text_color']       = $this->get('_nx_meta_mailchimp_text_color');
                $post['border']           = $this->get('_nx_meta_mailchimp_border');
                $post['border_size']      = $this->get('_nx_meta_mailchimp_border_size');
                $post['border_style']     = $this->get('_nx_meta_mailchimp_border_style');
                $post['border_color']     = $this->get('_nx_meta_mailchimp_border_color');
                $post['first_font_size']  = $this->get("_nx_meta_mailchimp_first_font_size");
                $post['second_font_size'] = $this->get("_nx_meta_mailchimp_second_font_size");
                $post['third_font_size']  = $this->get("_nx_meta_mailchimp_third_font_size");

                // Content Tab
                $post['notification-template'] = $this->get("_nx_meta_mailchimp_template_new");
                if($post['template_adv'] = $this->get("_nx_meta_mailchimp_template_adv")){
                    $post['advanced_template'] = $this->get("_nx_meta_mailchimp_template");
                }
                $show_avatar = $this->get("_nx_meta_show_avatar");
                if($show_avatar){
                    $post['show_notification_image'] = 'gravatar';
                }
                $post['sound'] = $this->get("_nx_meta_email_subscription_sound");

                switch ($post['source']) {
                    case 'mailchimp':
                        if( boolval( $post['enabled'] ) ) {
                            Cron::get_instance()->set_cron($nx_id, 'nx_mailchimp_interval');
                        }
                        $post['mailchimp_list']                   = $this->get("_nx_meta_mailchimp_list");
                        $sales  = $this->get("_nx_meta_mailchimp_content");
                        $mailchimp = MailChimp::get_instance();
                        if(is_array($sales)){
                            foreach ($sales as $key => $sale) {
                                $sales[$key] = [
                                    'nx_id'      => $_post['nx_id'],
                                    'source'     => $mailchimp->id,
                                    'entry_key'  => isset($sale['timestamp']) ? $sale['timestamp'] : '',
                                    'data'       => $sale,
                                ];
                            }
                            $mailchimp->update_notifications($sales);
                        }
                        # code...
                        break;
                    case 'convertkit':
                        if( boolval( $post['enabled'] ) ) {
                            Cron::get_instance()->set_cron($nx_id, 'nx_convertkit_interval');
                        }
                        $post['convertkit_form']             = $this->get("_nx_meta_convertkit_form");
                        $sales  = $this->get("_nx_meta_convertkit_content");
                        $convertkit = ConvertKit::get_instance();
                        if(is_array($sales)){
                            foreach ($sales as $key => $sale) {
                                $sales[$key] = [
                                    'nx_id'      => $_post['nx_id'],
                                    'source'     => $convertkit->id,
                                    'entry_key'  => isset($sale['timestamp']) ? $sale['timestamp'] : '',
                                    'data'       => $sale,
                                ];
                            }
                            $convertkit->update_notifications($sales);
                        }
                        break;
                    case 'zapier':
                        $post['source'] = 'zapier_email_subscription';
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'page_analytics':
                if( boolval( $post['enabled'] ) ) {
                    Cron::get_instance()->set_cron($nx_id, 'nx_ga_cache_duration');
                }
                // Source Tab
                $post['source']       = $this->get('_nx_meta_page_analytics_source');
                $post['themes']        = $this->get('_nx_meta_page_analytics_theme');

                // Theme Tab
                $post['bg_color']           = $this->get("_nx_meta_bg_color");
                $post['text_color']         = $this->get("_nx_meta_text_color");
                $post['border']             = $this->get("_nx_meta_border");
                $post['border_size']        = $this->get("_nx_meta_border_size");
                $post['border_style']       = $this->get("_nx_meta_border_style");
                $post['border_color']       = $this->get("_nx_meta_border_color");
                $post['image_shape']        = $this->get("_nx_meta_image_shape");
                $post['image_position']     = $this->get("_nx_meta_image_position");
                $post['custom_image_shape'] = $this->get("_nx_meta_image_custom_shape");
                $post['first_font_size']    = $this->get("_nx_meta_first_font_size");
                $post['second_font_size']   = $this->get("_nx_meta_second_font_size");
                $post['third_font_size']    = $this->get("_nx_meta_third_font_size");

                // Content Tab
                $template = $this->get("_nx_meta_page_analytics_template_new");
                $post['notification-template']['first_param'] = $this->get('first_param', '', $template);
                $post['notification-template']['second_param'] = $this->get('second_param', '', $template);
                $post['notification-template']['third_param'] = $this->get('page_third_param', '', $template);
                $post['notification-template']['custom_third_param'] = $this->get('custom_page_third_param', '', $template);
                if($this->get('first_param', '', $template) == 'tag_siteview'){
                    $post['notification-template']['ga_fourth_param'] = $this->get('analytics_fourth_param', '', $template);
                }
                else if($this->get('first_param', '', $template) == 'tag_realtime_siteview'){
                    $post['notification-template']['ga_fourth_param'] = $this->get('realtime_fourth_param', '', $template);
                }
                $post['notification-template']['ga_fifth_param'] = $this->get('fifth_param', '', $template);
                $post['notification-template']['sixth_param'] = $this->get('sixth_param', '', $template);

                $post['advance_edit']          = $this->get('_nx_meta_page_analytics_advance_edit');

                $post['sound']   = $this->get("_nx_meta_conversions_sound");

                // @todo check with new version.
                $sales  = $this->get("_nx_meta_custom_contents");
                if( boolval( $post['enabled'] ) ) {
                    Cron::get_instance()->set_cron($nx_id, 'nx_ga_cache_duration');
                }

                $ga = Google_Analytics::get_instance();
                if(is_array($sales)){
                    foreach ($sales as $key => $sale) {
                        $sales[$key] = [
                            'nx_id'      => $_post['nx_id'],
                            'source'     => $ga->id,
                            'data'       => $sale,
                        ];
                    }
                    $ga->update_notifications($sales);
                }

                break;
            case 'custom':
                // Source Tab
                $post['source']       = 'custom_notification';
                $post['themes']       = $this->get('_nx_meta_custom_theme');
                $post['advance_edit'] = $this->get('_nx_meta_custom_advance_edit');

                // Theme Tab
                $post['bg_color']           = $this->get("_nx_meta_bg_color");
                $post['text_color']         = $this->get("_nx_meta_text_color");
                $post['border']             = $this->get("_nx_meta_border");
                $post['border_size']        = $this->get("_nx_meta_border_size");
                $post['border_style']       = $this->get("_nx_meta_border_style");
                $post['border_color']       = $this->get("_nx_meta_border_color");
                $post['image_shape']        = $this->get("_nx_meta_image_shape");
                $post['image_position']     = $this->get("_nx_meta_image_position");
                $post['custom_image_shape'] = $this->get("_nx_meta_image_custom_shape");
                $post['first_font_size']    = $this->get("_nx_meta_first_font_size");
                $post['second_font_size']   = $this->get("_nx_meta_second_font_size");
                $post['third_font_size']    = $this->get("_nx_meta_third_font_size");

                // Content Tab
                $custom = CustomNotification::get_instance()->supported_themes();
                $conv_themes = array_keys(Conversions::get_instance()->get_themes());
                $temp_meta_key = '_nx_meta_type_custom_contents';
                $_themes = $post['themes'] = str_replace([
                    'comments-',
                    'reviews-',
                    'stats-',
                    'subs-',
                ], [
                    'comments_',
                    'reviews_',
                    'download_stats_',
                    'email_subscription_',
                ], $post['themes']); // = CustomNotification::get_instance()->get_theme_type($post);

                switch (true) {
                    case in_array($_themes, array_merge($conv_themes, ['maps_theme'])):
                        $post['themes'] = 'conversions_' . $post['themes'];
                        $final_meta_key = $temp_meta_key;
                        if (in_array($_themes, $custom['sales_count'])) {
                            $final_meta_key = $temp_meta_key . '_sales_count';
                        }
                        if (in_array($_themes, ['maps_theme'])) {
                            $final_meta_key = $temp_meta_key . '_maps_theme';
                        }
                        break;
                    case in_array($_themes, array_merge($custom['comments'], ['comments_maps_theme'])):
                        $final_meta_key = $temp_meta_key . '_comments';
                        if (in_array($_themes, array('comments_maps_theme'))) {
                            $final_meta_key = $temp_meta_key . '_maps_theme';
                        }
                        break;
                    case in_array($_themes, $custom['reviews']):
                        $final_meta_key = $temp_meta_key . '_reviews';

                        break;
                    case in_array($_themes, $custom['stats']):
                        $final_meta_key = $temp_meta_key . '_stats';
                        break;
                    case in_array($_themes, array_merge($custom['subs'], ['email_subscription_maps_theme'])):
                        $final_meta_key = $temp_meta_key . '_subs';
                        if (in_array($_themes, array('email_subscription_maps_theme'))) {
                            $final_meta_key = $temp_meta_key . '_maps_theme';
                        }
                        break;
                }


                $post['notification-template']   = $this->get("_nx_meta_woo_template_new");

                if (in_array($this->get('_nx_meta_custom_theme'), array('maps_theme', 'subs-maps_theme', 'comments-maps_theme'))) {
                    $template = $this->get('_nx_meta_maps_theme_template_new');
                    if(!empty($template)){
                        $p_template = &$post['notification-template'];
                        $p_template['first_param']         = $template['first_param'];
                        $p_template['custom_first_param']  = $template['custom_first_param'];
                        $p_template['second_param']        = $template['from'];
                        $p_template['third_param']         = $template['second_param'];
                        $p_template['map_fourth_param']    = $template['third_param'];
                        $p_template['fourth_param']        = $template['fourth_param'];
                        $p_template['fifth_param']         = $template['fifth_param'];
                        $p_template['custom_fourth_param'] = $template['custom_fourth_param'];
                    }
                    if($post['template_adv'] = $this->get("_nx_meta_maps_theme_template_adv")){
                        $post['advanced_template']  = $this->get("_nx_meta_maps_theme_template");
                    }
                }

                if (in_array($this->get('_nx_meta_custom_theme'), array('reviews-review-comment-3', 'reviews-review-comment-2', 'reviews-review-comment', 'reviews-reviewed', 'reviews-total-rated'))) {
                    $post['notification-template'] = $this->get('_nx_meta_wp_reviews_template_new');
                    if($post['template_adv'] = $this->get("_nx_meta_wp_reviews_template_adv")){
                        $post['advanced_template']  = $this->get("_nx_meta_wp_reviews_template");
                    }
                }

                if ($this->get('_nx_meta_custom_theme') === 'reviews-review_saying') {
                    $post['notification-template'] = $this->get('_nx_meta_review_saying_template_new');
                    if($post['template_adv'] = $this->get("_nx_meta_wp_reviews_template_adv")){
                        $post['advanced_template']  = $this->get("_nx_meta_wp_reviews_template");
                    }
                }

                if (in_array($this->get('_nx_meta_custom_theme'), array('stats-today-download', 'stats-total-download', 'stats-7day-download'))) {
                    $post['notification-template'] = $this->get('_nx_meta_wp_stats_template_new');
                    if($post['template_adv'] = $this->get("_nx_meta_wp_stats_template_adv")){
                        $post['advanced_template']  = $this->get("_nx_meta_wp_stats_template");
                    }
                }

                if ($this->get('_nx_meta_custom_theme') === 'stats-actively_using') {
                    $post['notification-template'] = $this->get('_nx_meta_actively_using_template_new');
                    if($post['template_adv'] = $this->get("_nx_meta_wp_stats_template_adv")){
                        $post['advanced_template']  = $this->get("_nx_meta_wp_stats_template");
                    }
                }

                if (in_array($this->get('_nx_meta_custom_theme'), array('subs-theme-one', 'subs-theme-two', 'subs-theme-three'))) {
                    $post['notification-template'] = $this->get('_nx_meta_mailchimp_template_new');
                    if($post['template_adv'] = $this->get("_nx_meta_mailchimp_template_adv")){
                        $post['advanced_template']  = $this->get("_nx_meta_mailchimp_template");
                    }
                }

                if (in_array($this->get('_nx_meta_custom_theme'), array('comments-theme-one', 'comments-theme-two', 'comments-theme-three', 'comments-theme-six-free', 'comments-theme-seven-free', 'comments-theme-eight-free', 'comments-theme-four', 'comments-theme-five'))) {
                    $post['notification-template'] = $this->get('_nx_meta_mailchimp_template_new');
                    if($post['template_adv'] = $this->get("_nx_meta_mailchimp_template_adv")){
                        $post['advanced_template']  = $this->get("_nx_meta_mailchimp_template");
                    }
                }

                if(!empty($_post[$final_meta_key])){
                    $post['custom_contents']  = $_post[$final_meta_key];
                    if(is_array($post['custom_contents'])){
                        foreach($post['custom_contents'] as &$entry){
                            if(!empty($entry['time'])){
                                $entry['timestamp'] = Helper::mysql_time($entry['time']);
                            }
                        }
                    }
                }



                $post['sound']              = $this->get("_nx_meta_custom_sound");

                break;

            default:
                break;
        }

        if(in_array($post['themes'], ['conv-theme-six', 'maps_theme'])){
            $template = $this->get('_nx_meta_maps_theme_template_new');
            if(!empty($template)){
                $p_template = &$post['notification-template'];
                $p_template['first_param']         = $template['first_param'];
                $p_template['custom_first_param']  = $template['custom_first_param'];
                $p_template['second_param']        = $template['from'];
                $p_template['third_param']         = $template['second_param'];
                $p_template['map_fourth_param']    = $template['third_param'];
                $p_template['fourth_param']        = $template['fourth_param'];
                $p_template['fifth_param']         = $template['fifth_param'];
                $p_template['custom_fourth_param'] = $template['custom_fourth_param'];
            }
            if($post['template_adv'] = $this->get("_nx_meta_maps_theme_template_adv")){
                $post['advanced_template']  = $this->get("_nx_meta_maps_theme_template");
            }
        }

        if($post['show_notification_image'] == 'product_image'){
            $post['show_notification_image'] = 'featured_image';
        }

        if (!empty($post['advanced_template']) && is_array($post['advanced_template'])) {
            $post['advanced_template'] = implode("\n", array_filter($post['advanced_template'], 'trim'));
        }

        if (empty($post['image_url']['id']) && empty($post['image_url']['url'])) {
            unset($post['image_url']);
        }
        if ($post['type'] == 'custom') {
            $post['themes'] = $post['themes'];
        } else if($post['source'] == 'woo_reviews'||$post['source'] == 'reviewx') {
            $post['themes'] = $post['source'] . '_' . $post['themes'];
        } else if($post['source'] == 'press_bar') {
            $post['themes'] = $post['source'] . '_' . $post['themes'];
        } else {
            $post['themes'] = $post['type'] . '_' . $post['themes'];
        }

        return $post;
    }

    public function migrate_entries($posts) {
        $notifications = get_option('notificationx_data', []);
        if(is_array($notifications)){
            foreach ($notifications as $source => $entries) {
                // @todo review later if it covers all case.
                $source = $this->normalize_source($source, $posts);
                $ext = ExtensionFactory::get_instance()->get($source);
                if ($ext && is_array($entries)) {
                    foreach ($entries as $key => $entry) {
                        $_entry = [
                            'source'     => $source,
                            'entry_key'  => $key,
                            'data'       => $entry,
                        ];
                        $ext->update_notification($_entry);
                    }
                } else {
                    error_log("$source not found");
                }
            }
        }
    }

    public function normalize_source($source, $posts) {
        $prefix = [
            'zapier',
            'grvf',
            'wpf',
            'njf',
            'cf7',
        ];
        if (strpos($source, 'zapier') === 0) {
            $nx_id = str_replace('zapier_', '', $source);
            if(!empty($posts[$nx_id])){
                return $posts[$nx_id]['source'];
            }

        }
        foreach ($prefix as $key => $value) {
            if (strpos($source, $value) === 0) {
                return $value;
            }
        }
        return $source;
    }

    public function migrate_stats($post) {
        $nx_id      = $post['ID'];
        if (!empty($post['_nx_meta_impression_per_day'])) {
            $impression = $post['_nx_meta_impression_per_day'];
            if(is_array($impression)){
                $stats = [];
                foreach ($impression as $_impressions) {
                    foreach ($_impressions as $date => $value) {
                        $data = [
                            'nx_id'      => $nx_id,
                            'clicks'     => !empty($value['clicks']) ? $value['clicks'] : 0,
                            'views'      => !empty($value['impressions']) ? $value['impressions'] : 0,
                            'created_at' => date(Analytics::$date_format, strtotime($date)),
                        ];
                        $stats[] = $data;
                    }
                }
                if( ! empty( $stats ) ) {
                    Analytics::get_instance()->migrate_analytics($stats);
                }
            }
        }
    }

    protected function get($index, $default = '', $arr = []){
        if(empty($arr)){
            $arr = $this->_post;
        }

        if(isset($arr[$index])){
            return $arr[$index];
        }
        return $default;
    }

}
