<?php $btn_class = isset($btn_class) ? $btn_class : 'btn-add-answer';?>
<?php $action    = isset($action)    ? $action    : 'add_answer';?>
<?php $btn_title = isset($btn_title) ? $btn_title : $question_type['btn_add_title'];?>
<?php $show      = isset($show )     ? $show      : true; ?>
<td>
  <a href="#" class="button button-large <?php echo $btn_class;?>" data-question-id="<?php echo $question['question_id'];?>" data-multiple-answers="<?php echo $question_type['multiple_answers'] ? 'true' : 'false';?>" data-action="<?php echo $action;?>" <?php echo !$show ? 'style="display: none;"' : '';?>>
    <i class="pswp_set_icon-plus"></i> <?php echo $btn_title ?>
  </a>
</td>
