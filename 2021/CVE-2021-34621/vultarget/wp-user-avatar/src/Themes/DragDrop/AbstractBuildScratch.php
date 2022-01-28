<?php

namespace ProfilePress\Core\Themes\DragDrop;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\FieldBase;
use ProfilePress\Core\Classes\FormRepository as FR;

abstract class AbstractBuildScratch extends AbstractTheme
{
    public function __construct($form_id, $form_type)
    {
        parent::__construct($form_id, $form_type);

        add_filter('ppress_form_builder_field_settings', [$this, 'add_field_properties'], 10, 2);

        add_filter('ppress_form_after_field_listing', [$this, 'add_field_icon'], 10, 2);
    }

    public function add_field_icon($output, $raw_field_setting)
    {
        if (isset($raw_field_setting['password_visibility_icon']) && $raw_field_setting['password_visibility_icon'] === true) {
            $raw_field_setting['icon'] = 'visibility';
        }

        if (isset($raw_field_setting['icon']) && ! empty($raw_field_setting['icon'])) {
            $output .= sprintf('<i class="pp-form-material-icons">%s</i>', $raw_field_setting['icon']);
        }

        return $output;
    }

    /**
     * @param $field
     * @param FieldBase $fieldBaseInstance
     *
     * @return mixed
     */
    public function add_field_properties($field, $fieldBaseInstance)
    {
        $field_type = $fieldBaseInstance->field_type();

        if ( ! in_array($fieldBaseInstance->field_type(), $this->disallowed_settings_fields())) {

            if (isset($field[FieldBase::GENERAL_TAB])) {

                $column_fields = [];

                if ( ! isset($field[FieldBase::GENERAL_TAB]['label'])) {
                    $column_fields['label'] = [
                        'label' => esc_html__('Label', 'wp-user-avatar'),
                        'field' => FieldBase::INPUT_FIELD,
                    ];
                } else {
                    $column_fields['label'] = $field[FieldBase::GENERAL_TAB]['label'];
                    unset($field[FieldBase::GENERAL_TAB]['label']);
                }

                if (isset($field[FieldBase::GENERAL_TAB]['placeholder'])) {
                    $column_fields['placeholder'] = $field[FieldBase::GENERAL_TAB]['placeholder'];
                    unset($field[FieldBase::GENERAL_TAB]['placeholder']);
                }

                $field[FieldBase::GENERAL_TAB] = array_merge(
                    [FieldBase::COLUMN_SETTINGS => $column_fields],
                    $field[FieldBase::GENERAL_TAB]
                );

                $field[FieldBase::GENERAL_TAB]['description'] = [
                    'label' => esc_html__('Description', 'wp-user-avatar'),
                    'field' => FieldBase::TEXTAREA_FIELD,
                ];
            }

            if (isset($field[FieldBase::STYLE_TAB])) {

                $class_settings = $field[FieldBase::STYLE_TAB]['class'];

                unset($field[FieldBase::STYLE_TAB]['class']);

                $field[FieldBase::STYLE_TAB][FieldBase::COLUMN_SETTINGS] = [];

                $field[FieldBase::STYLE_TAB][FieldBase::COLUMN_SETTINGS]['field_width'] = [
                    'label'   => esc_html__('Width', 'wp-user-avatar'),
                    'options' => [
                        'full'  => esc_html__('Full', 'wp-user-avatar'),
                        'half'  => esc_html__('Half', 'wp-user-avatar'),
                        'third' => esc_html__('One Third', 'wp-user-avatar'),
                    ],
                    'field'   => FieldBase::SELECT_FIELD,
                ];

                // the str_replace sorcery/ritual checks if field type does not contains any of the strings https://stackoverflow.com/a/42311760/2648410
                if (str_replace(['-select-role', '-select-dropdown', '-checkbox-list', '-radio-buttons', '-single-checkbox', '-country', '-cpf-agreeable', '-cpf-checkbox', '-cpf-radio', '-cpf-select'], '', $field_type) == $field_type) {
                    $field[FieldBase::STYLE_TAB][FieldBase::COLUMN_SETTINGS]['icon'] = [
                        'label' => esc_html__('Icon', 'wp-user-avatar'),
                        'field' => FieldBase::ICON_PICKER_FIELD,
                    ];
                }

                // if not password meter and password related field
                if (str_replace(['-password-meter'], '', $field_type) == $field_type && str_replace(['-password'], '', $field_type) != $field_type) {

                    $field[FieldBase::STYLE_TAB]['password_visibility_icon'] = [
                        'label'       => esc_html__('Enable Password Visibility Icon', 'wp-user-avatar'),
                        'description' => esc_html__('Check to enable visibility icon which when clicked, hides or shows a password.', 'wp-user-avatar'),
                        'type'        => 'checkbox',
                        'field'       => FieldBase::INPUT_FIELD,
                    ];
                }

                if (str_replace(['-checkbox', '-radio', '-country', '-select'], '', $field_type) == $field_type) {
                    $field[FieldBase::STYLE_TAB][FieldBase::COLUMN_2_SETTINGS]['label_display'] = [
                        'label'   => esc_html__('Label Display', 'wp-user-avatar'),
                        'options' => [
                            'above'  => esc_html__('Above', 'wp-user-avatar'),
                            'inside' => esc_html__('Inside', 'wp-user-avatar')
                        ],
                        'field'   => FieldBase::SELECT_FIELD,
                    ];
                }

                $field[FieldBase::STYLE_TAB][FieldBase::COLUMN_2_SETTINGS]['description_appearance'] = [
                    'label'   => esc_html__('Description Appearance', 'wp-user-avatar'),
                    'options' => [
                        'standard' => esc_html__('Standard', 'wp-user-avatar'),
                        'reveal'   => esc_html__('Reveal on Focus', 'wp-user-avatar'),
                        'tooltip'  => esc_html__('Tooltip', 'wp-user-avatar'),
                    ],
                    'field'   => FieldBase::SELECT_FIELD,
                ];

                $field[FieldBase::STYLE_TAB]['class'] = $class_settings;
            }
        }

        return $field;
    }

