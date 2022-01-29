<?php 
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_settings.php'); else {
    wp_enqueue_script('chart_js');
    /* * ****************************************************************
    * *************     Chart drawing - Line Chart        **************
    * **************************************************************** */
   $show_chart=0;
   $date_labels= array();
   $subs= array();
   $visits= array();
   foreach ($data->day_wise_stat as $date => $per_day) {
       array_push($date_labels,$date);
       array_push($subs,$per_day->submissions);
       array_push($visits,$per_day->visits);
       if(empty($show_chart) && !empty($per_day->visits) && !empty($per_day->submissions)){
           $show_chart=1;
       }
   }
   $date_labels= json_encode($date_labels);
   $subs= json_encode($subs);
   $visits= json_encode($visits);
?>
<link rel="stylesheet" type="text/css" href="<?php echo RM_BASE_URL . 'admin/css/'; ?>style_rm_form_dashboard.css">
<?php if(defined('REGMAGIC_ADDON')) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo RM_ADDON_BASE_URL . 'admin/css/'; ?>style_rm_form_dashboard.css">
<?php } ?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<pre class="rm-pre-wrapper-for-script-tags"><script src="<?php echo RM_BASE_URL . 'admin/js/'; ?>script_rm_form_dashboard.js"></script></pre>
<pre class='rm-pre-wrapper-for-script-tags'><script>
    //Takes value of various status variables (form_id, timeline_range) and reloads page with those parameteres updated.
    function rm_refresh_stats(){
    var form_id = jQuery('#rm_form_dropdown').val();
    var trange = jQuery('#rm_stat_timerange').val();
    if(typeof trange == 'undefined')
        trange = <?php echo $data->timerange; ?>;
    window.location = '?page=rm_form_sett_manage&rm_form_id=' + '<?php echo $data->form_id; ?>' + '&rm_tr='+trange;
}
</script></pre>

