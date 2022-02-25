<div id="survey_question_<?php echo $question['question_id']; ?>" class="survey_question_box survey_single_question" data-question-id="<?php echo $question['question_id']; ?>">
  <div class="survey_question_header">
    <?php prsv_resource_include_backend('questions_box/header_box', array('question' => $question, 'question_type' => $question_type)); ?>
  </div>
  <div class="survey_question_body" style="display: none">
    <div class="survey_settings survey_input survey_header_questions">
      <div>
        <?php prsv_resource_include_backend('questions_box/form_settings_modal', array('question' => $question, 'question_type' => $question_type));?>
        <table class="survey_settings survey_input survey_header_questions">
          <tr>
            <td class="survey_container_inner_settings survey_align_top">
              <?php prsv_resource_include_backend('questions_box/form', array('question' => $question, 'question_type' => $question_type)); ?>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
