<?php

namespace ProfilePress\Core\Admin\SettingsPages\EmailSettings;

class DefaultTemplateCustomizer
{
    use CustomizerTrait;

    const DBPREFIX = 'ppress_email';

    const body_section = 'ppress_email_template_body';
    const header_section = 'ppress_email_template_header';
    const footer_section = 'ppress_email_template_footer';

    protected static $site_title;

    public function __construct()
    {
        if ( ! empty($_REQUEST['ppress-preview-template'])) {

            $this->clean_up_customizer();
            $this->modify_customizer_publish_button();

            add_action('customize_controls_enqueue_scripts', [$this, 'monkey_patch_customizer_payload']);

            self::$site_title = ppress_site_title();

            add_action('customize_register', [$this, 'customizer_register'], -1);

            add_action('parse_request', [$this, 'include_customizer_template'], 1);

            add_action('customize_controls_init', [$this, 'set_customizer_urls']);

            add_action('customize_section_active', array($this, 'remove_sections'), 999999999, 2);

            // Remove all customizer panels.
            add_action('customize_panel_active', '__return_false');

            add_filter('gettext', array($this, 'rewrite_customizer_panel_description'), 10, 3);

            // rewrite panel name from blog name to PP email template.
            add_filter('pre_option_blogname', function () {
                return esc_html__('ProfilePress Email Template', 'wp-user-avatar');
            });
        }
    }

    public static function get_customizer_value($setting)
    {
        $settings = get_option(self::DBPREFIX, []);

        $default = self::defaults($setting);

        if ($setting == 'header_text' && empty($default)) $default = ppress_site_title();

        $default = str_replace('{{siteTitle}}', ppress_site_title(), $default);

        return ! empty($settings[$setting]) ? $settings[$setting] : $default;
    }