<!-- Joyride Magic begins -->
    <ol id="rm-form-sett-dashboard-joytips" style="display:none">
        
        <li data-id="rm_tour_timewise_stats" data-options="tipLocation:top;nubPosition:hide;tipAdjustmentX:200;tipAdjustmentY:230"><h5><?php _e('Welcome to the Form Dashboard!','custom-registration-form-builder-with-submission-manager'); ?></h5>
            <br/>
        <p>This is where the magic begins &#x1f600;. You will notice, this area is a little barren at the moment. But fret not! As your wonderfully named form, <b><?php echo $data->form->get_form_name(); ?> </b>starts performing and visitors fill it in, it will soon look busy enough.</p>
        </li>
        <li data-id="rm_form_dropdown" data-options="tipLocation:bottom;nubPosition:top-right;tipAdjustmentX:-300;tipAdjustmentY:15"><?php _e("Remember - each form will have its own dashboard; accessible by clicking DASHBOARD on its form card. That's how you reached here, right? But once you're here, you do not need to go back to open another form's dashboard. You can just...",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-grid-title" data-options="tipAdjustmentY:-15"><?php _e("This of course, is your currently selected form's name. And...",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-grid-button" data-options="tipLocation:bottom;tipAdjustmentY:12"><?php _e("You can start adding a new form from right here.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-grid-section" data-options="tipAdjustmentY:-15"><?php _e("This line graph shows how your form is performing. It displays your form views/ visits vs the number of times the form was filled and submitted.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm_stat_timerange" data-options="tipAdjustmentY:-15"><?php _e("By default it displays stats from last 30 days. But you can change that easily from this dropdown. You can find more stats in the Analytics section.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-section-icons" data-options="tipAdjustmentY:-10"> <?php _e('This area below contains all the sections within this form.','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-customfields-icon" data-options="tipAdjustmentX:25"><?php _e('While the <b>Design</b> takes care of the look of your form, <b>Pages and Field</b> decide its content. A form is made up of fillable fields. For example, Name, Email, Address etc. You can create, edit and manage fields from this section.','custom-registration-form-builder-with-submission-manager'); ?></li>       
        <li data-id="rm-design-icon" data-options="tipAdjustmentX:25" ><?php _e("This is where you tweak individual elements of your form to make the overall look match with your website's theme or experiment with different combinations. ",'custom-registration-form-builder-with-submission-manager'); ?></li>

        <li data-id="rm-general-icon"><?php _e('All the form specific tweaks and settings go here. Remember, these are separate from Global Settings which apply to all the forms.','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-general-settings" data-options="tipAdjustmentX:25"><?php _e('General Settings cover the basic properties of your form, like its Name, Description etc.','custom-registration-form-builder-with-submission-manager'); ?></li>        
        <li data-id="rm-accounts-icon" data-options="tipAdjustmentX:25"><?php _e('Now this is important! This section decides properties of  WordPress User accounts created when a person submits this form.','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-postsubmit-icon" data-options="tipAdjustmentX:25"><?php _e('Things do not end with submitting the form. You may want to show a success message or a token number or perhaps redirect the user to another page with relevant information. All that and more is configured through this section.','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-autoresponder-icon" data-options="tipAdjustmentX:25"><?php _e('Automatically send users an email notification with customized content after they have submitted the form.','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-limits-icon" data-options="tipAdjustmentX:25"><?php _e('Limits allow you to set, well, limits to your form. Once a limit is reached, the form goes to <i>expired</i> state. This will also be visible on the form card. Limits are useful if you have limited submission slots or if registration is only open before a specified date.','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-access-icon" data-options="tipAdjustmentX:25"><?php _e('Access Control allows you to lock the form and visitor will only be allowed to see it if they meets certain parameters. For example, if you want to allow registrations for people only above 18 years, you can set Date of Birth as access control.','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-email-templates"  data-options="tipAdjustmentX:25"><?php _e('You can tweak content of the emails being sent out to the users filling this form from here.','custom-registration-form-builder-with-submission-manager'); ?></li>                
        <li data-id="rm-overrides-icon" data-options="tipAdjustmentX:25"><?php _e("<b>Overrides</b> are basically Global Settings which can be over ridden specific to a form. This is useful when you have lots of forms and you want one or some of the forms to have separate configuration compared to others. Remember, not all settings can be over ridden. Only those which can, will appear here.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        
        <li data-id="rm-thirdparty-section"> <?php _e('All form specific integrations can be found in this section. System specific integrations are in Global Settings --> External Integration','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-mailchimp-icon" data-options="tipAdjustmentX:25"><?php _e("If you are using <b>MailChimp</b>, you can use options here to send your form data to a MailChimp user list. Make sure you configure your MailChimp account in RegistrationMagic -> Global Settings -> External Integrations first. ",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-profilegrid-icon" data-options="tipAdjustmentX:25"><?php _e("Add extra bells and whistles to your site by attaching your user registration system to ProfileGrid’s advance user profiles and groups system.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        
        <li data-id="rm-publish-section"><?php _e("Publish forms and user details on frontend using different methods described below.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-shortcode-icon" data-options="tipAdjustmentX:30"><?php _e("Learn how to use shortcode to publish this form on a page or post.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-form-widget-icon" data-options="tipAdjustmentX:30"><?php _e("Learn how to use WordPress theme widgets to publish this form.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-user-area-icon" data-options="tipAdjustmentX:30"><?php _e("Learn how to publish a user account area for registered users on the frontend. ",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-magic-popup-icon" data-options="tipAdjustmentX:30"><?php _e("Configure a clickable button with popup on the frontend which allows users to quickly fill forms, login and check their details.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-landing-page-icon" data-options="tipAdjustmentX:30"><?php _e("Learn how to publish this form inside a landing page on the frontend. ",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-login-box-icon" data-options="tipAdjustmentX:30"><?php _e("Learn how to publish login form for your users. ",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-otp-login-box-icon" data-options="tipAdjustmentX:30"><?php _e("Learn how to allow non-registered users to check their form submissions.  ",'custom-registration-form-builder-with-submission-manager'); ?></li>
        
        <li data-id="rm-manage-section"><?php _e("Manage data collection and user interactions through this form from this section.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-sub-icon-tour" data-options="tipAdjustmentX:30"> <?php printf(__('Like this very important form <b>Inbox</b>. This is where all user submission records for <b>%s</b> go.','custom-registration-form-builder-with-submission-manager'),$data->form->get_form_name()); ?></li>
        <li data-class="rm-sub-icon-tour-badge" data-options="tipAdjustmentX:-20;tipAdjustmentY:-20"><?php printf(__('See this badge? This tell you the number of time users have filled and submitted this form. Right now there are <b>%d</b> submissions associated with your form <b>%s</b>','custom-registration-form-builder-with-submission-manager'),$data->sub_count,$data->form->get_form_name()); ?></li>
        <li data-id="rm-attachment-icon" data-options="tipAdjustmentX:20"><?php _e('If your form has a file upload field(s), all the attachments go here. Remember, attachments are also visible inside individual submission records in <b>Inbox</b>. But think of this as a bucket where files are stored individually. If you want to download all of them as a zip, this is the place.','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-outbox-icon" data-options="tipAdjustmentX:20"><?php _e('All the outgoing emails to the users are recorded in this area.','custom-registration-form-builder-with-submission-manager'); ?></li>        
        
        <li data-id="rm-analytics-icon" data-options="tipAdjustmentX:20"><?php _e('<b>Analytics</b>, as the name suggests, collects and displays all the stats related to this form as table, graphs and charts.','custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-field-analytics-icon" data-options="tipAdjustmentX:20"><?php _e('<b>Field Analytics</b>, collects and displays stats related to individual fields inside the form. It can only display meaningful stats from pre-set option field types like <i>dropdown, checkbox or radiobox</i>','custom-registration-form-builder-with-submission-manager'); ?></li>        

        <li data-id="rm-automate-icon" data-options="tipAdjustmentX:20"><?php _e("This is a powerful system which allows you to 'program' the form to do essential workflow tasks for you. We recommend visiting it after you have familiarized yourself with other areas of the form.",'custom-registration-form-builder-with-submission-manager'); ?></li>        
        <li data-id="rm-emailusers-icon" data-options="tipAdjustmentX:25"><?php _e("This section allows you to email all the users who have submitted this form. You'll find this useful for broadcasting announcements and sending bulk updates.",'custom-registration-form-builder-with-submission-manager'); ?></li>

        <li data-id="rm_tour_timewise_stats" data-options="tipLocation:top;nubPosition:hide;tipAdjustmentX:200;tipAdjustmentY:230"><h5><?php _e("We hope you liked what you have seen so far! Let's move on to the two Sidebars.",'custom-registration-form-builder-with-submission-manager'); ?></h5></li>
        
        <li data-id="rm-form-pretoggle" data-options="tipLocation:bottom;nubPosition:top-right;tipAdjustmentX:-330;tipAdjustmentY:12"><?php _e("Click here, and a drop down will appear with list of your forms. And you just have to choose another form from the list. You can try that later. Let's move on...",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-permalink-textbox" data-options="tipAdjustmentY:-20"><?php _e("This is form's unique embed code. As the name suggests, you can use this code to embed the form where WordPress shortcode would not work. Like an external webpage. Otherwise, shortcode is always the best way to display the form.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-grid-sidebar-1" data-options="tipAdjustmentY:-18"><?php _e("First one is sort of live update feed for your form.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-grid-sidebar-2" data-options="tipLocation:left;tipAdjustmentX:-10"><?php _e("And the next one is very similar to what you find inside WordPress pages and posts. It provides surface view of the form.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-grid-sidebar-card" data-options="tipLocation:left;tipAdjustmentY:-10"><?php _e("The first block on this sidebar shows submissions feed as users submit the forms. Initially, when there are no submissions, it will simply show \"0\". It'll look a lot different when the submissions start.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-grid-sidebar-row-label" data-options="tipAdjustmentX:-10;tipAdjustmentY:-12"><?php _e("You can view all submissions by clicking on this button.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-attachments-card" data-options="tipAdjustmentY:-24"><?php _e("The second block shows a list of files received through your form. If you do not have a file upload field inside your form, this area will stay empty and you can ignore it.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-view-attachments" data-options="tipAdjustmentX:-10;tipAdjustmentY:-10"><?php _e("You can view all attachments anytime by clicking on this button.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-sc-icon" data-options="tipLocation:top;nubPosition:bottom-right;tipAdjustmentX:-300;tipAdjustmentY:-15"><?php _e("This is the shortcode for this form. As you already know, you need to paste the shortcode where you wish to show the form. You can simply copy the shortcode...",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-copy-sc" data-options="tipLocation:top;nubPosition:bottom-right;tipAdjustmentX:-344;tipAdjustmentY:-15"><?php _e("By clicking",'custom-registration-form-builder-with-submission-manager'); ?> <i><?php _e("Copy",'custom-registration-form-builder-with-submission-manager'); ?></i></li>
        <li data-id="rm-sidebar-visibility" data-options="tipLocation:bottom;nubPosition:top-right;tipAdjustmentX:-300;tipAdjustmentY:15"><?php _e("Visiblity of the form defines if the form is visible to all users who have access to form page or if the access control is on. We have already discussed how to open Access Control section. Clicking <i>Edit</i> will take you directly to it.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-delete" data-options="tipLocation:left;tipAdjustmentX:-20;tipAdjustmentY:-30"><?php _e("If you wish to delete this form (with all its contents, stats and submissions), you can do that by clicking Delete. To delete multiple forms, use batch selection checkboxes in Forms Manager.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-pages" data-options="tipLocation:left;tipAdjustmentX:-45;tipAdjustmentY:-30"><?php _e("Your form can be spread over multiple pages. This number shows the count of pages in your form.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-fields" data-options="tipLocation:left;tipAdjustmentX:-45;tipAdjustmentY:-30"><?php _e("This shows the number of fields on your form across all pages. For example, a form can have 3 pages with 5 fields on each page. The total field count will be 15.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-add-field"  data-options="tipLocation:top;nubPosition:bottom-right;tipAdjustmentX:-344;tipAdjustmentY:-15"><?php _e("While fields are managed by <b>Pages and Fields</b> section, you can also quickly add a new field by clicking <i>Add</i> here.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-add-submit" data-options="tipLocation:left;tipAdjustmentX:-45;tipAdjustmentY:-30"><?php _e("This is the label of the submit button of your form. It can be labelled Submit, Register, Send or anything you please. To change it, you can click on <i>Change</i> button on the right side. You can fully customise the look of the Submit button by visiting <b>Design</b> section.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-duplicate" data-options="tipLocation:left;tipAdjustmentX:-15;tipAdjustmentY:-30"><?php _e("You can create a clone of this form by clicking <i>Duplicate</i>. It will have same content, design and field/ pages.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-visitors" data-options="tipLocation:left;tipAdjustmentX:-45;tipAdjustmentY:-30"><?php _e("This number shows the total number of visitors (or views) of your form during last 30 days.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-submissions" data-options="tipLocation:left;tipAdjustmentX:-45;tipAdjustmentY:-30"><?php _e("Inbox badge counts the number of times the form has been filled and submitted. Do not confuse this with Users. A single user can submit a form multiple times. You can download all submission records as CSV file by clicking Download Records and open it as spreadsheet inside a desktop program like MS Excel, Apple Numbers etc.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-attachments" data-options="tipLocation:left;tipAdjustmentX:-45;tipAdjustmentY:-30"><?php _e("As the name suggests, this is total count of files received through this form. The <i>Download All</i> button on right will download all files as single zip.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-conversion" data-options="tipLocation:left;tipAdjustmentX:-45;tipAdjustmentY:-30"><?php _e("Basically this is number of submissions received versus number of total form views in percentage. If you are using the form on a landing page, this can provide useful insights into performance of the form.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-avgtime" data-options="tipLocation:left;tipAdjustmentX:-45;tipAdjustmentY:-30"><?php _e("This is the average time visitors take to fill out and submit your form.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-reset" data-options="tipLocation:left;tipAdjustmentX:-15;tipAdjustmentY:-30"><?php _e("Clicking <i>Reset</i> will set all stat counters to their initial state. You may want to do this after you have successfully tested your form and are preparing to make it live. It will ensure integrity of the Analytics data.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-sidebar-quick-toggles" data-options="tipLocation:left;tipAdjustmentX:-15;tipAdjustmentY:-10"><?php _e("This block has some useful toggles which allow you to turn options on and off without going into specific sections. Remember, some toggles will not work unless you have set them up first. Like the Autoresponder - you cannot turn it on when you have not yet setup the Autoresponder content inside the <b>Autoresponder</b> section.",'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm_tour_timewise_stats" data-button="Done"  data-options="tipLocation:top;nubPosition:hide;tipAdjustmentX:200;tipAdjustmentY:230"><?php _e("This ends the quick tour of the <b>Form Dashboard</b> area. You can restart the tour anytime in future by clicking on <i>Tour</i> button. As always, if anything does not works as expected, feel free to write to us ",'custom-registration-form-builder-with-submission-manager'); ?><a href="mailto:support@registrationmagic.com"><?php _e("here",'custom-registration-form-builder-with-submission-manager'); ?></a>. <?php _e("Good Luck!",'custom-registration-form-builder-with-submission-manager'); ?></li>
        
   </ol>
  <!-- Joyride Magic ends -->

<div class="rm-form-configuration-wrapper">
    <div class="rm-grid-top dbfl">
        <div class="rm-grid-title difl"><?php echo $data->form->get_form_name(); ?></div>
        <span class="rm-grid-button difl" onclick="rm_start_joyride()"><a class="rm_fd_link" href="javascript:void(0)"><?php echo RM_UI_Strings::get('LABEL_TOUR'); ?></a></span>
        
        <span class="rm-fd-form-toggle difr" id="rm_form_toggle">
        <?php
            if (count($data->all_forms) !== 0) {
                echo RM_UI_Strings::get('LABEL_TOGGLE_FORM');
                ?>            
                <select id="rm_form_dropdown" name="form_id" onchange = "rm_fd_switch_form(jQuery(this).val(), <?php echo $data->timerange; ?>)">
                <?php 
                    echo "<option value='rm_login_form'>".__('Login Form','custom-registration-form-builder-with-submission-manager')."</option>";
                    foreach ($data->all_forms as $form_id => $form)
                        if ($data->form_id == $form_id)
                            echo "<option value=$form_id selected>$form</option>";
                        else
                            echo "<option value=$form_id>$form</option>";
                    ?>
                </select>
                <?php
            }
            ?>
        </span>
    </div>
    <div class="rm-grid difl"> 
        
                <!--  -->
            <div class="rm-grid-section dbfl" id="rm_tour_timewise_stats">
                <div class="rm-grid-section-title dbfl rm-box-title"><?php echo RM_UI_Strings::get('LABEL_SUBS_OVER_TIME'); ?></div>
                <div class="rm-timerange-toggle rm-fd-form-toggle rm-timerange-dashboard">
                <?php echo RM_UI_Strings::get('LABEL_SELECT_TIMERANGE'); ?>
                    <select id="rm_stat_timerange" onchange="rm_refresh_stats()">
                    <?php $trs = array(7,30,60,90); 

                    foreach($trs as $tr)
                    {
                        echo "<option value=$tr";
                        if($data->timerange == $tr)
                            echo " selected";
                        printf(">".RM_UI_Strings::get("STAT_TIME_RANGES")."</option>",$tr);
                    }
                    ?>

                </select>
                </div>
                <canvas class="rm-box-graph" id="rm_subs_over_time_chart_div"></canvas>
            </div>
 
        <div class="rm-grid-section dbfl" id="rm-form-build-section">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_SEC_1_TITLE'); ?>
            </div>
            
            <div class="rm-grid-icon difl" id="rm-customfields-icon">
                <a href="?page=rm_field_manage&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <div class="rm-grid-icon-badge"><?php echo $data->field_count; ?></div>
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-custom-fields.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('FD_LABEL_FORM_FIELDS'); ?></div>
                </a>
            </div>
            
            <div class="rm-grid-icon difl" id="rm-design-icon"> 
                <a href="?page=rm_form_sett_view&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-view.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('FD_LABEL_DESIGN'); ?></div>
                </a>
            </div> 
            
             <div class="rm-grid-icon difl rm_dash_popup_link" id="rm_dash_popup_link_build"> 
                <a href="javascript:void(0)" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>dash-more-options.jpg">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('FD_LABEL_MORE'); ?></div>
                </a>
            </div> 
            
             

         
            
      

        </div>
        
        <div class="rm-grid-section dbfl" id="rm-general-icon">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_SEC_2_TITLE'); ?>               
            </div>
            
            <div class="rm-grid-icon difl" id="rm-general-settings">
                <a href="?page=rm_form_sett_general&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-settings.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_GEN_SETT'); ?></div>
                </a>
            </div>


            <div class="rm-grid-icon difl" id="rm-accounts-icon">
                <a href="?page=rm_form_sett_accounts&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-accounts.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_ACC_SETT'); ?></div>
                    
                </a>
            </div>  

            <div class="rm-grid-icon difl" id="rm-postsubmit-icon">
                <a href="?page=rm_form_sett_post_sub&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>post-submission.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_PST_SUB_SETT'); ?></div>
                </a>
            </div>  

            <div class="rm-grid-icon difl" id="rm-autoresponder-icon">
                <a href="?page=rm_form_sett_autoresponder&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl" >
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>auto-responder.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_AUTO_RESP_SETT'); ?></div>
                </a>
            </div> 

            <div class="rm-grid-icon difl" id="rm-limits-icon">
                <a href="?page=rm_form_sett_limits&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-limits.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_LIM_SETT'); ?></div>
                </a>
            </div>  
            
            <!--
            <div class="rm-grid-icon difl" id="rm-access-icon"> 
                <a href="?page=rm_form_sett_access_control&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-access.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_ACTRL_SETT'); ?></div>
                </a>
            </div>
            -->
            
            <div class="rm-grid-icon difl" id="rm-email-templates">
                <a href="?page=rm_form_sett_email_templates&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>email_templates.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_EMAIL_TEMPLATES_SETT'); ?></div>
                </a>
            </div> 
            <!--
            <div class="rm-grid-icon difl" id="rm-overrides-icon">
                <a href="?page=rm_form_sett_override&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link"> 
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-overrides.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_OVERRIDES_SETT'); ?></div>
                </a>
            </div>
            -->
            
            <div class="rm-grid-icon difl rm_dash_popup_link" id="rm_dash_popup_link_config"> 
                <a href="javascript:void(0)" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>dash-more-options.jpg">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('FD_LABEL_MORE'); ?></div>
                </a>
            </div> 
            
            
        </div>

        <div class="rm-grid-section dbfl" id="rm-thirdparty-section">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_SEC_3_TITLE'); ?>
            </div>
            
            <div class="rm-grid-icon difl" id="rm-mailchimp-icon">  
                <a href="?page=rm_form_sett_mailchimp&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">  
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>mailchimp.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_F_MC_SETT'); ?></div>
                    
                </a>
            </div>
            <?php if(class_exists('Profile_Magic')): ?>
            <div class="rm-grid-icon difl" id="rm-profilegrid-icon">  
                <a href="?page=rm_form_sett_profilegrid" class="rm_fd_link">  
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>profilegrid.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo __('ProfileGrid', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    
                </a>
            </div>
            <?php else: ?>
            <div class="rm-grid-icon difl" id="rm-profilegrid-icon">  
                <a id="pg_popup_btn" href="javascript:void(0)" class="rm_fd_link">  
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>profilegrid.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo __('ProfileGrid', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                    
                </a>
            </div>
            <!-- PG Integration Pop-up -->
            <div class="rmagic rm-hide-version-number rm-pg-integration">
            <div id="pg_popup_container" class="rm-modal-view" style="display:none">
                <div class="rm-modal-overlay"></div>
                <div class="rm-modal-wrap rm-publish-form-popup">
                    <div class="rm-modal-titlebar rm-new-form-popup-header">
                        <div class="rm-modal-title">
                            <?php _e('User Profiles, Groups, Memberships And Communities','custom-registration-form-builder-with-submission-manager'); ?>               
                        </div>
                        <span class="rm-modal-close">×</span>
                    </div>
                      <div class="rm-modal-container">
                <div class="rm-directory-container dbfl">
    <div class="rm-publish-directory-col rm-difl">
        <div class="rm-section-publish-note"><?php _e('User Profiles, Groups, Memberships And Communities','custom-registration-form-builder-with-submission-manager') ?></div>
        <div class="rm-publish-text"><?php _e('Turn front-end user area into powerful user profile hub. Allow users to edit their profiles including photos. Offer interactive features like groups memberships, real-time messaging and friend lists. Restrict content selectively. ProfileGrid is free and you can get started within minutes.','custom-registration-form-builder-with-submission-manager'); ?></div>
                    <a href="<?php echo $data->pg_install_url; ?>" class="pg-install-now"><?php _e('Install Now','custom-registration-form-builder-with-submission-manager') ?></a>
            
                    <div class="rm-pg-group-popup"></div>
    </div>
    <div class="rm-publish-directory-col rm-difl"></div>
</div>
                </div>
                    
                    
                </div>
              
            </div>
            </div>
               <!-- PG Integration Pop-up End -->
            <?php endif; ?>   
               
            <?php /* if(class_exists('Event_Magic')): ?>
            <div class="rm-grid-icon difl">  
                <a href="?page=rm_options_eventprime" class="rm_fd_link">  
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>event-prime-logo.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo __('EventPrime', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                </a>
            </div> 
            <?php else: ?>
            <div class="rm-grid-icon difl">  
                <a id="ep_popup_btn" href="javascript:void(0)" class="rm_fd_link">  
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>event-prime-logo.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo __('EventPrime', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                </a>
            </div>
            <div class="rmagic rm-hide-version-number rm-ep-integration">
                <div id="ep_popup_container" class="rm-modal-view" style="display:none">
                    <div class="rm-modal-overlay"></div>
                    <div class="rm-modal-wrap rm-publish-form-popup">
                        <div class="rm-modal-titlebar rm-new-form-popup-header">
                            <div class="rm-modal-title">
                                <?php _e('EventPrime - Event Calendar Management', 'custom-registration-form-builder-with-submission-manager'); ?>               
                            </div>
                            <span class="rm-modal-close">×</span>
                        </div>
                          <div class="rm-modal-container">
                            <div class="rm-directory-container dbfl">
                                <div class="rm-publish-directory-col rm-difl">
                                    <div class="ep-integration-logo dibfl">   <img class="" src="<?php echo RM_IMG_URL; ?>ep-logo.png"></div>                                   
                                    <div class="rm-publish-text"><?php _e('Planning to publish events calendar on your site? Use EventPrime to create simple or complex events and manage bookings. EventPrime is free and you can get started within minutes. ', 'custom-registration-form-builder-with-submission-manager'); ?></div>
                                    <div class="ep-integration-features difl">
                                        <ul>
                                        <li>Extend power of your RegistrationMagic forms by connecting them to a complete Event Management toolkit.</li>
                                        <li>Create/ edit, drag and drop Events directly on the Events Calendar.</li>
                                        <li>Manage Event Sites/ Venues (Optional)</li>
                                        <li>Manage Event Performers, Keynote Speakers, Hosts etc. (Optional)</li>
                                        <li>Powerful widgets to showcase Events and Calendar on your website.</li>
                                        <li>Dedicated area for your users to manage bookings.</li>
                                        <li>Works with any theme.</li>   
                                        </ul>
                                    </div>
                                    <a href="<?php echo $data->ep_install_url; ?>" class="pg-install-now"><?php _e('Install Now', 'custom-registration-form-builder-with-submission-manager') ?></a>     
                                </div>
                                <div class="rm-publish-directory-col rm-difl"> <img class="ep-integration-img" src="<?php echo RM_IMG_URL; ?>ep-popup-integration.png"></div>
                            </div>
                        </div>


                    </div>

                </div>
            </div>   
            <!-- EventPrime Integration Pop-up -->   
            <?php endif; */ ?>
            
            <?php do_action('rm_extended_apps'); ?>
            
            <div class="rm-grid-icon difl rm_dash_popup_link" id="rm_dash_popup_link_integrate"> 
                <a href="javascript:void(0)" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>dash-more-options.jpg">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('FD_LABEL_MORE'); ?></div>
                </a>
            </div>
             
        </div>

        <div class="rm-grid-section dbfl" id="rm-publish-section">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_SEC_4_TITLE'); ?>
            </div>            
       
            <div class="rm-grid-icon difl" id="rm-shortcode-icon">
                <a href="javascript:void(0)" class="rm_fd_link rm_publish_popup_link" data-publish_type="shortcode">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>publish_shortcode.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_PUBLISH_SHORTCODE'); ?></div>
                </a>
            </div> 
            <!--
            <div class="rm-grid-icon difl">
                <a href="javascript:void(0)" class="rm_fd_link rm_publish_popup_link" data-publish_type="embed">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>publish_embed.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_PUBLISH_HTML_CODE'); ?></div>
                </a>
            </div> 
            -->
            <div class="rm-grid-icon difl" id="rm-form-widget-icon">
                <a href="javascript:void(0)" class="rm_fd_link rm_publish_popup_link" data-publish_type="widget">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>publish_widget.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_PUBLISH_FORM_WIDGET'); ?></div>
                </a>
            </div> 
            <!--
            <div class="rm-grid-icon difl">
                <a href="javascript:void(0)" class="rm_fd_link rm_publish_popup_link" data-publish_type="userdir">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>publish_userdir.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_PUBLISH_USER_DIRECTORY'); ?></div>
                </a>
            </div> 
            -->
            <div class="rm-grid-icon difl" id="rm-user-area-icon">
                <a href="javascript:void(0)" class="rm_fd_link rm_publish_popup_link" data-publish_type="subs">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>publish_subs.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_PUBLISH_USER_AREA'); ?></div>
                </a>
            </div>
            <div class="rm-grid-icon difl" id="rm-magic-popup-icon">
                <a href="javascript:void(0)" class="rm_fd_link rm_publish_popup_link" data-publish_type="magicpopup">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>publish_magicpopup.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_PUBLISH_MAGIC_POPUP'); ?></div>
                </a>
            </div> 
            <div class="rm-grid-icon difl" id="rm-landing-page-icon">
                <a href="javascript:void(0)" class="rm_fd_link rm_publish_popup_link" data-publish_type="landingpage">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>publish_landingpage.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_PUBLISH_LANDING_PAGE'); ?></div>
                </a>
            </div> 
            <div class="rm-grid-icon difl" id="rm-login-box-icon">
                <a href="javascript:void(0)" class="rm_fd_link rm_publish_popup_link" data-publish_type="login">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>publish_login.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_PUBLISH_LOGIN_BOX'); ?></div>
                </a>
            </div> 
            
            <div class="rm-grid-icon difl"  id="rm-otp-login-box-icon">
                <a href="javascript:void(0)" class="rm_fd_link rm_publish_popup_link" data-publish_type="otp">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>publish_otp.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_PUBLISH_OTP_WIDGET'); ?></div>
                </a>
            </div> 
            
               <div class="rm-grid-icon difl rm_dash_popup_link" id="rm_dash_popup_link_publish"> 
                <a href="javascript:void(0)" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>dash-more-options.jpg">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('FD_LABEL_MORE'); ?></div>
                </a>
            </div>
           
        </div>
       
        <div class="rm-grid-section dbfl" id="rm-manage-section">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_SEC_5_TITLE'); ?>
            </div>
            
               <div class="rm-grid-icon difl rm-sub-icon-tour">
                <a href="?page=rm_submission_manage&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <div class="rm-grid-icon-badge rm-sub-icon-tour-badge"><?php echo $data->sub_count; ?></div>
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-inbox.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_REGISTRATIONS'); ?></div>
                </a>
            </div> 
            <div class="rm-grid-icon difl" id="rm-outbox-icon">
                <a href="?page=rm_sent_emails_manage&rm_form_id=<?php echo $data->form_id; ?>&astep=publish" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>outbox.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('LABEL_OUTBOX'); ?></div>
                </a>
            </div>
           <!--
            <div class="rm-grid-icon difl" id="rm-attachment-icon">
                <a href="?page=rm_attachment_manage&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link"> 
                    <div class="rm-grid-icon-area dbfl">
                        <div class="rm-grid-icon-badge"><?php echo $data->attachment_count ? : 0; ?></div>
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-attachments.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('TITLE_ATTACHMENT_PAGE'); ?></div>
                    
                </a>
            </div> 
            -->
               <div class="rm-grid-icon difl rm_dash_popup_link" id="rm_dash_popup_link_manage"> 
                <a href="javascript:void(0)" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>dash-more-options.jpg">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('FD_LABEL_MORE'); ?></div>
                </a>
            </div>
            
            
        </div>
        
        <div class="rm-grid-section dbfl"  id="rm-analyze-section">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_SEC_6_TITLE'); ?>
            </div>            
            <div class="rm-grid-icon difl" id="rm-analytics-icon">
                <a href="?page=rm_analytics_show_form&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>form-analytics.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('TITLE_FORM_STAT_PAGE'); ?></div>
                </a>
            </div> 
            <!--
            <div class="rm-grid-icon difl" id="rm-field-analytics-icon">
                <a href="?page=rm_analytics_show_field&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>field-analytics.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('TITLE_FIELD_STAT_PAGE'); ?></div>
                    
                </a>
            </div>
             -->
                <div class="rm-grid-icon difl rm_dash_popup_link" id="rm_dash_popup_link_analyze"> 
                <a href="javascript:void(0)" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>dash-more-options.jpg">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('FD_LABEL_MORE'); ?></div>
                </a>
            </div>
             
        </div>
       
        <div class="rm-grid-section dbfl" id="rm-automate-section">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_SEC_7_TITLE'); ?>
            </div>            
            <?php do_action("rm_form_settings_dashboard_action_icon", $data->form_id); ?>
            
            <div class="rm-grid-icon difl" id="rm-emailusers-icon">
                <a href="?page=rm_invitations_manage&rm_form_id=<?php echo $data->form_id; ?>" class="rm_fd_link">    
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>email-users.png">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('TITLE_INVITES'); ?></div>
                </a>
            </div> 
            
               <div class="rm-grid-icon difl rm_dash_popup_link" id="rm_dash_popup_link_automate"> 
                <a href="javascript:void(0)" class="rm_fd_link">   
                    <div class="rm-grid-icon-area dbfl">
                        <img class="rm-grid-icon dibfl" src="<?php echo RM_IMG_URL; ?>dash-more-options.jpg">
                    </div>
                    <div class="rm-grid-icon-label dbfl"><?php echo RM_UI_Strings::get('FD_LABEL_MORE'); ?></div>
                </a>
            </div>
            
        </div>
              
    </div>
    <div class="rm-grid-sidebar-1 difl">
        <div class="rm-grid-section-cards dbfl">        
            <?php
            if($data->sub_count == 0):
                ?>
            <div class="rm-grid-sidebar-card dbfl">
                <div class='rmnotice-container'><div class="rmnotice-container"><div class="rm-counter-box">0</div><div class="rm-counter-label"><?php echo RM_UI_Strings::get('LABEL_REGISTRATIONS'); ?></div></div></div>  
</div>
                <?php
            endif;
            foreach ($data->latest_subs as $submission):
                ?>
                <div class="rm-grid-sidebar-card dbfl <?php echo $submission->is_read? '' : "rm-grid-user-new"; ?>">
                    <a href="?page=rm_submission_view&rm_submission_id=<?php echo $submission->id; ?>" class="fd_sub_link">
                    <?php // echo $submission->is_read? '' : "<div class='rm-grid-user-badge'>". RM_UI_Strings::get('FD_BADGE_NEW')."!</div>"; ?>
                    <div class="rm-grid-card-profile-image dbfl">
                        <img class="fd_img" src="<?php echo $submission->user_avatar; ?>">
                    </div>
                    <div class="rm-grid-card-content difl">
                        <div class="dbfl"><?php echo $submission->user_name; ?></div>
                        <div class="rm-grid-card-content-subtext dbfl"><?php echo $submission->submitted_on; ?></div></div>
                    </a>
                </div>
                <?php
            endforeach;
            ?>
            <div class="rm-grid-quick-tasks dbfl">
                <div class="rm-grid-sidebar-row dbfl">
                    <div class="rm-grid-sidebar-row-label difl">
                        <a class="<?php echo $data->sub_count ? '' : 'rm_deactivated'?>" href="?page=rm_submission_manage&rm_form_id=<?php echo $data->form_id; ?>"><?php echo RM_UI_Strings::get('FD_LABEL_VIEW_ALL'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="rm-grid-section-cards dbfl"> 

            <div class="rm-grid-sidebar-card dbfl" id="rm-attachments-card">
                <div class='rmnotice-container'><div class="rmnotice-container"><div class="rm-counter-box">0</div><div class="rm-counter-label"><?php echo RM_UI_Strings::get('TITLE_ATTACHMENT_PAGE'); ?></div></div></div>  
            </div>

            <div class="rm-grid-quick-tasks dbfl">
                <div class="rm-grid-sidebar-row dbfl">
                    <div class="rm-grid-sidebar-row-label difl">
                        <a href="?page=rm_attachment_manage&rm_form_id=<?php echo $data->form_id; ?>"><?php echo RM_UI_Strings::get('FD_LABEL_VIEW_ALL'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        
                 <div class="rm-grid-section-cards rm-quick-links-box dbfl"> 
        
              <div class="rm-grid-section-title dbfl"><?php _e('Quick Links','custom-registration-form-builder-with-submission-manager'); ?></div>
                <div class="rm-grid-sidebar-card dbfl">
                
                <ul class="rm-quick-links">
                    <li><a href="<?php echo admin_url('admin.php?page=rm_options_general'); ?>"><?php _e('Set Form Layout','custom-registration-form-builder-with-submission-manager'); ?></a></li>   
                    <li><a href="<?php echo admin_url('admin.php?page=rm_options_security'); ?>"><?php _e('Configure reCAPTCHA','custom-registration-form-builder-with-submission-manager'); ?></a></li>    
                    <li><a href="<?php echo admin_url('admin.php?page=rm_options_autoresponder'); ?>"><?php _e('Admin Notification Settings','custom-registration-form-builder-with-submission-manager'); ?></a></li>     
                    <li><a href="<?php echo admin_url('admin.php?page=rm_options_payment'); ?>"><?php _e('Payment Settings','custom-registration-form-builder-with-submission-manager'); ?></a></li>     
                    <li><a href="<?php echo admin_url('admin.php?page=rm_options_default_pages'); ?>"><?php _e('Set Default Pages','custom-registration-form-builder-with-submission-manager'); ?></a></li>
                </ul>
                 
                </div>

                 </div> 


    </div>
    <div class="rm-grid-sidebar-2 difl">
        <div class="rm-grid-section dbfl">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_LABEL_STATUS'); ?>
                <span class="rm-grid-section-toggle rm-collapsible"></span>
            </div>
            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl" id="rm-sidebar-sc-icon">
                    <img src="<?php echo RM_IMG_URL; ?>shortcode.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl"><?php echo RM_UI_Strings::get('FD_LABEL_FORM_SHORTCODE'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><span id="rmformshortcode">[RM_Form id='<?php echo $data->form->get_form_id(); ?>']</span><a href="javascript:void(0)" onclick="rm_copy_to_clipboard(document.getElementById('rmformshortcode'))" id="rm-copy-sc"><?php echo RM_UI_Strings::get('FD_LABEL_COPY'); ?></a>
                    <div style="display:none" id="rm_msg_copied_to_clipboard"><?php _e("Copied to clipboard",'custom-registration-form-builder-with-submission-manager'); ?></div><div style="display:none" id="rm_msg_not_copied_to_clipboard"><?php _e("Could not be copied. Please try manually.",'custom-registration-form-builder-with-submission-manager'); ?></div></div>
            </div>
            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>visiblity.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl" id="rm-sidebar-visibility"><?php echo RM_UI_Strings::get('FD_LABEL_FORM_VISIBILITY'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><?php echo $data->form_access; ?><a href="?page=rm_form_sett_access_control&rm_form_id=<?php echo $data->form_id; ?>"><?php echo RM_UI_Strings::get('LABEL_EDIT'); ?></a></div>
            </div>
            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>event.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl"><?php echo RM_UI_Strings::get('FD_LABEL_FORM_CREATED_ON'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><?php echo RM_Utilities::localize_time($data->form->get_created_on()); ?></div>
            </div>

            <div class="rm-grid-quick-tasks dbfl">
                <div class="rm-grid-sidebar-row dbfl">
                    <div class="rm-grid-sidebar-row-label difl">
                        <a href="javascript:void(0)" id="rm-sidebar-delete" onclick="jQuery.rm_do_action_with_alert('<?php echo RM_UI_Strings::get('ALERT_DELETE_FORM'); ?>', 'rm_fd_action_form', 'rm_form_remove')"><?php echo RM_UI_Strings::get('LABEL_DELETE'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="rm-grid-section dbfl">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_LABEL_CONTENT'); ?>
                <span class="rm-grid-section-toggle rm-collapsible"></span>
            </div>
            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>pages.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl" id="rm-sidebar-pages"><?php echo RM_UI_Strings::get('FD_FORM_PAGES'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><?php echo 1; ?></div>
            </div>
            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>field.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl" id="rm-sidebar-fields"><?php echo RM_UI_Strings::get('FD_LABEL_F_FIELDS'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><?php echo $data->field_count; ?><a id="rm-sidebar-add-field" href="?page=rm_field_manage&rm_form_id=<?php echo $data->form->get_form_id(); ?>"><?php echo RM_UI_Strings::get('LABEL_ADD'); ?></a></div>
            </div>
            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>submit.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl" id="rm-sidebar-add-submit"><?php echo RM_UI_Strings::get('FD_FORM_SUBMIT_BTN_LABEL'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><div class="difl" id="rm-submit-label"><?php echo $data->form_options->form_submit_btn_label ? : 'Submit'; ?></div><a href='javascript:;' onclick='edit_label()' ><?php echo RM_UI_Strings::get('LABEL_FIELD_ICON_CHANGE'); ?></a></div>
                <div id="rm-submit-label-textbox" style="display:none"><input type="text" id="submit_label_textbox"/><div><input type="button" value ="Save" onclick="save_submit_label()"><input type="button" value ="Cancel" onclick="cancel_edit_label()"></div></div> </div>
            <div class="rm-grid-quick-tasks dbfl">
                <div class="rm-grid-sidebar-row dbfl">
                    <div class="rm-grid-sidebar-row-label difl">
                        <a id="rm-sidebar-duplicate" href="javascript:void(0)" onclick="jQuery.rm_do_action('rm_fd_action_form', 'rm_form_duplicate')"><?php echo RM_UI_Strings::get('LABEL_DUPLICATE'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="rm-grid-section rm-premium-option-grid dbfl">
            <div class="rm-grid-section-title dbfl">
                <?php echo RM_UI_Strings::get('FD_LABEL_STATS'); ?>
                <span class="rm-grid-section-toggle rm-collapsible"></span>
            </div>
            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>visitors.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl" id="rm-sidebar-visitors"><?php echo RM_UI_Strings::get('FD_LABEL_VISITORS'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><?php echo $data->visitors_count .__(" in last 30 days.",'custom-registration-form-builder-with-submission-manager'); ?></div>
            </div>
            <div class="rm-grid-sidebar-row rm-premium-option-popup-wrap dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>submissions.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl" id="rm-sidebar-submissions"><?php echo RM_UI_Strings::get('LABEL_REGISTRATIONS'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><?php echo $data->sub_count; ?><a href="javascript:void(0)" class="rm_deactivated rm-premium-option" onclick="CallModalBox(this)"><?php echo RM_UI_Strings::get('FD_DOWNLOAD_REGISTRATIONS'); ?></a></div>
                 <div class="rm-premium-option-popup" style="display:none">
                <span class="rm-premium-option-popup-nub"></span>
                <span class="rm_buy_pro_inline"><?php printf(__('To unlock downloading submissions (and many more features), please upgrade. <a href="%s" target="blank">Click here</a>', 'custom-registration-form-builder-with-submission-manager'), RM_Utilities::comparison_page_link()); ?> </span>
              </div>
            </div>

            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>conversion.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl" id="rm-sidebar-conversion"><?php echo RM_UI_Strings::get('LABEL_CONVERSION'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><?php echo $data->conversion_rate; ?>%</div>
            </div>

            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>avgtime.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl" id="rm-sidebar-avgtime"><?php echo RM_UI_Strings::get('FD_AVG_TIME'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><?php echo $data->avg_time; ?></div>
            </div>


            <div class="rm-grid-quick-tasks dbfl">
                <div class="rm-grid-sidebar-row dbfl">
                    <div class="rm-grid-sidebar-row-label difl">
                        <a id="rm-sidebar-reset" href="javascript:void(0)" onclick="jQuery.rm_do_action_with_alert('<?php _e("You are going to delete all stats for selected form. Do you want to proceed?",'custom-registration-form-builder-with-submission-manager'); ?>', 'rm_fd_action_form', 'rm_analytics_reset')"><?php echo RM_UI_Strings::get('LABEL_RESET'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="rm-grid-section dbfl">
            <div class="rm-grid-section-title dbfl" id="rm-sidebar-quick-toggles">
                <?php echo RM_UI_Strings::get('FD_LABEL_QCK_TOGGLE'); ?>
                <span class="rm-grid-section-toggle rm-collapsible"></span>
            </div>

             <?php if($data->form_options->form_email_subject && $data->form_options->form_email_content)
                  {
                        $deactivation_class = '';
                        $tooltip = '';
                  }else{
                        $deactivation_class = 'rm_transparent_deactivated';
                        $tooltip = 'title="'.sprintf(RM_UI_Strings::get('FD_TOGGLE_TOOLTIP'),admin_url('admin.php?page=rm_form_sett_autoresponder&rm_form_id='.$data->form_id)).'"';
                  }
                
             ?>
            <div   <?php echo $tooltip; ?> class="rm-grid-sidebar-row dbfl <?php echo $deactivation_class; ?>">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>auto-responder.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl" ><?php echo RM_UI_Strings::get('FD_AUTORESPONDER'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl<?php echo ($data->form_options->form_email_subject && $data->form_options->form_email_content) ? '' : ' rm_deactivated' ?>"><div class="rm-grid-sidebar-row-value difl"><div class="switch">
                            <input id="rm-toggle-1"  class="rm-toggle rm-toggle-round-flat" onchange="rm_fd_quick_toggle(this, <?php echo $data->form_id; ?>)" name="form_should_send_email" type="checkbox"<?php echo $data->form->get_form_should_send_email() == 1 ? ' checked' : '' ?>>
                            <label for="rm-toggle-1"></label>
                        </div></div></div>
            </div>

            <div class="rm-grid-sidebar-row dbfl">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>form-accounts.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl"><?php echo RM_UI_Strings::get('FD_WP_REG'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl"><div class="rm-grid-sidebar-row-value difl"><div class="switch">
                            <input id="rm-toggle-2" class="rm-toggle rm-toggle-round-flat" onchange="rm_fd_quick_toggle(this, <?php echo $data->form_id; ?>)" name="form_type" type="checkbox"<?php echo $data->form->get_form_type() == 1 ? ' checked' : '' ?>>
                            <label for="rm-toggle-2"></label>
                        </div></div></div>
            </div>

          <?php if($data->form_options->form_expired_by)
                  {
                        $deactivation_class = '';
                        $tooltip = '';
                  }else{
                        $deactivation_class = 'rm_transparent_deactivated';
                        $tooltip = 'title="'.sprintf(RM_UI_Strings::get('FD_TOGGLE_TOOLTIP'),admin_url('admin.php?page=rm_form_sett_limits&rm_form_id='.$data->form_id)).'"';
                  }
                
             ?>
            <div <?php echo $tooltip;?> class="rm-grid-sidebar-row dbfl <?php echo $deactivation_class; ?>">
                <div class="rm-grid-sidebar-row-icon difl">
                    <img src="<?php echo RM_IMG_URL; ?>form-limits.png">
                </div>
                <div class="rm-grid-sidebar-row-label difl"><?php echo RM_UI_Strings::get('LABEL_EXPIRY'); ?>:</div>
                <div class="rm-grid-sidebar-row-value difl<?php echo ($data->form_options->form_expired_by) ? '' : ' rm_deactivated' ?>"><div class="rm-grid-sidebar-row-value difl"><div class="switch">
                            <input id="rm-toggle-5" class="rm-toggle rm-toggle-round-flat" onchange="rm_fd_quick_toggle(this, <?php echo $data->form_id; ?>)" name="form_should_auto_expire" type="checkbox"<?php echo $data->form->get_form_should_auto_expire() == 1 ? ' checked' : '' ?>>
                            <label for="rm-toggle-5"></label>
                        </div></div></div>
            </div>

        </div>

    </div>

    <!-- action form to execute rm_slug_actions -->
    <form style="display:none" method="post" action="" id="rm_fd_action_form">
        <?php wp_nonce_field('rm_form_settings_controller'); ?>
        <input type="hidden" name="rm_slug" value="" id="rm_slug_input_field">
        <input type="hidden" name="req_source" value="form_dashboard">
        <input type="hidden" name="rm_interval" value="all">
        <input type="number" name="form_id" value="<?php echo $data->form_id; ?>">
        <input type="number" name="rm_selected" value="<?php echo $data->form_id; ?>">
    </form

    <!--    Forms toggle-->
    <div id="rm_form_toggle" style="display: none">
        <select onchange="rm_fd_switch_form()">
            <?php
            foreach ($data->all_forms as $form_id => $form_name):
                echo "<option value='$form_id'>$form_name</option>";
            endforeach;
            ?>
        </select>
    </div>
</div>

<!-- Dashboard-Promo popup -->
    <div class="rmagic rm-hide-version-number">
    <div id="rm_fd_promo_popup" class="rm-modal-view" style="display: none;">
        <div class="rm-modal-overlay"></div>
        <div class="rm-modal-wrap rm-fd-promo-popup">

            <div class="rm-modal-titlebar ">
                <div class="rm-modal-title">
                <?php _e("Extend Power of RegistrationMagic",'custom-registration-form-builder-with-submission-manager'); ?>
                </div>
                <span class="rm-modal-close">&times;</span>
            </div>
            <div class="rm-modal-container">
                <?php include_once RM_ADMIN_DIR . 'views/template_rm_fd_promo.php'; ?>
            </div>
            
            <div class="rm-modal-footer"><a href="?page=rm_support_premium_page" target="_blank"><?php _e("More Information",'custom-registration-form-builder-with-submission-manager'); ?></a></div>
        </div>

    </div>
    </div>
<!-- End Dashboard-promo pop-up  -->






<!-- Form Publish Pop-up -->
    <div class="rmagic rm-hide-version-number">
    <div id="rm_form_publish_popup" class="rm-modal-view" style="display: none;">
        <div class="rm-modal-overlay"></div>
        <div class="rm-modal-wrap rm-publish-form-popup">

            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title">
                    <?php _e("Publish",'custom-registration-form-builder-with-submission-manager'); ?>
                </div>
                <span class="rm-modal-close">&times;</span>
            </div>
            <div class="rm-modal-container">
                <?php $form_id_to_publish = $data->form_id; ?>
                <?php include_once RM_ADMIN_DIR . 'views/template_rm_formflow_publish.php'; ?>
            </div>
        </div>

    </div>
    </div>
<!-- End Form Publish Pop-up -->

<?php
            wp_enqueue_script('jquery-ui-tooltip',array('jquery'));
?>
<pre class='rm-pre-wrapper-for-script-tags'><script>
    function edit_label(){
        jQuery('#rm-submit-label-textbox').show();
    }
    
    function cancel_edit_label(){
        jQuery('#submit_label_textbox').val('');
        jQuery('#rm-submit-label-textbox').hide();
    }
    
    function save_submit_label(){
        
       var label= jQuery('#submit_label_textbox').val();
        if(label == '')
       {
           jQuery('#submit_label_textbox').focus();
           return 0;
       }
        var data = {
			'action': 'rm_save_submit_label',
			'label': label,
			'form_id':<?php echo $data->form_id ;?>
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
                    console.log(response);
                       if(response== 'changed')
                       {
                           jQuery('#rm-submit-label').html(label);
                           jQuery('#rm-submit-label-textbox').hide();
                       }
                       else
                       {
                           alert('<?php _e("Could not change. Please try again.",'custom-registration-form-builder-with-submission-manager'); ?>');
                           location.reload(); 
                       }
                      
		});
    }
    jQuery(function () { 
    jQuery(document).tooltip({
        content: function () {
            return jQuery(this).prop('title');
        },
        show: null, 
        close: function (event, ui) {
            ui.tooltip.hover(

            function () {
                jQuery(this).stop(true).fadeTo(400, 1);
            },

            function () {
                jQuery(this).fadeOut("400", function () {
                   jQuery(this).remove();
                })
            });
        }
    });
});

</script></pre>

<pre class='rm-pre-wrapper-for-script-tags'><script>
    
    jQuery(document).ready(function(){
       //Configure joyride
       //If autostart is false, call again "jQuery("#rm-form-man-joytips").joyride()" to start the tour.
       <?php if($data->autostart_tour): ?>
       jQuery("#rm-form-sett-dashboard-joytips").joyride({tipLocation: 'top',
                                               autoStart: false,
                                               postRideCallback: rm_joyride_tour_taken});
        <?php else: ?>
            jQuery("#rm-form-sett-dashboard-joytips").joyride({tipLocation: 'top',
                                               autoStart: false,
                                               postRideCallback: rm_joyride_tour_taken});
        <?php endif; ?>
            
        jQuery(".rm_publish_popup_link").each(function(){
            jQuery(this).click(function(){
                rm_set_publish_popup( <?php echo $data->form_id; ?>,jQuery(this).data("publish_type"));
                jQuery("#rm_form_publish_popup").show();
            });
            
        });
        jQuery(".rm_dash_popup_link").each(function(){		
             jQuery(this).click(function(){		
                 var target = jQuery(this).attr("id").substr("rm_dash_popup_link_".length);		
                 if(typeof target !== "undefined") {		
                     jQuery(".rm-fd-promo-section").hide();		
                     jQuery(".rm-fd-promo-section#rm-fd-promo-"+target).show();		
                     jQuery("#rm_fd_promo_popup").show();		
                 }		
             });		
             		
         });
        
        jQuery('.rm-modal-close, .rm-modal-overlay').click(function () {
            jQuery(this).parents('.rm-modal-view').hide();
        });
        
        jQuery("#pg_popup_btn").click(function(){
            jQuery("#pg_popup_container").show();
        });
        
        jQuery("#ep_popup_btn").click(function(){		
             jQuery("#ep_popup_container").show();		
         });
    });
   
   function rm_start_joyride(){
       //Expand any collapsed section before starting tour.
       jQuery('.rm-collapsed').each(function(){jQuery(this).click();});
       jQuery("#rm-form-sett-dashboard-joytips").joyride();
    }
    
    function rm_joyride_tour_taken(){
        var data = {
			'action': 'joyride_tour_update',
			'tour_id': 'form_setting_dashboard_tour',
                        'state': 'taken'
		};
        jQuery.post(ajaxurl, data, function(response) {});
    }
    
    function drawTimewiseStat()
    {
        if('<?php echo $show_chart; ?>'==0){
            jQuery("#rm_subs_over_time_chart_div,#rm_tour_timewise_stats").remove();
            return;
        }
        var data= {
                    labels: <?php echo $date_labels; ?>,
                    datasets:[{
                                label: 'Submissions',
                                data: <?php echo $subs; ?>,
                                fill: false,
                                borderColor: 'rgb(53,167,227)',
                                backgroundColor: 'rgb(53,167,227)'
                    },
                    {
                                label: 'Visits',
                                data: <?php echo $visits; ?>,
                                fill: false,
                                borderColor: 'rgb(72,84,104)',
                                backgroundColor: 'rgb(72,84,104)'
                    }]
        }
        var ctx = document.getElementById('rm_subs_over_time_chart_div');
       // ctx.height = 5000;
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {}
        });
    }
    
  jQuery(document).ready(function(){
    jQuery('.rm-grid-sidebar-row-value .rm-premium-option').on('click', function(e) {
        jQuery('.rm-premium-option-popup').toggle();
    });
   
});  
    
</script></pre>
<?php } ?>