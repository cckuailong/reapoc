<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder;


use ProfilePress\Core\Classes\FormRepository as FR;
use ProfilePress\Core\Themes\DragDrop\AbstractTheme;

class Metabox
{
    protected $args;
    protected $saved_values;
    protected $theme_class_instance;
    /** @var DragDropBuilder */
    protected $DragDropBuilderInstance;

    /**
     * @param array $args
     * @param AbstractTheme $theme_class_instance
     * @param DragDropBuilder $DragDropBuilderInstance
     */
    public function __construct($args, $theme_class_instance, $DragDropBuilderInstance)
    {
        $saved_db_values = FR::get_form_meta($DragDropBuilderInstance->form_id, $DragDropBuilderInstance->form_type, FR::METABOX_FORM_BUILDER_SETTINGS);

        $this->args                    = $args;
        $this->saved_values            = $saved_db_values;
        $this->theme_class_instance    = $theme_class_instance;
        $this->DragDropBuilderInstance = $DragDropBuilderInstance;
    }

    private function default_values()
    {
        return call_user_func([$this->theme_class_instance, 'default_metabox_settings']);
    }

    private function saved_values_bucket($key)
    {
        $defaults = $this->default_values();

        $constants = apply_filters(
            'ppress_form_builder_metabox_field_as_form_meta',
            array_values((new \ReflectionClass('ProfilePress\Core\Classes\FormRepository'))->getConstants())
        );

        if (in_array($key, $constants)) {
            $val = FR::get_form_meta(
                $this->DragDropBuilderInstance->form_id,
                $this->DragDropBuilderInstance->form_type,
                $key
            );

            if ( ! $val || empty($val)) {
                $val = isset($defaults[$key]) ? $defaults[$key] : '';
            }

            return $val;
        }

        return isset($this->saved_values[$key]) ? $this->saved_values[$key] : ppress_var($defaults, $key);
    }

    public function text($name, $options)
    {
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : '';
        printf(
            '<input type="text" class="short" name="%1$s" id="%1$s" value="%3$s" placeholder="%2$s">',
            esc_attr($name), esc_attr($placeholder), $this->saved_values_bucket($name)
        );
    }

    public function number($name, $options)
    {
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : '';
        printf(
            '<input type="number" class="short" name="%1$s" id="%1$s" value="%3$s" placeholder="%2$s">',
            esc_attr($name), esc_attr($placeholder), $this->saved_values_bucket($name)
        );
    }

    public function color($name, $options)
    {
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : '';

        printf(
            '<input type="text" class="short pp-color-field" name="%1$s" id="%1$s" value="%3$s" placeholder="%2$s" data-default-color="%4$s">',
            esc_attr($name), esc_attr($placeholder), $this->saved_values_bucket($name), $this->default_values()[$name]
        );
    }

    public function upload($name, $options)
    {
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : '';
        echo '<div class="pp_upload_field_container">';
        printf(
            '<input type="text" class="pp_upload_field short large-text" name="%1$s" id="%1$s" value="%3$s" placeholder="%2$s">',
            esc_attr($name), esc_attr($placeholder), $this->saved_values_bucket($name)
        );
        printf('<span class="pp_upload_file"><a href="#" class="pp_upload_button">%s</a></span>', esc_html__('Upload Image', 'wp-user-avatar'));
        echo '</div>';
    }

    public function textarea($name, $options)
    {
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : '';
        printf(
            '<textarea class="short" name="%1$s" id="%1$s" placeholder="%2$s">%3$s</textarea>',
            esc_attr($name), esc_attr($placeholder), $this->saved_values_bucket($name)
        );
    }

    public function checkbox($name, $options)
    {
        $checkbox_label = isset($options['checkbox_label']) ? $options['checkbox_label'] : '';

        printf('<input type="hidden" style="display: none" name="%1$s" value="false">', esc_attr($name));

        printf(
            '<input type="checkbox" class="checkbox" name="%1$s" id="%1$s" value="true" %2$s>',
            esc_attr($name), checked('true', $this->saved_values_bucket($name), false)
        );

        printf('<span class="description">%s</span>', $checkbox_label);
    }

    public function select($name, $options)
    {
        printf('<select id="%1$s" name="%1$s" class="select short">', esc_attr($name));

        if (is_array($options['options']) && ! empty($options['options'])) {
            foreach ($options['options'] as $id => $val) {
                if (is_array($val)) {
                    echo "<optgroup label='$id'>";
                    foreach ($val as $id2 => $val2) {
                        printf('<option value="%1$s" %3$s>%2$s</option>', $id2, $val2, selected($id2, $this->saved_values_bucket($name), false));
                    }
                    echo "</optgroup>";
                } else {
                    printf('<option value="%1$s" %3$s>%2$s</option>', $id, $val, selected($id, $this->saved_values_bucket($name), false));
                }
            }
        }

        echo '</select>';
    }

    public function custom($name, $options)
    {
        echo isset($options['content']) ? $options['content'] : '';
    }

    private function select2_selected($id, $name)
    {
        $bucket = $this->saved_values_bucket($name);

        return in_array($id, is_array($bucket) ? $bucket : []) ? 'selected="selected"' : '';
    }

    public function select2($name, $options)
    {
        printf('<input name="%1$s[]" type="hidden" value="">', esc_attr($name));
        printf('<select data-placeholder="%2$s" id="%1$s" name="%1$s[]" class="select ppselect2 short" multiple>', esc_attr($name), esc_html__('Select...', 'wp-user-avatar'));

        if (is_array($options['options']) && ! empty($options['options'])) {
            foreach ($options['options'] as $id => $val) {
                if (is_array($val)) {
                    echo "<optgroup label='$id'>";
                    foreach ($val as $id2 => $val2) {
                        printf('<option value="%1$s" %3$s>%2$s</option>', $id2, $val2, $this->select2_selected($id2, $name));
                    }
                    echo "</optgroup>";
                } else {
                    printf('<option value="%1$s" %3$s>%2$s</option>', $id, $val, $this->select2_selected($id, $name));
                }
            }
        }

        echo '</select>';
    }

