<?php
global $ps_post_meta;
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

require 'header_stats/header_stats_single.php';

if($ps_post_meta['ps_survey_tables_frontend'] == 'statistics_table_on') {
?>
<div class="ps_resposive_table">
  <table class="psrw_table_stilings_data" cellspacing="0">
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
          <td <?php echo prsv_is_answer_my($question,$answers[0] + $answer_value,$question['question_data']) ? 'class="ps_ui_answer_is_my"' : '';?>>
            <?php echo $answer_value['tot'];?>
          </td>
        <?php } ?>
      </tr>
    </tbody>
  </table>
</div>
<?php } ?>
