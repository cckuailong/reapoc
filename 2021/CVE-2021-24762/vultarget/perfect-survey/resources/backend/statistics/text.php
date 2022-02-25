<?php $answers_data = prsv_get_post_type_model()->get_question_data($question['question_id'], null,false, !empty($filters['session_id']) ? $filters['session_id'] : null, !empty($filters['user_id']) ? $filters['user_id'] : null); ?>
<div class="survey_question_body">
  <div class="ps_resposive_table">
    <table class="widefat survey_settings survey_input" cellspacing="0">
      <tbody>
        <tr>
          <td class='survey_container_table'>
            <table class="introdata display" cellspacing="0">
              <thead>
                <tr>
                  <th><?php _e('Answers', 'perfect-survey') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($answers_data as $answer_data){ ?>
                  <?php if($answer_data['value']) { ?>
                    <tr>
                      <td><?php echo $answer_data['value'];?></td>
                    </tr>
                  <?php } ?>
                <?php } ?>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