    public function social_login_buttons()
    {
        $html = '';

        if ( ! $this->is_show_social_login()) return $html;

        $active_social_logins = array_filter($this->get_meta('buildscratch_form_social_buttons'));

        foreach ($active_social_logins as $active_social_login) {
            $html .= "[pp-social-button type=$active_social_login]";
        }

        return $html;
    }

    public function default_metabox_settings()
    {
        $default_headline = esc_html__('Create an Account', 'wp-user-avatar');

        switch ($this->form_type) {
            case FR::LOGIN_TYPE:
                $default_headline = esc_html__('Sign in to Your Account', 'wp-user-avatar');
                break;
            case FR::PASSWORD_RESET_TYPE:
                $default_headline = esc_html__('Reset Your Password', 'wp-user-avatar');
                break;
            case FR::EDIT_PROFILE_TYPE:
                $default_headline = esc_html__('Edit Your Profile', 'wp-user-avatar');
                break;
        }

        $data                                        = parent::default_metabox_settings();
        $data['buildscratch_form_width']             = '400px';
        $data['buildscratch_form_font_family']       = 'Merriweather';
        $data['buildscratch_remove_form_frame']      = 'false';
        $data['buildscratch_hide_required_asterisk'] = 'false';
        $data['buildscratch_form_bg_color']          = '#ffffff';
        $data['buildscratch_form_social_buttons']    = ['facebook', 'twitter', 'google'];
        $data['buildscratch_form_headline']          = $default_headline;
        $data['buildscratch_forgot_password_label']  = esc_html__('Lost your password?', 'wp-user-avatar');
        $data['buildscratch_signup_label']           = esc_html__('Register', 'wp-user-avatar');
        $data['buildscratch_login_label']            = esc_html__('Login', 'wp-user-avatar');
        if ($this->form_type == FR::REGISTRATION_TYPE) {
            $data['buildscratch_login_label'] = esc_html__("Have an account? Login", 'wp-user-avatar');
        }

        if ($this->form_type == FR::PASSWORD_RESET_TYPE) {
            $data['buildscratch_login_label'] = esc_html__("Return to Login", 'wp-user-avatar');
        }

        $data['buildscratch_field_layout']               = 'round';
        $data['buildscratch_label_field_size']           = 'small';
        $data['buildscratch_label_field_icon_alignment'] = 'right';
        $data['buildscratch_field_icon_color']           = '#666666';
        $data['buildscratch_field_border_color']         = '#dbdbdb';
        $data['buildscratch_field_border_focus_color']   = '#999999';
        $data['buildscratch_field_bg_color']             = '#ffffff';
        $data['buildscratch_field_bg_focus_color']       = '#ffffff';

        $data['buildscratch_label_color']             = '#444444';
        $data['buildscratch_label_font_size']         = '14';
        $data['buildscratch_label_font_weight']       = 'bold';
        $data['buildscratch_description_color']       = '#666666';
        $data['buildscratch_description_alignment']   = 'left';
        $data['buildscratch_field_value_font_size']   = '14';
        $data['buildscratch_field_value_color']       = '#69717a';
        $data['buildscratch_field_placeholder_color'] = '#999999';

        $data['buildscratch_submit_button_layout']           = 'round';
        $data['buildscratch_submit_button_width']            = 'auto';
        $data['buildscratch_submit_button_font_size']        = '16';
        $data['buildscratch_submit_button_font_weight']      = 'bold';
        $data['buildscratch_submit_button_bg_color']         = '#000000';
        $data['buildscratch_submit_button_text_color']       = '#ffffff';
        $data['buildscratch_submit_button_bg_focus_color']   = '#dbdbdb';
        $data['buildscratch_submit_button_text_focus_color'] = '#000000';

        return $data;
    }

