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
      <div class="survey-wrap-input survey_image_container">
        <?php add_action('admin_enqueue_scripts', function () {
          if (is_admin()) wp_enqueue_media();
        }); ?>
        <div class="survey-image-single-block" style="background-image: url(<?php echo!empty($answer['image']) ? get_post($answer['image'])->guid : '#'; ?>);">
          <input name="ps_answers[<?php echo $answer['answer_id']; ?>][image]" type="hidden" value="<?php echo!empty($answer['image']) ? $answer['image'] : ''; ?>" class="process_custom_images regular-text">
          <input name="ps_answers[<?php echo $answer['answer_id']; ?>][image_properties]" type="hidden" value="<?php echo htmlspecialchars(is_array($answer['image_properties']) ? json_encode($answer['image_properties']) : $answer['image_properties']); ?>" class="process_custom_images_properties">
          <button class="answer-set-image-btn button survey-image-loads"><?php _e('Load Image', 'perfect-survey');?></button>
        </div>
      </div>
      <div class="survey-wrap-input">
        <input type="text" name="ps_answers[<?php echo $answer['answer_id']; ?>][text]" placeholder="<?php _e('Image text', 'perfect-survey') ?>" value="<?php echo esc_html($answer['text']); ?>">
      </div>
      <div class="survey-wrap-input">
        <input type="text" name="ps_answers[<?php echo $answer['answer_id']; ?>][description]" placeholder="<?php _e('Image description', 'perfect-survey') ?>" value="<?php echo esc_html($answer['description']); ?>">
      </div>
      <input type="hidden" name="ps_answers[<?php echo $answer['answer_id']; ?>][position]" value="<?php echo $answer['position']; ?>" >
    </div>
  </td>
</tr>
