<?php $answers_data = prsv_get_post_type_model()->get_question_data($question['question_id'], null,false, !empty($filters['session_id']) ? $filters['session_id'] : null, !empty($filters['user_id']) ? $filters['user_id'] : null); ?>
<div class="ps_resposive_table">
  <table class="psrw_table_stilings_data" cellspacing="0">
    <thead>
      <tr>
        <th><?php _e('Answers', 'perfect-survey') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($answers_data as $answer_data){ ?>
        <?php if($answer_data['value']) { ?>
        <tr>
          <td <?php echo prsv_is_answer_my($question,$answer_data,$question['question_data']) ? 'class="ps_ui_answer_is_my"' : '';?>><?php echo $answer_data['value'];?></td>
        </tr>
        <?php } ?>
      <?php } ?>
    </tbody>
  </table>
</div>
