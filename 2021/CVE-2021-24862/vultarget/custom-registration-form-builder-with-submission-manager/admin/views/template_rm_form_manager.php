<?php
if (!defined('WPINC')) {
    die('Closed');
}
$file = $_SERVER["SCRIPT_NAME"];
$file_break = Explode('/', $file);
$pfile = $file_break[count($file_break) - 1];
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_form_manager.php'); else {
/**
 * @internal Template File [Form Manager]
 *
 * This file renders the form manager page of the plugin which shows all the forms
 * to manage delete edit or manage
 */

global $rm_env_requirements;
global $regmagic_errors;
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
 <?php if (($rm_env_requirements & RM_REQ_EXT_CURL) && $data->newsletter_sub_link){ ?>
 <div class="rm-newsletter-banner" id="rm_newsletter_sub"><?php echo $data->newsletter_sub_link;?><img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/close-rm.png'; ?>" onclick="jQuery('#rm_newsletter_sub').hide()"></div>
 <?php } ?>
 
 <?php
 //Check errors
 RM_Utilities::fatal_errors();
 if(is_array($regmagic_errors)){
     foreach($regmagic_errors as $err)
    {
       //Display only non - fatal errors
       if($err->should_cont)
           echo '<div class="shortcode_notification ext_na_error_notice"><p class="rm-notice-para">'.$err->msg.'</p></div>';
    }
 }
 
 ?>
