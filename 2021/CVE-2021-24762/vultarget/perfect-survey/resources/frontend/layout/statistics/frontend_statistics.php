<?php

$ID         = $ps_post->ID;
$filters    = prsv_input_get('filters',array());
$usermeta   = !empty($filters) ? get_user_meta($filters['user_id']): array();
$show_chart = empty($filters);

if(!$ID)
{
  wp_die('Survey ID not valid!');
}

$post      = prsv_get_post_type_model()->get_survey($ID);
$questions = prsv_get_post_type_model()->get_questions($ID, array('answers' => true));

foreach($questions as $key => $question)
{
  $question['question_data'] = prsv_get_post_type_model()->get_question_data($question['question_id']);
  $questions[$key]           = $question;
  
  if(in_array($question['type'], array(PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_TEXT_SPAN)))
  {
    unset($questions[$key]);
  }
}

?>
<div class="ps_sfe_statistics">
  <div class="ps_sfe_toglger">
    <p class="ps_sfe_toglger_paragraph"><?php _e('Statistics', 'perfect-survey') ?> - <span class="ps_sfe_toglger_span"><?php _e('View the results', 'perfect-survey') ?></span></p>
    <div class="ps_sfe_toglger_arrow_bottom"><div class="ps_sfe_toglger_arrow_in"></div></div>
  </div>
  <div class="ps_sfw_allstats_content" style="display: none">
    <?php foreach($questions as $question) { ?>
      <div class="ps_sfe_statistics_body">
        <div class="ps_sfe_questions">
          <p class="ps_sfe_question"><?php echo esc_html($question['text']); ?></p>
        </div>
        <div class="ps_sfe_result">
          <?php prsv_resource_include_frontend('layout/statistics/stats_type/'.$question['type'], array('show_chart'=>$show_chart, 'filters'=>$filters,'question' => $question, 'question_type' => $question['question_type'],'answers' => $question['answers'], 'answers_values' => $question['answers_values'])); ?>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
