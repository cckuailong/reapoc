<?php

namespace ProfilePress\Core\Themes\DragDrop\UserProfile;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\DragDropBuilder;
use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\Themes\DragDrop\AbstractTheme;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;

class DefaultTemplate extends AbstractTheme
{

    public function __construct($form_id, $form_type)
    {
        parent::__construct($form_id, $form_type);

        add_action('ppress_profile_tab_content_main', [$this, 'profile_content_main']);
        add_action('ppress_profile_tab_content_posts', [$this, 'profile_content_posts']);
        add_action('ppress_profile_tab_content_comments', [$this, 'profile_content_comments']);
    }

    public static function default_field_listing()
    {
        Fields\Init::init();
        $standard_fields = DragDropBuilder::get_instance()->standard_fields();

        return [
            $standard_fields['profile-username'],
            $standard_fields['profile-email'],
            $standard_fields['profile-first-name'],
            $standard_fields['profile-last-name'],
            $standard_fields['profile-website'],
            $standard_fields['profile-bio'],
        ];
    }

    public function default_metabox_settings()
    {
        $data                                           = parent::default_metabox_settings();
        $data['ppress_dpf_max_width']                   = '1000px';
        $data['ppress_dpf_area_max_width']              = '600px';
        $data['ppress_dpf_profile_cover_enabled']       = 'true';
        $data['ppress_dpf_profile_show_bio']            = 'true';
        $data['ppress_dpf_profile_cover_ratio']         = '2.7';
        $data['ppress_dpf_profile_header_display_name'] = 'first_last_names';
        $data['ppress_dpf_profile_user_meta']           = [];
        $data['ppress_dpf_profile_menu_tabs']           = ['main', 'posts', 'comments'];


        $data['ppress_dpf_profile_header_name_color']        = '#555555';
        $data['ppress_dpf_profile_body_text_color']          = '#666666';
        $data['ppress_dpf_profile_menu_background_color']    = '#444444';
        $data['ppress_dpf_profile_menu_tab_text_color']      = '#ffffff';
        $data['ppress_dpf_profile_active_menu_tab_bg_color'] = '#007bff';

        return $data;
    }