    public function appearance_settings($settings)
    {
        $settings[] = [
            'id'       => 'buildscratch_form_width',
            'type'     => 'text',
            'label'    => esc_html__('Width', 'wp-user-avatar'),
            'priority' => 10
        ];

        $settings[] = [
            'id'       => 'buildscratch_form_bg_color',
            'type'     => 'color',
            'label'    => esc_html__('Background', 'wp-user-avatar'),
            'priority' => 20
        ];

        $settings[] = [
            'id'       => 'buildscratch_form_font_family',
            'type'     => 'font_family',
            'label'    => esc_html__('Font Family', 'wp-user-avatar'),
            'priority' => 30
        ];

        $settings[] = [
            'id'       => 'buildscratch_form_headline',
            'type'     => 'text',
            'label'    => esc_html__('Headline', 'wp-user-avatar'),
            'priority' => 35
        ];

        if (in_array($this->form_type, [FR::LOGIN_TYPE])) {
            $settings[] = [
                'id'       => 'buildscratch_forgot_password_label',
                'type'     => 'text',
                'label'    => esc_html__('Forgot Password Label', 'wp-user-avatar'),
                'priority' => 37
            ];
        }


        if (in_array($this->form_type, [FR::LOGIN_TYPE])) {
            $settings[] = [
                'id'       => 'buildscratch_signup_label',
                'type'     => 'text',
                'label'    => esc_html__('Sign Up Label', 'wp-user-avatar'),
                'priority' => 39
            ];
        }


        if (in_array($this->form_type, [FR::REGISTRATION_TYPE, FR::PASSWORD_RESET_TYPE])) {
            $settings[] = [
                'id'       => 'buildscratch_login_label',
                'type'     => 'text',
                'label'    => esc_html__('Login Label', 'wp-user-avatar'),
                'priority' => 39
            ];
        }

        if ($this->is_show_social_login()) {

            $settings[] = [
                'id'       => 'buildscratch_form_social_buttons',
                'type'     => 'select2',
                'options'  => [
                    'facebook' => esc_html__('Facebook', 'wp-user-avatar'),
                    'twitter'  => esc_html__('Twitter', 'wp-user-avatar'),
                    'google'   => esc_html__('Google', 'wp-user-avatar'),
                    'linkedin' => esc_html__('LinkedIn', 'wp-user-avatar'),
                    'github'   => esc_html__('GitHub', 'wp-user-avatar'),
                    'vk'       => esc_html__('VK.com', 'wp-user-avatar')
                ],
                'label'    => esc_html__('Social Login Buttons', 'wp-user-avatar'),
                'priority' => 40
            ];
        }

        $settings[] = [
            'id'             => 'buildscratch_remove_form_frame',
            'type'           => 'checkbox',
            'label'          => esc_html__('Remove Form Frame', 'wp-user-avatar'),
            'checkbox_label' => esc_html__('Check to remove'),
            'priority'       => 50
        ];

        $settings[] = [
            'id'             => 'buildscratch_hide_required_asterisk',
            'type'           => 'checkbox',
            'label'          => sprintf(esc_html__('Hide Required %s'), '<span style="color:red">*</span>'),
            'checkbox_label' => esc_html__('Check to hide', 'wp-user-avatar'),
            'priority'       => 60
        ];

        return $settings;
    }

