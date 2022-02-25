<!-- QUESTION TYPE <?php echo strtoupper($question['type']);?> -->
<div id="survey_question_<?php echo $question['question_id'];?>" class="survey_question_box survey_question_type_<?php echo $question['type'];?>"
  data-post-id="<?php echo $question['ID'];?>"
  data-question-id="<?php echo $question['question_id'];?>"
  data-validation-required="<?php echo $question['required'] ? 'true' : 'false';?>"
  data-validation-message="<?php echo htmlspecialchars(sprintf(count($answers) > 1 && $question['answer_show_type'] != 'select' ? '"%s" '.__('Required', 'perfect-survey').'' : '"%s" '.__('Required', 'perfect-survey').'', esc_html($question['text'])));?>"
  data-question-text="<?php echo esc_html($question['text']);?>"
  >
  <div class="survey_queryble <?php echo $question['required'] ? 'required' : '';?>">
    <h2><?php echo esc_html($question['text']); ?> <?php if($question['required']){ ?><span class="ps_required">*</span><?php } ?></h2>
    <?php if($question['description']){ ?>
      <p class="question-description"><?php echo esc_html($question['description']); ?></p>
    <?php } ?>

    <?php if(!empty($question['image'])){ ?>
      <div class="ps-container-image">
        <?php echo wp_get_attachment_image($question['image'],$question['image_properties']['size']);?>
      </div>
    <?php } ?>
