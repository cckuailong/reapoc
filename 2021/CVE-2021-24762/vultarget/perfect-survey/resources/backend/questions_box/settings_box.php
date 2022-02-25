<ul class="survey_optional_settings">
  <?php if (!empty($question_type['answer_show_types'])) { ?>
    <li class="ps_titleform_settings"><?php _e('Show answers type', 'perfect-survey'); ?></li>
    <?php foreach ($question_type['answer_show_types'] as $answer_show_type => $answer_show_type_text) { ?>
      <li>
        <label>
          <input name="ps_questions[<?php echo $question['question_id']; ?>][answer_show_type]" type="radio" value="<?php echo $answer_show_type; ?>" <?php echo $question['answer_show_type'] == $answer_show_type ? 'checked="checked"' : ''; ?>>
          <?php echo $answer_show_type_text['name']; ?>
        </label>
      </li>
    <?php } ?>
  <?php } ?>
</ul>
