<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_user_edit_widget.php'); else {
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if($data->total_sub > 0):
?>
<h2><?php _e('RegistrationMagic Submissions','custom-registration-form-builder-with-submission-manager') ?></h2>
<table class="rm_user_submissions form-table">
  <tr>
    <th class="rm_user_sr">#</th>
    <th class="rm_form_title"><?php echo RM_UI_Strings::get('LABEL_FORM_TITLE');?></th>
    <th class="rm_submission_date"><?php echo RM_UI_Strings::get('LABEL_DATE'); ?></th>
    <th class="rm_form_payment"><?php echo RM_UI_Strings::get('LABEL_PAYMENT'); ?></th> 
    <th class="rm_view_submission"><?php //echo RM_UI_Strings::get('ACTION'); ?></th>
  </tr>
  <?php  for($i=0; $i < $data->total_sub; $i++):?>
  <tr>
    <td class="rm_user_sr"><?php echo ($i+1);?></td>
    <td class="rm_form_title"><?php if($data->submissions[$i]->name) echo $data->submissions[$i]->name; else echo RM_UI_Strings::get('LABEL_FORM_DELETED'); ?></td>
    <td class="rm_submission_date"><?php echo $data->submissions[$i]->date;?></td>
    <td class="rm_form_payment"><?php if($data->submissions[$i]->payment_status) echo $data->submissions[$i]->payment_status; else echo RM_UI_Strings::get('LABEL_NOT_APPLICABLE_ABB');?></td>
    <td class="rm_view_submission"><a href="<?php printf($data->view_action_link,$data->submissions[$i]->submission_id);?>"><?php echo RM_UI_Strings::get('VIEW'); ?></a></td>
  </tr>
  <?php endfor;?>
</table>
<?php
endif;


}