    protected function get_google_fonts($amount = 300)
    {
        $cache_folder = dirname(__FILE__);

        $fontFile = $cache_folder . '/google-web-fonts.txt';
        //Total time the file will be cached in seconds, set to a month.
        $cachetime = MONTH_IN_SECONDS;
        if (file_exists($fontFile) && $cachetime < filemtime($fontFile)) {
            $content = json_decode(file_get_contents($fontFile));
        } else {
            $googleApi = 'https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=AIzaSyA-iyjXck3LdOsAcGBDBfmlMtXM6jUp1Fk';
            $response  = wp_remote_retrieve_body(wp_remote_get($googleApi, array('sslverify' => false)));
            $fp        = fopen($fontFile, 'w');
            fwrite($fp, $response);
            fclose($fp);
            $content = json_decode($response);
        }

        if ($amount == 'all') {
            $google_fonts = $content->items;
        } else {
            $google_fonts = array_slice($content->items, 0, $amount);
        }

        // reduce google font collection to indexed array
        $fonts = array_reduce($google_fonts, function ($carry, $item) {
            $option_value         = str_replace(' ', '+', $item->family);
            $carry[$option_value] = $item->family;

            return $carry;
        });

        // sort in alphabetic order.
        natsort($fonts);

        // combine fonts.
        return $fonts;
    }

    public function font_family($name, $setting)
    {
        $setting['options'] = [
            ''                                             => esc_html__('Select...', 'wp-user-avatar'),
            esc_html__('Standard Fonts', 'wp-user-avatar') => [
                'Arial'           => esc_html__('Arial', 'wp-user-avatar'),
                'Comic Sans MS'   => esc_html__('Comic Sans MS', 'wp-user-avatar'),
                'Courier New'     => esc_html__('Courier New', 'wp-user-avatar'),
                'Georgia'         => esc_html__('Georgia', 'wp-user-avatar'),
                'Helvetica'       => esc_html__('Helvetica', 'wp-user-avatar'),
                'Lucida'          => esc_html__('Lucida', 'wp-user-avatar'),
                'Tahoma'          => esc_html__('Tahoma', 'wp-user-avatar'),
                'Times New Roman' => esc_html__('Times New Roman', 'wp-user-avatar'),
                'Trebuchet MS'    => esc_html__('Trebuchet MS', 'wp-user-avatar'),
                'Verdana'         => esc_html__('Verdana', 'wp-user-avatar')
            ],
            esc_html__('Google Fonts', 'wp-user-avatar')   => $this->get_google_fonts()
        ];

        $this->select($name, $setting);
    }

    public function tab_radio($name, $options)
    {
        echo '<div class="ppmb-tab-radios">';
        if (is_array($options['options']) && ! empty($options['options'])) {
            foreach ($options['options'] as $id => $val) {
                printf('<input type="radio" name="%1$s" id="%3$s" value="%2$s" %4$s>', $name, $id, $name . '-' . $id, checked($id, $this->saved_values_bucket($name), false));
                printf('<label for="%1$s">%2$s</label>', $name . '-' . $id, $val);
            }
        }
        echo isset($options['tab_description']) ? $options['tab_description'] : '';
        echo '</div>';
    }

    public function build()
    {
        $tabs         = [];
        $tab_settings = [];
        foreach ($this->args as $key => $value) {
            $tabs[$key] = $value['tab_title'];
            unset($value['tab_title']);
            $tab_settings[$key] = $value;
        }

        $metabox_title = esc_html__('Form Settings', 'wp-user-avatar');

        if ($this->DragDropBuilderInstance->form_type == FR::USER_PROFILE_TYPE) {
            $metabox_title = esc_html__('User Profile Settings', 'wp-user-avatar');
        }

        if ($this->DragDropBuilderInstance->form_type == FR::MEMBERS_DIRECTORY_TYPE) {
            $metabox_title = esc_html__('Directory Settings', 'wp-user-avatar');
        }
        ?>
        <div class="postbox-container" id="settings">
            <div id="pp-form-builder-metabox" class="postbox">
                <div class="postbox-header"><h2 class="hndle is-non-sortable"><span><?= $metabox_title ?></span></h2>
                </div>
                <div class="inside">
                    <div class="panel-wrap pp-form-builder-mb-data">
                        <ul class="pp-form-builder-mb-data_tabs pp-tabs">
                            <?php foreach ($tabs as $key => $value) : ?>
                                <?php if (empty($tab_settings[$key])) continue; ?>
                                <li class="<?= $key ?>_options <?= $key ?>_tab">
                                    <a href="#<?= $key ?>_data"><span><?= $value ?></span></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <?php foreach ($tab_settings as $key => $fields) {
                            echo '<div id="' . $key . '_data" class="panel pp-form-builder_options_panel hidden">';

                            foreach ($fields as $options) {
                                $field_id = $options['id'];

                                echo sprintf('<div class="form-field %s_wrap">', $field_id);
                                echo "<label for=\"$field_id\">" . $options['label'] . '</label>';
                                if ( ! empty($options['description'])) {
                                    printf('<span class="pp-form-builder-help-tip" title="%s"></span>', $options['description']);
                                }
                                echo '<div class="pp-field-row-content">';
                                $this->{$options['type']}($field_id, $options);
                                echo '</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}