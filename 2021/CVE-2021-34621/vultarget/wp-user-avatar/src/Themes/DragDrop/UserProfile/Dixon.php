<?php

namespace ProfilePress\Core\Themes\DragDrop\UserProfile;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\DragDropBuilder;
use ProfilePress\Core\Base;
use ProfilePress\Core\Themes\DragDrop\AbstractTheme;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Fields;

class Dixon extends AbstractTheme
{
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

    public function appearance_settings($settings)
    {
        $settings[] = [
            'id'       => 'dixon_profile_header_text',
            'type'     => 'select',
            'label'    => esc_html__('Header Text', 'wp-user-avatar'),
            'options'  => ppress_standard_fields_key_value_pair(),
            'priority' => 10
        ];

        $settings[] = [
            'id'       => 'dixon_profile_header_byline',
            'type'     => 'text',
            'label'    => esc_html__('Header Byline', 'wp-user-avatar'),
            'priority' => 12
        ];

        $settings[] = [
            'id'       => 'dixon_profile_section_header_text',
            'type'     => 'text',
            'label'    => esc_html__('Profile Header Text', 'wp-user-avatar'),
            'priority' => 15
        ];

        return $settings;
    }

    public function color_settings($settings)
    {
        $settings2 = [
            [
                'id'    => 'dixon_profile_bg_color',
                'type'  => 'color',
                'label' => esc_html__('Background', 'wp-user-avatar')
            ],
            [
                'id'    => 'dixon_profile_header_bg_color',
                'type'  => 'color',
                'label' => esc_html__('Header Background', 'wp-user-avatar')
            ],
            [
                'id'    => 'dixon_profile_header_text_color',
                'type'  => 'color',
                'label' => esc_html__('Header Text', 'wp-user-avatar')
            ],
            [
                'id'    => 'dixon_profile_header_text_underline_color',
                'type'  => 'color',
                'label' => esc_html__('Text Underline', 'wp-user-avatar')
            ],
            [
                'id'    => 'dixon_profile_avatar_border_color',
                'type'  => 'color',
                'label' => esc_html__('Avatar Border', 'wp-user-avatar')
            ],
            [
                'id'    => 'dixon_profile_info_border_color',
                'type'  => 'color',
                'label' => esc_html__('Profile Info Border', 'wp-user-avatar')
            ],
            [
                'id'    => 'dixon_profile_info_bg_color',
                'type'  => 'color',
                'label' => esc_html__('Profile Info Background', 'wp-user-avatar')
            ],
            [
                'id'    => 'dixon_profile_info_text_color',
                'type'  => 'color',
                'label' => esc_html__('Profile Info Text', 'wp-user-avatar')
            ]
        ];

        return array_merge($settings, $settings2);
    }

    public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
    {
        $settings['dixon_profile_social_links'] = [
            'tab_title' => esc_html__('Social Links', 'wp-user-avatar'),
            [
                'id'             => 'dixon_profile_hide_social_links',
                'type'           => 'checkbox',
                'label'          => esc_html__('Hide Social Links', 'wp-user-avatar'),
                'checkbox_label' => esc_html__('Check to hide', 'wp-user-avatar'),
            ]
        ];

        return $settings;
    }

    public function default_metabox_settings()
    {
        $data                                              = parent::default_metabox_settings();
        $data['dixon_profile_header_text']                 = 'username';
        $data['dixon_profile_header_byline']               = esc_html__('Profile', 'wp-user-avatar');
        $data['dixon_profile_section_header_text']         = esc_html__('PROFILE DETAILS', 'wp-user-avatar');
        $data['dixon_profile_hide_social_links']           = 'false';
        $data['dixon_profile_bg_color']                    = '#ececec';
        $data['dixon_profile_header_bg_color']             = '#196783';
        $data['dixon_profile_header_text_color']           = '#ffffff';
        $data['dixon_profile_avatar_border_color']         = '#ffffff';
        $data['dixon_profile_header_text_underline_color'] = '#52edc7';
        $data['dixon_profile_info_border_color']           = '#ff0000';
        $data['dixon_profile_info_bg_color']               = '#ffffff';
        $data['dixon_profile_info_text_color']             = '#6b6969';

        return $data;
    }

