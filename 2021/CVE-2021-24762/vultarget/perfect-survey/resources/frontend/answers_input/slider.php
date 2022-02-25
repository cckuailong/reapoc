<div class="survey-wrap-input">
  <span class="ps_span_title"><?php echo esc_html($answer['text']); ?></span>
  <ul>
    <?php foreach($answers_values as $answer_value){ ?>
      <li>
        <label>
          <i class="<?php echo $question['answer_css_class'];?> survey-rating slider"></i>
          <input type="radio" value="<?php echo $answer_value['answer_value_id'];?>" name="ps_questions[<?php echo $question['question_id']; ?>][<?php echo $answer['answer_id'];?>][]" style="display: none;"/>
        </label>
      </li>
    <?php } ?>
  </ul>
</div>
