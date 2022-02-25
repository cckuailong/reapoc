<?php

$answers_totals = $answers_labels = $answers_rgb = array();

foreach($answers_values as $key => $answer_value)
{
  $tot = prsv_get_post_type_model()->count_answers_values($answer_value['answer_value_id'], null, $filters);
  $answers_totals[$answer_value['answer_value_id']] = $tot;
  $answers_labels[$answer_value['answer_value_id']] = $answer_value['value'].'/'.$question['answer_max_value'];
  $answers_rgb[$answer_value['answer_value_id']]    = 'rgb('.rand(200,255).', '.rand(200,255).', '.rand(200,255).')';
  $answer_value['tot']   = $tot;
  $answers_values[$key]  = $answer_value;
}
?>
<div id="survey_question_body_<?php echo $question['question_id'];?>"  class="survey_question_body">
  <?php if($show_chart){
    require 'header_stats/header_stats_single.php';
  } ?>
  <div class="ps_resposive_table">
    <table class="widefat survey_settings survey_input" cellspacing="0">
      <tbody>
        <tr>
          <td class='survey_container_table'>
            <table class="introdata display" cellspacing="0">
              <thead>
                <tr>
                  <th><?php _e('Question', 'perfect-survey') ?></th>
                  <?php foreach($answers_values as $answer_value){ ?>
                    <th><?php echo $answer_value['value'];?>/<?php echo $question['answer_max_value'];?></th>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong><?php echo $question['text'];?></strong></td>
                  <?php foreach($answers_values as $answer_value){ ?>
                    <td>
                      <?php echo $answer_value['tot'];?>
                    </td>
                  <?php } ?>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
