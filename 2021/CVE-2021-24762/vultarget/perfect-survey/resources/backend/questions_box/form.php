<table class="survey_inner_settings">
  <tbody>
    <?php prsv_resource_include_backend('questions_box/form_question', array('question' => $question, 'question_type' => $question_type)); ?>
    <tr>
      <td class="surgey_repeater_field">
        <?php prsv_resource_include_backend('questions_box/form_answers', array('question' => $question, 'question_type' => $question_type)); ?>
      </td>
    </tr>
  </tbody>
</table>
