<table id="survey_question_answers_<?php echo $question['question_id'];?>" class="survey_inner_settings">
  <tbody class="answers_container_box sortable">
    <?php
    $answers = prsv_get_post_type_model()->get_answers($question['question_id']);
    if($answers)
    {
      foreach($answers as $answer)
      {
        prsv_resource_include_backend('answers_input/'.$question_type['answer_input'], array(
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
        'show'          => $question_type['add_answer'] && (empty($answers) || $question_type['multiple_answers']),
        'question_type' => $question_type,
        'question'      => $question
      ));
      ?>
    </tr>
  </tfoot>
</table>
