<tr>
  <td>
    <div class="survey-block-image-general">
      <div class="survey-image-header-controll">
        <div class="survey_move_element">
          <span class="survey-btn-support survey_positive"><i class="pswp_set_icon-enlarge"></i></span>
        </div>
        <div class="survey-image-delete-item">
          <?php prsv_resource_include_backend('answers_box/delete_btn', array('answer' => $answer, 'question' => $question, 'question_type' => $question_type)) ?>
        </div>
      </div>
      <div class="survey-wrap-input">
        <ul>
          <?php for ($i = 0; $i < $question['answer_max_value']; $i++) { ?>
            <li>
              <i class="<?php echo $question['answer_css_class']; ?> survey-rating-single"></i>
            </li>
          <?php } ?>
        </ul>
        <input type="text" name="ps_answers[<?php echo $answer['answer_id']; ?>][text]" placeholder="<?php _e('Insert an answer', 'perfect-survey') ?>" value="<?php echo $answer['text']; ?>"/>
        <input type="hidden" name="ps_answers[<?php echo $answer['answer_id']; ?>][position]" value="<?php echo $answer['position']; ?>"/>
      </div>
    </div>
  </td>
</tr>
