<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(!empty($data->emails)){
$option_string='';

foreach($data->emails as $email)
{
    $opt_value= $email->field_type.'_'.$email->field_id;
    $type= strtolower($email->field_type);
    if($type=='username'){
        $opt_value= 'Username';
    } else if($type=='userpassword'){
        $opt_value= 'UserPassword';
    }
    $option_string .= '<option value="'.$opt_value.'">'.$email->field_label.'</option>';
}
?>

<?php
 if(isset($data->editor_control_id) && $data->editor_control_id)
 	$select_input_id = $data->editor_control_id;
 else
 	$select_input_id = 'rm_editor_add_email';
?>
<select id="<?php echo $select_input_id;?>">
    <option value="0"><?php echo RM_UI_Strings::get("LABEL_ADD_EMAIL"); ?></option>
    <?php echo $option_string; ?>
</select>
<?php }