<?php
$post_image = get_post($answer['image']);
$active_zoom = prsv_global_options_get('ps_options_modal_bar_active') == 'ps_modal_on';
?>
<div class="survey-image-block-frontend">
  <label class="check-btn survey_image-block">
    <div class="container-image">
      <?php if($active_zoom) { ?>
        <a href="#" data-featherlight="<?php echo!empty($post_image) ? $post_image->guid : '#'; ?>">
        <?php } ?>
        <div class="survey-container-image-front" style="background-image:url(<?php echo !empty($post_image) ? $post_image->guid : '#'; ?>)">
          <?php if($active_zoom) { ?>
            <div class="ps_icon_search_image"><i class="pswp_set_icon-search"></i></div>
          <?php } ?>
        </div>
        <?php if($active_zoom) { ?>
        </a>
      <?php } ?>
      <label class="<?php echo $question['answer_field_type'] == 'radio' ? 'radio-btn' : 'check-btn'; ?>">
        <input type="<?php echo $question['answer_field_type'] ? $question['answer_field_type'] : 'checkbox';?>"  name="ps_questions[<?php echo $question['question_id']; ?>][]" value="<?php echo $answer['answer_id'];?>" class="ps-answers" />
        <span></span>
        <span class="ps_survey_onlyext"><?php echo esc_html($answer['text']); ?></span>
      </label>
    </div>
  </label>
</div>
