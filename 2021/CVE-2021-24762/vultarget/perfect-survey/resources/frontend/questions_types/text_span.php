<div id="survey_question_<?php echo $question['question_id'];?>" class="survey_question_box survey_single_question" data-question-id="<?php echo $question['question_id'];?>">
  <?php echo $question['description'];?>
  <input type="hidden" name="ps_questions[<?php echo $question['question_id'];?>]" value="0" />
</div>
