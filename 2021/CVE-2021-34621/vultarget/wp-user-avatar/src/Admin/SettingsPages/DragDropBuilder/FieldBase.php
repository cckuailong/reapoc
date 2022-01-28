<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder;

use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls\IconPicker;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls\Input;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls\Select;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls\Textarea;
use ProfilePress\Core\Admin\SettingsPages\DragDropBuilder\Controls\WPEditor;
use ProfilePress\Core\Classes\FormRepository;

abstract class FieldBase implements FieldInterface
{
    const STANDARD_CATEGORY = 'standard';
    const EXTRA_CATEGORY = 'extra';

    const INPUT_FIELD = 'input';
    const WPEDITOR_FIELD = 'wpeditor';
    const SELECT_FIELD = 'select';
    const TEXTAREA_FIELD = 'textarea';
    const ICON_PICKER_FIELD = 'icon_picker';

    const GENERAL_TAB = 'pp_tab_general';
    const SETTINGS_TAB = 'pp_tab_settings';
    const STYLE_TAB = 'pp_style_settings';
    const COLUMN_SETTINGS = 'pp_column_settings';
    const COLUMN_2_SETTINGS = 'pp_column_2_settings';

    public $tag_name;

    public $form_id;
    public $form_type;
    public $form_class;

    public function __construct()
    {
        $this->form_id    = isset($_GET['id']) ? absint($_GET['id']) : 0;
        $this->form_type  = sanitize_text_field($_GET['form-type']);
        $this->form_class = FormRepository::get_form_meta($this->form_id, $this->form_type, FormRepository::FORM_CLASS);

        $this->register_field();

        $this->tag_name = $this->form_type == FormRepository::REGISTRATION_TYPE ? 'reg' : 'edit-profile';

        add_action('admin_footer', [$this, 'print_template']);
    }

    public function field_bar_title()
    {
        return $this->field_title();
    }

    public function category()
    {
        return self::STANDARD_CATEGORY;
    }

    public function register_field()
    {
        $defaults             = [];
        $theme_class_instance = FormRepository::forge_class($this->form_id, $this->form_class, $this->form_type);
        if ($theme_class_instance) {
            $defaults = call_user_func([FormRepository::forge_class($this->form_id, $this->form_class, $this->form_type), 'default_fields_settings']);
        }

        $filter = 'ppress_form_builder_' . $this->category() . '_fields';

        add_filter($filter, function ($fields) use ($defaults) {
            $fields[$this->field_type()] = [
                'fieldType'     => $this->field_type(),
                'fieldTitle'    => $this->field_title(),
                'fieldBarTitle' => $this->field_bar_title(),
                'fieldIcon'     => $this->field_icon()
            ];

            if (isset($defaults[$this->field_type()])) {
                $fields[$this->field_type()] = wp_parse_args($fields[$this->field_type()], $defaults[$this->field_type()]);
            }

            return $fields;
        });
    }

    public function field_settings_tabs()
    {
        return [
            self::GENERAL_TAB  => esc_html__('General', 'wp-user-avatar'),
            self::STYLE_TAB    => esc_html__('Style', 'wp-user-avatar'),
            self::SETTINGS_TAB => esc_html__('Settings', 'wp-user-avatar'),
        ];
    }

    public function print_template()
    {
        if ( ! ppress_is_admin_page() || $_GET['view'] != 'drag-drop-builder') return;

        $this->field_settings_modal_tmpl();

        $this->field_bar_tmpl();
    }

