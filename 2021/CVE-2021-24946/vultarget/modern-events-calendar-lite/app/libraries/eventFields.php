<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Event Fields class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_eventFields extends MEC_base
{
    /**
     * @var MEC_main
     */
    public $main;
    public $settings;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Main
        $this->main = $this->getMain();

        // MEC Settings
        $this->settings = $this->main->get_settings();
    }

    public function form($args)
    {
        if(!isset($this->settings['display_event_fields_backend']) or (isset($this->settings['display_event_fields_backend']) and $this->settings['display_event_fields_backend'] != 1)) return;

        $post = (isset($args['post']) ? $args['post'] : NULL);
        $id = (isset($args['id']) ? $args['id'] : 'mec-event-data');
        $class = (isset($args['class']) ? $args['class'] : 'mec-meta-box-fields mec-event-tab-content');
        $data = (isset($args['data']) ? $args['data'] : array());
        $name_prefix = (isset($args['name_prefix']) ? $args['name_prefix'] : 'mec');
        $id_prefix = (isset($args['id_prefix']) ? $args['id_prefix'] : 'mec_event_fields_');
        $mandatory_status = (isset($args['mandatory_status']) ? $args['mandatory_status'] : true);

        $event_fields = $this->main->get_event_fields();
        ?>
        <div class="<?php echo esc_attr($class); ?>" id="<?php echo esc_attr($id); ?>">
            <h4><?php echo __('Event Data', 'modern-events-calendar-lite'); ?></h4>

            <?php foreach($event_fields as $j => $event_field): if(!is_numeric($j)) continue; ?>
                <div class="mec-form-row">

                    <div class="mec-col-2">
                        <?php
                        $event_field_name = isset($event_field['label']) ? strtolower(str_replace([' ',',',':','"',"'"], '_', $event_field['label'])) : '';
                        $value = isset($data[$j]) ? $data[$j] : NULL;
                        ?>
                        <?php if(isset($event_field['label'])): ?><label for="<?php echo esc_attr($id_prefix); ?><?php echo $j; ?>"><?php _e(stripslashes($event_field['label']), 'modern-events-calendar-lite'); ?><?php echo (($mandatory_status and isset($event_field['mandatory']) and $event_field['mandatory']) ? '<span class="wbmec-mandatory">*</span>' : ''); ?></label><?php endif; ?>
                    </div>

                    <div class="mec-col-10">
                        <?php /** Text **/ if($event_field['type'] == 'text'): ?>
                            <input id="<?php echo esc_attr($id_prefix); ?><?php echo $j; ?>" type="text" name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>]" value="<?php echo esc_attr($value); ?>" placeholder="<?php esc_attr($event_field_name); ?>" <?php if($mandatory_status and isset($event_field['mandatory']) and $event_field['mandatory']) echo 'required'; ?> />

                        <?php /** Email **/ elseif($event_field['type'] == 'email'): ?>
                            <input id="<?php echo esc_attr($id_prefix); ?><?php echo $j; ?>" type="email" name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>]" value="<?php echo esc_attr($value); ?>" placeholder="<?php esc_attr($event_field_name); ?>" <?php if($mandatory_status and isset($event_field['mandatory']) and $event_field['mandatory']) echo 'required'; ?> />

                        <?php /** URL **/ elseif($event_field['type'] == 'url'): ?>
                            <input id="<?php echo esc_attr($id_prefix); ?><?php echo $j; ?>" type="url" name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>]" value="<?php echo esc_attr($value); ?>" placeholder="<?php esc_attr($event_field_name); ?>" <?php if($mandatory_status and isset($event_field['mandatory']) and $event_field['mandatory']) echo 'required'; ?> />

                        <?php /** Date **/ elseif($event_field['type'] == 'date'): ?>
                            <input id="<?php echo esc_attr($id_prefix); ?><?php echo $j; ?>" class="mec-date-picker" type="text" name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>]" value="<?php echo esc_attr($value); ?>" placeholder="<?php esc_attr($event_field_name); ?>" <?php if($mandatory_status and isset($event_field['mandatory']) and $event_field['mandatory']) echo 'required'; ?> min="1970-01-01" max="2099-12-31" />

                        <?php /** Tel **/ elseif($event_field['type'] == 'tel'): ?>
                            <input id="<?php echo esc_attr($id_prefix); ?><?php echo $j; ?>" oninput="this.value=this.value.replace(/(?![0-9])./gmi,'')" type="tel" name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>]" value="<?php echo esc_attr($value); ?>" placeholder="<?php esc_attr($event_field_name); ?>" <?php if($mandatory_status and isset($event_field['mandatory']) and $event_field['mandatory']) echo 'required'; ?> />

                        <?php /** Textarea **/ elseif($event_field['type'] == 'textarea' and (!isset($event_field['editor']) or (isset($event_field['editor']) and !$event_field['editor']))): ?>
                            <textarea id="<?php echo esc_attr($id_prefix); ?><?php echo $j; ?>" name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>]" placeholder="<?php esc_attr($event_field_name); ?>" <?php if($mandatory_status and isset($event_field['mandatory']) and $event_field['mandatory']) echo 'required'; ?>><?php echo esc_textarea($value); ?></textarea>

                        <?php /** Textarea (Editor) **/ elseif($event_field['type'] == 'textarea' and (isset($event_field['editor']) and $event_field['editor'])): wp_editor($value, $id_prefix.$j, array(
                            'textarea_name' => $name_prefix.'[fields]['.$j.']',
                            'teeny' => true,
                            'media_buttons' => false,
                        )); ?>

                        <?php /** Paragraph **/ elseif($event_field['type'] == 'p'):
                            echo '<p>'.do_shortcode(stripslashes($event_field['content'])).'</p>';
                        ?>

                        <?php /** Dropdown **/ elseif($event_field['type'] == 'select'): ?>
                            <select id="<?php echo esc_attr($id_prefix); ?><?php echo $j; ?>" name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>]" title="<?php esc_attr($event_field_name); ?>" <?php if($mandatory_status and isset($event_field['mandatory']) and $event_field['mandatory']) echo 'required'; ?>>
                                <?php if(isset($event_field['options']) and is_array($event_field['options'])): $efd = 0; foreach($event_field['options'] as $event_field_option): $efd++; ?>
                                    <option value="<?php echo (($efd == 1 and isset($event_field['ignore']) and $event_field['ignore']) ? '' : esc_attr__($event_field_option['label'], 'modern-events-calendar-lite')); ?>" <?php echo ($event_field_option['label'] == $value ? 'selected="selected"' : ''); ?>><?php _e(stripslashes($event_field_option['label']), 'modern-events-calendar-lite'); ?></option>
                                <?php endforeach; endif; ?>
                            </select>

                        <?php /** Radio **/ elseif($event_field['type'] == 'radio'): ?>
                            <?php $r = 0; foreach($event_field['options'] as $event_field_option): $r++; ?>
                                <label for="<?php echo esc_attr($id_prefix); ?><?php echo $j.'_'.strtolower(str_replace(' ', '_', $event_field_option['label'])); ?>">
                                    <input type="radio" id="<?php echo esc_attr($id_prefix); ?><?php echo $j.'_'.strtolower(str_replace(' ', '_', $event_field_option['label'])); ?>" <?php echo ($event_field_option['label'] == $value ? 'checked="checked"' : ''); ?> name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>]" value="<?php _e($event_field_option['label'], 'modern-events-calendar-lite'); ?>" <?php if($mandatory_status and $r == 1 and isset($event_field['mandatory']) and $event_field['mandatory']) echo 'required'; ?> />
                                    <?php _e(stripslashes($event_field_option['label']), 'modern-events-calendar-lite'); ?>
                                </label>
                            <?php endforeach; ?>

                        <?php /** Checkbox **/ elseif($event_field['type'] == 'checkbox'): ?>
                            <?php if(isset($event_field['options']) and is_array($event_field['options'])): foreach($event_field['options'] as $event_field_option): ?>
                                <label for="<?php echo esc_attr($id_prefix); ?><?php echo $j.'_'.strtolower(str_replace(' ', '_', $event_field_option['label'])); ?>">
                                    <input type="hidden" name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>][]" value="" />
                                    <input type="checkbox" id="<?php echo esc_attr($id_prefix); ?><?php echo $j.'_'.strtolower(str_replace(' ', '_', $event_field_option['label'])); ?>" <?php echo ((is_array($value) and in_array($event_field_option['label'], $value)) ? 'checked="checked"' : ''); ?> name="<?php echo esc_attr($name_prefix); ?>[fields][<?php echo $j; ?>][]" value="<?php _e($event_field_option['label'], 'modern-events-calendar-lite'); ?>" <?php if($mandatory_status and isset($event_field['mandatory']) and $event_field['mandatory'] and count($event_field['options']) == 1) echo 'required'; ?> />
                                    <?php _e(stripslashes($event_field_option['label']), 'modern-events-calendar-lite'); ?>
                                </label>
                            <?php endforeach; endif; ?>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>

        </div>
        <?php
    }
}