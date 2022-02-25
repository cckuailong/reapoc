<?php
global $ps_post_meta;

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

require 'header_stats/header_stats_single.php';


if($ps_post_meta['ps_survey_tables_frontend'] === 'statistics_table_on') {
?>
<div class="ps_resposive_table">
  <table class="psrw_table_stilings_data" cellspacing="0">
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
            <img src="<?php echo $answer['post']->guid;?>" alt="<?php echo $answer['description'];?>" width="70px" />
          </td>
          <td>
            <?php echo $answer['name'];?>
          </td>
          <td <?php echo prsv_is_answer_my($question,$answer,$question['question_data']) ? 'class="ps_ui_answer_is_my"' : '';?>>
            <?php echo $answer['total'];?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<?php } ?>
