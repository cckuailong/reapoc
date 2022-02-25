<div class="survey-wrap-input">
  <label class="check-btn-input-custom">
    <?php
    switch($question['answer_field_type'])
    {
      case 'textarea': ?>

      <textarea rows="3" name="ps_questions[<?php echo $question['question_id']; ?>][<?php echo $answer['answer_id'];?>]" class="survey_textarea" maxlength="1000"></textarea>
      <div class="ps_perfect_survey_spancounter">1000 / 1000</div>

      <?php break;
      case 'date': ?>

      <input type="text" name="ps_questions[<?php echo $question['question_id']; ?>][<?php echo $answer['answer_id'];?>]" class="ps-datepicker">

      <?php break;
      default:

      if($question['answer_field_type'] == 'number') {
        $stringname = __('Number', 'perfect-survey');
      } elseif($question['answer_field_type'] == 'email') {
        $stringname = __('E-mail','perfect-survey');
      } elseif($question['answer_field_type'] == 'text') {
        $stringname = __('Text','perfect-survey');
      }
      ?>
      <input type="<?php echo $question['answer_field_type'];?>" name="ps_questions[<?php echo $question['question_id']; ?>][<?php echo $answer['answer_id'];?>]">

      <?php break;
    }
    ?>
  </label>
</div>