    public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
    {
        $submit_button_settings = $settings['submit_button'];
        unset($settings['submit_button']);

        $settings['buildscratch_field_styling'] = [
            'tab_title' => esc_html__('Field & Styling', 'wp-user-avatar'),
            [
                'id'              => 'buildscratch_field_layout',
                'type'            => 'tab_radio',
                'options'         => [
                    'round'    => esc_html__('Round', 'wp-user-avatar'),
                    'square'   => esc_html__('Square', 'wp-user-avatar'),
                    'pill'     => esc_html__('Pill', 'wp-user-avatar'),
                    'material' => esc_html__('Material', 'wp-user-avatar'),
                    'flat'     => esc_html__('Flat', 'wp-user-avatar'),
                ],
                'tab_description' => $this->field_layout_description(),
                'label'           => esc_html__('Layout', 'wp-user-avatar'),
                'priority'        => 20
            ],
            [
                'id'          => 'buildscratch_label_field_size',
                'type'        => 'select',
                'options'     => [
                    'small'  => esc_html__('Small', 'wp-user-avatar'),
                    'medium' => esc_html__('Medium', 'wp-user-avatar'),
                    'large'  => esc_html__('Large', 'wp-user-avatar'),
                ],
                'label'       => esc_html__('Field Size', 'wp-user-avatar'),
                'description' => esc_html__('Select the size (in height) of input and textarea form fields.', 'wp-user-avatar')
            ],
            [
                'id'    => 'buildscratch_field_icon_color',
                'type'  => 'color',
                'label' => esc_html__('Icon', 'wp-user-avatar')
            ],
            [
                'id'      => 'buildscratch_label_field_icon_alignment',
                'type'    => 'tab_radio',
                'options' => [
                    'left'  => esc_html__('Left', 'wp-user-avatar'),
                    'right' => esc_html__('Right', 'wp-user-avatar'),
                ],
                'label'   => esc_html__('Icon Alignment', 'wp-user-avatar')
            ],
            [
                'id'    => 'buildscratch_field_border_color',
                'type'  => 'color',
                'label' => esc_html__('Border', 'wp-user-avatar')
            ],
            [
                'id'    => 'buildscratch_field_border_focus_color',
                'type'  => 'color',
                'label' => esc_html__('Border on Focus', 'wp-user-avatar')
            ],
            [
                'id'    => 'buildscratch_field_bg_color',
                'type'  => 'color',
                'label' => esc_html__('Background', 'wp-user-avatar')
            ],
            [
                'id'    => 'buildscratch_field_bg_focus_color',
                'type'  => 'color',
                'label' => esc_html__('Background on Focus', 'wp-user-avatar')
            ]
        ];

        $settings['buildscratch_labels_text'] = [
            // calling label title here in case in future when there is membership and the heading will have the same styling as label
            // and any additional text will have same styling as description.
            'tab_title' => esc_html__('Labels & Text', 'wp-user-avatar'),
            [
                'id'    => 'buildscratch_label_color',
                'type'  => 'color',
                'label' => esc_html__('Title', 'wp-user-avatar')
            ],
            [
                'id'    => 'buildscratch_label_font_size',
                'type'  => 'number',
                'label' => esc_html__('Title Font Size (px)', 'wp-user-avatar')
            ],
            [
                'id'      => 'buildscratch_label_font_weight',
                'type'    => 'select',
                'options' => [
                    'normal' => esc_html__('Normal', 'wp-user-avatar'),
                    'bold'   => esc_html__('Bold', 'wp-user-avatar'),
                ],
                'label'   => esc_html__('Title Font Weight', 'wp-user-avatar')
            ],
            [
                'id'    => 'buildscratch_description_color',
                'type'  => 'color',
                'label' => esc_html__('Description', 'wp-user-avatar')
            ],
            [
                'id'      => 'buildscratch_description_alignment',
                'type'    => 'select',
                'options' => [
                    'left'   => esc_html__('Left', 'wp-user-avatar'),
                    'center' => esc_html__('Center', 'wp-user-avatar'),
                    'right'  => esc_html__('Right', 'wp-user-avatar'),
                ],
                'label'   => esc_html__('Description Alignment', 'wp-user-avatar')
            ],
            [
                'id'          => 'buildscratch_field_value_font_size',
                'type'        => 'number',
                'label'       => esc_html__('Value / Text Font Size', 'wp-user-avatar'),
                'description' => esc_html__('Font size in pixel (px) of field values and text on form.')
            ],
            [
                'id'          => 'buildscratch_field_value_color',
                'type'        => 'color',
                'label'       => esc_html__('Value / Text', 'wp-user-avatar'),
                'description' => esc_html__('Color of the value of a field and text on the form.')
            ],
            [
                'id'    => 'buildscratch_field_placeholder_color',
                'type'  => 'color',
                'label' => esc_html__('Placeholder', 'wp-user-avatar')
            ],
        ];

        $settings['submit_button'] = $submit_button_settings;

        return $settings;
    }

