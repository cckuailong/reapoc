<?php
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
?>
<div id="survey_question_body_<?php echo $question['question_id'];?>" class="survey_question_body">
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
                  <th><?php _e('Answer', 'perfect-survey') ?></th>
                  <th><?php _e('Answers', 'perfect-survey') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($answers as $key => $answer){ ?>
                  <tr>
                    <td><?php echo $answer['text'];?></td>
                    <td><?php echo $answer['total'];?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
