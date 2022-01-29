<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_dashboard_widget.php'); else {
//echo "<pre>", var_dump($data),die;
?>

<div class="rm-dashboard-widget-container">

<div class="rm-dash-widget-head">
    <div class="rm-dash-widget-logo"><img src="<?php echo RM_IMG_URL.'logo.png';?>"></div>
    <div  class="rm-dash-widget-summary">
        <div><?php echo RM_UI_Strings::get('LABEL_TODAY');?><span><?php echo $data->count->today; ?></span></div>
        <div><?php echo RM_UI_Strings::get('LABEL_THIS_WEEK');?><span><?php echo $data->count->this_week; ?></span></div>
        <div><?php echo RM_UI_Strings::get('LABEL_THIS_MONTH');?><span><?php echo $data->count->this_month; ?></span></div>
    </div>
</div>
<hr>

<table class="rm_user_submissions">
    <caption><?php echo RM_UI_Strings::get('DASHBOARD_WIDGET_TABLE_CAPTION'); ?></caption>

  <?php  foreach($data->submissions as $submission):?>
  <tr>
    <td class="rm_submission_date"><?php echo $submission->date;?></td>
    <td class="rm_form_title"><?php if($submission->name) echo $submission->name; else echo RM_UI_Strings::get('LABEL_FORM_DELETED'); ?></td>
    <!-- <td class="rm_form_payment"><?php // if($submission->payment_status) echo $submission->payment_status; else echo RM_UI_Strings::get('LABEL_NOT_APPLICABLE_ABB');?></td> -->
    <td class="rm_view_submission"><a href= <?php echo "'admin.php?page=rm_submission_view&rm_submission_id=",$submission->submission_id,"'>";?><?php echo RM_UI_Strings::get('VIEW'); ?></a></td>
  </tr>
  <?php endforeach;?>
</table>
</div>
<?php } ?>