    public function field_layout_description()
    {
        $placeholder = esc_html__('Name', 'wp-user-avatar');
        ob_start();
        ?>
        <div class="ppmb-tab"><input type="text" placeholder="<?= $placeholder ?>" class="ppfl ppfl-round" readonly>
        </div>
        <div class="ppmb-tab"><input type="text" placeholder="<?= $placeholder ?>" class="ppfl ppfl-square" readonly>
        </div>
        <div class="ppmb-tab"><input type="text" placeholder="<?= $placeholder ?>" class="ppfl ppfl-pill" readonly>
        </div>
        <div class="ppmb-tab"><input type="text" placeholder="<?= $placeholder ?>" class="ppfl ppfl-material" readonly>
        </div>
        <div class="ppmb-tab"><input type="text" placeholder="<?= $placeholder ?>" class="ppfl ppfl-flat" readonly>
        </div>
        <?php
        return ob_get_clean();
    }

    public function submit_button_layout_description()
    {
        $label = esc_html__('Submit', 'wp-user-avatar');
        ob_start();
        ?>
        <div class="ppmb-tab"><input type="button" value="<?= $label ?>" class="ppfl ppfl-round" disabled=""></div>
        <div class="ppmb-tab"><input type="button" value="<?= $label ?>" class="ppfl ppfl-square" disabled></div>
        <div class="ppmb-tab"><input type="button" value="<?= $label ?>" class="ppfl ppfl-pill" disabled></div>
        <div class="ppmb-tab"><input type="button" value="<?= $label ?>" class="ppfl ppfl-flat" disabled></div>
        <?php
        return ob_get_clean();
    }

