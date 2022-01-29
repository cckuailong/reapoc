<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_sent_emails_view.php'); else {
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="rmagic">
    <?php 
    ?>
    <!-----Operations bar Starts----->

    <div class="operationsbar">
        <div class="rmtitle"><?php echo RM_UI_Strings::get('LABEL_EMAIL_TO').': '. $data->email->to; ?></div>
        <div class="icons">
            <a href="?page=rm_options_manage"><img alt="" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/global-settings.png'; ?>"></a>

        </div>
        <div class="nav">
            <ul>
                <?php if($data->search_state) {?>
                <li><a href="?page=rm_sent_emails_manage&<?php echo $data->search_state; ?>"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>
                <?php } else { ?>
                <li onclick="window.history.back()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>
                <?php } ?>
                
<!--                <li onclick="jQuery.rm_do_action('rm_view_submission_page_form', 'rm_submission_print_pdf')"><a href="javascript:void(0)"><?php // echo RM_UI_Strings::get("LABEL_PRINT"); ?></a></li>-->
                <?php if($data->search_state) {?>
                <li><a href="?page=rm_sent_emails_view&<?php echo $data->search_state; ?>&rm_sent_email_id=<?php echo $data->email->mail_id; ?>&rm_action=delete"><?php echo RM_UI_Strings::get("LABEL_DELETE"); ?></a></li>
                <?php } else { ?>
                <li><a href="?page=rm_sent_emails_view&rm_sent_email_id=<?php echo $data->email->mail_id; ?>&rm_action=delete"><?php echo RM_UI_Strings::get("LABEL_DELETE"); ?></a></li>
                <?php } ?>
              <?php
              $user_email=$data->email->to;
              /*
              if($data->submission->is_blocked_email($user_email)){
              ?>
                <li><a href="?page=rm_submission_view&rm_user_email=<?php echo $data->email->to; ?>&rm_submission_id=<?php echo $data->mail->mail_id(); ?>&rm_action=unblock_email"><?php echo RM_UI_Strings::get("LABEL_UNBLOCK_EMAIL"); ?></a></li>
              <?php }
              else
              {
                   ?>
                <li><a href="?page=rm_submission_view&rm_user_email=<?php echo $data->email->to; ?>&rm_submission_id=<?php echo $data->email->mail_id(); ?>&rm_action=block_email"><?php echo RM_UI_Strings::get("LABEL_BLOCK_EMAIL"); ?></a></li>
              <?php }
             
              */
              ?>
               
               <?php /* if($data->related > 0){ ?>
               <li><a href="?page=rm_submission_related&rm_user_email=<?php echo $data->email->to; ?>"><?php echo RM_UI_Strings::get("LABEL_RELATED").' ('.$data->related.')'; ?></a></li>
               <?php
               } 
               else 
               {?>
                <li><a><?php echo RM_UI_Strings::get("LABEL_RELATED").' (0)'; ?></a></li>
               <?php } */?>
            </ul>
        </div>

    </div>
    <!--****Operations bar Ends**-->

    <!--**Content area Starts**-->
    <div class="rm-submission">        

        <form method="post" action="" name="rm_view_submission" id="rm_view_submission_page_form">
            <input type="hidden" name="rm_slug" value="" id="rm_slug_input_field">

            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_EMAIL_SENT_ON'); ?></div>
                <div class="rm-submission-value"><?php echo RM_Utilities::localize_time($data->email->sent_on); ?></div>
            </div>

            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_EMAIL_SUB'); ?></div>
                <div class="rm-submission-value"><?php echo htmlspecialchars_decode($data->email->sub); ?></div>
            </div>
            
            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_EMAIL_BODY'); ?></div>
                <div class="rm-submission-value"><?php echo htmlspecialchars_decode($data->email->body); ?></div>
            </div>
            
        </form>
    </div>  
    <?php     
    $rm_promo_banner_title = __('Unlock Note,Print,Block Email/IP and Send Message options, by upgrading','custom-registration-form-builder-with-submission-manager');
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
    
    
</div>
<?php } ?>