    public function appearance_settings($settings)
    {
        $profile_tabs = $this->profile_tabs();

        $settings[] = [
            'id'          => 'ppress_dpf_max_width',
            'type'        => 'text',
            'label'       => esc_html__('Profile Maximum Width', 'wp-user-avatar'),
            'description' => esc_html__('The maximum width of the profile template.', 'wp-user-avatar'),
            'priority'    => 10
        ];

        $settings[] = [
            'id'          => 'ppress_dpf_area_max_width',
            'type'        => 'text',
            'label'       => esc_html__('Profile Area Maximum Width', 'wp-user-avatar'),
            'description' => esc_html__('The maximum width of the profile area inside profile below the header.', 'wp-user-avatar'),
            'priority'    => 20
        ];

        $settings[] = [
            'id'       => 'ppress_dpf_profile_cover_enabled',
            'type'     => 'checkbox',
            'label'    => esc_html__('Enable Cover Image', 'wp-user-avatar'),
            'priority' => 40
        ];

        $settings[] = [
            'id'          => 'ppress_dpf_profile_cover_ratio',
            'type'        => 'select',
            'label'       => esc_html__('Profile Cover Ratio', 'wp-user-avatar'),
            'options'     => [
                '1.6' => '1.6:1',
                '2.7' => '2.7:1',
                '3.2' => '3.2:1',
            ],
            'description' => esc_html__('Choose an aspect ratio of the profile cover image.', 'wp-user-avatar'),
            'priority'    => 50
        ];

        $settings[] = [
            'id'          => 'ppress_dpf_profile_header_display_name',
            'type'        => 'select',
            'label'       => esc_html__('Header Text', 'wp-user-avatar'),
            'options'     => [
                'hide'             => esc_html__('Hide', 'wp-user-avatar'),
                'username'         => esc_html__('Username', 'wp-user-avatar'),
                'display-name'     => esc_html__('Display Name', 'wp-user-avatar'),
                'nickname'         => esc_html__('Nickname', 'wp-user-avatar'),
                'first_last_names' => esc_html__('First & Last Name', 'wp-user-avatar'),
                'last_first_names' => esc_html__('Last & First Name', 'wp-user-avatar'),
                'first-name'       => esc_html__('First Name Only', 'wp-user-avatar')
            ],
            'description' => esc_html__('What to use as display name in profile header. Select "Hide" to hide it from showing.', 'wp-user-avatar'),
            'priority'    => 60
        ];

        if (EM::is_enabled(EM::CUSTOM_FIELDS)) {

            $settings[] = [
                'id'       => 'ppress_dpf_profile_show_social_links',
                'type'     => 'checkbox',
                'label'    => esc_html__('Show Social Links in Header', 'wp-user-avatar'),
                'priority' => 70
            ];
        } else {

            $upgrade_url = 'https://profilepress.net/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=default_profile_social_link';
            $settings[]  = [
                'id'      => 'ppress_dpf_profile_social_link_upsell',
                'label'   => '',
                'type'    => 'custom',
                'content' => sprintf(
                    esc_html__('%sUpgrade to ProfilePress premium%s if you don\'t have the custom field addon to display users social profiles.', 'wp-user-avatar'),
                    '<a href="' . $upgrade_url . '" target="_blank">', '</a>'
                ),
            ];
        }

        $settings[] = [
            'id'       => 'ppress_dpf_profile_show_bio',
            'type'     => 'checkbox',
            'label'    => esc_html__('Show Bio in Header', 'wp-user-avatar'),
            'priority' => 80
        ];

        $settings[] = [
            'id'       => 'ppress_dpf_profile_user_meta',
            'type'     => 'select2',
            'label'    => esc_html__('Fields to Show in User Meta', 'wp-user-avatar'),
            'options'  => ppress_standard_custom_fields_key_value_pair(true),
            'priority' => 90
        ];

        $settings[] = [
            'id'       => 'ppress_dpf_profile_menu_tabs',
            'type'     => 'select2',
            'label'    => esc_html__('Profile Menu Tabs', 'wp-user-avatar'),
            'options'  => array_reduce(array_keys($profile_tabs), function ($carry, $item) use ($profile_tabs) {
                $carry[$item] = $profile_tabs[$item]['title'];

                return $carry;
            }),
            'priority' => 100
        ];

        return $settings;
    }

