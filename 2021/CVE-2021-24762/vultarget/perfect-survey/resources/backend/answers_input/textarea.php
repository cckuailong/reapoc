<tr>
  <td>
    <div class="survey_answer_box">
      <div class="survey_move_element">
        <span class="survey-btn-support survey_positive"><i class="pswp_set_icon-enlarge"></i></span>
      </div>
      <div class="survey-wrap-input survey_center_form_element">
        <textarea  name="ps_answers[<?php echo $answer['answer_id'];?>][text]" placeholder="<?php _e('Insert an answer', 'perfect-survey') ?>" value=""/>
          <input type="hidden" name="ps_answers[<?php echo $answer['answer_id'];?>][position]" value="<?php echo $answer['position'];?>"/>
        </div>
        <?php prsv_resource_include_backend('answers_box/delete_btn', array('answer' => $answer,'question' => $question,'question_type' => $question_type)) ?>
      </div>
    </td>
  </tr>
