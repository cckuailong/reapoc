<?php

/**
 * WPOrgStats Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\WordPress;

use NotificationX\Admin\Cron;
use NotificationX\Core\Helper;
use NotificationX\Core\PostType;
use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;
use NotificationX\Extensions\GlobalFields;

/**
 * WPOrgStats Extension
 */
class WPOrgStats extends Extension {
    /**
     * Instance of WPOrgStats
     *
     * @var WPOrgStats
     */
    use GetInstance;
    use WordPress;

    /**
     * Undocumented variable
     *
     * @var [WPOrg_Helper]
     */
    public $helper;

    public $priority = 5;
    public $id       = 'wp_stats';
    public $img      = NOTIFICATIONX_ADMIN_URL . 'images/extensions/sources/wordpress.png';
    public $doc_link = 'https://notificationx.com/docs-category/configurations/';
    public $types    = 'download_stats';
    public $module   = 'modules_wordpress';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title = __('WP.Org Stats', 'notificationx');
        $this->module_title = __('WordPress', 'notificationx');
        parent::__construct();
    }


    public function init() {
        parent::init();
        add_filter("nx_filtered_entry_{$this->id}", array($this, 'conversion_data'), 10, 2);
    }

    public function init_fields() {
        parent::init_fields();
        add_filter('nx_content_fields', [$this, 'content_fields']);
    }

    /**
     * This functions is hooked
     *
     * @return void
     */
    public function admin_actions() {
        parent::admin_actions();
        add_action("nx_cron_update_data_{$this->id}", array($this, 'update_data'), 10, 2);
    }

    /**
     * This functions is hooked
     *
     * @hooked nx_public_action
     *
     * @return void
     */
    public function public_actions() {
        parent::public_actions();

        // add_filter('nx_fields_data', array($this, 'conversion_data'), 10, 2);
    }

    /**
     * Get data for WooCommerce Extension.
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function content_fields($fields) {
        $content_fields = &$fields['content']['fields'];

        $content_fields['wp_stats_product_type'] = [
            'name' => 'wp_stats_product_type',
            'type'     => 'select',
            'label'    => __('Product Type', 'notificationx'),
            'priority' => 79,
            'default'  => 'plugin',
            'options' => GlobalFields::get_instance()->normalize_fields([
                'plugin' => __('Plugin', 'notificationx'),
                'theme' => __('Theme', 'notificationx'),
            ]),
            'rules'  => ['is', 'source', $this->id]
        ];

        $content_fields['wp_stats_slug'] = [
            'name' => 'wp_stats_slug',
            'type'     => 'text',
            'label'    => __('Slug', 'notificationx'),
            'priority' => 80,
            'rules'  => ['is', 'source', $this->id]
        ];
        return $fields;
    }

    // @todo
    public function fallback_data($data, $saved_data, $settings) {
        $data['today'] = 0;
        $data['yesterday'] = 0;
        $data['last_week'] = 0;
        $data['all_time'] = 0;
        $data['active_installs'] = 0;
        $data['today_text'] = __('Try It Out', 'notificationx');
        $data['last_week_text'] = __('Get Started for Free.', 'notificationx');
        $data['all_time_text']  = __('Why Don\'t You?', 'notificationx');
        $data['active_installs_text'] = __( 'Try It Out', 'notificationx' );

        if(!empty($saved_data['name'])){
            $data['plugin_theme_name'] = $saved_data['name'];
        }

        return $data;
    }

    // @todo Frontend
    public function conversion_data($saved_data, $settings) {

        // translators: %s: number of downloads today.
        $saved_data['today'] = sprintf(__( '%s times today', 'notificationx' ), Helper::nice_number( $saved_data['today'] ));
        // translators: %s: number of downloads yesterday.
        $saved_data['yesterday'] = sprintf(__( '%s times', 'notificationx' ), Helper::nice_number( $saved_data['yesterday'] ));
        // translators: %s: number of downloads in last 7 days.
        $saved_data['last_week'] = sprintf(__( '%s times in last 7 days', 'notificationx' ), Helper::nice_number( $saved_data['last_week'] ));
        // translators: %s: number of downloads of all time.
        $saved_data['all_time'] = sprintf(__( '%s times', 'notificationx' ), Helper::nice_number( $saved_data['all_time'] ));
        $saved_data['active_installs'] = __( Helper::nice_number( $saved_data['active_installs'] ), 'notificationx' );
        // wp_send_json($saved_data);
        return $saved_data;
    }

    // @todo
    public function notification_image($image_data, $data, $settings) {
        if (!$settings['show_default_image']) {
            $image_url = '';
            if (isset($data['icons']) && $settings['wp_stats_product_type'] === 'plugin') {
                if (isset($data['icons']['2x'])) {
                    $image_url = $data['icons']['2x'];
                } else {
                    $image_url = isset($data['icons']['1x']) ? $data['icons']['1x'] : '';
                }
            }
            if (isset($data['screenshot_url']) && $settings['wp_stats_product_type'] === 'theme') {
                $image_url = $data['screenshot_url'];
            }
            $image_data['url'] = $image_url;
        }

        return $image_data;
    }

    public function saved_post($post, $data, $nx_id) {
        $this->update_data($nx_id, $data);
        Cron::get_instance()->set_cron($nx_id, 'nx_wp_stats_interval');
        return $post;
    }

    public function update_data($post_id, $data = array()) {
        if (empty($post_id)) {
            return;
        }
        if (empty($data)) {
            $data = PostType::get_instance()->get_post($post_id);
        }

        $plugins_data = $this->get_plugins_data($post_id, $data);
        $this->delete_notification(null, $post_id);
        $this->update_notification([
            'nx_id'      => $post_id,
            'source'     => $this->id,
            'entry_key'  => $plugins_data['slug'],
            'data'       => $plugins_data,
        ]);
    }

    public function get_plugins_data($post_id, $_nx_meta) {
        if (!$post_id) {
            return;
        }
        $this->helper = new WPOrg_Helper();
        $product_type = $_nx_meta['wp_stats_product_type'];
        $plugin_slug = $_nx_meta['wp_stats_slug'];

        if (!$plugin_slug) {
            return;
        }

        $total_stats = array();

        if ($product_type == 'plugin') {
            $raw_stats              = $this->helper->get_plugin_stats($plugin_slug);
            $raw_historical_summary = $this->remote_get('https://api.wordpress.org/stats/plugin/1.0/downloads.php?slug=' . $plugin_slug . '&historical_summary=1');
            $historical_summary     = json_decode(json_encode($raw_historical_summary), true);
            $total_stats            = array_merge($raw_stats, $historical_summary);
            $total_stats['link'] = "https://wordpress.org/plugins/" . $total_stats['slug'];
        }

        if ($product_type == 'theme') {
            $stats              = $this->helper->get_theme_stats($plugin_slug);
            $raw_historical_summary = $this->remote_get('https://api.wordpress.org/stats/themes/1.0/downloads.php?slug=' . $plugin_slug . '&historical_summary=1');
            $historical_summary     = json_decode(json_encode($raw_historical_summary), true);
            $total_stats            = array_merge($stats, $historical_summary);
        }
        $total_stats['plugin_theme_name'] = !empty($total_stats['name']) ? html_entity_decode($total_stats['name']) : '';
        return $total_stats;
    }

    /**
     * This function is responsible for making the notification ready for first time we make the notification.
     *
     * @param string $type
     * @param array $data
     * @return void
     */
    public function get_notification_ready($data = array()) {
        if (!is_null($data['nx_id'])) {
            $this->update_data($data['nx_id'], $data);
        }
    }
}
