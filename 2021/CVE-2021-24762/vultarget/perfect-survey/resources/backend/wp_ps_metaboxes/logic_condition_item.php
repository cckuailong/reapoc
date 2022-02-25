<?php
$questions             = prsv_get_post_type_model()->get_logic_conditions_questions($logic_condition['ID']);
$selected_question     = $logic_condition['question_id']    ?  prsv_get_post_type_model()->get_question($logic_condition['question_id'])    : array();
$answers               = $selected_question                 ?  prsv_get_post_type_model()->get_answers($selected_question['question_id'])   : array();
$selected_answer       = $logic_condition['answer_id']      ?  prsv_get_post_type_model()->get_answer($logic_condition['answer_id'])        : array();
$selected_question_to  = $logic_condition['question_id_to'] ?  prsv_get_post_type_model()->get_question($logic_condition['question_id_to']) : array();
$logic_condition_types = prsv_get_post_type_model()->get_logic_condition_types();
?>
<!--Condition single row-->
<div class="survey_row survey_logic_condition_row" id="survey_logic_condition_<?php echo $logic_condition['logic_condition_id'];?>" data-logic-condition-id="<?php echo $logic_condition['logic_condition_id'];?>">
  <div class="survey_settings survey_logic_condition">
    <div class="psrv_rower_logic_condition">
      <div class="psrv_comand_logic_condition">
        <div class="survey_move_header ui-sortable-handle">
          <i class="pswp_set_icon-enlarge"></i>
        </div>
      
        <div>
          <a data-tooltip="<?php _e('Delete condition', 'perfect-survey') ?>" class="survey-btn-support survey_alert btn-delete-logic-condition" data-logic-condition-id="<?php echo $logic_condition['logic_condition_id'];?>" href="#" title="<?php _e('Delete condition', 'perfect-survey') ?>" data-confirm-text="<?php _e('Are you sure to delete this logic condition?', 'perfect-survey');?>"><i class="pswp_set_icon-cross"></i></a>
        </div>
      </div>
      <div class="psrv_rower_logic_condition_form">
        <select class="survey_conditional_select" required name="ps_logic_conditions[<?php echo $logic_condition['logic_condition_id'];?>][question_id]" >
          <option value="" <?php echo empty($logic_condition['question_id']) ? 'selected' : '';?>><?php _e('-- Choose question --','perfect-survey');?></option>
          <?php foreach($questions as $question){ ?>
            <option value="<?php echo $question['question_id'];?>" <?php echo $selected_question && $selected_question['question_id'] == $question['question_id'] ? 'selected' : '';?>><?php echo $question['text'];?></option>
          <?php } ?>
        </select>
      </div>
      
      <div class="psrv_rower_logic_condition_form">
        <select class="survey_conditional_select" name="ps_logic_conditions[<?php echo $logic_condition['logic_condition_id'];?>][type]"  required>
          <option value="" <?php echo empty($logic_condition['type']) ? 'selected' : '';?>><?php _e('--Choose condition--','perfect-survey');?></option>
          <?php foreach($logic_condition_types as $logic_condition_type => $logic_condition_type_text){ ?>
            <option value="<?php echo $logic_condition_type;?>" <?php echo $logic_condition['type'] == $logic_condition_type ? 'selected' : '';?>><?php echo $logic_condition_type_text;?></option>
          <?php } ?>
        </select>
      </div>
      
      <div class="psrv_rower_logic_condition_form">
        <select class="survey_conditional_select" name="ps_logic_conditions[<?php echo $logic_condition['logic_condition_id'];?>][answer_id]" >
          <option value="" <?php echo empty($logic_condition['answer_id']) ? 'selected' : '';?>><?php _e('-- Choose answer --','perfect-survey');?></option>
          <?php foreach($answers as $answer){ ?>
            <option value="<?php echo $answer['answer_id'];?>" <?php echo $logic_condition['answer_id'] == $answer['answer_id'] ? 'selected' : '';?>><?php echo $answer['text'];?></option>
          <?php } ?>
        </select>
      </div>
    </div>

  </div>

  <div class="survey_settings psrv_rower_logic_condition_separator">
    
    <div class="psrv_rower_logic_condition">
      <div class="psrv_comand_logic_condition">
        <p><strong><?php _e('Go to','perfect-survey');?></strong></p>
      </div>
      <div class="psrv_rower_logic_condition_form psrv_last_select">
        <select class="survey_conditional_select" required name="ps_logic_conditions[<?php echo $logic_condition['logic_condition_id'];?>][question_id_to]" >
          <option value="" <?php echo  $logic_condition['question_id_to'] === '' ?  'selected' : '';?>><?php _e('-- Choose --','perfect-survey');?></option>
          <?php foreach($questions as $question){ ?>
            <option value="<?php echo $question['question_id'];?>" <?php echo $logic_condition['question_id_to']  == $question['question_id'] ? 'selected' : '';?>><?php echo $question['text'];?></option>
          <?php } ?>
          <option value="0" <?php echo $logic_condition['question_id_to'] != '' && $logic_condition['question_id_to']  == 0 ? 'selected' : '';?>><?php _e('End questionary','perfect-survey');?></option>
        </select>
      </div>

    </div>

  </div>

  <input type="hidden" name="ps_logic_conditions[<?php echo $logic_condition['logic_condition_id'];?>][position]" value="<?php echo $logic_condition['position'];?>" class="logic-condition-position-field" />
</div>
<!--END Condition single row-->