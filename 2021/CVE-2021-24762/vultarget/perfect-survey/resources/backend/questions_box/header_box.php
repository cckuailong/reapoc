<table class="survey_settings survey_input survey_header_questions">
  <tbody>
    <tr>
      <td class="survey_first-colum survey_move_header">
        <i class="pswp_set_icon-enlarge"></i>
      </td>
      <td class='survey_question'>
        <p class="survey_question_p"><?php echo esc_html($question['text']) ? esc_html($question['text']) : __('(no title)'); ?></p>
          <ul class="survey_tool_edit">
            <li><a class="survey-btn-support btn-copy-question" data-question-id="<?php echo $question['question_id'];?>" data-confirm-text="<?php _e('Are you sure to copy this question? All relative stats will be ignored','perfect-survey');?>" href="#" title="<?php _e('Copy element', 'perfect-survey') ?>"><?php _e('Copy'); ?></a></li>
            <li><a class="survey-btn-support survey_alert btn-delete-question" data-question-id="<?php echo $question['question_id'];?>" data-confirm-text="<?php _e('Are you sure to delete this question? All relative stats will be removed','perfect-survey');?>" href="#" title="<?php _e('Delete element', 'perfect-survey') ?>"><?php _e('Delete'); ?></a></li>
          </ul>
        </td>
        <td class="survey_last-colum">
          <p class="survey_type_choose"><span data-tooltip="<?php _e($question_type['name'], 'perfect-survey') ?>"><i class="<?php echo $question_type['icon_class'];?>"></i></span></p>
        </td>
      </tr>
    </tbody>
  </table>
