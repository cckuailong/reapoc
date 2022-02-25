<?php
prsv_get_row_css('::placeholder', array(
  'font-family' => array(
    PRSV_OPTION_CODE . '_setting_description_font',
    ''
  ),
  'font-size' => array(
    PRSV_OPTION_CODE . '_font_size_descriptions',
    'px',
  ),
  'line-height' => array(
    PRSV_OPTION_CODE . '_line_height_descriptions',
    'px',
  ),
  'font-weight' => array(
    PRSV_OPTION_CODE . '_font_weight_descriptions',
    ''
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_text_form'.'ffcc',
    '',
  ),
));
prsv_get_row_css(':-ms-input-placeholder', array(
  'font-family' => array(
    PRSV_OPTION_CODE . '_setting_description_font',
    ''
  ),
  'font-size' => array(
    PRSV_OPTION_CODE . '_font_size_descriptions',
    'px',
  ),
  'line-height' => array(
    PRSV_OPTION_CODE . '_line_height_descriptions',
    'px',
  ),
  'font-weight' => array(
    PRSV_OPTION_CODE . '_font_weight_descriptions',
    ''
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_text_form'.'ffcc',
    '',
  ),
));
prsv_get_row_css('::-ms-input-placeholder', array(
  'font-family' => array(
    PRSV_OPTION_CODE . '_setting_description_font',
    ''
  ),
  'font-size' => array(
    PRSV_OPTION_CODE . '_font_size_descriptions',
    'px',
  ),
  'line-height' => array(
    PRSV_OPTION_CODE . '_line_height_descriptions',
    'px',
  ),
  'font-weight' => array(
    PRSV_OPTION_CODE . '_font_weight_descriptions',
    ''
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_text_form'.'ffcc',
    '',
  ),
));