    public function submit_button_settings($settings)
    {
        $settings[] = [
            'id'              => 'buildscratch_submit_button_layout',
            'type'            => 'tab_radio',
            'options'         => [
                'round'  => esc_html__('Round', 'wp-user-avatar'),
                'square' => esc_html__('Square', 'wp-user-avatar'),
                'pill'   => esc_html__('Pill', 'wp-user-avatar')
            ],
            'tab_description' => $this->submit_button_layout_description(),
            'label'           => esc_html__('Layout', 'wp-user-avatar')
        ];

        $settings[] = [
            'id'      => 'buildscratch_submit_button_width',
            'type'    => 'select',
            'options' => [
                'auto'       => esc_html__('Auto (Default)', 'wp-user-avatar'),
                'wide'       => esc_html__('Wide', 'wp-user-avatar'),
                'full-width' => esc_html__('Full Stretched Width', 'wp-user-avatar')
            ],
            'label'   => esc_html__('Width', 'wp-user-avatar'),
        ];

        $settings[] = [
            'id'    => 'buildscratch_submit_button_font_size',
            'type'  => 'number',
            'label' => esc_html__('Font Size (px)', 'wp-user-avatar')
        ];

        $settings[] = [
            'id'      => 'buildscratch_submit_button_font_weight',
            'type'    => 'select',
            'options' => [
                'normal' => esc_html__('Normal', 'wp-user-avatar'),
                'bold'   => esc_html__('Bold', 'wp-user-avatar'),
            ],
            'label'   => esc_html__('Font Weight', 'wp-user-avatar')
        ];

        $settings[] = [
            'id'    => 'buildscratch_submit_button_bg_color',
            'type'  => 'color',
            'label' => esc_html__('Background', 'wp-user-avatar')
        ];

        $settings[] = [
            'id'    => 'buildscratch_submit_button_bg_focus_color',
            'type'  => 'color',
            'label' => esc_html__('Background on Focus', 'wp-user-avatar')
        ];

        $settings[] = [
            'id'    => 'buildscratch_submit_button_text_color',
            'type'  => 'color',
            'label' => esc_html__('Text', 'wp-user-avatar')
        ];

        $settings[] = [
            'id'    => 'buildscratch_submit_button_text_focus_color',
            'type'  => 'color',
            'label' => esc_html__('Text Focused', 'wp-user-avatar')
        ];

        return $settings;
    }

    public function form_structure()
    {
        $headline       = '';
        $saved_headline = $this->get_meta('buildscratch_form_headline');
        if ( ! empty($saved_headline)) {
            $headline = '<div class="ppbs-headline">' . $saved_headline . '</div>';
        }

        $social_login_buttons = $this->social_login_buttons();

        $fields = (new FieldListing($this->form_id, $this->form_type, true))
            ->defaults($this->default_fields_settings())
            ->shortcode_field_wrap_start('<div class="pp-form-field-input-textarea-wrap">')
            ->shortcode_field_wrap_end('</div>')
            ->forge();

        $button = $this->form_submit_button();

        $fl  = 'ppfl-' . $this->get_meta('buildscratch_field_layout');
        $rf  = $this->get_meta('buildscratch_remove_form_frame') == 'true' ? ' ppf-remove-frame' : '';
        $hra = $this->get_meta('buildscratch_hide_required_asterisk') == 'true' ? ' ppf-hide-asterisk' : '';

        $fs  = ' ppfs-' . $this->get_meta('buildscratch_label_field_size');
        $fia = ' ppfia-' . $this->get_meta('buildscratch_label_field_icon_alignment');

        $sbl = ' ppsbl-' . $this->get_meta('buildscratch_submit_button_layout');
        $sbw = ' ppsbw-' . $this->get_meta('buildscratch_submit_button_width');

        $form_link = $this->form_links();

        return <<<HTML
[pp-form-wrapper class="ppBuildScratch {$fl}{$sbl}{$sbw}{$rf}{$hra}{$fs}{$fia}"]
$headline
$social_login_buttons
$fields
<div class="pp-form-submit-button-wrap">
$button
</div>
[/pp-form-wrapper]
$form_link
HTML;
    }

