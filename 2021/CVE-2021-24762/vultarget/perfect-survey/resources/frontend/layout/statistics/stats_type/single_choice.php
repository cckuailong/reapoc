<?php
global $ps_post_meta;
$answers_ids    = array();
$answers_labels = array();
$answers_rgb    = array();
foreach($answers as $key => $answer)
{
  $tot               = prsv_get_post_type_model()->count_answers($answer['answer_id'], $filters);
  $answers_ids[]    = $answer['answer_id'];
  $answers_labels[] = $answer['text'];
  $answers_totals[] = $tot;
  $answers_rgb[]    = 'rgb('.rand(200,255).', '.rand(200,255).', '.rand(200,255).')';
  $answer['total']  = $tot;
  $answers[$key]    = $answer;
}

require 'header_stats/header_stats_single.php';
if($ps_post_meta['ps_survey_tables_frontend'] == 'statistics_table_on') {
?>
<div class="ps_resposive_table">
  <table class="psrw_table_stilings_data" cellspacing="0">
    <thead>
      <tr>
        <th><?php _e('Answer', 'perfect-survey') ?></th>
        <th><?php _e('Answers', 'perfect-survey') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($answers as $key => $answer){ ?>
        <tr>
          <td <?php echo prsv_is_answer_my($question,$answer,$question['question_data']) ? 'class="ps_ui_answer_is_my"' : '';?>><?php echo $answer['text'];?></td>
          <td <?php echo prsv_is_answer_my($question,$answer,$question['question_data']) ? 'class="ps_ui_answer_is_my"' : '';?>><?php echo $answer['total'];?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<?php } ?>