    protected function social_links_block()
    {
        if ($this->get_meta('dixon_profile_hide_social_links') == 'true') return;

        $asset_url = $this->asset_image_url . '/dixon-fe-profile';

        $facebook_field = $this->get_profile_field(Base::cif_facebook, true);
        $twitter_field  = $this->get_profile_field(Base::cif_twitter, true);
        $linkedin_field = $this->get_profile_field(Base::cif_linkedin, true);

        ob_start();
        ?>
        <div class="dixon-icon">
            <ul>
                <?php if ( ! empty($facebook_field)) : ?>
                    <li>
                        <a href="<?= $facebook_field ?>"><img src="<?= $asset_url ?>/facebook.png"/></a>
                    </li>
                <?php endif;
                if ( ! empty($twitter_field)) : ?>
                    <li>
                        <a href="<?= $twitter_field ?>"><img src="<?= $asset_url ?>/twitter.png"/></a>
                    </li>
                <?php endif; ?>
                <?php if ( ! empty($linkedin_field)) : ?>
                    <li>
                        <a href="<?= $linkedin_field ?>"><img src="<?= $asset_url ?>/linkedin.png"/></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    public function form_structure()
    {
        $header_text = $this->get_profile_field($this->get_meta('dixon_profile_header_text'));

        $header_byline       = $this->get_meta('dixon_profile_header_byline');
        $section_header_text = $this->get_meta('dixon_profile_section_header_text');

        $social_links_block = $this->social_links_block();

        $profile_listing = $this->profile_listing()
                                ->title_start_tag('<div class="dixon-section-left"><span>')
                                ->title_end_tag('</span></div>')
                                ->info_start_tag('<div class="dixon-section-right"><span>')
                                ->info_end_tag('</span></div>')
                                ->forge()
                                ->output();

        return <<<HTML
[pp-form-wrapper class="dixon-wrapper"]
    <div class="dixon-header">
        <div class="dixon-container">
            <div class="dixon-img">
			    <div class="dixon-avatar">
                    <img src="[profile-avatar-url]" />
				</div>
            </div>
            <div class="dixon-text"> <span>$header_text</span><br>
                $header_byline
             </div>
            $social_links_block
        </div>
    </div>
    <div class="dixon-container">
        <div style="text-align:center;">
            <h3>$section_header_text</h3>
        </div>
        <div class="dixon-section">
            $profile_listing
        </div>
    </div>
[/pp-form-wrapper]
HTML;

    }

    public function form_css()
    {
        $form_id   = $this->form_id;
        $form_type = $this->form_type;

        $bg_color                = $this->get_meta('dixon_profile_bg_color');
        $header_bg_color         = $this->get_meta('dixon_profile_header_bg_color');
        $header_text_color       = $this->get_meta('dixon_profile_header_text_color');
        $avatar_border_color     = $this->get_meta('dixon_profile_avatar_border_color');
        $text_underline_color    = $this->get_meta('dixon_profile_header_text_underline_color');
        $info_border_color       = $this->get_meta('dixon_profile_info_border_color');
        $profile_info_background = $this->get_meta('dixon_profile_info_bg_color');
        $profile_info_text       = $this->get_meta('dixon_profile_info_text_color');

        return <<<CSS
@import url(https://fonts.googleapis.com/css?family=Lato:300,400,600,700|Raleway:300,400,600,700&display=swap);

div#pp-$form_type-$form_id.dixon-wrapper * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

div#pp-$form_type-$form_id.dixon-wrapper {
    width: 100%;
    max-width: 800px;
	background: $bg_color;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-section-left {
    float:left;
    color:$profile_info_text;
}
div#pp-$form_type-$form_id.dixon-wrapper .dixon-section-right {
    margin-left:30%;
    color: $profile_info_text;
}
div#pp-$form_type-$form_id.dixon-wrapper .dixon-container {
    margin:auto;
    width:100%;
	padding-bottom: 10px;
	min-height: 240px;
}
div#pp-$form_type-$form_id.dixon-wrapper .dixon-section-left > span, 
div#pp-$form_type-$form_id.dixon-wrapper .dixon-section-right > span {
    padding-bottom:20px;
	margin:0;
	display: block;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-section-left > a, 
div#pp-$form_type-$form_id.dixon-wrapper .dixon-section-right > a {
    text-decoration:none;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-container h3 {
    margin:30px 0;
    font-family:Lato, sans-serif;
    color: $header_bg_color;
    font-size:28px;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-header {
    background-color: $header_bg_color;
    width:100%;
    height:60%;
	padding-bottom: 5px;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-aside {
    margin-top:30px;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-section {
    background:$profile_info_background;
    padding:40px 20px 10px;
    border-top:4px solid $info_border_color;
	margin: 0 10px;
}
div#pp-$form_type-$form_id.dixon-wrapper .dixon-img {
    padding-top:40px;
    position:relative;
    float:left;
	width: 50%;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-icon > ul > li img {
    box-shadow: none;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-text {
    text-align:center;
    font-size:30px;
    color: $header_text_color;
    padding-top:50px;
    line-height:40px;
    font-family:Raleway, sans-serif;
    text-transform:uppercase;
}


div#pp-$form_type-$form_id.dixon-wrapper div#pp-$form_type-$form_id.dixon-wrapper .dixon-icon ul li::before {
    content: none;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-icon li {
    display:inline-block;
    text-align:center;
}
div#pp-$form_type-$form_id.dixon-wrapper .dixon-icon {
    text-align:center;
    margin-top:15px;
}
div#pp-$form_type-$form_id.dixon-wrapper .dixon-text span {
    border-bottom:1px solid $text_underline_color;
    padding-bottom:4px;
}
div#pp-$form_type-$form_id.dixon-wrapper .dixon-aside img {
    width:190px;
    margin-top:-30px;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-section-right span a {
    color:#24758e;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-light {
    margin-left:10px;
}
div#pp-$form_type-$form_id.dixon-wrapper .dixon-img-left {
    margin-left:5px;
}
div#pp-$form_type-$form_id.dixon-wrapper .dixon-aside-text {
    border-bottom: 1px solid #949798;
    border-top: 1px solid #949798;
    padding:25px 0;
    margin-top: 50px;
}

div#pp-$form_type-$form_id.dixon-wrapper .dixon-avatar > img {
    border: 3px solid $avatar_border_color;
    border-radius: 50%;
    margin: 10px;
    width: 70%;
    height: auto;
    float: left;
}
CSS;

    }
}