    public function form_links()
    {
        $forgot_password_link  = ppress_password_reset_url();
        $signup_link           = ppress_registration_url();
        $login_link            = ppress_login_url();
        $forgot_password_label = $this->get_meta('buildscratch_forgot_password_label');
        $signup_label          = $this->get_meta('buildscratch_signup_label');
        $login_label           = $this->get_meta('buildscratch_login_label');

        $login_form_link = <<<HTML
<div class="ppress-form-bottom-links">
    <a href="$forgot_password_link">$forgot_password_label</a> | <a href="$signup_link">$signup_label</a>
</div>
HTML;

        $signup_form_link = <<<HTML
<div class="ppress-form-bottom-links">
    <a href="$login_link">$login_label</a>
</div>
HTML;

        $password_reset_form_link = <<<HTML
<div class="ppress-form-bottom-links">
    <a href="$login_link">$login_label</a>
</div>
HTML;

        if ($this->form_type == FR::LOGIN_TYPE) return $login_form_link;
        if ($this->form_type == FR::REGISTRATION_TYPE) return $signup_form_link;
        if ($this->form_type == FR::PASSWORD_RESET_TYPE) return $password_reset_form_link;

        return '';

    }

    public function form_css()
    {
        $form_id   = $this->form_id;
        $form_type = $this->form_type;

        $width                          = $this->get_meta('buildscratch_form_width');
        $form_bg_color                  = $this->get_meta('buildscratch_form_bg_color');
        $form_font_family               = $this->get_meta('buildscratch_form_font_family');
        $form_font_family_plus_to_space = str_replace('+', ' ', $form_font_family);
        $value_font_size                = $this->get_meta('buildscratch_field_value_font_size');
        $field_icon_color               = $this->get_meta('buildscratch_field_icon_color');
        $field_border_color             = $this->get_meta('buildscratch_field_border_color');
        $field_border_focus_color       = $this->get_meta('buildscratch_field_border_focus_color');
        $field_bg_color                 = $this->get_meta('buildscratch_field_bg_color');
        $field_bg_focus_color           = $this->get_meta('buildscratch_field_bg_focus_color');
        $field_value_color              = $this->get_meta('buildscratch_field_value_color');
        $field_placeholder_color        = $this->get_meta('buildscratch_field_placeholder_color');
        $field_label_color              = $this->get_meta('buildscratch_label_color');
        $field_label_font_size          = $this->get_meta('buildscratch_label_font_size');
        $field_label_font_weight        = $this->get_meta('buildscratch_label_font_weight');

        $description_color     = $this->get_meta('buildscratch_description_color');
        $description_alignment = $this->get_meta('buildscratch_description_alignment');

        $submit_button_font_size        = $this->get_meta('buildscratch_submit_button_font_size');
        $submit_button_font_weight      = $this->get_meta('buildscratch_submit_button_font_weight');
        $submit_button_bg_color         = $this->get_meta('buildscratch_submit_button_bg_color');
        $submit_button_bg_focus_color   = $this->get_meta('buildscratch_submit_button_bg_focus_color');
        $submit_button_text_color       = $this->get_meta('buildscratch_submit_button_text_color');
        $submit_button_text_focus_color = $this->get_meta('buildscratch_submit_button_text_focus_color');

        $status_class = '.profilepress-reg-status';
        switch ($this->form_type) {
            case FR::LOGIN_TYPE :
                $status_class = '.profilepress-login-status';
                break;
            case FR::PASSWORD_RESET_TYPE :
                $status_class = '.profilepress-reset-status';
                break;
            case FR::EDIT_PROFILE_TYPE :
                $status_class = '.profilepress-edit-profile-status';
                break;
        }

        return <<<CSS
@import url('https://fonts.googleapis.com/css?family={$form_font_family}:300,400,600,700&display=swap');

#pp-$form_type-$form_id-wrap $status_class {
  border-radius: 5px;
  font-size: 16px;
  line-height: 1.471;
  padding: 10px;
  background-color: #e74c3c;
  color: #ffffff;
  font-weight: normal;
  text-align: center;
  vertical-align: middle;
  margin: 10px 0;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  max-width: 100%;
}

#pp-$form_type-$form_id-wrap $status_class.success {
  background-color: #2ecc71;
  color: #ffffff;
}

