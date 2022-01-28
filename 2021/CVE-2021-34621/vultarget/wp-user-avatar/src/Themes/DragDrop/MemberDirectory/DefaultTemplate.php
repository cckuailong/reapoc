<?php

namespace ProfilePress\Core\Themes\DragDrop\MemberDirectory;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\DragDropBuilder;
use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\ShortcodeParser\Builder\FrontendProfileBuilder;
use ProfilePress\Core\Themes\DragDrop\AbstractMemberDirectoryTheme;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;

class DefaultTemplate extends AbstractMemberDirectoryTheme
{
    public static function default_field_listing()
    {
        Fields\Init::init();
        $standard_fields = DragDropBuilder::get_instance()->standard_fields();

        return [
            $standard_fields['profile-display-name'],
            $standard_fields['profile-bio'],
        ];
    }

    public function default_metabox_settings()
    {
        $data                                          = parent::default_metabox_settings();
        $data['ppress_md_default_profile_picture']     = 'true';
        $data['ppress_md_default_cover_image']         = 'true';
        $data['ppress_md_default_social_icons']        = 'true';
        $data['ppress_md_default_text_color']          = '#999999';
        $data['ppress_md_default_display_name_color']  = '#444444';
        $data['ppress_md_default_avatar_border_color'] = '#ffffff';
        $data['ppress_md_default_background_color']    = '#ffffff';
        $data['ppress_md_default_border_color']        = '#dddddd';

        return $data;
    }

    public function appearance_settings($settings)
    {
        $settings = parent::appearance_settings($settings);

        $settings[] = [
            'id'       => 'ppress_md_default_profile_picture',
            'type'     => 'checkbox',
            'label'    => esc_html__('Enable Profile Picture', 'wp-user-avatar'),
            'priority' => 15
        ];

        $settings[] = [
            'id'       => 'ppress_md_default_cover_image',
            'type'     => 'checkbox',
            'label'    => esc_html__('Enable Cover Image', 'wp-user-avatar'),
            'priority' => 20
        ];


        if (EM::is_enabled(EM::CUSTOM_FIELDS)) {
            $settings[] = [
                'id'       => 'ppress_md_default_social_icons',
                'type'     => 'checkbox',
                'label'    => esc_html__('Enable Social Connect Icons', 'wp-user-avatar'),
                'priority' => 30
            ];
        } else {
            $upgrade_url = 'https://profilepress.net/pricing/?utm_source=wp_dashboard&utm_medium=upgrade&utm_campaign=md_social_connect_icons';
            $settings[]  = [
                'id'      => 'ppress_md_default_social_icons_upsell',
                'label'   => '',
                'type'    => 'custom',
                'content' => sprintf(
                    esc_html__('%sUpgrade to ProfilePress premium%s if you don\'t have the custom field addon to display social profile icons of users.', 'wp-user-avatar'),
                    '<a href="' . $upgrade_url . '" target="_blank">', '</a>'
                ),
            ];
        }

        return $settings;
    }

    public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
    {
        $appearance_settings = $settings['appearance'];

        unset($settings['appearance']);

        $new_settings['appearance'] = $appearance_settings;

        $new_settings = array_merge($new_settings, parent::metabox_settings($settings, $form_type, $DragDropBuilderInstance));

        return array_merge($new_settings, $settings);
    }

