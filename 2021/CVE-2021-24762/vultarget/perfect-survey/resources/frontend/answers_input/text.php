<label class="<?php echo $question_type['answer_input_type'] == 'radio' ? 'radio-btn' : 'check-btn'; ?>">
  <?php switch($question['answer_show_type']){
    case 'select': ?>
    <option value="<?php echo $answer['answer_id'];?>"><?php echo esc_html($answer['text']);?></option>
    <?php
    break;
    default:  ?>
    <input type="<?php echo $question_type['answer_input_type'];?>"  name="ps_questions[<?php echo $question['question_id'];?>][]" value="<?php echo $answer['answer_id'];?>" class="ps-answers" />
    <span></span>
    <span class="ps_survey_onlyext"><?php echo esc_html($answer['text']);?></span>
    <?php
  }
  ?>
</label>
