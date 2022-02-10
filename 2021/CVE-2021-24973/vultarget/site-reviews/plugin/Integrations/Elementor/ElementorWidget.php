<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use Elementor\Widget_Base;
use GeminiLabs\SiteReviews\Helpers\Str;

abstract class ElementorWidget extends Widget_Base
{
    /**
     * @var \GeminiLabs\SiteReviews\Shortcodes\Shortcode|callable
     */
    private $_shortcode_instance = null;

    /**
     * @return array
     */
    public function get_categories()
    {
        return [glsr()->id];
    }

    /**
     * @return string
     */
    public function get_icon()
    {
        return 'eicon-star-o';
    }

    /**
     * @return string
     */
    public function get_name()
    {
        return $this->get_shortcode_instance()->shortcode;
    }

    /**
     * @param string $setting_key
     * @return mixed
     */
    public function get_settings_for_display($setting_key = null)
    {
        $settings = parent::get_settings_for_display();
        if (!empty($settings['assigned_posts_custom'])) {
            $settings['assigned_posts'] = $settings['assigned_posts_custom'];
        }
        $hide = [];
        foreach ($settings as $key => $value) {
            if (Str::startsWith('hide-', $key) && !empty($value)) {
                $hide[] = Str::removePrefix($key, 'hide-');
            }
        }
        $settings['class'] = $settings['shortcode_class']; // @compat
        $settings['hide'] = array_filter($hide);
        $settings['id'] = $settings['shortcode_id']; // @compat
        return $settings;
    }

    /**
     * @return string
     */
    abstract public function get_shortcode();

    /**
     * @return \GeminiLabs\SiteReviews\Shortcodes\Shortcode
     */
    public function get_shortcode_instance()
    {
        if (is_null($this->_shortcode_instance)) {
            $this->_shortcode_instance = glsr($this->get_shortcode());
        }
        return $this->_shortcode_instance;
    }

    protected function get_review_types()
    {
        if (count(glsr()->retrieve('review_types')) > 2) {
            return [
                'default' => 'local',
                'label' => _x('Limit the Type of Reviews', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'options' => glsr()->retrieve('review_types'),
                'type' => \Elementor\Controls_Manager::SELECT,
            ];
        }
        return [];
    }

    /**
     * @return void
     */
    protected function register_controls()
    {
        $settings = array_filter(glsr()->filterArray('integration/elementor/settings', $this->settings_basic(), $this->get_name()));
        if (!empty($settings)) {
            $this->register_shortcode_options($settings, 'settings', _x('Settings', 'admin-text', 'site-reviews'));
        }
        $advanced = array_filter(glsr()->filterArray('integration/elementor/advanced', $this->settings_advanced(), $this->get_name()));
        if (!empty($advanced)) {
            $this->register_shortcode_options($advanced, 'advanced', _x('Advanced', 'admin-text', 'site-reviews'));
        }
    }

    /**
     * @return void
     */
    protected function register_shortcode_options($options, $tabKey, $tabLabel)
    {
        $this->start_controls_section($tabKey, [
            'label' => $tabLabel,
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        foreach ($options as $key => $settings) {
            $this->add_control($key, $settings);
        }
        $this->end_controls_section();
    }

    protected function render()
    {
        $shortcode = $this->get_shortcode_instance()->build($this->get_settings_for_display());
        $shortcode = str_replace('class="glsr-fallback">', 'class="glsr-fallback" style="display:none;">', $shortcode);
        echo $shortcode;
    }

    /**
     * @return array
     */
    protected function settings_advanced()
    {
        return [
            'shortcode_id' => [
                'label_block' => true,
                'label' => _x('Custom ID', 'admin-text', 'site-reviews'),
                'type' => \Elementor\Controls_Manager::TEXT,
            ],
            'shortcode_class' => [
                'description' => _x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'label_block' => true,
                'label' => _x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'type' => \Elementor\Controls_Manager::TEXT,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function settings_basic()
    {
        return [];
    }
}
