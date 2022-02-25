<div id="survey_question_<?php echo $question['question_id'];?>"  class="survey_question_box survey_single_question survey_matrix_question" data-question-id="<?php echo $question['question_id'];?>">
  <div class="survey_question_header">
    <?php prsv_resource_include_backend('questions_box/header_box', array('question' => $question,  'question_type' => $question_type)); ?>
  </div>
  <div class="survey_question_body" style="display: none">
    <div class="survey_settings survey_input survey_header_questions">
      <div>
        <?php prsv_resource_include_backend('questions_box/form_settings_modal', array('question' => $question, 'question_type' => $question_type));?>
        <table class="survey_settings survey_input survey_header_questions">
          <tr>
            <td class="survey_container_inner_settings survey_align_top">
              <table class="survey_inner_settings">
                <tbody>
                  <?php prsv_resource_include_backend('questions_box/form_question', array('question' => $question, 'question_type' => $question_type)); ?>
                  <tr>
                    <td class="surgey_repeater_field">
                      <div>
                        <table class="survey_inner_settings">
                          <tbody class="sortable">
                            <tr>
                              <td class="survey-left-sortable survey_leftresponsive">
                                <table class="survey_inner_settings">
                                  <tbody class="sortable">
                                    <?php
                                    $answers = prsv_get_post_type_model()->get_answers($question['question_id']);
                                    if($answers)
                                    {
                                      foreach($answers as $answer)
                                      {
                                        prsv_resource_include_backend('answers_input/text', array(
                                          'answer'        => $answer,
                                          'question_type' => $question_type,
                                          'question'      => $question
                                        ));
                                      }
                                    }
                                    ?>
                                  </tbody>
                                  <tfoot>
                                    <tr>
                                      <?php
                                      prsv_resource_include_backend('answers_box/add_btn', array(
                                        'question_type' => $question_type,
                                        'question'      => $question
                                      ));
                                      ?>
                                    </tr>
                                  </tfoot>
                                </table>
                              </td>
                              <td class="survey-left-sortable survey_leftresponsive">
                                <table class="survey_inner_settings">
                                  <tbody class="sortable">
                                    <?php
                                    $answers_values = prsv_get_post_type_model()->get_answers_values($question['question_id']);
                                    if($answers_values)
                                    {
                                      foreach($answers_values as $answer_value)
                                      {
                                        prsv_resource_include_backend('answers_input/'.$question['type'].'_values', array(
                                          'answer_value'  => $answer_value,
                                          'question_type' => $question_type,
                                          'question'      => $question
                                        ));
                                      }
                                    }
                                    ?>
                                  </tbody>
                                  <tfoot>
                                    <tr>
                                      <?php
                                      prsv_resource_include_backend('answers_box/add_btn', array(
                                        'action'        => 'add_answer_value',
                                        'btn_title'     => __('Add answer value', 'perfect-survey'),
                                        'question_type' => $question_type,
                                        'question'      => $question
                                      ));
                                      ?>
                                    </tr>
                                  </tfoot>
                                </table>
                              </td>
                              <tr>
                              </tbody>
                            </table>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