    public function monkey_patch_customizer_payload()
    {
        wp_add_inline_script('customize-controls', '(function ( api ) {
              api.bind( "ready", function () {
                  var _query = api.previewer.query;
                      api.previewer.query = function () {
                          var query = _query.call( this );
                          query["ppress-preview-template"] = "true";
                          return query;
                      };
                  // needed to ensure save button is publising changes and not saving draft.
                  // esp for wp.com business hosting with save button set to draft by default.
                  api.state("selectedChangesetStatus").set("publish");
                  });
              })( wp.customize );'
        );

        wp_add_inline_style(
            'customize-controls',
            'button#publish-settings {display: none !important;}#customize-save-button-wrapper .save.has-next-sibling {border-radius: 3px;}');
    }

    public function include_customizer_template()
    {
        if (is_customize_preview() && ! empty($_GET['ppress-preview-template']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'ppress-preview-template')) {
            include(dirname(__FILE__) . '/email-template-preview.php');
            exit;
        }

        wp_safe_redirect(PPRESS_SETTINGS_EMAIL_SETTING_PAGE);
        exit;
    }

    public function set_customizer_urls()
    {
        global $wp_customize;

        $preview_url = add_query_arg(
            '_wpnonce',
            wp_create_nonce('ppress-preview-template'),
            sprintf(home_url('/?ppress-preview-template=true'))
        );

        $return_url = PPRESS_SETTINGS_EMAIL_SETTING_PAGE;

        $wp_customize->set_preview_url($preview_url);
        $wp_customize->set_return_url($return_url);
    }

    public static function get_prefixed_id($setting)
    {
        return self::DBPREFIX . "[" . $setting . "]";
    }

    public function remove_sections($active, $section)
    {
        $sections_ids = [self::header_section, self::footer_section, self::body_section];

        return in_array($section->id, $sections_ids);
    }

    public function rewrite_customizer_panel_description($translations, $text, $domain)
    {
        if (strpos($text, 'Customizer allows you to preview changes to your site')) {
            $translations = __(
                'The customizer allows you to design and preview ProfilePress default email template.',
                'wp-user-avatar'
            );
        }

        return $translations;
    }

    public function customizer_register($wp_customize)
    {
        remove_all_actions('customize_register'); // improve compatibility with hestia, generatepress themes etc
        $this->sections($wp_customize);
        $this->settings($wp_customize);
        $this->controls($wp_customize);
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     */
    public function sections($wp_customize)
    {
        $wp_customize->add_section(self::body_section, array(
                'title'    => esc_html__('Body', 'wp-user-avatar'),
                'priority' => 10,
            )
        );

        $wp_customize->add_section(self::header_section, array(
                'title'    => esc_html__('Header', 'wp-user-avatar'),
                'priority' => 10,
            )
        );

        $wp_customize->add_section(self::footer_section, array(
                'title'    => esc_html__('Footer', 'wp-user-avatar'),
                'priority' => 20,
            )
        );
    }

    public static function defaults($setting)
    {
        $defaults = [
            'background_color'         => '#f2f4f6',
            'background_text_color'    => '#a8aaaf',
            'content_background_color' => '#ffffff',
            'content_text_color'       => '#51545e',
            'header_text'              => self::$site_title,
            'footer_text'              => sprintf(esc_html__('© %s %s. All rights reserved.'), date('Y'), '{{siteTitle}}')
        ];

        return isset($defaults[$setting]) ? $defaults[$setting] : '';
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     */
    public function settings($wp_customize)
    {
        $wp_customize->add_setting(self::get_prefixed_id('background_color'), [
            'type'    => 'option',
            'default' => self::defaults('background_color')
        ]);

        $wp_customize->add_setting(self::get_prefixed_id('background_text_color'), [
            'type'    => 'option',
            'default' => self::defaults('background_text_color')
        ]);

        $wp_customize->add_setting(self::get_prefixed_id('content_background_color'), [
            'type'    => 'option',
            'default' => self::defaults('content_background_color')
        ]);

        $wp_customize->add_setting(self::get_prefixed_id('content_text_color'), [
            'type'    => 'option',
            'default' => self::defaults('content_text_color')
        ]);

        $wp_customize->add_setting(self::get_prefixed_id('header_logo'), [
            'type' => 'option'
        ]);

        $wp_customize->add_setting(self::get_prefixed_id('header_text'), [
            'default' => self::defaults('header_text'),
            'type'    => 'option'
        ]);

        $wp_customize->add_setting(self::get_prefixed_id('footer_text'), [
            'default' => sprintf(esc_html__('© %s %s. All rights reserved.'), date('Y'), self::$site_title),
            'type'    => 'option'
        ]);
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     */
    public function controls($wp_customize)
    {
        $wp_customize->add_control(new \WP_Customize_Color_Control(
            $wp_customize,
            self::get_prefixed_id('background_color'),
            array(
                'label'    => __('Background Color', 'wp-user-avatar'),
                'section'  => self::body_section,
                'settings' => self::get_prefixed_id('background_color'),
                'priority' => 10
            )
        ));

        $wp_customize->add_control(new \WP_Customize_Color_Control(
            $wp_customize,
            self::get_prefixed_id('background_text_color'),
            array(
                'label'    => __('Background Text Color', 'wp-user-avatar'),
                'section'  => self::body_section,
                'settings' => self::get_prefixed_id('background_text_color'),
                'priority' => 20
            )
        ));
        $wp_customize->add_control(new \WP_Customize_Color_Control(
            $wp_customize,
            self::get_prefixed_id('content_background_color'),
            array(
                'label'    => __('Content Background Color', 'wp-user-avatar'),
                'section'  => self::body_section,
                'settings' => self::get_prefixed_id('content_background_color'),
                'priority' => 30
            )
        ));

        $wp_customize->add_control(new \WP_Customize_Color_Control(
            $wp_customize,
            self::get_prefixed_id('content_text_color'),
            array(
                'label'    => __('Content Text Color', 'wp-user-avatar'),
                'section'  => self::body_section,
                'settings' => self::get_prefixed_id('content_text_color'),
                'priority' => 40
            )
        ));

        $wp_customize->add_control(new \WP_Customize_Cropped_Image_Control(
            $wp_customize,
            self::get_prefixed_id('header_logo'),
            array(
                'label'         => __('Logo', 'wp-user-avatar'),
                'section'       => self::header_section,
                'settings'      => self::get_prefixed_id('header_logo'),
                'flex_width'    => true,
                'flex_height'   => true,
                'width'         => 308,
                'height'        => 48,
                'button_labels' => array(
                    'select'       => __('Select Logo', 'wp-user-avatar'),
                    'change'       => __('Change Logo', 'wp-user-avatar'),
                    'default'      => __('Default', 'wp-user-avatar'),
                    'remove'       => __('Remove', 'wp-user-avatar'),
                    'placeholder'  => __('No logo selected', 'wp-user-avatar'),
                    'frame_title'  => __('Select Logo', 'wp-user-avatar'),
                    'frame_button' => __('Choose Logo', 'wp-user-avatar'),
                ),
                'priority'      => 50
            )
        ));

        $wp_customize->add_control(self::get_prefixed_id('header_text'), array(
            'label'       => __('Header Text', 'wp-user-avatar'),
            'description' => __('This is used when template logo is not set.', 'wp-user-avatar'),
            'section'     => self::header_section,
            'type'        => 'text',
            'settings'    => self::get_prefixed_id('header_text'),
            'priority'    => 60
        ));

        $wp_customize->add_control(self::get_prefixed_id('footer_text'), array(
            'label'    => __('Footer Text', 'wp-user-avatar'),
            'section'  => self::footer_section,
            'type'     => 'textarea',
            'settings' => self::get_prefixed_id('footer_text'),
            'priority' => 70
        ));
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}