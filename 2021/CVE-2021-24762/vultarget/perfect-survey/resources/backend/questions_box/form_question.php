<tr>
  <td>
    <input type="hidden" name="ps_questions[<?php echo $question['question_id'];?>][position]" value="<?php echo $question['position'];?>" class="question-position-field" />
    <div class="survey-wrap-input">
      <input type="text" name="ps_questions[<?php echo $question['question_id'];?>][text]" placeholder="<?php _e('Insert a question', 'perfect-survey') ?> - <?php _e('Required', 'perfect-survey') ?>" value="<?php echo $question['text']; ?>" class="survey-question-text question-validate <?php echo $question['text'] ? '' : 'warning'; ?>" required/>
    </div>
  </td>
</tr>
<tr>
  <td>
    <div class="survey-wrap-input">
      <?php if(isset($editor) && $editor){ ?>
        <div class="survey-wrap-input">
          <?php
          $content   = $question['description'];
          $editor_id = 'ps_question_description_text_'.$question['question_id'];
          $settings  = array(
            'textarea_name' =>'ps_questions['.$question['question_id'].'][description]',
            'media_buttons' => true,
            'editor_height' => 150,
            'tinymce'   => array(
              'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
              'toolbar2' => '',
              'toolbar3' => '',
            ),
          );
          wp_editor($content, $editor_id, $settings);
          ?>
          <script>jQuery(function(){ if(typeof tinymce == 'undefined') { return false; } tinymce.execCommand( 'mceAddEditor', false, '<?php echo $editor_id;?>'); window.setTimeout(function(){ jQuery('#ps_question_description_text_<?php echo $question['question_id'];?>-tmce').click(); }, 1000); });</script>
        </div>
      <?php } else { ?>
        <input type="text" name="ps_questions[<?php echo $question['question_id'];?>][description]" placeholder="<?php _e('Insert a description', 'perfect-survey') ?>" value="<?php echo $question['description'];?>" />
      <?php } ?>
    </div>
  </td>
</tr>
