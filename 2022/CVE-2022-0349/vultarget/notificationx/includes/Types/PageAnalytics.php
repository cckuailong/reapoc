<?php
/**
 * Extension Abstract
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Types;
use NotificationX\GetInstance;
use NotificationX\Modules;

/**
 * Extension Abstract for all Extension.
 */
class PageAnalytics extends Types {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
	use GetInstance;

    public $priority = 50;
    public $is_pro = true;
    public $module = ['modules_google_analytics'];
    public $default_source    = 'google';


    /**
     * Initially Invoked when initialized.
     */
    public function __construct(){
        $this->id = 'page_analytics';
        $this->title = __('Page Analytics', 'notificationx');
        parent::__construct();
        $this->themes = [
            'pa-theme-one'   => [
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/analytics/ga-theme-one.jpg',
                'template' => [
                    'first_param'        => 'tag_siteview',
                    'second_param'       => __('marketers', 'notificationx'),
                    'third_param'        => 'ga_title',
                    'custom_third_param' => __('Surfed this page', 'notificationx'),
                    'ga_fourth_param'    => __('in last ', 'notificationx'),
                    'ga_fifth_param'     => __('30', 'notificationx'),
                    'sixth_param'        => 'tag_day',
                ],
            ],
            'pa-theme-two'   => [
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/analytics/pa-theme-one.png',
                'image_shape' => 'rounded',
                'template' => [
                    'first_param'        => 'tag_siteview',
                    'second_param'       => __('people visited', 'notificationx'),
                    'third_param'        => 'ga_title',
                    'custom_third_param' => __('this page', 'notificationx'),
                    'ga_fourth_param'    => __('in last ', 'notificationx'),
                    'ga_fifth_param'     => __('1', 'notificationx'),
                    'sixth_param'        => 'tag_day',
                ],
            ],
            'pa-theme-three' => [
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/analytics/pa-theme-two.png',
                'image_shape' => 'circle',
                'template' => [
                    'first_param'        => 'tag_realtime_siteview',
                    'second_param'       => __('people looking', 'notificationx'),
                    'third_param'        => 'ga_title',
                    'custom_third_param' => __('this deal', 'notificationx'),
                    'ga_fourth_param'    => __('right now', 'notificationx'),
                    // need to set this two param unless they won't show up when changing the first param.
                    'ga_fifth_param'     => __('30', 'notificationx'),
                    'sixth_param'        => 'tag_day',
                ],
            ],
        ];

        $this->templates = [
            'pa_template_new' => [
                'first_param' => [
                    'tag_siteview'          => __('Total Site View', 'notificationx'),
                    'tag_realtime_siteview' => __('Realtime site view', 'notificationx')
                ],
                'third_param' => [
                    'ga_title'  => __('Site Title', 'notificationx'),
                ],
                'sixth_param' => [
                    'tag_day'   => __('Day', 'notificationx'),
                    'tag_month' => __('Month', 'notificationx'),
                    'tag_year'  => __('Year', 'notificationx'),
                ],
                '_themes' => [
                    'page_analytics_pa-theme-one',
                    'page_analytics_pa-theme-two',
                    'page_analytics_pa-theme-three',
                ],
            ],
        ];

    }

    /**
     * Runs when modules is enabled.
     *
     * @return void
     */
    public function init(){
        parent::init();

    }


}
