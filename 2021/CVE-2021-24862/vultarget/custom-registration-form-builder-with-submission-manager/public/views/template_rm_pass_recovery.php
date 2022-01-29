<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'views/template_rm_pass_recovery.php'); else {
echo '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">';
$form = new RM_PFBC_Form($data->form_type);

$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => "",
     "style" => isset($data->design['style_form'])?$data->design['style_form']:null
));

if(isset($data->design['placeholder_css'])){		
    $p_css = $data->design['placeholder_css'];
    $form->addElement(new Element_HTML($p_css));		
}
if(isset($data->design['style_label'])){	
    $p_css = "<style>#$data->password_form_slug .rmrow .rmfield label { ".$data->design['style_label']." }</style>";
    $form->addElement(new Element_HTML($p_css));		
}

if($data->buttons['align']=='left' || $data->buttons['align']=='right'){
    if(empty($data->design['style_btnfield'])){
        $data->design['style_btnfield']='float:'.$data->buttons['align'];
    }
    else
    {
        $data->design['style_btnfield']=$data->design['style_btnfield'].';float:'.$data->buttons['align'];
    }
}

if($data->form_type=='rm_recovery_form'){
    if(isset($data->expired_token) && $data->expired_token==1){
        $form->addElement(new Element_HTML('<div class="rm_error_msg-wrap"><div class="rm_pr_warning_msg"><span class="rm_waring_symbol">&excl;</span>'.wpautop($data->options['rec_link_exp_err']).'</div></div>'));
    }
    
    if(isset($data->valid_email) && $data->valid_email==1){
        $form->addElement(new Element_HTML('<div class="rm_error_msg-wrap"><div class="rm_pr_success_msg"><span class="rm_green_tik">&check;</span>'.$data->options['rec_link_sent_msg'].'</div></div>'));
    }else{
        $form->addElement(new Element_Email($data->options['rec_email_label'], "user_email", array("required" => "1","class"=>'', "placeholder" => '','style'=>isset($data->design['style_textfield'])?$data->design['style_textfield']:null)));

        $btn_label= !empty($data->options['rec_btn_label'])?$data->options['rec_btn_label']:__('Reset Password', 'custom-registration-form-builder-with-submission-manager');
        $form->addElement(new Element_Button($btn_label, "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit",'style'=>isset($data->design['style_btnfield'])?$data->design['style_btnfield']:null)));
        
        if(isset($data->valid_email) && $data->valid_email==0){
            $form->addElement(new Element_HTML('<div class="rm_error_msg-wrap"><div class="rm_pr_error_msg"><span class="rm_red_cross">&times;</span>'.$data->options['rec_email_not_found_msg'].'</div></div>'));
        }
    }
}else if($data->form_type=='rm_token_form'){
    if(isset($data->invalid_copy_token) && $data->invalid_copy_token==1){
        $page_id= $data->options['recovery_page'];
        $recovery_link= get_permalink($page_id);
        $rec_invalid_tok_err= wpautop(str_replace('{{password_recovery_link}}',$recovery_link,$data->options['rec_invalid_tok_err']));
        $form->addElement(new Element_HTML('<div class="rm_error_msg-wrap"><div class="rm_pr_error_msg"><span class="rm_red_cross">&times;</span>'.$rec_invalid_tok_err.'</div></div>'));
    }else if(isset($data->invalid_token) && $data->invalid_token==1){
        $form->addElement(new Element_HTML('<div class="rm_error_msg-wrap"><div class="rm_pr_warning_msg"><span class="rm_waring_symbol">&excl;</span>'.wpautop($data->options['rec_invalid_reset_err']).'</div></div>'));
    }
    
    $form->addElement(new Element_Textbox(__('Security Token', 'custom-registration-form-builder-with-submission-manager'), "token_val", array("required" => "1","class"=>'', "placeholder" => '','style'=>isset($data->design['style_textfield'])?$data->design['style_textfield']:null)));
    
    $btn_label= !empty($data->options['rec_tok_sub_label'])?$data->options['rec_tok_sub_label']:__('Proceed', 'custom-registration-form-builder-with-submission-manager');
    $form->addElement(new Element_Button($btn_label, "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit",'style'=>isset($data->design['style_btnfield'])?$data->design['style_btnfield']:null)));
    
}else if($data->form_type=='rm_reset_password_form'){
    if(empty($data->password_updated)){
        $form->addElement(new Element_Password($data->options['rec_new_pass_label'], "password", array("required" => "1","class"=>'', "placeholder" => '','style'=>isset($data->design['style_textfield'])?$data->design['style_textfield']:null,'minLength'=>7)));
        $form->addElement(new Element_Password($data->options['rec_conf_pass_label'], "confirm_password", array("required" => "1","class"=>'', "placeholder" => '','style'=>isset($data->design['style_textfield'])?$data->design['style_textfield']:null,'minLength'=>7)));
        $form->addElement(new Element_Hidden("token_val",$data->sec_token,array()));
        $btn_label= !empty($data->options['rec_pass_btn_label'])?$data->options['rec_pass_btn_label']:__('Change Password', 'custom-registration-form-builder-with-submission-manager');
        $form->addElement(new Element_Button($btn_label, "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit",'style'=>isset($data->design['style_btnfield'])?$data->design['style_btnfield']:null)));
    }
 
   
    if(isset($data->password_mismatch) && $data->password_mismatch==1){
        $form->addElement(new Element_HTML('<div class="rm_error_msg-wrap"><div class="rm_pr_error_msg"><span class="rm_red_cross">&times;</span>'.$data->options['rec_pass_match_err'].'</div></div>'));
    }
    else if(!empty($data->error)){
        $form->addElement(new Element_HTML('<div class="rm_error_msg">'.$data->error.'</div>'));
    }
    else if(isset($data->password_updated) && $data->password_updated==1){
        $form->addElement(new Element_HTML('<div class="rm_error_msg-wrap"><div class="rm_pr_success_msg"><span class="rm_green_tik">&check;</span>'.$data->options['rec_pas_suc_message'].'</div></div>'));
    }
}
/*
 * Render the form if user is not logged in
 */
?>
<div class='rmagic'>    
    <div class='rmcontent rm-login-wrapper'>
        <?php if (!is_user_logged_in() || (isset($_GET['form_prev']) && $_GET['form_prev']==1 && $_GET['form_type']=='login')) : ?>
            <?php
            $form->render();
            ?>
        <?php endif; ?>
    </div>
</div>
<?php } ?>