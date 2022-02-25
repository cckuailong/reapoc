<div class="survey_delete_element">
  <a href="#" class="survey-btn-support survey_alert btn-delete-answer"
  data-tooltip="<?php _e('Delete element', 'perfect-survey') ?>"
  data-confirm-text="<?php _e((!empty($answer_value) ? 'Are you sure to delete this answer value?' : 'Are you sure to delete this answer?') . ' All relative stats will be removed', 'perfect-survey');?>"
  data-multiple-answers="<?php echo $question_type['multiple_answers'] ? 'true' : 'false';?>"
  data-answer-value-id="<?php echo !empty($answer_value) ? $answer_value['answer_value_id'] : '';?>"
  data-answer-id="<?php echo !empty($answer) ? $answer['answer_id'] : '';?>"
  data-action="<?php echo !empty($answer_value) ? 'delete_answer_value' : 'delete_answer';?>" >
  <i class="pswp_set_icon-cross"></i>
</a>
</div>
