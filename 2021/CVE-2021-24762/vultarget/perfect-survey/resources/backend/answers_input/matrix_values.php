<tr>
  <td>
    <div class="survey_answer_box">
      <div class="survey_move_element">
        <span class="survey-btn-support survey_positive"><i class="pswp_set_icon-enlarge"></i></span>
      </div>
      <div class="survey-wrap-input survey_center_form_element">
        <input type="text" name="ps_answers_values[<?php echo $answer_value['answer_value_id'];?>][value]" placeholder="<?php _e('Insert an answer value', 'perfect-survey') ?>" value="<?php echo $answer_value['value'];?>"/>
        <input type="hidden" name="ps_answers_values[<?php echo $answer_value['answer_value_id'];?>][position]" value="<?php echo $answer_value['position'];?>"/>
      </div>
      <?php prsv_resource_include_backend('answers_box/delete_btn', array('answer_value' => $answer_value,'question' => $question,'question_type' => $question_type)) ?>
    </div>
  </td>
</tr>
