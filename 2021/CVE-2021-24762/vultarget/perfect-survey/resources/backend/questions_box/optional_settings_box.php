<ul class="survey_optional_settings">
  <?php if (!empty($question_type['answer_max_values'])) { ?>
    <li class="ps_all_select_setting">
      <label class="ps_label_select_setting"><?php _e('Values range', 'perfect-survey'); ?></label>
      <select name="ps_questions[<?php echo $question['question_id']; ?>][answer_max_value]">
        <option <?php !$question['answer_max_value'] ? 'selected="selected"' : ''; ?> value=""><?php _e('Number of', 'perfect-survey') ?> <?php _e('stars', 'perfect-survey') ?></option>
        <?php foreach ($question_type['answer_max_values'] as $max_value => $max_value_name) { ?>
          <option value="<?php echo $max_value; ?>" <?php echo $question['answer_max_value'] == $max_value ? 'selected="selected"' : ''; ?> ><?php echo $max_value_name; ?></option>
        <?php } ?>
      </select>
    </li>
  <?php } ?>
  <?php if (!empty($question_type['answer_css_classes'])) { ?>
    <li class="ps_all_select_setting">
      <label class="ps_label_select_setting"><?php _e('Icon type', 'perfect-survey'); ?></label>
      <select name="ps_questions[<?php echo $question['question_id']; ?>][answer_css_class]">
        <option value="" <?php echo!$question['answer_css_class'] ? 'selected="selected"' : ''; ?> value="" ><?php _e('Icon type', 'perfect-survey') ?></option>
        <?php foreach ($question_type['answer_css_classes'] as $css_class => $css_class_name) { ?>
          <option value="<?php echo $css_class; ?>" <?php echo $question['answer_css_class'] == $css_class ? 'selected="selected"' : ''; ?> ><?php echo $css_class_name; ?></option>
        <?php } ?>
      </select>
    </li>
  <?php } ?>
  <?php if (!empty($question_type['answer_field_types'])) { ?>
    <li class="ps_titleform_settings"><?php _e('Answer field type', 'perfect-survey'); ?></li>
    <?php foreach ($question_type['answer_field_types'] as $field_type => $field_name) { ?>
      <li>
        <label>
          <input type="radio" name="ps_questions[<?php echo $question['question_id']; ?>][answer_field_type]" value="<?php echo $field_type; ?>" <?php echo $question['answer_field_type'] == $field_type ? 'checked="checked"' : ''; ?> ><?php echo $field_name; ?></option>
        </label>
      </li>
    <?php } ?>
  <?php } ?>
</ul>