if ($progress == 'progress_bar_on') {
  prsv_get_row_css('.survey-container .ps_progressbar_generalcontainer', array(
    'height' => array(
      PRSV_OPTION_CODE . '_progress_bar_height_value',
      'px',
    ),
    'background' => array(
      PRSV_OPTION_CODE . '_background_first_progress',
      '',
    ),
    'margin-bottom' => array(
      PRSV_OPTION_CODE . '_margin_bottom_progress',
      'px',
    ),
    'margin-top' => array(
      PRSV_OPTION_CODE . '_margin_top_progress',
      'px',
    ),
    'margin-left' => array(
      PRSV_OPTION_CODE . '_margin_left_progress',
      'px',
    ),
    'margin-right' => array(
      PRSV_OPTION_CODE . '_margin_right_progress',
      'px',
    ),
    'border-top-right-radius' => array(
      PRSV_OPTION_CODE . '_br_top_right_progress',
      'px',
    ),
    'border-top-left-radius' => array(
      PRSV_OPTION_CODE . '_br_top_left_progress',
      'px',
    ),
    'border-bottom-right-radius' => array(
      PRSV_OPTION_CODE . '_br_bottom_right_progress',
      'px',
    ),
    'border-bottom-left-radius' => array(
      PRSV_OPTION_CODE . '_br_bottom_left_progress',
      'px',
    )
  ));
  prsv_get_row_css('.survey-container .ps_width_curret', array(
    'background' => array(
      PRSV_OPTION_CODE . '_background_second_progress',
      '',
    ),
    'height' => array(
      PRSV_OPTION_CODE . '_progress_bar_height_value',
      'px',
    ),
  ));
}
if (!$ps_post->complete && $ps_post_meta['ps_question_submit_complete'] == 'one') {
  prsv_get_row_css('.survey-container .ps_paginator_step_number', array(
    'border-width' => array(
      PRSV_OPTION_CODE . '_border_width_pagination',
      'px',
    ),
    'border-style' => array(
      PRSV_OPTION_CODE . '_border_style_pagination',
      '',
    ),
    'border-color' => array(
      '',
      '',
    ),
    'background' => array(
      PRSV_OPTION_CODE . '_color_background_pagination',
      '',
    ),
    'padding-top' => array(
      PRSV_OPTION_CODE . '_padding_pagination_top',
      'px',
    ),
    'padding-bottom' => array(
      PRSV_OPTION_CODE . '_padding_pagination_bottom',
      'px',
    ),
    'padding-right' => array(
      PRSV_OPTION_CODE . '_padding_pagination_right',
      'px',
    ),
    'padding-left' => array(
      PRSV_OPTION_CODE . '_padding_pagination_left',
      'px',
    ),
    'margin-top' => array(
      PRSV_OPTION_CODE . '_margin_pagination_top',
      'px',
    ),
    'margin-bottom' => array(
      PRSV_OPTION_CODE . '_margin_pagination_bottom',
      'px',
    ),
    'margin-right' => array(
      PRSV_OPTION_CODE . '_margin_pagination_right',
      'px',
    ),
    'margin-left' => array(
      PRSV_OPTION_CODE . '_margin_pagination_left',
      'px',
    ),
    'color' => array(
      PRSV_OPTION_CODE . '_br_top_left_progress',
      'px',
    ),
    'border-top-right-radius' => array(
      PRSV_OPTION_CODE . '_br_top_right_pagination',
      'px',
    ),
    'border-top-left-radius' => array(
      PRSV_OPTION_CODE . '_br_top_left_pagination',
      'px',
    ),
    'border-bottom-right-radius' => array(
      PRSV_OPTION_CODE . '_br_bottom_right_pagination',
      'px',
    ),
    'border-bottom-left-radius' => array(
      PRSV_OPTION_CODE . '_br_bottom_left_pagination',
      'px',
    ),
    'text-align' => array(
      PRSV_OPTION_CODE . '_pagination_text_align',
      '',
    )
  ));
  prsv_get_row_css('.ps_paginator_step ul li', array(
    'width' => array(
      PRSV_OPTION_CODE . '_dots_width',
      'px',
    ),
    'height' => array(
      PRSV_OPTION_CODE . '_dots_height',
      'px',
    ),
    'background' => array(
      PRSV_OPTION_CODE . '_color_background_pagination_dots',
      '',
    ),
    'border-top-right-radius' => array(
      PRSV_OPTION_CODE . '_br_top_right_dots',
      'px',
    ),
    'border-top-left-radius' => array(
      PRSV_OPTION_CODE . '_br_top_left_dots',
      'px',
    ),
    'border-bottom-right-radius' => array(
      PRSV_OPTION_CODE . '_br_bottom_right_dots',
      'px',
    ),
    'border-bottom-left-radius' => array(
      PRSV_OPTION_CODE . '_br_bottom_left_dots',
      'px',
    ),
    'border-width' => array(
      PRSV_OPTION_CODE . '_border_width_pagination_dots',
      'px',
    ),
    'border-style' => array(
      PRSV_OPTION_CODE . '_border_style_pagination_dots',
      '',
    ),
    'border-color' => array(
      PRSV_OPTION_CODE . '_color_border_pagination_dots',
      '',
    ),
  ));
  prsv_get_row_css('.ps_paginator_step ul li.ps_checked', array(
    'background' => array(
      PRSV_OPTION_CODE . '_color_background_pagination_complete_dots',
      '',
    ),
    'border-width' => array(
      PRSV_OPTION_CODE . '_border_width_pagination_dots',
      'px',
    ),
    'border-style' => array(
      PRSV_OPTION_CODE . '_border_style_pagination_dots',
      '',
    ),
    'border-color' => array(
      PRSV_OPTION_CODE . '_color_border_pagination_complete_dots',
      '',
    ),
  ));
  prsv_get_row_css('.ps_paginator_step ul li.ps_checked.ps_current', array(
    'background' => array(
      PRSV_OPTION_CODE . '_color_background_pagination_current_dots',
      '',
    ),
    'border-width' => array(
      PRSV_OPTION_CODE . '_border_width_pagination_dots',
      'px',
    ),
    'border-style' => array(
      PRSV_OPTION_CODE . '_border_style_pagination_dots',
      '',
    ),
    'border-color' => array(
      PRSV_OPTION_CODE . '_color_border_pagination_current_dots',
      '',
    ),
  ));
}