    public function color_settings($settings)
    {
        $settings2 = [
            [
                'id'    => 'ppress_dpf_profile_header_name_color',
                'type'  => 'color',
                'label' => esc_html__('Header Name', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_dpf_profile_body_text_color',
                'type'  => 'color',
                'label' => esc_html__('Body Text', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_dpf_profile_menu_background_color',
                'type'  => 'color',
                'label' => esc_html__('Menu Background', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_dpf_profile_menu_tab_text_color',
                'type'  => 'color',
                'label' => esc_html__('Menu Tab Text', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_dpf_profile_active_menu_tab_bg_color',
                'type'  => 'color',
                'label' => esc_html__('Active Menu Tab Background', 'wp-user-avatar')
            ]
        ];

        return array_merge($settings, $settings2);
    }

    private function cover_image_structure()
    {
        $cover_ratio = $this->get_meta('ppress_dpf_profile_cover_ratio');
        if (empty($cover_ratio)) $cover_ratio = '2.7';
        ?>
        <div class="ppress-default-profile-cover" data-ratio="<?php echo esc_attr($cover_ratio); ?>">
            <div class="ppress-default-profile-cover-e" data-ratio="<?php echo esc_attr($cover_ratio); ?>">
                <img src="[profile-cover-image-url]">
            </div>
        </div>
        <?php
    }

    private function is_profile_cover_enabled()
    {
        return $this->get_meta('ppress_dpf_profile_cover_enabled') == 'true';
    }

    private function header_social_links()
    {
        if ($this->get_meta('ppress_dpf_profile_show_social_links') !== 'true') return;

        $this->social_profile_icons();
    }

    public function profile_tabs()
    {
        return apply_filters('ppress_profile_tabs', [
            'main'     => [
                'icon'  => 'person',
                'title' => esc_html__('About', 'wp-user-avatar')
            ],
            'posts'    => [
                'icon'  => 'create',
                'title' => esc_html__('Posts', 'wp-user-avatar')
            ],
            'comments' => [
                'icon'  => 'comment',
                'title' => esc_html__('Comments', 'wp-user-avatar')
            ]
        ]);
    }

    private function active_tab()
    {
        $active = 'main';

        if ( ! empty($_GET['tab'])) {

            $sanitized_tab = sanitize_text_field($_GET['tab']);

            if (in_array($sanitized_tab, array_keys($this->profile_tabs()))) {
                $active = sanitize_text_field($_GET['tab']);
            }
        }

        return $active;
    }

    public function profile_tabs_section()
    {
        $saved_tabs = array_filter($this->get_meta('ppress_dpf_profile_menu_tabs'), function ($item) {
            return ! empty($item);
        });

        if (empty($saved_tabs)) $saved_tabs = ['main', 'posts' . 'comments'];

        $profile_tabs = $this->profile_tabs();

        if ( ! empty($saved_tabs)) {

            echo '<div class="ppress-dpf-profile-nav">';

            foreach ($saved_tabs as $tab_id) :
                $url = esc_url(add_query_arg('tab', $tab_id));
                ?>
                <div class="ppress-dpf-profile-nav-item ppress-dpf-nav-<?= $tab_id ?><?= $this->active_tab() == $tab_id ? ' ppress-dpf-active' : ''; ?>">
                    <a href="<?= $url ?>">
                        <span class="ppress-material-icons"><?= $profile_tabs[$tab_id]['icon'] ?></span>
                        <span class="ppress-dpf-nav-title"><?= $profile_tabs[$tab_id]['title'] ?></span>
                    </a>
                </div>
            <?php endforeach;
            echo '<div class="ppress-dpf-clear"></div>';
            echo '</div>';
        }
    }

    public function profile_content_main()
    {
        $profile_listing = $this->profile_listing()
                                ->item_wrap_start_tag('<div class="ppress-dpf-profile-body-item">')
                                ->item_wrap_end_tag('</div>')
                                ->title_start_tag('<div class="ppress-dpf-item-label">')
                                ->title_end_tag('</div>')
                                ->info_start_tag('<div class="ppress-dpf-item-value">')
                                ->info_end_tag('</div>')
                                ->forge();

        $has_field_data = $profile_listing->has_field_data();

        $profile_listing = $profile_listing->output();

        if ($has_field_data === false) : ?>
            <p class="ppress-dpf-profile-note">
                <span class="ppress-material-icons">sentiment_dissatisfied</span>
                <span>
                        <?= sprintf(
                            __('Your profile is looking a little empty. Why not <a href="%s">add some information</a>?', 'wp-user-avatar'),
                            ppress_edit_profile_url()
                        ); ?>
                </span>
            </p>
        <?php endif;

        if ($has_field_data === true) { ?>
            <div class="ppress-dpf-profile-body-items">
                <?php echo $profile_listing; ?>
            </div>
            <?php
        }
    }

    public function profile_content_posts()
    {
        echo '[profile-post-list]';
        if (ppress_settings_by_key('author_slug_to_profile') != 'on') {
            printf(
                '<div class="ppress-dpf-more-post-wrap"><a href="%s" class="ppress-dpf-more-post-btn">%s</a></div>',
                '[profile-author-posts-url]',
                esc_html__('See all Posts', 'wp-user-avatar')
            );
        }
    }

    public function profile_content_comments()
    {
        echo '[profile-comment-list]';
    }

    public function form_structure()
    {
        $active_tab = $this->active_tab();

        $max_width              = $this->get_meta('ppress_dpf_max_width');
        $profile_area_max_width = $this->get_meta('ppress_dpf_area_max_width');
        $header_display_name    = $this->get_meta('ppress_dpf_profile_header_display_name');

        /** @see https://stackoverflow.com/a/20373067/2648410  using array_values to reindex array numeric keys */
        $profile_user_meta = array_values(
            array_filter(
                array_map(function ($meta_key) {

                    if (empty($meta_key)) return false;

                    $value = $this->get_profile_field($meta_key, true);

                    return empty($value) ? false : $value;

                }, $this->get_meta('ppress_dpf_profile_user_meta'))
            )
        );

        $classes = 'ppress-default-profile';

        if ( ! $this->is_profile_cover_enabled()) {
            $classes .= ' ppdf-nocover';
        }
        ob_start();
        ?>
        [pp-form-wrapper class="<?= $classes ?>" style="max-width:<?= $max_width ?>"]

        <?php if ($this->is_profile_cover_enabled()) $this->cover_image_structure(); ?>

        <div class="ppress-dpf-header">
            <?php if (ppress_is_my_own_profile()) : ?>
                <div class="ppress-dpf-headericon">
                    <a href="<?= ppress_my_account_url(); ?>" class="ppress-dpf-edit-a hint--bottom" aria-label="<?= esc_html__('Edit your profile') ?>">
                        <span class="ppress-material-icons">settings</span>
                    </a>
                </div>
            <?php endif; ?>

            <div class="ppress-dpf-profile-photo">
                <img src="[profile-avatar-url]" width="190" height="190">
            </div>

            <div class="ppress-dpf-profile-meta">
                <div class="ppress-dpf-main-meta">

                    <?php if ('hide' !== $header_display_name) : ?>
                        <div class="ppress-dpf-name"><?= $this->get_profile_field($header_display_name) ?></div>
                    <?php endif; ?>

                    <?php $this->header_social_links(); ?>
                </div>

                <?php if (is_array($profile_user_meta) && ! empty($profile_user_meta)) : $count = count($profile_user_meta) - 1; ?>
                    <div class="ppress-dpf-meta">
                        <?php foreach ($profile_user_meta as $index => $meta_data) : ?>
                            <?= $meta_data ?>
                            <?php if ($index !== $count): ?>
                                <span class="b">&bull;</span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->get_meta('ppress_dpf_profile_show_bio') == 'true') : ?>
                    <div class="ppress-dpf-meta-text">
                        [profile-bio]
                    </div>
                <?php endif; ?>

            </div>

            <div class="ppress-dpf-clear"></div>

        </div>

        <?php $this->profile_tabs_section(); ?>

        <div class="ppress-dpf-profile-body ppdf-<?= $active_tab ?>"<?= ! empty($profile_area_max_width) ? ' style="max-width:' . $profile_area_max_width . '"' : '' ?>>
            <?php do_action(sprintf('ppress_profile_tab_content_%s', $active_tab)); ?>
        </div>
        [/pp-form-wrapper]

        <?php
        return ob_get_clean();
    }

    public function form_css()
    {
        $form_id   = $this->form_id;
        $form_type = $this->form_type;

        $photo_size = 190;

        $meta_padding = ($photo_size + 60) . 'px';

        $photosize_up = ($photo_size / 2) + 5;

        $profile_photo_style = [
            "width:{$photo_size}px",
            "height:{$photo_size}px"
        ];

        if ($this->is_profile_cover_enabled()) {
            $profile_photo_style[] = "top:-" . $photosize_up . "px";
        }

        $profile_photo_style = implode(';', $profile_photo_style);

        $header_name_color        = $this->get_meta('ppress_dpf_profile_header_name_color');
        $body_text_color          = $this->get_meta('ppress_dpf_profile_body_text_color');
        $menu_background_color    = $this->get_meta('ppress_dpf_profile_menu_background_color');
        $menu_tab_text_color      = $this->get_meta('ppress_dpf_profile_menu_tab_text_color');
        $active_menu_tab_bg_color = $this->get_meta('ppress_dpf_profile_active_menu_tab_bg_color');

        return <<<CSS
div#pp-$form_type-$form_id.ppress-default-profile .ppress-dpf-profile-photo {
    $profile_photo_style
}

div#pp-$form_type-$form_id.ppress-default-profile {
    color: $body_text_color;
}

div#pp-$form_type-$form_id.ppress-default-profile .ppress-dpf-profile-nav {
    background: $menu_background_color;
}

div#pp-$form_type-$form_id.ppress-default-profile .ppress-dpf-profile-nav-item a {
    color: $menu_tab_text_color;
}

div#pp-$form_type-$form_id.ppress-default-profile .ppress-dpf-name {
    color: $header_name_color;
}

div#pp-$form_type-$form_id.ppress-default-profile .ppress-dpf-profile-meta {
    padding-left: $meta_padding;
}

div#pp-$form_type-$form_id.ppress-default-profile .ppress-dpf-profile-nav-item.ppress-dpf-active a {
    background: $active_menu_tab_bg_color;
}
CSS;

    }
}