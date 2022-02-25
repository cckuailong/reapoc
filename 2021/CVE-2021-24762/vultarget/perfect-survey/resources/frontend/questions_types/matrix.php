<?php include 'generic_header.php'; ?>
<div class="survey_row">
  <div class="survey_col12 <?php echo $question['answer_show_type']; ?>">
    <div class="ps_resposive_table ps_matrix_responsiveness">
      <table>
        <thead class="ps_header_hide_mobile">
          <tr>
            <th>&nbsp;</th>
            <?php foreach ($answers_values as $answer_value) { ?>
              <th class="ps_label_table ps-centered"><?php echo esc_html($answer_value['value']); ?></th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($answers as $answer) { ?>
            <tr>
              <td class="ps_label_table"><?php echo esc_html($answer['text']); ?></td>
              <?php foreach ($answers_values as $answer_value) { ?>
                <td class="ps-centered">
                  <label class="radio-btn">
                    <input type="radio"  name="ps_questions[<?php echo $question['question_id']; ?>][<?php echo $answer['answer_id']; ?>][]" value="<?php echo $answer_value['answer_value_id']; ?>" class="ps-answers-values" />
                    <span></span>
                    <span class="ps_survey_onlyext ps_header_hide_descktop"><?php echo esc_html($answer_value['value']); ?></span>
                  </label>
                </td>
              <?php } ?>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include 'generic_footer.php'; ?>
