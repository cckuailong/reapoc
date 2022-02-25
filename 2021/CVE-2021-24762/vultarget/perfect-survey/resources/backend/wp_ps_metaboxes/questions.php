<?php
add_meta_box('survey_editor', __('Create your survey', 'perfect-survey'), function(){
  $questions = prsv_get_post_type_model()->get_questions();
  ?>
  <div class="ps_modal_back"></div>
    <div class="psrv_editor_general_container">
      <div class="survey-add-footer-inner">
        <div id="survey_open_modal" class="button button-primary button-large survey_open_modal"><i class="pswp_set_icon-plus"></i> <?php _e('Add a question now', 'perfect-survey') ?></div>
        <div class="survey-select-type">
          <ul>
            <?php
            $questions_types = prsv_get_post_type_model()->get_all_questions_types();
            foreach($questions_types as $question_type => $question_type_info)
            {
              ?>
              <li class="survey-btn-add" data-ps-question-type="<?php echo $question_type;?>">
                <a href="#">
                  <span>
                    <i class="<?php echo $question_type_info['icon_class'];?>"></i>
                  </span>
                  <span>
                    <?php echo $question_type_info['name']; ?>
                  </span>
                </a>
              </li>
              <?php
            }
            ?>
          </ul>
        </div>
      </div>


      <div class="survey-questions-mount">
        <div id="survey-empty-questions-box" class="psrv_set_emptyfield survey_settings survey_input <?php echo $questions ? 'hidden' : ''; ?>">
          <div class='survey-empty'>
            <div class="psrv_nosurvey_yet_container">
                  <img class="psrv_nosurvey_yet" src="<?php echo esc_url( plugins_url( 'assets/img/psrv_no_survey_found.png', dirname(__FILE__) ) ); ?>">
            </div>  
            <p class="survey-empty-message"><?php _e('This page is empty.<br>Add a question now!', 'perfect-survey') ?></p>
          </div>
        </div>
        <div class="survey_sortable-item" id="post-questions-box">
            <?php
            if($questions) {
            foreach($questions as $question)
              {
                prsv_resource_include_backend('questions_types/'.$question['type'], array(
                  'question'      => $question,
                  'question_type' => $question['question_type']
                ));
              }
            }
            ?>
        </div>
      </div>

</div>

<div class="psrv_toast_add_question"><div class="psrv_container_toast"><i class="pswp_set_icon-spinner2"></i> <span><?php _e('Operation successfull', 'perfect-survey'); ?></span></div></div>

  
  <table class="survey_settings survey_input">
    <tbody>
      <tr class="survey-add-footer">

      </tr>
    </tbody>
  </table>
  <?php
}, PRSV_PLUGIN_CODE, 'normal', 'high');
?>