    public function color_settings($settings)
    {
        $settings2 = array_merge(parent::color_settings($settings), [
            [
                'id'    => 'ppress_md_default_background_color',
                'type'  => 'color',
                'label' => esc_html__('Card Background', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_md_default_border_color',
                'type'  => 'color',
                'label' => esc_html__('Card Border', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_md_default_text_color',
                'type'  => 'color',
                'label' => esc_html__('Card Text', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_md_default_display_name_color',
                'type'  => 'color',
                'label' => esc_html__('Display Name', 'wp-user-avatar')
            ],
            [
                'id'    => 'ppress_md_default_avatar_border_color',
                'type'  => 'color',
                'label' => esc_html__('Profile Picture Border', 'wp-user-avatar')
            ]
        ]);

        return array_merge($settings, $settings2);
    }

    public function form_wrapper_class()
    {
        return 'ppress-md-default';
    }

    /**
     * @return false|string|void
     */
    public function directory_structure()
    {
        $wp_user_query = $this->wp_user_query();

        $profile_picture_enabled = $this->get_meta('ppress_md_default_profile_picture') == 'true';

        $cover_image_enabled = $this->get_meta('ppress_md_default_cover_image') == 'true';

        $social_icons_enabled = $this->get_meta('ppress_md_default_social_icons') == 'true';

        ?>
        <div class="ppmd-members-wrap<?= ! $profile_picture_enabled ? ' ppmd-no-photo' : ''; ?><?= ! $cover_image_enabled ? ' ppmd-no-cover' : ''; ?>">

            <div class="ppmd-member-gutter"></div>

            <?php
            /** @var \WP_User $user */
            foreach ($wp_user_query['users'] as $user) : new FrontendProfileBuilder($user);

                $profile_url = ppress_get_frontend_profile_url($user->ID);

                $cover_image = ppress_get_cover_image_url($user->ID);
                // important we parse immediately because FrontendProfileBuilder resets for every user.
                $display_name = do_shortcode('[profile-display-name]', true);

                $social_profile_icons = $this->social_profile_icons();
                ?>

                <div class="ppmd-member-wrap">
                    <?php if ($cover_image_enabled) : ?>
                        <div class="ppmd-member-cover">
                            <div class="ppress-md-member-cover-e">
                                <a href="<?= $profile_url ?>" title="<?= $display_name ?>">
                                    <?php if ( ! empty($cover_image)) : ?>
                                        <?= sprintf('<img src="%s" alt="%s">', $cover_image, esc_attr($display_name)); ?>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($profile_picture_enabled) : ?>
                        <div class="ppmd-member-photo">
                            <a href="<?= $profile_url ?>" title="<?= $display_name ?>">
                                [pp-user-avatar user="<?= $user->ID ?>" size=40]
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="ppmd-member-card">
                        <?php echo $this->directory_listing($user->ID)->forge()->output(); ?>

                        <?php if ($social_icons_enabled && $social_profile_icons !== false) : ?>
                            <div class="ppress-md-member-meta-main">
                                <div class="ppress-md-member-meta">
                                    <div class="ppress-md-member-connect">
                                        <?php $this->social_profile_icons() ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    public function form_css()
    {
        $form_id   = $this->form_id;
        $form_type = $this->form_type;

        $card_text_color                  = $this->get_meta('ppress_md_default_text_color');
        $card_display_name_color          = $this->get_meta('ppress_md_default_display_name_color');
        $card_avatar_border_color         = $this->get_meta('ppress_md_default_avatar_border_color');
        $card_bg_color                    = $this->get_meta('ppress_md_default_background_color');
        $card_border_color                = $this->get_meta('ppress_md_default_border_color');
        $card_social_profile_border_color = $card_border_color == '#dddddd' ? '#e5e5e5' : $card_border_color;

        return parent::form_css() . <<<CSS
#pp-$form_type-$form_id.ppress-md-default * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

#pp-$form_type-$form_id.ppress-md-default a {
    text-decoration: none !important;
    border-bottom: none !important;
    box-shadow: none !important;
    border-bottom: 0 !important;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-members-wrap {
    width: 100%;
    margin: 0 0 10px 0;
    padding: 0;
}

/* (32% x 3) + (2%) =  ~100 */
#pp-$form_type-$form_id.ppress-md-default .ppmd-member-gutter {
    width: 2%;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-member-wrap {
    background: $card_bg_color;
    text-align: center;
    margin-bottom: 40px;
    border: 1px solid $card_border_color;
    width: 32%;
}

#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui960 .ppmd-member-wrap,
#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui800 .ppmd-member-wrap
 {
    width: 48%;
}

#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui960 .ppmd-member-gutter,
#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui800 .ppmd-member-gutter {
    width: 4%;
}

#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui500 .ppmd-member-gutter,
#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui340 .ppmd-member-gutter {
    width: 0;
}

#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui500 .ppmd-member-wrap,
#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui340 .ppmd-member-wrap {
    width: 100%;
}

#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui960 .ppmd-member-wrap {
    width: 48%;
}

#pp-$form_type-$form_id.ppress-md-default.pp-member-directory.ppressui960 .ppmd-member-gutter {
    width: 4%;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-member-cover {
    height: 170px;
    background-color: #eee;
    box-sizing: border-box;
    position: relative;
}

#pp-$form_type-$form_id.ppress-md-default .ppress-md-profile-item-wrap {
    margin: 0 0 5px;
    font-size: 14px;
    padding: 10px 15px 0;
    line-height: 16px;
}

#pp-$form_type-$form_id.ppress-md-default .ppress-md-profile-item-wrap:nth-child(2) {
    padding-top: 0;
    padding-bottom: 0;
}

#pp-$form_type-$form_id.ppress-md-default .ppress-md-profile-item-wrap.profile-display-name a {
    line-height: 26px;
    color: $card_display_name_color;
    font-weight: 700;
    font-size: 16px;
}

#pp-$form_type-$form_id.ppress-md-default .ppress-md-profile-item-title {
    font-weight: bold;
    display: block;
}

#pp-$form_type-$form_id.ppress-md-default .ppress-md-member-cover-e {
    text-align: center;
    box-sizing: border-box;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-member-photo {
    padding: 0 0 20px 0;
    text-align: center;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-no-cover .ppmd-member-photo {
    padding-top: 30px;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-member-photo a {
    text-decoration: none;
    box-shadow: none !important;
    display: inline !important;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-member-photo a img {
    display: inline;
    border: 5px solid $card_avatar_border_color;
    background: $card_avatar_border_color;
    border-radius: 100%;
    width: 90px;
    height: 90px;
    position: relative;
    top: -35px;
    margin-bottom: -45px;
    float: none;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-no-cover .ppmd-member-photo a img {
    top: auto;
    margin-bottom: 0;
    width: 140px;
    height: 140px;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-member-card {
    padding: 0;
    padding-bottom: 15px;
    color: $card_text_color;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-no-photo .ppmd-member-card {
    padding-top: 15px;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-no-cover .ppmd-member-card {
    padding-bottom: 30px;
}

#pp-$form_type-$form_id.ppress-md-default .ppress-md-member-cover-e img {
    width: 100%;
    height: 170px;
    box-shadow: none;
    overflow: hidden;
    -moz-border-radius: 0;
    -webkit-border-radius: 0;
    border-radius: 0;
    margin: 0;
    padding: 0;
}

#pp-$form_type-$form_id.ppress-md-default .ppmd-member-name {
    margin: 0 0 4px 0;
    justify-content: center;
}

#pp-$form_type-$form_id.ppress-md-default .ppress-md-member-meta {
    margin: 20px 15px 0 15px;
    padding: 15px 0 0;
    border-top: 1px solid $card_social_profile_border_color;
}

#pp-$form_type-$form_id.ppress-md-default .ppress-md-member-connect a {
    display: inline-block;
    width: 40px;
    line-height: 40px;
    height: 40px;
    opacity: 0.85;
    margin: 0 1px;
    transition: 0.25s;
}

#pp-$form_type-$form_id.ppress-md-default .ppress-pf-profile-connect {
    padding-bottom: 0;
}
CSS;
    }
}