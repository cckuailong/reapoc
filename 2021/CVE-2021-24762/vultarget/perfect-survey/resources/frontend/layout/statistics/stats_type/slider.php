<?php
global $ps_post_meta;
$answers_totals = $answers_labels = $answers_rgb = array();

foreach($answers as $key => $answer)
{
  foreach($answers_values as $i => $answer_value)
  {
    $tot = prsv_get_post_type_model()->count_answers_values($answer_value['answer_value_id'],$answer['answer_id'], $filters);

    $answer['values'][$answer_value['answer_value_id']] = array(
      'value' => $answer_value['value'].'/'.$question['answer_max_value'],
      'tot'   => $tot
    );
    $answer_value['tot'] = $tot;
    $answers_totals[$answer['answer_id']][$answer_value['answer_value_id']] = $tot;
    $answers_labels[$answer['answer_id']][$answer_value['answer_value_id']] = $answer_value['value'];
    $answers_rgb[$answer['answer_id']][$answer_value['answer_value_id']]    = 'rgb('.rand(200,255).', '.rand(200,255).', '.rand(200,255).')';
    $answers_values[$i] = $answer_value;
  }

  $answers[$key]    = $answer;
}

require 'header_stats/header_stats_multi.php';

if($ps_post_meta['ps_survey_tables_frontend'] == 'statistics_table_on') {
?>
<div class="ps_resposive_table">
  <table class="psrw_table_stilings_data" cellspacing="0">
    <thead>
      <tr>
        <th><?php _e('Answers', 'perfect-survey') ?></th>
        <?php foreach($answers_values as $answer_value){ ?>
          <th><?php echo $answer_value['value'];?>/<?php echo $question['answer_max_value'];?></th>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($answers as $answer){ ?>
        <tr>
          <td><strong><?php echo $answer['text'];?></strong></td>
          <?php foreach($answer['values'] as $answer_value){ ?>
            <td <?php echo prsv_is_answer_my($question,$answer_value,$question['question_data']) ? 'class="ps_ui_answer_is_my"' : '';?>>
              <?php echo $answer_value['tot']; ?>
            </td>
          <?php } ?>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<?php } ?>
