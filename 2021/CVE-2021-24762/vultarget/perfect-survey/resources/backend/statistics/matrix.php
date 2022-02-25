<?php

$answers_totals = $answers_labels = $answers_rgb = array();

foreach($answers as $key => $answer)
{
  foreach($answers_values as $answer_value)
  {
    $tot = prsv_get_post_type_model()->count_answers_values($answer_value['answer_value_id'],$answer['answer_id'], $filters);

    $answer['values'][$answer_value['answer_value_id']] = array(
      'value' => $answer_value['value'],
      'tot'   => $tot
    );

    $answers_totals[$answer['answer_id']][$answer_value['answer_value_id']] = $tot;
    $answers_labels[$answer['answer_id']][$answer_value['answer_value_id']] = $answer_value['value'];
    $answers_rgb[$answer['answer_id']][$answer_value['answer_value_id']]    = 'rgb('.rand(200,255).', '.rand(200,255).', '.rand(200,255).')';
  }

  $answers[$key]    = $answer;
}

?>
<div id="survey_question_body_<?php echo $question['question_id'];?>" class="survey_question_body">
  <?php if($show_chart){
    require 'header_stats/header_stats_multi.php';
  } ?>
  <div class="ps_resposive_table">
    <table class="widefat survey_settings survey_input" cellspacing="0">
      <tbody>
        <tr>
          <td class='survey_container_table'>
            <table class="introdata display" cellspacing="0">
              <thead>
                <tr>
                  <th><?php _e('Answers', 'perfect-survey') ?></th>
                  <?php foreach($answers_values as $answer_value){ ?>
                    <th><?php echo $answer_value['value'];?></th>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach($answers as $answer){ ?>
                  <tr>
                    <td><strong><?php echo $answer['text'];?></strong></td>
                    <?php foreach($answer['values'] as $answer_value){ ?>
                      <td>
                        <?php echo $answer_value['tot'];?>
                      </td>
                    <?php } ?>
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