if (true) {
  prsv_get_row_css('.survey-container .survey_general_container .survey_question_container.boxed', array(
    'padding-top' => array(
      PRSV_OPTION_CODE . '_padding_boxed_top',
      'px',
    ),
    'padding-bottom' => array(
      PRSV_OPTION_CODE . '_padding_boxed_bottom',
      'px',
    ),
    'padding-right' => array(
      PRSV_OPTION_CODE . '_padding_boxed_right',
      'px',
    ),
    'padding-left' => array(
      PRSV_OPTION_CODE . '_padding_boxed_left',
      'px',
    ),
    'margin-top' => array(
      PRSV_OPTION_CODE . '_margin_boxed_top',
      'px',
    ),
    'margin-bottom' => array(
      PRSV_OPTION_CODE . '_margin_boxed_bottom',
      'px',
    ),
    'margin-right' => array(
      PRSV_OPTION_CODE . '_margin_boxed_right',
      'px',
    ),
    'margin-left' => array(
      PRSV_OPTION_CODE . '_margin_boxed_left',
      'px',
    ),
    'background' => array(
      PRSV_OPTION_CODE . '_color_background_boxed',
      '',
    ),
    'border-top-right-radius' => array(
      PRSV_OPTION_CODE . '_br_top_right_boxed',
      'px',
    ),
    'border-top-left-radius' => array(
      PRSV_OPTION_CODE . '_br_top_left_boxed',
      'px',
    ),
    'border-bottom-right-radius' => array(
      PRSV_OPTION_CODE . '_br_bottom_right_boxed',
      'px',
    ),
    'border-bottom-left-radius' => array(
      PRSV_OPTION_CODE . '_br_bottom_left_boxed',
      'px',
    ),
    'border-width' => array(
      PRSV_OPTION_CODE . '_border_width_boxed',
      'px',
    ),
    'border-style' => array(
      PRSV_OPTION_CODE . '_border_style_boxed',
      '',
    ),
    'border-color' => array(
      PRSV_OPTION_CODE . '_color_border_boxed',
      '',
    ),
    'font-family' => array(
      PRSV_OPTION_CODE . '_setting_description_font',
      ''
    ),
    'font-size' => array(
      PRSV_OPTION_CODE . '_font_size_descriptions',
      'px',
    ),
    'line-height' => array(
      PRSV_OPTION_CODE . '_line_height_descriptions',
      'px',
    ),
    'font-weight' => array(
      PRSV_OPTION_CODE . '_font_weight_descriptions',
      ''
    ),
    'letter-spacing' => array(
      PRSV_OPTION_CODE . '_letter_spacing_descriptions',
      'px'
    ),
    'color' => array(
      PRSV_OPTION_CODE . '_color_description_option',
      '',
    ),
  ));
}
prsv_get_row_css('.survey_general_container .survey_question_container h2', array(
  'font-family' => array(
    PRSV_OPTION_CODE . '_setting_question_font',
    ''
  ),
  'font-size' => array(
    PRSV_OPTION_CODE . '_font_size_questions',
    'px',
  ),
  'line-height' => array(
    PRSV_OPTION_CODE . '_line_height_questions',
    'px',
  ),
  'font-weight' => array(
    PRSV_OPTION_CODE . '_font_weight_questions',
    ''
  ),
  'letter-spacing' => array(
    PRSV_OPTION_CODE . '_letter_spacing_questions',
    'px'
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_questions_option',
    '',
  ),
));
prsv_get_row_css('p.question-description', array(
  'font-family' => array(
    PRSV_OPTION_CODE . '_setting_description_font',
    ''
  ),
  'font-size' => array(
    PRSV_OPTION_CODE . '_font_size_descriptions',
    'px',
  ),
  'line-height' => array(
    PRSV_OPTION_CODE . '_line_height_descriptions',
    'px',
  ),
  'font-weight' => array(
    PRSV_OPTION_CODE . '_font_weight_descriptions',
    ''
  ),
  'letter-spacing' => array(
    PRSV_OPTION_CODE . '_letter_spacing_descriptions',
    'px'
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_description_option',
    '',
  ),
  'margin-bottom' => array(
    '',
    '20px',
  ),
));
prsv_get_row_css('button.post-edit-btn.ps_survey_btn_submit.survey_submit_btn,.swal-button', array(
  'font-family' => array(
    PRSV_OPTION_CODE . '_setting_button_font',
    ''
  ),
  'font-size' => array(
    PRSV_OPTION_CODE . '_font_size_buttons',
    'px',
  ),
  'line-height' => array(
    PRSV_OPTION_CODE . '_line_height_buttons',
    'px',
  ),
  'font-weight' => array(
    PRSV_OPTION_CODE . '_font_weight_buttons',
    ''
  ),
  'letter-spacing' => array(
    PRSV_OPTION_CODE . '_letter_spacing_buttons',
    'px'
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_buttons_option',
    '',
  ),
  'padding-top' => array(
    PRSV_OPTION_CODE . '_padding_buttons_top',
    'px',
  ),
  'padding-bottom' => array(
    PRSV_OPTION_CODE . '_padding_buttons_bottom',
    'px',
  ),
  'padding-right' => array(
    PRSV_OPTION_CODE . '_padding_buttons_right',
    'px',
  ),
  'padding-left' => array(
    PRSV_OPTION_CODE . '_padding_buttons_left',
    'px',
  ),
  'margin-top' => array(
    PRSV_OPTION_CODE . '_margin_buttons_top',
    'px',
  ),
  'margin-bottom' => array(
    PRSV_OPTION_CODE . '_margin_buttons_bottom',
    'px',
  ),
  'margin-right' => array(
    PRSV_OPTION_CODE . '_margin_buttons_right',
    'px',
  ),
  'margin-left' => array(
    PRSV_OPTION_CODE . '_margin_buttons_left',
    'px',
  ),
  'background' => array(
    PRSV_OPTION_CODE . '_color_background_buttons',
    '',
  ),
  'border-top-right-radius' => array(
    PRSV_OPTION_CODE . '_br_top_right_buttons',
    'px',
  ),
  'border-top-left-radius' => array(
    PRSV_OPTION_CODE . '_br_top_left_buttons',
    'px',
  ),
  'border-bottom-right-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_right_buttons',
    'px',
  ),
  'border-bottom-left-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_left_buttons',
    'px',
  ),
  'border-width' => array(
    PRSV_OPTION_CODE . '_border_width_buttons',
    'px',
  ),
  'border-style' => array(
    PRSV_OPTION_CODE . '_border_style_buttons',
    '',
  ),
  'border-color' => array(
    PRSV_OPTION_CODE . '_color_border_buttons',
    '',
  ),
  'outline' => array(
    'none',
    'none'
  )
));
prsv_get_row_css('.ui-widget.ui-widget-content.ps_ui_customize_survey , .ps_ui_customize_survey .ui-state-default, .ui-widget-content.ps_ui_customize_survey  .ui-state-default, .ui-datepicker.ps_ui_customize_survey table, .ps_ui_customize_survey .ui-state-highlight, .ui-widget-content.ps_ui_customize_survey .ui-state-highlight, .ps_ui_customize_survey table.ui-datepicker-calendar thead, .ps_ui_customize_survey .ui-widget-header, .ps_ui_customize_survey .ui-state-active, .ui-widget-content.ps_ui_customize_survey  .ui-state-active', array(
  'background' => array(
    PRSV_OPTION_CODE . '_color_background_datepicker',
    '',
  ),
));
prsv_get_row_css('.survey_general_container select', array(
  'font-family' => array(
    PRSV_OPTION_CODE . '_setting_description_font',
    ''
  ),
  'font-size' => array(
    PRSV_OPTION_CODE . '_font_size_descriptions',
    'px',
  ),
  'line-height' => array(
    PRSV_OPTION_CODE . '_line_height_descriptions',
    'px',
  ),
  'font-weight' => array(
    PRSV_OPTION_CODE . '_font_weight_descriptions',
    ''
  ),
  'background-color' => array(
    PRSV_OPTION_CODE . '_color_background_form',
    '',
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_text_form',
    '',
  ),
  'padding-right' => array(
    PRSV_OPTION_CODE . '_padding_forms_right',
    'px',
  ),
  'padding-left' => array(
    PRSV_OPTION_CODE . '_padding_forms_left',
    'px',
  ),
  'border-top-right-radius' => array(
    PRSV_OPTION_CODE . '_br_top_right_forms',
    'px',
  ),
  'border-top-left-radius' => array(
    PRSV_OPTION_CODE . '_br_top_left_forms',
    'px',
  ),
  'border-bottom-right-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_right_forms',
    'px',
  ),
  'border-bottom-left-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_left_forms',
    'px',
  ),
  'border-width' => array(
    PRSV_OPTION_CODE . '_border_width_form',
    'px',
  ),
  'border-style' => array(
    PRSV_OPTION_CODE . '_border_style_form',
    '',
  ),
  'border-color' => array(
    PRSV_OPTION_CODE . '_color_border_form',
    '',
  ),
));
prsv_get_row_css('.survey_general_container textarea,.survey_general_container input[type="date"],.survey_general_container input[type="number"],.survey_general_container input[type="email"],.survey_general_container input[type="text"]', array(
  'font-family' => array(
    PRSV_OPTION_CODE . '_setting_description_font',
    ''
  ),
  'font-size' => array(
    PRSV_OPTION_CODE . '_font_size_descriptions',
    'px',
  ),
  'line-height' => array(
    PRSV_OPTION_CODE . '_line_height_descriptions',
    'px',
  ),
  'font-weight' => array(
    PRSV_OPTION_CODE . '_font_weight_descriptions',
    ''
  ),
  'background-color' => array(
    PRSV_OPTION_CODE . '_color_background_form',
    '',
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_text_form',
    '',
  ),
  'padding-top' => array(
    PRSV_OPTION_CODE . '_padding_forms_top',
    'px',
  ),
  'padding-bottom' => array(
    PRSV_OPTION_CODE . '_padding_forms_bottom',
    'px',
  ),
  'padding-right' => array(
    PRSV_OPTION_CODE . '_padding_forms_right',
    'px',
  ),
  'padding-left' => array(
    PRSV_OPTION_CODE . '_padding_forms_left',
    'px',
  ),
  'border-top-right-radius' => array(
    PRSV_OPTION_CODE . '_br_top_right_forms',
    'px',
  ),
  'border-top-left-radius' => array(
    PRSV_OPTION_CODE . '_br_top_left_forms',
    'px',
  ),
  'border-bottom-right-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_right_forms',
    'px',
  ),
  'border-bottom-left-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_left_forms',
    'px',
  ),
  'border-width' => array(
    PRSV_OPTION_CODE . '_border_width_form',
    'px',
  ),
  'border-style' => array(
    PRSV_OPTION_CODE . '_border_style_form',
    '',
  ),
  'border-color' => array(
    PRSV_OPTION_CODE . '_color_border_form',
    '',
  ),
  'outline' => array(
    'none',
    'none',
  )
));
prsv_get_row_css('.swal-title', array(
  'color' => array(
    PRSV_OPTION_CODE . '_color_background_buttons',
    '',
  ),
));
prsv_get_row_css('.survey_general_container select:focus', array(
  'background-color' => array(
    PRSV_OPTION_CODE . '_color_background_form_focus',
    '',
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_text_form_focus',
    '',
  ),
  'padding-right' => array(
    PRSV_OPTION_CODE . '_padding_forms_right_focus',
    'px',
  ),
  'padding-left' => array(
    PRSV_OPTION_CODE . '_padding_forms_left_focus',
    'px',
  ),
  'border-top-right-radius' => array(
    PRSV_OPTION_CODE . '_br_top_right_forms_focus',
    'px',
  ),
  'border-top-left-radius' => array(
    PRSV_OPTION_CODE . '_br_top_left_forms_focus',
    'px',
  ),
  'border-bottom-right-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_right_forms_focus',
    'px',
  ),
  'border-bottom-left-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_left_forms_focus',
    'px',
  ),
  'border-width' => array(
    PRSV_OPTION_CODE . '_border_width_form_focus',
    'px',
  ),
  'border-style' => array(
    PRSV_OPTION_CODE . '_border_style_form_focus',
    '',
  ),
  'border-color' => array(
    PRSV_OPTION_CODE . '_color_border_form_focus',
    '',
  ),
));
prsv_get_row_css('.survey_general_container textarea:focus,.survey_general_container input[type="date"]:focus,.survey_general_container input[type="number"]:focus,.survey_general_container input[type="email"]:focus,.survey_general_container input[type="text"]:focus', array(
  'background-color' => array(
    PRSV_OPTION_CODE . '_color_background_form_focus',
    '',
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_text_form_focus',
    '',
  ),
  'padding-top' => array(
    PRSV_OPTION_CODE . '_padding_forms_top_focus',
    'px',
  ),
  'padding-bottom' => array(
    PRSV_OPTION_CODE . '_padding_forms_bottom_focus',
    'px',
  ),
  'padding-right' => array(
    PRSV_OPTION_CODE . '_padding_forms_right_focus',
    'px',
  ),
  'padding-left' => array(
    PRSV_OPTION_CODE . '_padding_forms_left_focus',
    'px',
  ),
  'border-top-right-radius' => array(
    PRSV_OPTION_CODE . '_br_top_right_forms_focus',
    'px',
  ),
  'border-top-left-radius' => array(
    PRSV_OPTION_CODE . '_br_top_left_forms_focus',
    'px',
  ),
  'border-bottom-right-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_right_forms_focus',
    'px',
  ),
  'border-bottom-left-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_left_forms_focus',
    'px',
  ),
  'border-width' => array(
    PRSV_OPTION_CODE . '_border_width_form_focus',
    'px',
  ),
  'border-style' => array(
    PRSV_OPTION_CODE . '_border_style_form_focus',
    '',
  ),
  'border-color' => array(
    PRSV_OPTION_CODE . '_color_border_form_focus',
    '',
  ),
));
prsv_get_row_css('.survey-container', array(
  'background-color' => array(
    PRSV_OPTION_CODE . '_color_background_general_container',
    '',
  ),
  'margin-top' => array(
    PRSV_OPTION_CODE . '_margin_top_general_container',
    'px',
  ),
  'margin-bottom' => array(
    PRSV_OPTION_CODE . '_margin_bottom_general_container',
    'px',
  ),
  'margin-right' => array(
    PRSV_OPTION_CODE . '_margin_right_general_container',
    '',
  ),
  'margin-left' => array(
    PRSV_OPTION_CODE . '_margin_left_general_container',
    '',
  ),
  'padding-top' => array(
    PRSV_OPTION_CODE . '_padding_top_general_container',
    'px',
  ),
  'padding-bottom' => array(
    PRSV_OPTION_CODE . '_padding_bottom_general_container',
    'px',
  ),
  'padding-right' => array(
    PRSV_OPTION_CODE . '_padding_right_general_container',
    'px',
  ),
  'padding-left' => array(
    PRSV_OPTION_CODE . '_padding_left_general_container',
    'px',
  ),
  'border-top-right-radius' => array(
    PRSV_OPTION_CODE . '_br_top_right_general_container',
    'px',
  ),
  'border-top-left-radius' => array(
    PRSV_OPTION_CODE . '_br_top_left_general_container',
    'px',
  ),
  'border-bottom-right-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_right_general_container',
    'px',
  ),
  'border-bottom-left-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_left_general_container',
    'px',
  ),
  'border-width' => array(
    PRSV_OPTION_CODE . '_border_width_general_container',
    'px',
  ),
  'border-style' => array(
    PRSV_OPTION_CODE . '_border_style_general_container',
    '',
  ),
  'border-color' => array(
    PRSV_OPTION_CODE . '_color_border_general_container',
    '',
  ),
));
prsv_get_row_css('.survey_general_container label, span.ps_span_title, .survey_general_container .ps_resposive_table table th, .survey_general_container .ps_resposive_table table td', array(
  'font-family' => array(
    PRSV_OPTION_CODE . '_setting_label_font',
    '',
  ),
  'font-size' => array(
    PRSV_OPTION_CODE . '_font_size_labels',
    'px',
  ),
  'line-height' => array(
    PRSV_OPTION_CODE . '_line_height_labels',
    'px',
  ),
  'font-weight' => array(
    PRSV_OPTION_CODE . '_font_weight_labels',
    '',
  ),
  'letter-spacing' => array(
    PRSV_OPTION_CODE . '_letter_spacing_labels',
    'px',
  ),
  'color' => array(
    PRSV_OPTION_CODE . '_color_labels_option',
    '',
  )
));
prsv_get_row_css('.survey_general_container .check-btn input[type="checkbox"]:checked ~ span:before, .survey_general_container .radio-btn input[type="radio"]:checked ~ span:before', array(
  'background-color' => array(
    PRSV_OPTION_CODE . '_active_background_color_checkradio',
    '',
  ),
));
prsv_get_row_css('.survey_general_container .ps_resposive_table tbody tr:nth-child(odd)', array(
  'background-color' => array(
    PRSV_OPTION_CODE . '_alternate_color_table_matrix',
    '',
  )
));
prsv_get_row_css('.survey_general_container .ps_resposive_table table', array(
  'background-color' => array(
    PRSV_OPTION_CODE . '_background_color_table_matrix',
    ''
  ),
));
prsv_get_row_css('.survey_general_container .ps_resposive_table table th,.survey_general_container .ps_resposive_table table td', array(
  'border-width' => array(
    PRSV_OPTION_CODE . '_border_width_table_matrix',
    'px',
  ),
  'border-style' => array(
    PRSV_OPTION_CODE . '_border_style_table_matrix',
    '',
  ),
  'border-color' => array(
    PRSV_OPTION_CODE . '_border_color_table_matrix',
    '',
  ),
  'padding-top' => array(
    PRSV_OPTION_CODE . '_padding_top_bottom_cell_matrix',
    'px',
  ),
  'padding-bottom' => array(
    PRSV_OPTION_CODE . '_padding_top_bottom_cell_matrix',
    'px',
  ),
  'padding-left' => array(
    PRSV_OPTION_CODE . '_padding_right_left_cell_matrix',
    'px',
  ),
  'padding-right' => array(
    PRSV_OPTION_CODE . '_padding_right_left_cell_matrix',
    'px',
  ),
));
prsv_get_row_css('.survey_general_container .survey-image-block-frontend', array(
  'padding-top' => array(
    PRSV_OPTION_CODE . '_padding_top_image_choice',
    'px',
  ),
  'padding-bottom' => array(
    PRSV_OPTION_CODE . '_padding_bottom_image_choice',
    'px',
  ),
  'padding-left' => array(
    PRSV_OPTION_CODE . '_padding_left_image_choice',
    'px',
  ),
  'padding-right' => array(
    PRSV_OPTION_CODE . '_padding_right_image_choice',
    'px',
  ),
  'border-width' => array(
    PRSV_OPTION_CODE . '_border_width_image_choice',
    'px',
  ),
  'border-style' => array(
    PRSV_OPTION_CODE . '_border_style_image_choice',
    '',
  ),
  'border-color' => array(
    PRSV_OPTION_CODE . '_color_border_image_choice',
    '',
  ),
  'background-color' => array(
    PRSV_OPTION_CODE . '_color_background_image_choice',
    ''
  ),
  'border-top-right-radius' => array(
    PRSV_OPTION_CODE . '_br_top_right_image_choice',
    'px',
  ),
  'border-top-left-radius' => array(
    PRSV_OPTION_CODE . '_br_top_left_image_choice',
    'px',
  ),
  'border-bottom-right-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_right_image_choice',
    'px',
  ),
  'border-bottom-left-radius' => array(
    PRSV_OPTION_CODE . '_br_bottom_left_image_choice',
    'px',
  ),
));
echo prsv_global_options_get(PRSV_OPTION_CODE . '_custom_css');
