<?php

namespace ProfilePress\Core\Themes\DragDrop\MemberDirectory;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\DragDropBuilder;
use ProfilePress\Core\ShortcodeParser\Builder\FrontendProfileBuilder;
use ProfilePress\Core\Themes\DragDrop\AbstractMemberDirectoryTheme;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;

class Gerbera extends AbstractMemberDirectoryTheme
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
        $data['ppress_md_gerbera_profile_picture']     = 'true';
        $data['ppress_md_gerbera_cover_image']         = 'true';
        $data['ppress_md_gerbera_text_color']          = '#999999';
        $data['ppress_md_gerbera_display_name_color']  = '#444444';
        $data['ppress_md_gerbera_avatar_border_color'] = '#ffffff';
        $data['ppress_md_gerbera_background_color']    = '#ffffff';
        $data['ppress_md_gerbera_border_color']        = '#dddddd';

        return $data;
    }

    public function appearance_settings($settings)
    {
        $settings = parent::appearance_settings($settings);

        $settings[] = [
            'id'       => 'ppress_md_gerbera_profile_picture',
            'type'     => 'checkbox',
            'label'    => esc_html__('Enable Profile Picture', 'wp-user-avatar'),
            'priority' => 15
        ];

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

    public function form_wrapper_class()
    {
        return 'pppress_md_gerbera';
    }

    /**
     * @return false|string|void
     */
    public function directory_structure()
    {
        $wp_user_query = $this->wp_user_query();

        $profile_picture_enabled = $this->get_meta('ppress_md_gerbera_profile_picture') == 'true';

        echo '<div class="pppress_md_gerbera_members">';

        /** @var \WP_User $user */
        foreach ($wp_user_query['users'] as $user) : new FrontendProfileBuilder($user);

            $profile_url = ppress_get_frontend_profile_url($user->ID);

            // important we parse immediately because FrontendProfileBuilder resets for every user.
            $display_name = do_shortcode('[profile-display-name]', true);
            ?>

            <div class="pppress_md_gerbera_members-item">

                <?php if ($profile_picture_enabled) : ?>
                    <div class="pppress_md_gerbera_members_avatar">
                        <a href="<?= $profile_url ?>" title="<?= $display_name ?>">
                            [pp-user-avatar user="<?= $user->ID ?>" size=256]
                        </a>
                    </div>
                <?php endif; ?>

                <?php echo $this->directory_listing($user->ID)->forge()->output(); ?>
            </div>

        <?php endforeach;

        echo '</div>';
    }

    public function form_css()
    {
        $form_id   = $this->form_id;
        $form_type = $this->form_type;

        return parent::form_css() . <<<CSS
#pp-$form_type-$form_id.ppress-md-default * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

.pppress_md_gerbera_members {
    grid-template-columns: 1fr 1fr 1fr 1fr;
    display: grid;
    grid-column-gap: 2rem;
    grid-row-gap: 5rem;
    text-align: center;
}

.ppressui960 .pppress_md_gerbera_members {
    grid-template-columns: 1fr 1fr 1fr;
}

.ppressui800 .pppress_md_gerbera_members {
    grid-template-columns: 1fr 1fr;
}

.ppressui500 .pppress_md_gerbera_members {
    grid-template-columns: 1fr;
}

.pppress_md_gerbera_members-item {
    word-break: break-word;
}

.pppress_md_gerbera_members_avatar img {
    border-radius: 50%;
    width: 200px;
    height: auto;
}

.ppress-md-profile-item-wrap.profile-display-name {
    font-size: 18px;
    font-weight: 700;
}

.ppress-md-profile-item-wrap {
    margin: 0 0 5px;
    line-height: 26px;
}

.pppress_md_gerbera_members_avatar {
    margin: 0 0 10px;
}

.ppress-md-profile-item-wrap *,
.ppress-md-profile-item-title {
    display: inline;
}
.ppress-md-profile-item-title {
    font-weight: bold;
}

CSS;
    }
}