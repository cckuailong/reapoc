<?php include 'generic_header.php'; ?>
<div class="survey_row">
  <div class="survey_col12 <?php echo $question['answer_show_type'];?>">

    <?php if($question['answer_show_type'] == 'select'){ ?>
      <select name="ps_questions[<?php echo $question['question_id'];?>][]" <?php echo $question['type'] == PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_MULTIPLE_CHOICE ? 'multiple="multiple"': '';?> <?php echo $question['required'] ? 'required' : '';?> class="ps-select-answer">
        <option value="" style="font-style: italic;"><?php _e($question['type'] == PerfectSurveyPostTypeModel::PS_QUESTION_TYPE_MULTIPLE_CHOICE ? 'no answer' : '--Select--', 'perfect-survey');  ?></option>
      <?php } ?>

      <?php
      foreach($answers as $answer)
      {
        prsv_resource_include_frontend($answer['frontend_template'], array(
          'answer'        => $answer,
          'question'      => $question,
          'question_type' => $question_type,
          'answers_values'=> $question['answers_values']
        ));
      }
      ?>

      <?php if($question['answer_show_type'] == 'select'){ ?>
      </select>
    <?php } ?>
  </div>
</div>
<?php include 'generic_footer.php';?>
