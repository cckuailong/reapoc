<span class="ps_open_settings">
  <span class="button button-primary button-large">
    <i class="pswp_set_icon-cog"></i> <?php _e('Settings', 'perfect-survey');?>
  </span>
</span>
<div class="label survey_settings_area ps_settings-modal">
  <div class="ps_settings_header">
    <?php echo _e('Settings', 'perfect-survey');?>
    <span class="ps_close_modal">
      <i class="pswp_set_icon-cross"></i>
    </span>
  </div>
  <div class="ps_body_container_modal">
    <?php prsv_resource_include_backend('questions_box/options_box', array('question' => $question, 'question_type' => $question_type)); ?>
    <?php prsv_resource_include_backend('questions_box/settings_box', array('question' => $question, 'question_type' => $question_type)); ?>
    <?php prsv_resource_include_backend('questions_box/optional_settings_box', array('question' => $question, 'question_type' => $question_type)); ?>
  </div>
  <p class="ps_submit_modal">
    <input type="button" class="button button-primary button-large survey-close-windows" value="<?php _e('Ok' ,'perfect-survey');?>" />
  </p>
</div>
