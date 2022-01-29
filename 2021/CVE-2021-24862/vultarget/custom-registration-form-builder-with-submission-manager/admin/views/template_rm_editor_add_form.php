<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_editor_add_form.php'); else {
$option_string='<option value="__0" class="rm_login_form_option_in_drop_down">'.__('Login Form', 'custom-registration-form-builder-with-submission-manager').'</option>';
if($data->forms){
    foreach($data->forms as $form){
        $option_string .= '<option value="'.$form->form_id.'">'.$form->form_name.'</option>';
    }
}
?>
<?php if( current_user_can('administrator') ) {  ?>
<select id="rm_editor_add_form">
    <option value="0"><?php echo RM_UI_Strings::get("LABEL_ADD_FORM"); ?></option>
    <?php echo $option_string; ?>
</select>
<?php } ?>

<?php
}
/*
}else{
    ?>
<select id="rm_editor_add_form">
    <option value="0"><?php echo RM_UI_Strings::get("LABEL_NO_FORMS"); ?></option>
</select>
<?php

}
*/