#pp-$form_type-$form_id-wrap $status_class a {
    color: #fff;
    text-decoration: underline;
}

/* settings CSS */
#pp-$form_type-$form_id-wrap.pp-form-container {
    max-width: $width !important;
    width: 100%;
    margin: 0 auto;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch {
    background: $form_bg_color;
    color: $field_value_color;
    font-size: {$value_font_size}px;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap .pp-form-field-input-textarea-wrap {
    position: relative;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch * {
    font-family: '$form_font_family_plus_to_space',-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap input,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap select,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap textarea {
    font-size: {$value_font_size}px;
    border-color: $field_border_color;
    background: $field_bg_color;
    color :$field_value_color; 
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap .pp-form-material-icons {
    color: $field_icon_color;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-checkbox-wrap label,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-radio-wrap label {
    font-size: {$value_font_size}px;
    color :$field_value_color;
    vertical-align: middle;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap input[type=text]:focus,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap select:focus,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap textarea:focus {
    outline: 0;
    background: $field_bg_focus_color;
    border-color: $field_border_focus_color;
    box-shadow: 0 0 0 1px $field_border_focus_color;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap ::placeholder{
  color: $field_placeholder_color;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap ::-webkit-input-placeholder{
  color: $field_placeholder_color;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap ::-moz-placeholder{
  color: $field_placeholder_color;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap :-moz-placeholder{
  color: $field_placeholder_color;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap :-ms-input-placeholder{
  color: $field_placeholder_color;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .ppbs-headline,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch h1,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch h2,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch h3,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch {
    color: $field_label_color;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-wrap .pp-form-label-wrap .pp-form-label {
    color: $field_label_color;
    font-size: {$field_label_font_size}px;
    font-weight: $field_label_font_weight;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-field-description {
    color: $description_color;
    text-align: $description_alignment;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-submit-button-wrap input[type="submit"] {
	font-size: {$submit_button_font_size}px;
	font-weight: $submit_button_font_weight;
	background: $submit_button_bg_color;
	color: $submit_button_text_color;
}

.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-submit-button-wrap input[type="submit"]:hover,
.pp-form-container #pp-$form_type-$form_id.ppBuildScratch .pp-form-submit-button-wrap input[type="submit"]:focus {
	background: $submit_button_bg_focus_color;
	color: $submit_button_text_focus_color;
}

.pp-form-container div#pp-$form_type-$form_id.ppBuildScratch a.pp-button-social-login {
    margin-right: 6.387%;
}

.pp-form-container div#pp-$form_type-$form_id.ppBuildScratch a.pp-button-social-login {
    display: block;
    height: 3em;
    line-height: 3em;
    text-decoration: none;
    margin-bottom: 10px;
}

.pp-form-container div#pp-$form_type-$form_id.ppBuildScratch a.pp-button-social-login .ppsc {
    width: 3em;
    height: 3em;
}

.pp-form-container div#pp-$form_type-$form_id.ppBuildScratch a.pp-button-social-login span.ppsc-text {
    margin-left: 50px;
}

.pp-form-container div#pp-registration-12.ppBuildScratch a.pp-button-social-login:last-of-type {
    margin-bottom: 20px;
}

.pp-form-container .ppress-form-bottom-links {
    padding-top: 15px;
    padding-bottom: 15px;
    text-align: center;
}

.pp-form-container .ppress-form-bottom-links a {
        line-height: 22px;
        font-size: 14px;
        color: $field_value_color !important;
        display: inline-block;
        text-decoration: none!important;
        font-weight: 400;
        text-align: center;
        border-bottom: none!important;
}

.pp-form-container .ppress-form-bottom-links a:hover {
    text-decoration: underline!important;
}
CSS;

    }
}