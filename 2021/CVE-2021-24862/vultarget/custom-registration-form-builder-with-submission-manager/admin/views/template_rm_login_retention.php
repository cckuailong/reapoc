<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_login_retention.php'); else {
?>
<div class="rmagic">
    <!--Dialogue Box Starts-->
    <div class="rmcontent">  
            <div class="rmheader"><?php echo _e('Logs Retention', 'custom-registration-form-builder-with-submission-manager'); ?></div>
        <div class="rmrow"><div class="rmnotice"><?php echo _e('Note: Logs record information about login events on your site. RegistrationMagic uses this information to generate stats, find security risks and offer a clearer overview of the login process.  You can limit retention of this information only for a certain period to keep database clean. You can also use this option for compliance with local privacy laws and your business data retention requirements. If you wish to turn off any event logging, select By Number and input 0.', 'custom-registration-form-builder-with-submission-manager'); ?></div></div>
        <?php
        $form = new RM_PFBC_Form("login-retention");

        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        
        $form->addElement(new Element_Radio(__('Keep logs based on', 'custom-registration-form-builder-with-submission-manager'), "logs_retention", array('records'=>__("No. of records",'custom-registration-form-builder-with-submission-manager'),'days'=>__("No. of days",'custom-registration-form-builder-with-submission-manager'),), array('class'=>'rm_logs_retention',"value" => $data->params['logs_retention'], "longDesc"=>__('Define the criteria of retaining login records.', 'custom-registration-form-builder-with-submission-manager'))));
            $form->addElement(new Element_HTML('<div id="rm_records" '.(isset($data->params['logs_retention']) && $data->params['logs_retention']!="records"?'style="display:none;"':'').' class="childfieldsrow">'));
                $form->addElement(new Element_Number(__('No of records', 'custom-registration-form-builder-with-submission-manager'), "no_of_records", array("value" => $data->params['no_of_records'], "longDesc"=>__('Enter the number of latest records you wish to retain', 'custom-registration-form-builder-with-submission-manager'))));
            $form->addElement(new Element_HTML('</div>'));
            
            $form->addElement(new Element_HTML('<div id="rm_days" '.(isset($data->params['logs_retention']) && $data->params['logs_retention']!="days"?'style="display:none;"':'').' class="childfieldsrow">'));
                $form->addElement(new Element_Select('Retain for', "no_of_days", array("7"=>__("Last 7 Days",'custom-registration-form-builder-with-submission-manager'),"30"=>__("Last 30 Days",'custom-registration-form-builder-with-submission-manager'),"90"=>__("Last 90 Days",'custom-registration-form-builder-with-submission-manager'),), array("value" =>$data->params['no_of_days'], "longDesc"=>__("Select number of days for which you wish to retain login records.", 'custom-registration-form-builder-with-submission-manager'))));
            $form->addElement(new Element_HTML('</div>'));
            
            $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_login_sett_manage', array('class' => 'cancel')));
            $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit")));
        $form->render();
        ?>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
            jQuery(".rm_logs_retention").change(function(){
                jQuery(".rm_logs_retention").each(function(){
                    if(jQuery(this).is(':checked')){
                    jQuery("#rm_" + jQuery(this).val()).slideDown();
                    }
                    else{
                         jQuery("#rm_" + jQuery(this).val()).slideUp();
                    }
                })
            });
       
        jQuery(".rm_logs_retention").trigger('change');
    });
    
    
    
</script>    
<?php } ?>