    public function field_settings_modal_tmpl()
    {
        $tabs           = $this->field_settings_tabs();
        $field_settings = apply_filters('ppress_form_builder_field_settings', $this->field_settings(), $this);

        ?>
        <script type="text/html" id="tmpl-pp-form-builder-popup-settings-<?= $this->field_type() ?>">
            <div class="pp-form-buider-settings-popup-content">
                <div class="pp-form-buider-settings-popup-header">
                    <h3><?php _e('Edit Field', 'wp-user-avatar'); ?></h3>
                    <div class="pp-actions-left">
                        <span class="pp-form-buider-settings-field-type">{{ data.fieldTitle }}</span>
                    </div>

                    <div class="pp-actions-right">
                        <span class="pp-form-buider-settings-field-close-btn pp-form-cancel-btn">&times;</span>
                    </div>
                </div>
                <div class="pp-form-buider-settings-popup-body">
                    <div class="pp-form-buider-settings-popup-tabs">
                        <?php foreach ($tabs as $tab_id => $tab_title) {
                            if ( ! empty($field_settings[$tab_id])) {
                                ?>
                                <a href="#<?= $tab_id ?>" class="pp-form-buider-settings-popup-tab-menu"><?= $tab_title ?></a>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <?php
                    // we are re-arranging this because it is necessary for the modal field settings tab menu to work.
                    $ordered_field_settings = [];
                    if (isset($field_settings[self::GENERAL_TAB])) {
                        $ordered_field_settings[self::GENERAL_TAB] = $field_settings[self::GENERAL_TAB];
                    }
                    if (isset($field_settings[self::STYLE_TAB])) {
                        $ordered_field_settings[self::STYLE_TAB] = $field_settings[self::STYLE_TAB];
                    }
                    if (isset($field_settings[self::SETTINGS_TAB])) {
                        $ordered_field_settings[self::SETTINGS_TAB] = $field_settings[self::SETTINGS_TAB];
                    }

                    foreach ($ordered_field_settings as $tab_id => $fields) {
                        printf('<div id="%s" class="pp-form-buider-settings-popup-tab-content">', $tab_id);
                        foreach ($fields as $field_id => $settings) {

                            if (in_array($field_id, [self::COLUMN_SETTINGS, self::COLUMN_2_SETTINGS])) { ?>
                                <div class="pp-form-row">
                                    <?php foreach ($settings as $key => $settingz) : ?>
                                        <div class="pp-form-column">
                                            <div class="pp-form-builder-settings-field">
                                                <?php
                                                $settingz['name'] = $key;
                                                if (isset($settingz['field'])) {
                                                    switch ($settingz['field']) {
                                                        case self::INPUT_FIELD :
                                                            (new Input($settingz))->render();
                                                            break;
                                                        case self::WPEDITOR_FIELD :
                                                            (new WPEditor($settingz))->render();
                                                            break;
                                                        case self::TEXTAREA_FIELD :
                                                            (new Textarea($settingz))->render();
                                                            break;
                                                        case self::SELECT_FIELD :
                                                            (new Select($settingz))->render();
                                                            break;
                                                        case self::ICON_PICKER_FIELD :
                                                            (new IconPicker($settingz))->render();
                                                            break;
                                                    }
                                                } ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php } else { ?>
                                <div class="pp-form-row">
                                    <div class="pp-form-column">
                                        <div class="pp-form-builder-settings-field">
                                            <?php $settings['name'] = $field_id;
                                            switch ($settings['field']) {
                                                case self::INPUT_FIELD :
                                                    (new Input($settings))->render();
                                                    break;
                                                case self::WPEDITOR_FIELD :
                                                    (new WPEditor($settings))->render();
                                                    break;
                                                case self::TEXTAREA_FIELD :
                                                    (new Textarea($settings))->render();
                                                    break;
                                                case self::SELECT_FIELD :
                                                    (new Select($settings))->render();
                                                    break;
                                                case self::ICON_PICKER_FIELD :
                                                    (new IconPicker($settings))->render();
                                                    break;
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        }
                        echo '</div>';
                    } ?>
                </div>
                <div class="pp-form-buider-settings-popup-footer">
                    <a href="#" class="pp-form-cancel-btn button"><?= esc_html__('Cancel', 'wp-user-avatar') ?></a>

                    <div class="pp-actions-right">
                        <a href="#" class="pp-form-save-btn button-primary"><?= esc_html__('Apply Changes', 'wp-user-avatar') ?></a>
                    </div>
                </div>
            </div>
        </script>
        <?php
    }

    public function field_bar_tmpl()
    {
        ?>
        <script type="text/html" id="tmpl-pp-form-builder-field-bar">
            <# var fieldBarTitle = typeof data.fieldBarTitle !== 'undefined' && data.fieldBarTitle != '' ? data.fieldBarTitle : data.fieldTitle; #>
            <div class="pp-builder-element-header">
                <# if( typeof data.fieldIcon !== 'undefined' ) { #>
                <span class="pp-builder-element-icon">{{{ data.fieldIcon }}}</span>
                <# } #>
                <span class="pp-builder-element-header-title">{{{fieldBarTitle}}}</span>
                <span class="pp-form-buider-settings-field-type field-bar-title">{{ data.fieldTitle }}</span>
                <a href="#" class="pp-builder-element-expand-button pp-settings" title="<?php _e('Settings', 'wp-user-avatar'); ?>">
                    <span class="dashicons dashicons-admin-generic"></span>
                </a>
                <a href="#" class="pp-builder-element-expand-button pp-delete expand-btn-hide" title="<?php _e('Delete', 'wp-user-avatar'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </a>
                <a href="#" class="pp-builder-element-expand-button pp-clone expand-btn-hide" title="<?php _e('Clone', 'wp-user-avatar'); ?>">
                    <span class="dashicons dashicons-admin-page"></span>
                </a>
            </div>
        </script>
        <?php
    }
}