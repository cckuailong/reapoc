<?php if (!defined('ABSPATH')) exit;

class PerfectSurveyGlobalSetting extends PerfectSurveyCore
{

/**
* Return all fonts
*
* @var array
*
*/
public static function get_all_fonts() {
  return array(
    'Arial, Helvetica, sans-serif' => 'Arial, Helvetica, sans-serif',
    'Arial Black, Gadget, sans-serif' => 'Arial Black, Gadget, sans-serif',
    'Comic Sans MS, cursive, sans-serif' => 'Comic Sans MS, cursive, sans-serif',
    'Impact, Charcoal, sans-serif' => 'Impact, Charcoal, sans-serif',
    'Lucida Sans Unicode, Lucida Grande, sans-serif' => 'Lucida Sans Unicode, Lucida Grande, sans-serif',
    'Tahoma, Geneva, sans-serif' => 'Tahoma, Geneva, sans-serif',
    'Times New Roman, Times, serif' => 'Times New Roman, Times, serif',
    'Georgia, serif' => 'Georgia, serif',
    'Verdana, Geneva, sans-serif' => 'Verdana, Geneva, sans-serif',
    'Trebuchet MS, Helvetica, sans-serif' => 'Trebuchet MS, Helvetica, sans-serif',
    'Palatino Linotype, Book Antiqua, Palatino, serif' => 'Palatino Linotype, Book Antiqua, Palatino, serif',
    'Courier New, Courier, monospace' => 'Courier New, Courier, monospace',
    'Lucida Console, Monaco, monospace' => 'Lucida Console, Monaco, monospace'
  );
}

public static function get_all_border() {
  return array(
    'Dotted' => 'dotted',
    'Dashed' => 'dashed',
    'Solid' => 'solid',
    'Double' => 'double',
    'Groove' => 'groove',
    'Ridge' => 'ridge',
    'Inset' => 'inset',
    'Outset' => 'outset',
    'None' => 'none',
    'Hidden' => 'hidden'
  );
}

public static function get_all_textalign() {
  return array(
    'Left' => 'left',
    'Center' => 'center',
    'Right' => 'right'
  );
}

public static function get_all_weight() {
  return array(
    '100' => '100',
    '200' => '200',
    '300' => '300',
    '400' => '400',
    '500' => '500',
    '600' => '600',
    '700' => '700',
    '800' => '800',
    '900' => '900',
  );
}

public static function get_all_options_fields() {

  $fonts_select_options = static::get_all_fonts();
  $border_options = static::get_all_border();
  $text_align_options = static::get_all_textalign();
  $fonts_weight_options = static::get_all_weight();

  return array(
    PRSV_PLUGIN_CODE . '_settings_global' => array(
      'title_page' => __('Global settings', 'perfect-survey'),
      'icon' => 'cog',
      'composer' => array(

        PRSV_OPTION_CODE . '_settings_progressbar_title' => array(
          'label' => '',
          'description' => '',
          'description' => FALSE,
          'input' => array(
            'type' => 'section_title',
            'content' => __('Progress bar', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_progress_bar_active' => array(
          'label' => __('Active progress bar', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'checkbox',
            'value' => 'progress_bar_on',
            'value_two' => 'progress_bar_off',
          ),
        ),
        PRSV_OPTION_CODE . '_position_progressbar' => array(
          'label' => __('Progressbar position', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'radio_image',
            'options' => array(
              'progressbar_bottom' => array(
                'image' => plugins_url(PRSV_NAMING . '/resources/backend/assets/img/progress_bar_bottom.png'),
                'label' => __('Below questions', 'perfect-survey'),
              ),
              'progressbar_top' => array(
                'image' => plugins_url(PRSV_NAMING . '/resources/backend/assets/img/progress_bar_top.png'),
                'label' => __('Above questions', 'perfect-survey'),
              ),
            )
          )
        ),
        PRSV_OPTION_CODE . '_background_first_progress' => array(
          'label' => __('Background first color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_background_second_progress' => array(
          'label' => __('Background second color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_progress_bar_height' => array(
          'label' => __('Height', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_progress_bar_height_value' => array(
              'label' => __('Value in (px)', 'perfect-survey'),
              'type' => 'number'
            ),
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_border_radius_progress' => array(
          'label' => __('Border radius', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_br_top_left_progress' => array(
              'label' => __('Top left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_top_right_progress' => array(
              'label' => __('Top right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_left_progress' => array(
              'label' => __('Bottom left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_right_progress' => array(
              'label' => __('Bottom right (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_margin_progress' => array(
          'label' => __('Margin', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_margin_top_progress' => array(
              'label' => __('Margin top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_right_progress' => array(
              'label' => __('Margin right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_bottom_progress' => array(
              'label' => __('Margin bottom (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_left_progress' => array(
              'label' => __('Margin left (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
      )
    ),
    PRSV_PLUGIN_CODE . '_settings_layout' => array(
      'title_page' => __('Layout', 'perfect-survey'),
      'icon' => 'insert-template',
      'composer' => array(
        PRSV_OPTION_CODE . '_settings_general_container' => array(
          'label' => '',
          'description' => '',
          'input' => array(
            'type' => 'section_title',
            'content' => __('General container', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_border_radius_general_container' => array(
          'label' => __('Border radius', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_br_top_left_general_container' => array(
              'label' => __('Top left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_top_right_general_container' => array(
              'label' => __('Top right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_left_general_container' => array(
              'label' => __('Bottom left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_right_general_container' => array(
              'label' => __('Bottom right (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_padding_general_container' => array(
          'label' => __('Padding', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_padding_top_general_container' => array(
              'label' => __('Padding top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_right_general_container' => array(
              'label' => __('Padding right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_left_general_container' => array(
              'label' => __('Padding left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_bottom_general_container' => array(
              'label' => __('Padding bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_margin_general_container' => array(
          'label' => __('Margin', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_margin_top_general_container' => array(
              'label' => __('Margin top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_right_general_container' => array(
              'label' => __('Margin right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_left_general_container' => array(
              'label' => __('Margin left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_bottom_general_container' => array(
              'label' => __('Margin bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_color_border_general_container' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_general_container' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_multiple_border_general_container' => array(
          'label' => __('Border manager', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_border_width_general_container' => array(
              'label' => __('Border Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_border_style_general_container' => array(
              'label' => __('Border style', 'perfect-survey'),
              'type' => 'select',
              'option' => $border_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_settings_layout_boxed' => array(
          'label' => '',
          'description' => '',
          'input' => array(
            'type' => 'section_title',
            'content' => __('Boxed layout', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_border_radius_boxed_mumber' => array(
          'label' => __('Border radius', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_br_top_left_boxed' => array(
              'label' => __('Top left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_top_right_boxed' => array(
              'label' => __('Top right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_left_boxed' => array(
              'label' => __('Bottom left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_right_boxed' => array(
              'label' => __('Bottom right (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_padding_boxed' => array(
          'label' => __('Padding', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_padding_boxed_top' => array(
              'label' => __('Padding top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_boxed_right' => array(
              'label' => __('Padding right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_boxed_left' => array(
              'label' => __('Padding left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_boxed_bottom' => array(
              'label' => __('Padding bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_margin_boxed' => array(
          'label' => __('Margin', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_margin_boxed_top' => array(
              'label' => __('Margin top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_boxed_right' => array(
              'label' => __('Margin right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_boxed_left' => array(
              'label' => __('Margin left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_boxed_bottom' => array(
              'label' => __('Margin bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_color_border_boxed' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_boxed' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_multiple_border_pboxed' => array(
          'label' => __('Border manager', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_border_width_boxed' => array(
              'label' => __('Border Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_border_style_boxed' => array(
              'label' => __('Border style', 'perfect-survey'),
              'type' => 'select',
              'option' => $border_options
            ),
          )
        ),
      ),
    ),
    PRSV_PLUGIN_CODE . '_settings_fonts' => array(
      'title_page' => __('Fonts', 'perfect-survey'),
      'icon' => 'font-size',
      'composer' => array(
        PRSV_OPTION_CODE . '_settings_family_font' => array(
          'label' => '',
          'description' => '',
          'input' => array(
            'type' => 'section_title',
            'content' => __('Font family', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_questions_fonts' => array(
          'label' => __('Questions', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_setting_question_font' => array(
              'label' => __('Fonts', 'perfect-survey'),
              'type' => 'select',
              'option' => $fonts_select_options
            ),
            PRSV_OPTION_CODE . '_font_weight_questions' => array(
              'label' => __('Font weight', 'perfect-survey'),
              'type' => 'select',
              'option' => $fonts_weight_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_description_fonts' => array(
          'label' => __('Descriptions', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_setting_description_font' => array(
              'label' => __('Fonts', 'perfect-survey'),
              'type' => 'select',
              'option' => $fonts_select_options
            ),
            PRSV_OPTION_CODE . '_font_weight_descriptions' => array(
              'label' => __('Font weight', 'perfect-survey'),
              'type' => 'select',
              'option' => $fonts_weight_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_label_fonts' => array(
          'label' => __('Labels', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_setting_label_font' => array(
              'label' => __('Fonts', 'perfect-survey'),
              'type' => 'select',
              'option' => $fonts_select_options
            ),
            PRSV_OPTION_CODE . '_font_weight_labels' => array(
              'label' => __('Font weight', 'perfect-survey'),
              'type' => 'select',
              'option' => $fonts_weight_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_buttons_fonts' => array(
          'label' => __('Buttons', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_setting_button_font' => array(
              'label' => __('Fonts', 'perfect-survey'),
              'type' => 'select',
              'option' => $fonts_select_options
            ),
            PRSV_OPTION_CODE . '_font_weight_buttons' => array(
              'label' => __('Font weight', 'perfect-survey'),
              'type' => 'select',
              'option' => $fonts_weight_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_settings_size_font' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_title',
            'content' => __('Font size', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_questions' => array(
          'label' => __('Questions', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_font_size_questions' => array(
              'label' => __('Font size (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_line_height_questions' => array(
              'label' => __('Line height (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_letter_spacing_questions' => array(
              'label' => __('Letter spacing (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_descriptions' => array(
          'label' => __('Descriptions', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_font_size_descriptions' => array(
              'label' => __('Font size (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_line_height_descriptions' => array(
              'label' => __('Line height (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_letter_spacing_descriptions' => array(
              'label' => __('Letter spacing (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_labels' => array(
          'label' => __('Labels', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_font_size_labels' => array(
              'label' => __('Font size (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_line_height_labels' => array(
              'label' => __('Line height (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_letter_spacing_labels' => array(
              'label' => __('Letter spacing (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_buttons' => array(
          'label' => __('Buttons', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_font_size_buttons' => array(
              'label' => __('Font size (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_line_height_buttons' => array(
              'label' => __('Line height (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_letter_spacing_buttons' => array(
              'label' => __('Letter spacing (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_settings_color_font' => array(
          'input' => array(
            'type' => 'section_title',
            'content' => __('Fonts color', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_color_questions_option' => array(
          'label' => __('Questions', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_description_option' => array(
          'label' => __('Descriptions', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_labels_option' => array(
          'label' => __('Labels', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_buttons_option' => array(
          'label' => __('Buttons', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_pagination_option' => array(
          'label' => __('Pagination text', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
      )
    ),
    PRSV_PLUGIN_CODE . '_settings_forms' => array(
      'title_page' => __('Forms', 'perfect-survey'),
      'icon' => 'menu',
      'composer' => array(
        PRSV_OPTION_CODE . '_settings_form' => array(
          'input' => array(
            'type' => 'section_title',
            'content' => __('Input, select e textarea', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_color_text_form' => array(
          'label' => __('Text color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_form' => array(
          'label' => __('Background', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_border_form' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_multiple_border_manage_form' => array(
          'label' => __('Border manager', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_border_width_form' => array(
              'label' => __('Border Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_border_style_form' => array(
              'label' => __('Border style', 'perfect-survey'),
              'type' => 'select',
              'option' => $border_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_forms_border_radius' => array(
          'label' => __('Border radius', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_br_top_left_forms' => array(
              'label' => __('Top left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_top_right_forms' => array(
              'label' => __('Top right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_left_forms' => array(
              'label' => __('Bottom left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_right_forms' => array(
              'label' => __('Bottom right (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_padding_forms' => array(
          'label' => __('Padding', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_padding_forms_top' => array(
              'label' => __('Padding top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_forms_right' => array(
              'label' => __('Padding right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_forms_left' => array(
              'label' => __('Padding left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_forms_bottom' => array(
              'label' => __('Padding bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_settings_focus' => array(
          'input' => array(
            'type' => 'section_title',
            'content' => __('Focus', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_color_text_form_focus' => array(
          'label' => __('Text color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_form_focus' => array(
          'label' => __('Background', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_border_form_focus' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_multiple_border_manage_form_focus' => array(
          'label' => __('Border manager', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_border_width_form_focus' => array(
              'label' => __('Border Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_border_style_form_focus' => array(
              'label' => __('Border style', 'perfect-survey'),
              'type' => 'select',
              'option' => $border_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_forms_border_radius_focus' => array(
          'label' => __('Border radius', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_br_top_left_forms_focus' => array(
              'label' => __('Top left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_top_right_forms_focus' => array(
              'label' => __('Top right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_left_forms_focus' => array(
              'label' => __('Bottom left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_right_forms_focus' => array(
              'label' => __('Bottom right (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_padding_forms_focus' => array(
          'label' => __('Padding', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_padding_forms_top_focus' => array(
              'label' => __('Padding top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_forms_right_focus' => array(
              'label' => __('Padding right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_forms_left_focus' => array(
              'label' => __('Padding left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_forms_bottom_focus' => array(
              'label' => __('Padding bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_settings_focus_button' => array(
          'input' => array(
            'type' => 'section_title',
            'content' => __('Buttons', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_border_radius_buttons' => array(
          'label' => __('Border radius', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_br_top_left_buttons' => array(
              'label' => __('Top left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_top_right_buttons' => array(
              'label' => __('Top right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_left_buttons' => array(
              'label' => __('Bottom left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_right_buttons' => array(
              'label' => __('Bottom right (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_padding_buttons' => array(
          'label' => __('Padding', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_padding_buttons_top' => array(
              'label' => __('Padding top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_buttons_right' => array(
              'label' => __('Padding right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_buttons_left' => array(
              'label' => __('Padding left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_buttons_bottom' => array(
              'label' => __('Padding bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_margin_buttons' => array(
          'label' => __('Margin', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_margin_buttons_top' => array(
              'label' => __('Margin top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_buttons_right' => array(
              'label' => __('Margin right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_buttons_left' => array(
              'label' => __('Margin left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_buttons_bottom' => array(
              'label' => __('Margin bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_color_border_buttons' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_buttons' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_multiple_border_buttons' => array(
          'label' => __('Border manager', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_border_width_buttons' => array(
              'label' => __('Border Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_border_style_buttons' => array(
              'label' => __('Border style', 'perfect-survey'),
              'type' => 'select',
              'option' => $border_options
            ),
          )
        ),
      )
    ),
    PRSV_PLUGIN_CODE . '_settings_extrafields' => array(
      'title_page' => __('Extra fields', 'perfect-survey'),
      'icon' => 'dice',
      'composer' => array(
        PRSV_OPTION_CODE . '_settings_extrafields' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_title',
            'content' => __('Extra fields', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_settings_extrafields_datepicker' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_paragraph',
            'content' => __('Date field', 'perfect-survey')
          )
        ),
        PRSV_OPTION_CODE . '_color_background_datepicker' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_settings_extrafields_checkradio' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_paragraph',
            'content' => __('Checkbox and radiobuttons', 'perfect-survey')
          )
        ),
        PRSV_OPTION_CODE . '_border_color_checkradio' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_background_color_checkradio' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_active_background_color_checkradio' => array(
          'label' => __('Selected background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_settings_extrafields_matrix' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_paragraph',
            'content' => __('Matrix fields', 'perfect-survey')
          )
        ),
        PRSV_OPTION_CODE . '_background_color_table_matrix' => array(
          'label' => __('Table background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_alternate_color_table_matrix' => array(
          'label' => __('Alternate row background', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_border_color_table_matrix' => array(
          'label' => __('Table border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_multiple_border_table_matrix' => array(
          'label' => __('Border manager', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_border_width_table_matrix' => array(
              'label' => __('Border Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_border_style_table_matrix' => array(
              'label' => __('Border style', 'perfect-survey'),
              'type' => 'select',
              'option' => $border_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_padding_cell_matrix' => array(
          'label' => __('Padding', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_padding_top_bottom_cell_matrix' => array(
              'label' => __('Padding top bottom (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_right_left_cell_matrix' => array(
              'label' => __('Padding left right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
          )
        ),


        PRSV_OPTION_CODE . '_settings_extrafields_images' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_paragraph',
            'content' => __('Image choice fields', 'perfect-survey')
          )
        ),
        PRSV_OPTION_CODE . '_modal_bar_active' => array(
          'label' => __('Active image zoom on click', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'checkbox',
            'value' => 'ps_modal_on',
            'value_two' => 'ps_modal_off',
          ),
        ),




        PRSV_OPTION_CODE . '_border_radius_image_choice' => array(
          'label' => __('Border radius', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_br_top_left_image_choice' => array(
              'label' => __('Top left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_top_right_image_choice' => array(
              'label' => __('Top right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_left_image_choice' => array(
              'label' => __('Bottom left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_right_image_choice' => array(
              'label' => __('Bottom right (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_padding_image_choice' => array(
          'label' => __('Padding', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_padding_top_image_choice' => array(
              'label' => __('Padding top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_right_image_choice' => array(
              'label' => __('Padding right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_left_image_choice' => array(
              'label' => __('Padding left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_bottom_image_choice' => array(
              'label' => __('Padding bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_color_border_image_choice' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_image_choice' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_multiple_border_image_choice' => array(
          'label' => __('Border manager', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_border_width_image_choice' => array(
              'label' => __('Border Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_border_style_image_choice' => array(
              'label' => __('Border style', 'perfect-survey'),
              'type' => 'select',
              'option' => $border_options
            ),
          )
        ),





      ),
    ),
    PRSV_PLUGIN_CODE . '_settings_pagination' => array(
      'title_page' => __('Pagination', 'perfect-survey'),
      'icon' => 'tab',
      'composer' => array(
        PRSV_OPTION_CODE . '_settings_pagination_position' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_title',
            'content' => __('Pagination position', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_settings_position_pagination' => array(
          'label' => __('Chose the pagination layout', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'radio_image',
            'options' => array(
              'below_questions' => array(
                'image' => plugins_url(PRSV_NAMING . '/resources/backend/assets/img/pagination_bottom.png'),
                'label' => __('Below questions', 'perfect-survey'),
              ),
              'above_questions' => array(
                'image' => plugins_url(PRSV_NAMING . '/resources/backend/assets/img/pagination_top.png'),
                'label' => __('Above questions', 'perfect-survey'),
              ),
            )
          )
        ),
        PRSV_OPTION_CODE . '_settings_pagination_dot_title' => array(
          'label' => '',
          'description' => '',
          'description' => FALSE,
          'input' => array(
            'type' => 'section_title',
            'content' => __('Pagination with dots', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_dots_graphic' => array(
          'label' => __('Dots', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_dots_width' => array(
              'label' => __('Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_dots_height' => array(
              'label' => __('Height (px)', 'perfect-survey'),
              'type' => 'number'
            ),
          )
        ),
        PRSV_OPTION_CODE . '_color_paragraph_dot' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_paragraph',
            'content' => __('Color of dots', 'perfect-survey')
          )
        ),
        PRSV_OPTION_CODE . '_color_border_pagination_dots' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_pagination_dots' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_paragraph_current_dots' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_paragraph',
            'content' => __('Color of current dots', 'perfect-survey')
          )
        ),
        PRSV_OPTION_CODE . '_color_border_pagination_current_dots' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_pagination_current_dots' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_paragraph_complete_dots' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_paragraph',
            'content' => __('Color of completed dots', 'perfect-survey')
          )
        ),
        PRSV_OPTION_CODE . '_color_border_pagination_complete_dots' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_pagination_complete_dots' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_multiple_border_pagination_dots' => array(
          'label' => __('Border manager', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_border_width_pagination_dots' => array(
              'label' => __('Border Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_border_style_pagination_dots' => array(
              'label' => __('Border style', 'perfect-survey'),
              'type' => 'select',
              'option' => $border_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_multiple_input_border_radius_dots' => array(
          'label' => __('Border radius', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_br_top_left_dots' => array(
              'label' => __('Top left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_top_right_dots' => array(
              'label' => __('Top right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_left_dots' => array(
              'label' => __('Bottom left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_right_dots' => array(
              'label' => __('Bottom right (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_settings_pagination_mumber_title' => array(
          'label' => '',
          'description' => '',
          'description' => FALSE,
          'input' => array(
            'type' => 'section_title',
            'content' => __('Pagination with number (1 of 10)', 'perfect-survey'),
          )
        ),
        PRSV_OPTION_CODE . '_border_radius_pagination_mumber' => array(
          'label' => __('Border radius', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_br_top_left_pagination' => array(
              'label' => __('Top left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_top_right_pagination' => array(
              'label' => __('Top right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_left_pagination' => array(
              'label' => __('Bottom left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_br_bottom_right_pagination' => array(
              'label' => __('Bottom right (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_padding_pagination_mumber' => array(
          'label' => __('Padding', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_padding_pagination_top' => array(
              'label' => __('Padding top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_pagination_right' => array(
              'label' => __('Padding right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_pagination_left' => array(
              'label' => __('Padding left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_padding_pagination_bottom' => array(
              'label' => __('Padding bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_margin_pagination_mumber' => array(
          'label' => __('Margin', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_margin_pagination_top' => array(
              'label' => __('Margin top (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_pagination_right' => array(
              'label' => __('Margin right (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_pagination_left' => array(
              'label' => __('Margin left (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_margin_pagination_bottom' => array(
              'label' => __('Margin bottom (px)', 'perfect-survey'),
              'type' => 'number'
            )
          )
        ),
        PRSV_OPTION_CODE . '_color_border_pagination' => array(
          'label' => __('Border color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_color_background_pagination' => array(
          'label' => __('Background color', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'text',
            'class' => 'color-field'
          )
        ),
        PRSV_OPTION_CODE . '_multiple_border_pagination' => array(
          'label' => __('Border manager', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'multi_input',
          ),
          'inputs' => array(
            PRSV_OPTION_CODE . '_border_width_pagination' => array(
              'label' => __('Border Width (px)', 'perfect-survey'),
              'type' => 'number'
            ),
            PRSV_OPTION_CODE . '_border_style_pagination' => array(
              'label' => __('Border style', 'perfect-survey'),
              'type' => 'select',
              'option' => $border_options
            ),
          )
        ),
        PRSV_OPTION_CODE . '_pagination_text_align' => array(
          'label' => __('Text align', 'perfect-survey'),
          'description' => '',
          'input' => array(
            'type' => 'select',
            'options' => $text_align_options
          )
        ),
      )
    ),
    PRSV_PLUGIN_CODE . '_settings_custom_css' => array(
      'title_page' => __('Custom css', 'perfect-survey'),
      'icon' => 'embed2',
      'composer' => array(
        PRSV_OPTION_CODE . '_settings_custom_css_title' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_title',
            'content' => __('Custom css', 'perfect-survey')
          )
        ),
        PRSV_OPTION_CODE . '_settings_custom_css_paragraph' => array(
          'label' => '',
          'input' => array(
            'type' => 'section_paragraph',
            'content' => __('If you want you can paste your custom css here', 'perfect-survey')
          )
        ),
        PRSV_OPTION_CODE . '_custom_css' => array(
          'description' => '',
          'input' => array(
            'type' => 'textarea',
            'rows' => 25,
            'class' => 'ps_text_coded'
          )
        ),
      )
    )
  );
}

}
