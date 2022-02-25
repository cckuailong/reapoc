<?php
if (!defined('ABSPATH'))
exit; // Exit if accessed directly

/**
* Global vars
*/
global $ps_post; /* @var $post WP_Post */
global $ps_post_atts; /* @var $ps_post_atts array */
global $ps_questions; /* @var $ps_questions array current questions */
global $ps_all_questions; /* @var $ps_all_questions array all available survey questions */
global $ps_answers; /* @var $ps_answers array */
global $ps_answers_values; /* @var $ps_answers_values array */
global $ps_post_meta; /* @var $ps_post_meta array */

$progress = prsv_global_options_get('ps_options_progress_bar_active');
$progress_position = prsv_global_options_get('ps_options_position_progressbar');
$pagination_position = prsv_global_options_get('ps_options_settings_position_pagination');
$maincolors = $ps_post_meta['ps_survey_main_color_frontend_single'];
/**
* There are a questions to show
*/
if (!empty($ps_questions)) {
  $survey_form_id = "survey-question-form-" . $ps_post->ID;
  ?>
  <div id="<?php echo $survey_form_id; ?>" class="survey-container" data-ID="<?php echo $ps_post->ID; ?>" data-metadata="<?php echo htmlentities(json_encode($ps_post_meta)); ?>" data-total-questions="<?php echo count($ps_questions); ?>">
    <?php if ($ps_post_meta['ps_survey_turn_on'] === 'survey_on') { ?>
      <?php
      if (!$ps_post->complete && $ps_post_meta['ps_question_submit_complete'] == 'one') {
        if ($pagination_position == 'above_questions') {
          require 'layout/pagination/pagination.php';
        }
        if ($progress_position == 'progressbar_top' && $progress == 'progress_bar_on') {
          require 'layout/progressbar/progressbar.php';
        }
      }
      ?>
      <div class="survey_general_container submit-<?php echo $ps_post_meta['ps_question_submit_complete']; ?>">
        <?php
        foreach ($ps_questions as $question) {
          ?>
          <div class="survey_question_container <?php echo $question['css_class']; ?>">
            <?php
            prsv_resource_include_frontend($question['frontend_template'], array(
              'question' => $question,
              'answers' => $question['answers'],
              'answers_values' => $question['answers_values'],
              'question_type' => $question['question_type']
            ));
            ?>
          </div>
          <?php
        }
        if (!$ps_post->complete && $ps_post_meta['ps_question_submit_complete'] == 'one') {
          if ($progress_position == 'progressbar_bottom' && $progress == 'progress_bar_on') {
            require 'layout/progressbar/progressbar.php';
          }
          if ($pagination_position == 'below_questions' ) {
            require 'layout/pagination/pagination.php';
          }
        }
        if ($ps_post->complete) { ?>
          <div class="survey-complete-message">
            <?php
            if(!empty($ps_post_meta['ps_success_message_complete'])) {
              echo $ps_post_meta['ps_success_message_complete'];
            } else {
              _e('Thank you for submitting the questionnaire, your data has been successfully saved.', 'perfect-survey');
            }
            ?>
          </div>
        <?php } else { ?>
          <div class="survey_btns">
            <button class="post-edit-btn ps_survey_btn_submit survey_submit_btn">
              <?php echo $ps_post->submit_btn_text; ?>
            </button>
          </div>
        <?php } ?>
      </div>
      <style type="text/css">
      <?php
      require_once 'layout/rulesgenerator/rulesgenerator.php';
      if($maincolors != '') {
        echo '.survey_general_container select:focus{color:'.$maincolors.';border-color:'.$maincolors.';}.survey_general_container .survey_question_container h2 {color: '.$maincolors.'}.survey_general_container .check-btn input[type="checkbox"]:checked ~ span:before, .survey_general_container .radio-btn input[type="radio"]:checked ~ span:before {background-color: '.$maincolors.'}button.post-edit-btn.ps_survey_btn_submit.survey_submit_btn, .swal-button {background: '.$maincolors.';border-color: '.$maincolors.';}.survey_general_container textarea:focus, .survey_general_container input[type="date"]:focus, .survey_general_container input[type="number"]:focus, .survey_general_container input[type="email"]:focus, .survey_general_container input[type="text"]:focus {border-color: '.$maincolors.';color: '.$maincolors.';}.ui-widget.ui-widget-content.ps_ui_customize_survey, .ps_ui_customize_survey .ui-state-default, .ui-widget-content.ps_ui_customize_survey .ui-state-default, .ui-datepicker.ps_ui_customize_survey table, .ps_ui_customize_survey .ui-state-highlight, .ui-widget-content.ps_ui_customize_survey .ui-state-highlight, .ps_ui_customize_survey table.ui-datepicker-calendar thead, .ps_ui_customize_survey .ui-widget-header, .ps_ui_customize_survey .ui-state-active, .ui-widget-content.ps_ui_customize_survey .ui-state-active {background: '.$maincolors.';}.ps_paginator_step ul li.ps_checked.ps_current {background: '.$maincolors.';border-color: '.$maincolors.';}.ps_paginator_step ul li.ps_checked {background: #ffffff;border-width: 2px;border-style: solid;border-color: '.$maincolors.';}button.swal-button.swal-button--confirm:focus {background: '.$maincolors.';box-shadow: 0 0 0 1px #fff, 0 0 0 3px '.$maincolors.';}button.swal-button.swal-button--confirm:hover {background: '.$maincolors.';box-shadow: 0 0 0 1px #fff, 0 0 0 3px '.$maincolors.';opacity:0.5}';
      }
      ?>
      </style>
    <?php } else {  ?>
      <div class="ps_survey_suspended">
        <p><?php echo $ps_post_meta['ps_survey_turn_off_message']; ?></p>
      </div>
    <?php } echo prsv_dankon_amiko(); ?>
  </div>
  <?php
  if ($ps_post_meta['ps_survey_stats_frontend'] === 'statistics_frontend_on') {
    if($ps_post_meta['ps_survey_stats_frontend_end_survey'] === 'statistics_frontend_always') {
      require 'layout/statistics/frontend_statistics.php';
    } elseif ($ps_post_meta['ps_survey_stats_frontend_end_survey'] === 'statistics_frontend_on_end' && $ps_post->complete) {
      require 'layout/statistics/frontend_statistics.php';
    }
  }
}
?>