<div class="rmagic rm-all-forms">
    
    <!-- Joyride Magic begins -->
    <ol id="rm-form-man-joytips" style="display:none">
        <li data-id="rm-tour-title" data-options="tipLocation:top;nubPosition:hide;tipAdjustmentX:200;tipAdjustmentY:230">
            <h2>
                <?php _e('Welcome to RegistrationMagic', 'custom-registration-form-builder-with-submission-manager'); ?>
            </h2>
            <p><?php _e("RegistrationMagic is a powerful plugin that allows you to build custom registration system on your WordPress site. This is the main landing page - Forms Manager. Click <b>Next</b> to start a quick tour of this page. To stop at anytime, click the close icon on top right.", 'custom-registration-form-builder-with-submission-manager'); ?></p>
        </li>
        <li data-id="rmbar" data-options="tipLocation:bottom"><?php _e('You will see this flat white box on top of different sections inside RegistrationMagic. We call it operations bar. It contains...', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-tour-title" data-options="tipLocation:bottom"><?php _e('The heading of the section you are presently in...', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-ob-icons" data-options="nubPosition:bottom-right;tipAdjustmentX:-330"><?php _e('Quick access icons relevant to the section...', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-ob-sort" data-options="nubPosition:bottom-right;tipAdjustmentX:-320"><?php _e('A filter and sort drop down menu. In this section, it allows you to sort your forms.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-ob-nav"><?php _e("And a navigation menu with most important functions laid horizontally. Let's look at the Form Manager functions one by one.", 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-ob-new"><?php _e('This allows you to create new forms.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-ob-duplicate"><?php _e("This allows you to duplicate one or multiple forms. Form's configuration and fields are also duplicated. Note: This does not duplicates conditional logic applied to the form.", 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-ob-delete"><?php _e('This allows you to delete one or multiple forms. All associated form data is deleted.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-ob-export"><?php _e('This allows you to export all your forms and associated data in a single XML file. Handy if you are reinstalling your site, moving forms to another site or simply backing up your hard work. Note: This does not exports conditional logic applied to the form.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-ob-import"><?php _e('Import button allows you to import the XML file saved on your computer.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-card-area"><h3> <?php _e('Forms As Cards', 'custom-registration-form-builder-with-submission-manager'); ?></h3>
            <p><?php _e('RegistrationMagic displays all forms as rectangular cards. This is a novel new approach. You will later see that a form card is much more than a symbolic representation of a form. It can show you form related data and stats at a glance.', 'custom-registration-form-builder-with-submission-manager'); ?></p>
        </li>
        <li data-id="rm-card-area"><?php _e("All form cards are displayed as grid, starting from here. You may not need to create more than one registrations form, but it's totally up to you. RegistrationMagic gives a playground to experiment and play to find the best combination for your site. First card slot is reserved for <b>Login Form</b>", 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-card-tour"><?php _e('This is a form card. We automatically created it for you to give you a head start.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-title-tour"><?php _e('This shows title of the form. When you create a new form, you can define its title. You can always change title of this form later, by going into its <b>General Settings</b>', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-checkbox-tour" data-options="tipAdjustmentX:-28;tipAdjustmentY:-5"><?php _e("The checkbox on left side of the title allows you to select multiple forms and perform batch operations. For example, deleting multiple forms. Of course there's nothing stopping you from deleting or duplicating a single form.", 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm_formcard_menu_icon" data-options="tipAdjustmentX:-25;tipAdjustmentY:-2"><?php _e('Clicking this will open a popup menu allowing you direct access to different sections of this form.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="unread-box" data-options="tipAdjustmentX:-22;tipAdjustmentY:-5"><?php _e('On top right side of each card is a red number badge. This is the count of total times this form has been filled and submitted on your site by visitors.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-last-submission"><?php _e("This area displays 3 latest submissions for this form. On new forms it will be empty in the start. Each submission will also show user's Gravatar and time stamp.", 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-shortcode-tour" data-options="tipAdjustmentX:50"><?php _e("Now this is important. RegistrationMagic works through shortcodes. That means, to display a form on the site, you must paste its shortcode inside a page, post or a widget (where you want this form to appear). Form shortcodes are always in this format - ", 'custom-registration-form-builder-with-submission-manager'); ?><b>[RM_Form id='x']</b></li>
        <li data-class="rm_def_star_tour" data-options="tipAdjustmentX:-24;tipAdjustmentY:-5"><?php _e('This little star allows you to mark a form as your default registration form.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-form-settings"><?php _e('Each form has its own dashboard or operations area, that is accessible by clicking the <b>Settings</b> button on the respective form card.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-class="rm-form-fields" data-options="tipAdjustmentX:-12"><?php _e('Any form once created is empty. Form fields need to be added manually. This is where <b>Custom Fields Manager</b> comes in. Clicking it will take you to a separate section, where you can add all sorts of fields and pages to your form.', 'custom-registration-form-builder-with-submission-manager'); ?></li>
        <li data-id="rm-tour-title" data-options="tipLocation:top;nubPosition:hide;tipAdjustmentX:200;tipAdjustmentY:230" data-button="Done"><?php printf(__('This ends our tour of Forms Manager. Feel free to explore other sections of RegistrationMagic. We would recommend visiting the form Dashboard first. If anything does not works as expected, please write to us <a href="%s"><u>here</u></a> and we will help you sort it out asap.', 'custom-registration-form-builder-with-submission-manager'),'https://registrationmagic.com/help-support'); ?></li>

    </ol>
  <!-- Joyride Magic ends -->

    <!--  Operations bar Starts  -->
    <form name="rm_form_manager" id="rm_form_manager_operartionbar" class="rm_static_forms" method="post" action="">
        
        <input type="hidden" name="rm_slug" value="" id="rm_slug_input_field">
        <div class="operationsbar" id="rmbar">
            <div class="rmtitle" id="rm-tour-title"><?php echo RM_UI_Strings::get('TITLE_FORM_MANAGER');?></div>
            <div class="icons" id="rm-ob-icons">
                <a href="?page=rm_options_manage"><img alt="" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/general-settings.png'; ?>"></a>
            </div>
            <div class="nav" id="rm-ob-nav">
                <ul>
                    <li id="rm-ob-new"><a href="#rm_add_new_form_popup" onclick="CallModalBox(this)"><?php echo RM_UI_Strings::get('LABEL_ADD_NEW');?></a></li>
                    <li id="rm-ob-duplicate" class="rm_deactivated" onclick="jQuery.rm_do_action('rm_form_manager_operartionbar','rm_form_duplicate')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get('LABEL_DUPLICATE'); ?></a></li>
                    <li id="rm-ob-delete" class="rm_deactivated" onclick="jQuery.rm_do_action_with_alert('<?php echo RM_UI_Strings::get('ALERT_DELETE_FORM'); ?>','rm_form_manager_operartionbar','rm_form_remove')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get('LABEL_REMOVE'); ?></a></li>
                    <?php $localized_str_exportall = RM_UI_Strings::get('LABEL_EXPORT')." <span class='rm-export-count'>(".RM_UI_Strings::get('LABEL_ALL').")</span>"; $localized_str_exportselected = RM_UI_Strings::get('LABEL_EXPORT'); ?>
                    <li id="rm-ob-export" data-rmlocalstrall="<?php echo $localized_str_exportall; ?>" data-rmlocalstrselected="<?php echo $localized_str_exportselected; ?>" onclick="jQuery.rm_do_action('rm_form_manager_operartionbar','rm_form_export')"><a href="javascript:void(0)"><?php echo $localized_str_exportall; ?></a></li>
                    <li id="rm-ob-import"><a href="admin.php?page=rm_form_import"><?php echo RM_UI_Strings::get('LABEL_IMPORT'); ?></a></li>
                    <li><a href="javascript:void(0)" onclick="rm_start_joyride()"><?php echo RM_UI_Strings::get('LABEL_TOUR'); ?></a></li>
                     <li id="rm-ob-demo"><a target="_blank" href="https://registrationmagic.com/create-wordpress-registration-page-starter-guide/"><?php echo RM_UI_Strings::get('LABEL_STARTER_GUIDE'); ?><span class="dashicons dashicons-book-alt"></span></a></li>
                    <!-- <li id="rm-ob-demo"><a target="_blank" href="http://demo.registrationmagic.com/"><?php //echo RM_UI_Strings::get('LABEL_DEMO'); ?></a></li> -->
                    <li class="rm-form-toggle" id="rm-ob-sort"><?php _e('Sort Forms', 'custom-registration-form-builder-with-submission-manager'); ?><select onchange="rm_sort_forms(this,'<?php echo $data->curr_page;?>')">
                            <option value=null><?php echo RM_UI_Strings::get('LABEL_SELECT'); ?></option>
                            <option value="form_name"><?php echo RM_UI_Strings::get('LABEL_NAME'); ?></option>
                            <option value="form_id"><?php echo RM_UI_Strings::get('FIELD_TYPE_DATE'); ?></option>
                            <option value="form_submissions"><?php echo RM_UI_Strings::get('LABEL_SUBMISSIONS'); ?></option>
                        </select></li>
                </ul>
            </div>
        </div>
        <input type="hidden" name="rm_selected" value="">
        <?php wp_nonce_field('rm_form_manager_template'); ?>
        <input type="hidden" name="req_source" value="form_manager">
    </form>

    <!--  *****Operations bar Ends****  -->

    <!--  ****Content area Starts****  -->

    <div class="rmagic-cards" id="rm-card-area">
        
        <!-- Quick Form card
        <div class="rmcard" id="rm-new-form">
            <?php /*
            $form = new RM_PFBC_Form("rm_form_quick_add");
            $form->configure(array(
                "prevent" => array("bootstrap", "jQuery"),
                "action" => ""
            ));
            $form->addElement(new Element_HTML('<div class="rm-new-form">'));
            $form->addElement(new Element_Hidden("rm_slug",'rm_form_quick_add'));
            $form->addElement(new Element_Textbox('', "form_name", array("id" => "rm_form_name", "required" => 1)));
            $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_CREATE_FORM'), "submit", array("id" => "rm_submit_btn", "onClick" => "jQuery.prevent_quick_add_form(event)", "class" => "rm_btn", "name" => "submit")));
            $form->addElement(new Element_HTML('</div>'));
            $form->render(); */
            ?> 
            </div> -->
            <div id="login_form" class="rmcard">

                <div class="cardtitle">
                    <input class="rm_checkbox" type="checkbox" disabled="disabled"><?php _e('Login Form', 'custom-registration-form-builder-with-submission-manager'); ?></div>                       
                <div class="rm-form-shortcode"><b>[RM_Login]</b></div>
                
                <div class="rm-form-links">
                    <div class="rm-form-row rm-formcard-dashboard">
                        <a class="rm-form-settings" href="admin.php?page=rm_login_sett_manage">
                            <?php _e('Dashboard', 'custom-registration-form-builder-with-submission-manager'); ?></a>
                    </div>   
                    <div class="rm-form-row rm-formcard-setup"><a class="rm-form-fields" href="admin.php?page=rm_login_field_manage"><?php _e('Fields', 'custom-registration-form-builder-with-submission-manager'); ?></a></div> 

                </div>

            </div>
        <?php
        $last_form_id= 0;
        if (is_array($data->data) || is_object($data->data))
            foreach ($data->data as $index=>$entry)
            {
                if(!empty($entry->expiry_details) && $entry->expiry_details->state == 'not_expired' && $entry->expiry_details->criteria != 'date')
                   $subcount_display = $entry->expiry_details->remaining_subs;// $subcount_display = $entry->count.'/'.$entry->expiry_details->sub_limit;
                else
                    $subcount_display = null;//$entry->count;
                
                //Check if form is one of the sample forms.
                $ex_form_card_class = '';
                $sample_data = get_site_option('rm_option_inserted_sample_data', null);
                if(isset($sample_data->forms) && is_array($sample_data->forms)):
                    foreach($sample_data->forms as $sample_form):
                        if($entry->form_id == $sample_form->form_id):
                            $ex_form_card_class = ($sample_form->form_type == RM_REG_FORM)? 'rm-sample-reg-form-card' : 'rm-sample-contact-form-card';                            
                        endif;
                    endforeach;
                endif;                
                    
                if($index==0){
                    $last_form_id= $entry->form_id;
                }
                
                //Check if it is a newly added form
                if($data->new_added_form == $entry->form_id || (isset($_GET['last_form_id']) && $_GET['last_form_id']<$entry->form_id))
                    $ex_form_card_class .= " rm_new_added_form";
                ?>

                <div id="<?php echo $entry->form_id; ?>" class="rmcard rm-card-tour <?php echo $ex_form_card_class; ?>">
                <?php if($entry->count > 0): ?>
                <div class='unread-box'>
                    <a href="?page=rm_submission_manage&rm_form_id=<?php echo $entry->form_id; ?>&rm_interval=<?php echo $data->submission_type; ?>"><?php echo $entry->count; ?></a>
                </div>
                <?php endif; ?>
                    <div class="cardtitle rm-title-tour">
                        <input class="rm_checkbox rm-checkbox-tour" type="checkbox" onclick="rm_on_form_selection_change()" name="rm_selected_forms[]" value="<?php echo $entry->form_id; ?>"><span class="rm_form_name rm_formcard_menu_icon" style="float: none; transform: none; margin: 0px;" data-menu-panel="#fcm_<?php echo $entry->form_id; ?>"><?php echo htmlentities(stripslashes($entry->form_name)); ?></span>
                    </div>
                    <span class="rm_formcard_menu_icon" data-menu-panel="#fcm_<?php echo $entry->form_id; ?>"><i class="material-icons">&#xE5D3;</i></span>
                    <div class="rm-last-submission">
                          <b><?php if($subcount_display)
                              printf(RM_UI_Strings::get('RM_SUB_LEFT_CAPTION'),$subcount_display);
                              ?></b></div>
                            
                    <?php
                    if ($entry->count > 0)
                    {
                        foreach ($entry->submissions as $submission)
                        {
                            ?>
                            <div class="rm-last-submission">

                                <?php
                                echo $submission->gravatar . ' ' . RM_Utilities::localize_time($submission->submitted_on);
                                ?>
                            </div>
                            <?php
                        }
                    } else
                        echo '<div class="rm-last-submission"></div>';
                    ?>
                    <?php
                    if(!empty($entry->expiry_details) && $entry->expiry_details->state == 'expired')
                        echo "<div class='rm-form-expiry-info'>".RM_UI_Strings::get('LABEL_FORM_EXPIRED')."</div>";
                    else if(!empty($entry->expiry_details) && $entry->expiry_details->state == 'not_expired' && $entry->expiry_details->criteria != 'subs')
                    {
                        if($entry->expiry_details->remaining_days < 26)
                           echo "<div class='rm-form-expiry-info'>".sprintf(RM_UI_Strings::get('LABEL_FORM_EXPIRES_IN'),$entry->expiry_details->remaining_days)."</div>";
                        else
                        {
                           $exp_date = gmdate('d M Y', strtotime($entry->expiry_details->date_limit));
                           echo "<div class='rm-form-expiry-info'>".RM_UI_Strings::get('LABEL_FORM_EXPIRES_ON')." {$exp_date}</div>";
                        }
                    }
                     
                    ?><div class="rm-form-shortcode">
                        <?php if($data->def_form_id == $entry->form_id && $entry->form_type == 1) { ?>
                    <i class="material-icons rm_def_form_star rm_def_star_tour" onclick="make_me_a_star(this)" id="rm-star_<?php echo $entry->form_id; ?>">&#xe838</i>
                        <?php } 
                        else { 
                                if($entry->form_type == 1){ ?>
                            <i class="material-icons rm_not_def_form_star rm_def_star_tour" onclick="make_me_a_star(this)" id="rm-star_<?php echo $entry->form_id; ?>">&#xe838</i>
                            
                              <?php  }
                              else { ?>
                            <i class="material-icons rm_not_def_form_star rm_def_star_tour" id="rm-star_<?php echo $entry->form_id; ?>">&#xe838</i>
                            <span class="rm-star-tip"><?php echo RM_UI_Strings::get('NOTE_DEFAULT_FORM'); ?></span>
                                <?php }
                         } ?>
                    <b class="rm-shortcode-tour">[RM_Form id='<?php echo $entry->form_id; ?>']</b></div>
                    <div class="rm-form-links">
                                <div class="rm-form-row rm-formcard-dashboard"><a class="rm-form-settings" href="admin.php?page=rm_form_sett_manage&rm_form_id=<?php echo $entry->form_id; ?>"><?php _e('Dashboard', 'custom-registration-form-builder-with-submission-manager') ?></a>
                                </div>  
                                <div class="rm-form-row rm-formcard-setup"><a class="rm-form-fields" href="admin.php?page=rm_field_manage&rm_form_id=<?php echo $entry->form_id; ?>"><?php _e('Fields', 'custom-registration-form-builder-with-submission-manager') ?></a></div> 
                     </div>
                    <?php include RM_ADMIN_DIR."views/template_rm_formcard_menu.php";?>                </div>
                <?php
            } else
            echo "<h4>" . RM_UI_Strings::get('LABEL_NO_FORMS') . "</h4>";
        ?>
    </div>
    <?php if ($data->total_pages > 1): ?>
        <ul class="rmpagination">
            <?php if ($data->curr_page > 1): ?>
                <li><a href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=<?php echo $data->curr_page - 1;
        if ($data->sort_by) echo'&rm_sortby=' . $data->sort_by;if (!$data->descending) echo'&rm_descending=' . $data->descending; ?>">«</a></li>
                <?php
            endif;
            for ($i = 1; $i <= $data->total_pages; $i++):
                if ($i != $data->curr_page):
                    ?>
                    <li><a href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=<?php echo $i;
            if ($data->sort_by) echo'&rm_sortby=' . $data->sort_by;if (!$data->descending) echo'&rm_descending=' . $data->descending; ?>"><?php echo $i; ?></a></li>
                <?php else:
                    ?>
                    <li><a class="active" href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=<?php echo $i;
            if ($data->sort_by) echo'&rm_sortby=' . $data->sort_by;if (!$data->descending) echo'&rm_descending=' . $data->descending; ?>"><?php echo $i; ?></a></li> <?php
                endif;
            endfor;
            ?>
            <?php if ($data->curr_page < $data->total_pages): ?>
                <li><a href="?page=<?php echo $data->rm_slug ?>&rm_reqpage=<?php echo $data->curr_page + 1;
        if ($data->sort_by) echo'&rm_sortby=' . $data->sort_by;if (!$data->descending) echo'&rm_descending=' . $data->descending; ?>">»</a></li>
            <?php endif;
        ?>
        </ul>
    <?php
    endif;
    /** BEGIN: Banner at Footer **/
/*if($data->should_show_fb_footer) {
?>
    <div id="fb_sub_footer" class="rm-footer-banner">
        <div class="rm-fb-like-us">
            <a onclick="save_fb_subscribe_action()">
                <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/fb-thumb-up.png'; ?>"> Like us on Facebook for upgrade notifications, important tips and feature updates.
            </a>
        </div>
    </div>
<?php
} */


/** FOOTER ENDS **/
 
?>
    <?php $new_form_pop_up_style = (isset($_GET['create_new_form'])) ? 'style="display:block"' : 'style="display:none"';?>
    <!-- Add New Form popup -->
    <div id="rm_add_new_form_popup" class="rm-modal-view" <?php echo $new_form_pop_up_style;?>>
        <div class="rm-modal-overlay rm-form-popup-overlay-fade-in"></div>

        <div class="rm_add_new_form_wrap rm-create-new-from rm-form-popup-out">
            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title">
                    <?php _e('Create New Form','custom-registration-form-builder-with-submission-manager'); ?>
                </div>
                <span  class="rm-modal-close">&times;</span>
            </div>
            <div class="rm-modal-container">
            <?php require RM_ADMIN_DIR.'views/template_rm_new_form_exerpt.php'; ?>
            </div>
        </div>
    </div>
    <!-- End: Add New Form popup -->
    
    <!-- Form Publish Pop-up -->
    
    <div id="rm_form_publish_popup" class="rm-modal-view" style="display: none;">
        <div class="rm-modal-overlay"></div>
        <div class="rm-modal-wrap rm-publish-form-popup">

            <div class="rm-modal-titlebar rm-new-form-popup-header">
                <div class="rm-modal-title">
                    Publish
                </div>
                <span class="rm-modal-close">&times;</span>
            </div>
            <div class="rm-modal-container">
                <?php $form_id_to_publish = $entry->form_id; ?>
                <?php include_once RM_ADMIN_DIR . 'views/template_rm_formflow_publish.php'; ?>
            </div>
        </div>

    </div>
    
        <!-- End Form Publish Pop-up -->
    
        <div id="rm_embed_code_dialog" style="display:none"><textarea readonly="readonly" id="rm_embed_code" onclick="jQuery(this).focus().select()"></textarea><img class="rm-close" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/close-rm.png'; ?>" onclick="jQuery('#rm_embed_code_dialog').fadeOut()"></div>
        
</div>
 
     
    <div class="rm-side-banner">
        <div class="rm-sidebanner-image">
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/rm-support-banner.png'; ?>">
        </div>
        <div class="rm-sidebanner-mg-logo">
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/mg-logo.png'; ?>">
        </div>
        <div class="sidebanner-content-wrapper">
            <div class="sidebanner-text-content">
                <div class="sidebanner-text">Something not working?</div>
                <div class="sidebanner-help-text">We want to help!</div>

                <p>If you face any issues with our plugin, or have questions, we're here to help!</p>			
            </div>
            <div class="rm-sidebanner-buttons">
		    
		 <div class="rm-sidebanner-button">
                    <a target="_blank" href="https://registrationmagic.com/create-wordpress-registration-page-starter-guide/">Starter Guide</a>			
                </div>
		    
                <div class="rm-sidebanner-button">
                    <a target="_blank" href="https://registrationmagic.com/help-support/">Create Support Ticket</a>			
                </div>

                <div class="rm-sidebanner-button">
                    <a target="_blank" href="https://metagauss.com/helpdesk">Check your Ticket Status</a>
                </div>
                 <div class="rm-sidebanner-button">
                    <a target="_blank" href="https://registrationmagic.com/translate-wordpress-plugins/">Translate</a>
                </div>	
            </div>


        </div> <!-- sidebanner-content-wrapper -->
    </div> 

  <pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
   
    jQuery(document).ready(function(){
       //Configure joyride
       //If autostart is false, call again "jQuery("#rm-form-man-joytips").joyride()" to start the tour.
       <?php if(false && $data->autostart_tour): ?>
       /*jQuery("#rm-form-man-joytips").joyride({tipLocation: 'top',
                                               autoStart: true,
                                               postRideCallback: rm_joyride_tour_taken});*/
        <?php else: ?>
            jQuery("#rm-form-man-joytips").joyride({tipLocation: 'top',
                                               autoStart: false,
                                               postRideCallback: rm_joyride_tour_taken});
        <?php endif; ?>
    });
   
   function rm_start_joyride(){
       jQuery("#rm-form-man-joytips").joyride();
    }
    
    function rm_joyride_tour_taken(){
        var data = {
			'action': 'joyride_tour_update',
			'tour_id': 'form_manager_tour',
                        'state': 'taken'
		};

        jQuery.post(ajaxurl, data, function(response) {});
    }
    
    function rm_open_dial(form_id){
        jQuery('textarea#rm_embed_code').html('<?php echo RM_UI_Strings::get('MSG_BUY_PRO_GOLD_EMBED'); ?>');
        jQuery('#rm_embed_code_dialog').fadeIn(100);
    }
    jQuery(document).mouseup(function (e) {
        var container = jQuery("#rm_embed_code_dialog,.rm_form_card_settings_dialog");
        if (!container.is(e.target) // if the target of the click isn't the container... 
                && container.has(e.target).length === 0) // ... nor a descendant of the container 
        {
            container.hide();
        }
    });
    
    function  rm_on_form_selection_change() {    
        var selected_forms = jQuery("input.rm_checkbox:checked");
        if(selected_forms.length > 0) {   
            jQuery("#rm-ob-export a").html( jQuery("#rm-ob-export").data("rmlocalstrselected") + ' <span class="rm-export-count">(' + selected_forms.length +')</span>');
            jQuery("#rm-ob-delete").removeClass("rm_deactivated");   
            jQuery("#rm-ob-duplicate").removeClass("rm_deactivated");
        } else {
            jQuery("#rm-ob-export a").html(jQuery("#rm-ob-export").data("rmlocalstrall"));
            jQuery("#rm-ob-delete").addClass("rm_deactivated");
             jQuery("#rm-ob-duplicate").addClass("rm_deactivated");
            
        }  
    }
    
    function make_me_a_star(e){
        var form_id = jQuery(e).attr('id').slice(8);
        var variable_id="#rm-star_"+form_id;
        
        if(jQuery(variable_id).hasClass( "rm_def_form_star" ))
        {
             var data = {
			'action': 'unset_default_form',
			'rm_def_form_id': form_id
		};
            jQuery.post(ajaxurl, data, function(response) {
                jQuery(variable_id).removeClass( "rm_def_form_star" );
                jQuery(variable_id).addClass( "rm_not_def_form_star" );
                
            });
            return false;
        }
      
        //toggle();
        if(typeof form_id != 'undefined' && !jQuery(e).hasClass('rm_def_form_star')){
        
        var ajaxnonce = '<?php echo wp_create_nonce('rm_formflow'); ?>';
        var data = {
			'action': 'set_default_form',
                        'rm_ajaxnonce':ajaxnonce,
			'rm_def_form_id': form_id
		};

        jQuery.post(ajaxurl, data, function(response) {
                        var old_form = jQuery('.rm_def_form_star');
			old_form.removeClass('rm_def_form_star');
                        old_form.addClass('rm_not_def_form_star');
                        
                        var curr_form = jQuery('#rm-star_'+form_id);
                        curr_form.removeClass('rm_not_def_form_star');
                        curr_form.addClass('rm_def_form_star');
		});
            }
    }
    
    function rm_show_form_sett_dialog(form_id){
        jQuery("#rm_settings_dailog_"+form_id).show();
    }
      
jQuery("#rm_rateit_banner").bind('rated', function (event, value) { 
        if(value<=3)
        {
            
             jQuery("#rm-rate-popup-wrap").fadeOut();  
             jQuery("#wordpress_review").fadeOut(100);  
             jQuery("#feedback_message").fadeIn(100);  
             jQuery('#feedback_message').removeClass('rm-blur');
             jQuery('#feedback_message').addClass('rm-hop');
             handle_review_banner_click('rating',value);
        }
        else
        {
             jQuery("#rm-rate-popup-wrap").fadeOut();  
             jQuery("#feedback_message").fadeOut();  
             jQuery("#wordpress_review").fadeIn(100);
             jQuery('#wordpress_review').removeClass('rm-blur');
             jQuery('#wordpress_review').addClass('rm-hop');
             handle_review_banner_click('rating',value);
        }
    
    
    });
    
    function save_fb_subscribe_action()
    {
            window.open("https://www.facebook.com/registrationmagic", '_blank');
        jQuery.ajax({
            url:ajaxurl,
            type:'post',
            data:{action:'rm_fb_subscribe_action'},
            success:function(data)
            {               
               jQuery('#fb_sub_footer').hide();
            }
        });
    }
    
        function CallModalBox(ele) {
          jQuery(jQuery(ele).attr('href')).toggle().find("input[type='text']").focus();
          if(jQuery(ele).attr('href')=='#rm_add_new_form_popup'){
            jQuery('.rmagic .rm_add_new_form_wrap.rm-create-new-from').removeClass('rm-form-popup-out');
            jQuery('.rmagic .rm_add_new_form_wrap.rm-create-new-from').addClass('rm-form-popup-in');
            
            jQuery('.rm-modal-overlay').removeClass('rm-form-popup-overlay-fade-out');
            jQuery('.rm-modal-overlay').addClass('rm-form-popup-overlay-fade-in');
          }
      }
    
      jQuery(document).ready(function () {
          jQuery('.rm-modal-close, .rm-modal-overlay').click(function () {
              setTimeout(function(){
                  //jQuery(this).parents('.rm-modal-view').hide();
                  jQuery('.rm-modal-view').hide();
              }, 400);
              
          });
          

            jQuery('.rmagic .rm-create-new-from .rm-new-form-popup-header .rm-modal-close, #rm_add_new_form_popup .rm-modal-overlay').on('click', function(){
            jQuery('.rmagic .rm_add_new_form_wrap.rm-create-new-from').removeClass('rm-form-popup-in');
            jQuery('.rmagic .rm_add_new_form_wrap.rm-create-new-from').addClass('rm-form-popup-out');
            
            jQuery('.rm-modal-overlay').removeClass('rm-form-popup-overlay-fade-in');
            jQuery('.rm-modal-overlay').addClass('rm-form-popup-overlay-fade-out');
          });
          
      });
    
    function recursive_import(form_id) {
        var id = form_id;
        var ajaxnonce = '<?php echo wp_create_nonce('rm_import_first'); ?>';
        var data = {
            'action': 'import_first',
            'rm_ajaxnonce': ajaxnonce,
            'form_id': id
        };
        jQuery.post(ajaxurl, data, function (response) {
            if (response == 0)
            {
               _getEl("progressBar").value = Math.round(100);
                _getEl("status").innerHTML = '<?php _e('Import Successfully Completed', 'custom-registration-form-builder-with-submission-manager'); ?>';
                setTimeout(function(){
                     new_url= "<?php echo admin_url('admin.php?'); ?>" + update_current_url_with_param("last_form_id","<?php echo $last_form_id; ?>");
                     window.location= new_url;
                },3000)
            } else {

                //jQuery("#rm_import_progress").append("(Imported)</br></br>Importing RM Form--" + response + "");

                recursive_import(response);
            }
        });
    }
    
    function start_import(){
        jQuery("#rm_import_errors").html();
         var ajaxnonce = '<?php echo wp_create_nonce('rm_import_first'); ?>';
        var data = {
            'action': 'import_first',
            'rm_ajaxnonce': ajaxnonce
        };
        jQuery.post(ajaxurl, data, function (response) {
            if (response == 0)
            {
                _getEl("progressBar").value = Math.round(100);
                _getEl("status").innerHTML = '<?php _e('Import Successfully Completed', 'custom-registration-form-builder-with-submission-manager') ?>';
                setTimeout(function(){
                     new_url= "<?php echo admin_url('admin.php?'); ?>" + update_current_url_with_param("last_form_id","<?php echo $last_form_id; ?>");
                     window.location= new_url;
                },3000)
              
            } else if (response === "INVALID_FILE") {
                jQuery("#rm_import_errors").html('');
                jQuery("#rm_import_errors").append("<div class='rm_import_error'><?php _e('Invalid RegistrationMagic template file. Please upload valid template file with XML extension.', 'custom-registration-form-builder-with-submission-manager') ?></div>");
                jQuery("#progressBar,#status").hide();
            } else {
                var pre = parseInt(response) - 1;
                recursive_import(response);
            }

        });
    }
    
    /* Upload Handler */
    function _getEl(el) {
     return document.getElementById(el);
    }
    
    function check_file_extension(obj){
        var file = obj.files[0];
        if(file && file.type!="text/xml"){
            jQuery("#rm_import_errors").html("<div class='rm_import_error'><?php _e('Invalid RegistrationMagic template file. Please upload valid template file with XML extension.', 'custom-registration-form-builder-with-submission-manager'); ?>");
            obj.value='';
        }
    }
    var rm_file_ajax=null;    
    function uploadFile() {
      var file = _getEl("xml_file").files[0];
      if(!file){
           jQuery("#rm_import_errors").html("<div class='rm_import_error'><?php _e('Please select  a file.', 'custom-registration-form-builder-with-submission-manager'); ?></div>");
           return;
      }
      jQuery("#rm_import_errors").html('');
      var formdata = new FormData();
      var ajaxnonce = '<?php echo wp_create_nonce('rm_admin_upload_template'); ?>';
      formdata.append("action", "rm_admin_upload_template");
      formdata.append("file", file);
      formdata.append("rm_ajaxnonce", ajaxnonce);
      rm_file_ajax = new XMLHttpRequest();
      rm_file_ajax.upload.addEventListener("progress", progressHandler, false);
      rm_file_ajax.addEventListener("load", completeHandler, false);
      rm_file_ajax.addEventListener("error", errorHandler, false);
      rm_file_ajax.addEventListener("abort", abortHandler, false);
      rm_file_ajax.open("POST", "<?php echo admin_url('admin-ajax.php'); ?>");
      jQuery("#progressBar,#status").show();
      rm_file_ajax.send(formdata);
    }

    function progressHandler(event) {
      var percent = (event.loaded / event.total) * 50;
      _getEl("progressBar").value = Math.round(percent);
      _getEl("status").innerHTML = "<?php _e('File upload is in progress...', 'custom-registration-form-builder-with-submission-manager') ?>";
    }

    function completeHandler(event) {
       var percent = 50;
      _getEl("progressBar").value = Math.round(percent);
      _getEl("status").innerHTML = "<?php _e('Form Import is in progress....', 'custom-registration-form-builder-with-submission-manager') ?>";
      start_import();
    }

    function errorHandler(event) {
      _getEl("status").innerHTML = "<?php _e('Upload Failed', 'custom-registration-form-builder-with-submission-manager') ?>";
    }

    function abortHandler(event) {
      _getEl("status").innerHTML = "<?php _e('Upload Aborted', 'custom-registration-form-builder-with-submission-manager') ?>";
    }     
    
    function cancel_file_upload(){
       // rm_file_ajax.abort();
        location.reload();
    }
  </script></pre>





<?php } ?>
