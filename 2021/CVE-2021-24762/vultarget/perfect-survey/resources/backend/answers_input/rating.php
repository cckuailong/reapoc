<tr>
  <td>
    <div class="survey-block-image-general">
      <div class="survey-image-header-controll">
        <div class="survey-image-delete-item">
          <?php prsv_resource_include_backend('answers_box/delete_btn', array('answer' => $answer, 'question' => $question, 'question_type' => $question_type)) ?>
        </div>
      </div>
      <div class="survey-wrap-input">
        <ul>
          <?php for ($i = 0; $i < $question['answer_max_value']; $i++) { ?>
            <li><i class="<?php echo $question['answer_css_class']; ?> survey-rating-single"></i></li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </td>
</tr>
