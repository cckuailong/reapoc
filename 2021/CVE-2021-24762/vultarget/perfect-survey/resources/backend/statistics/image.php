<?php

$answers_totals = $answers_labels = $answers_rgb = array();

foreach($answers as $key => $answer)
{
  $answer['total']  = prsv_get_post_type_model()->count_answers($answer['answer_id'], $filters);
  $post             = get_post($answer['image']);
  $answer['name']   = !empty($answer['text']) ? $answer['text'] : $post->post_name;
  $answer['post']   = $post;
  $answers_totals[] = $answer['total'];
  $answers_labels[] = $answer['name'];
  $answers_rgb[]    = 'rgb('.rand(200,255).', '.rand(200,255).', '.rand(200,255).')';
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
                  <th><?php _e('Images', 'perfect-survey') ?></th>
                  <th><?php _e('Answer', 'perfect-survey') ?></th>
                  <th><?php _e('Answers', 'perfect-survey') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($answers as $key => $answer){ ?>
                  <tr>
                    <td>
                      <img src="<?php echo $answer['post']->guid;?>" alt="<?php echo $answer['description'];?>" width="50px" />
                    </td>
                    <td>
                      <?php echo $answer['name'];?>
                    </td>
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
