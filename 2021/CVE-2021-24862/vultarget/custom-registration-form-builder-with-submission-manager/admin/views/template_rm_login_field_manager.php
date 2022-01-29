<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_login_field_manager.php'); else {
add_thickbox();
$buttons = $data['buttons']; ?>

<link rel="stylesheet" type="text/css" href="<?php echo RM_BASE_URL . 'admin/css/'; ?>style_rm_form_dashboard.css">
<link rel="stylesheet" type="text/css" href="<?php echo RM_BASE_URL . 'admin/css/'; ?>style_rm_formflow.css">
<?php if(defined('REGMAGIC_ADDON')) { ?>
<link rel="stylesheet" type="text/css" href="<?php echo RM_ADDON_BASE_URL . 'admin/css/'; ?>style_rm_form_dashboard.css">
<link rel="stylesheet" type="text/css" href="<?php echo RM_ADDON_BASE_URL . 'admin/css/'; ?>style_rm_formflow.css">
<?php } ?>

<?php wp_enqueue_script('rm-formflow'); ?>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">



<div class="rm-formflow-top-bar">



    <!-- Step 1 -->

    <div class="rm-formflow-top-section" style="text-align: left">

        <div class="rm-formflow-top-action" >

            <span class="rm-formflow-top-left"><a href="<?php echo admin_url('admin.php?page=rm_form_manage'); ?>"><i class="material-icons">keyboard_arrow_left</i> <?php _e('All Forms', 'custom-registration-form-builder-with-submission-manager'); ?></a></span>

        </div>

    </div>

    <!-- Step 1 -->



    <!-- Step 2 -->

    <div class="rm-formflow-top-section" style="text-align: center">

        <div class="rm-formflow-top-action  rm-formflow-top-action-center" >

            <span>&nbsp;</span>

        </div>

    </div>

    <!-- Step 2 -->



    <!-- Step 3 -->

    <div class="rm-formflow-top-section" style="text-align: right">

        <div class="rm-formflow-top-action rm-formflow-top-action-right" >



            <span class="rm-formflow-top-right"><a href="<?php echo admin_url('admin.php?page=rm_login_sett_manage'); ?>"><?php _e('Form Dashboard', 'custom-registration-form-builder-with-submission-manager'); ?> <i class="material-icons">keyboard_arrow_right</i></a></span>

        </div>

    </div>

</div>





<div class="rmagic">

    <!-----Operationsbar Starts----->

    <form method="post" id="rm_field_manager_form">

        <input type="hidden" name="rm_slug" value="" id="rm_slug_input_field">

        <div class="operationsbar">

            <div class="rmtitle"><?php echo _e('Fields Manager', 'custom-registration-form-builder-with-submission-manager'); ?></div>

            <div class="icons">                

            </div>

            <div class="nav">

                <ul>

<?php /* ?><li><a href="#rm-widget-selector" onclick="CallModalBox(this)"><?php echo _e('Add Widget', 'custom-registration-form-builder-with-submission-manager'); ?></a></li><?php */ ?>

                    <li title=""><a href="?page=rm_login_field_view_sett&rdrto=rm_login_field_manage"><?php echo _e('Design', 'custom-registration-form-builder-with-submission-manager'); ?></a></li>

                    <li><a class="thickbox rm_form_preview_btn" id="rm_form_preview_action" href="<?php echo site_url() ?>/rm_login/?form_prev=1&form_type=login&amp;TB_iframe=true&amp;width=1216&amp;height=416.1"><?php echo _e('Preview', 'custom-registration-form-builder-with-submission-manager'); ?></a></li>

                    <!--    Forms toggle-->

                    <li id="rm_form_toggle" class="rm-form-toggle">

<?php echo RM_UI_Strings::get('LABEL_TOGGLE_FORM'); ?>

                        <select onchange="rm_load_page(this, 'field_manage')">

                            <?php
                            echo "<option selected value='rm_login_form'>" . __('Login Form', 'custom-registration-form-builder-with-submission-manager') . "</option>";

                            foreach ($data['all_forms'] as $form_id => $form_name):

                                echo "<option value='$form_id'>$form_name</option>";

                            endforeach;
                            ?>

                        </select>

                    </li>



                </ul>

            </div>







        </div>

        <!--------Operationsbar Ends----->



        <!----Field Selector Starts---->







        <div class="rm-field-creator rm-login-field-creator">

            <div id="rm_form_page_tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">



                <div class="field-selector-pills">

                    <div class="rm-field-manager-sorting-tip">

                        <div class="rm-slab-drag-handle">

                            <span class="">

                                <img alt="" src="<?php echo RM_IMG_URL ?>rm-drag.png">

                            </span>

                        </div>

                        <div class="rm-field-manager-sorting"><?php echo RM_UI_Strings::get('SORT_FIELD_ORDER_DISC'); ?></div>

                    </div>

                    <div id="rm_form_page_1" aria-labelledby="rm_form_page_tab_link_1" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-hidden="false">

                        <div class="rm-custom-fields-page">  
                            <div class="rmrow">&nbsp;</div>

                            <ul class="rm-field-container rm_sortable_login_fields ui-sortable">



                                <?php foreach ($data['form_fields'] as $index => $field): ?>    

                                    <li id="<?php echo $index ?>">

                                        <div class="rm-custom-field-page-slab">

                                            <div class="rm-slab-drag-handle">

                                                <span class="rm_sortable_handle ui-sortable-handle">

                                                    <img alt="" src="<?php echo RM_IMG_URL ?>rm-drag.png">

                                                </span>

                                            </div>

                                            <div class="rm-slab-info">

                                                <input type="checkbox" name="rm_selected[]" onclick="rm_on_field_selection()" value="797" <?php echo (in_array(strtolower($field['field_type']), array('username', 'password'))) ? 'disabled=""' : ''; ?> >

                                                <span class="rm-field-slab-label"><?php echo $field['field_label']; ?></span><span class="rm-field-type"><?php echo strtoupper($field['field_type']); ?></span>

                                            </div>

                                            <div class="rm-slab-buttons">

                                                <a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=' . $field['field_type'] . '&field_index=' . $index); ?>"><?php echo _e('Edit', 'custom-registration-form-builder-with-submission-manager'); ?></a>

                                                <?php if (strtolower($field['field_type']) == 'username') : ?>

                                                    <a href="javascript:void(0)" class="rm_deactivated"><?php echo _e('Delete', 'custom-registration-form-builder-with-submission-manager'); ?></a>

                                                <?php elseif (strtolower($field['field_type']) == 'password') : ?>
                                                    <a href="javascript:void(0)" class="rm_deactivated"><?php echo _e('Delete', 'custom-registration-form-builder-with-submission-manager'); ?></a>

                                                <?php else : ?>

                                                    <a href="<?php echo '?page=rm_login_field_manage&field_index=' . $index . '&rm_action=delete"'; ?>"><?php echo _e('Delete', 'custom-registration-form-builder-with-submission-manager'); ?></a>

                                                <?php endif; ?>    

                                            </div>

                                        </div>

                                    </li>

                                <?php endforeach; ?>

                            </ul>





                        </div>



                    </div><div id="ui-id-2" class="ui-tabs-panel ui-widget-content ui-corner-bottom" aria-live="polite" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="true" style="display: none;"></div>

                    <?php
                    $login_label = empty($buttons['login_btn']) ? __('Login', 'custom-registration-form-builder-with-submission-manager') : $buttons['login_btn'];

                    $btn_align = $buttons['align'];

                    $register_label = empty($buttons['register_btn']) ? __('Register', 'custom-registration-form-builder-with-submission-manager') : $buttons['register_btn'];
                    ?>

                    <!-- Begin: Submit Field -->

                    <div class="rm-field-submit-field-holder">

                        <div class="rm-field-submit-field">

                            <div class="rm-field-submit-field-btn-container rm-field-btn-align-<?php echo $btn_align; ?>">

                                &#8203;

                                <div class="rm-field-next-btn rm_field_btn" id="rm_field_login_button" title="<?php _e('Click to edit button label', 'custom-registration-form-builder-with-submission-manager'); ?>" contenteditable="true" spellcheck="false"><?php echo htmlentities(stripslashes($login_label)); ?></div>

                                &#8203;

                                <div class="rm-field-sub-btn rm_field_btn" id="rm_field_register_button" title="<?php _e('Click to edit button label', 'custom-registration-form-builder-with-submission-manager') ?>" contenteditable="true" spellcheck="false"><?php echo htmlentities(stripslashes($register_label)); ?></div>

                                &#8203;

                            </div>

                            <div class="rm-field-submit-field-options">

                                <div class="rm-field-submit-field-option-row rm-field-submit-hide-prev">

                                    &nbsp;

                                </div>

                                <div class="rm-field-submit-field-option-row rm-field-submit-alignment">

                                    <input type="radio" name="rm_field_submit_field_align" value="left" id="rm_field_submit_field_align_left" <?php echo $btn_align == 'left' ? 'checked' : '' ?>><label for="rm_field_submit_field_align_left"><?php echo _e('Left', 'custom-registration-form-builder-with-submission-manager'); ?></label>

                                    <input type="radio" name="rm_field_submit_field_align" value="center" id="rm_field_submit_field_align_center" <?php echo $btn_align == 'center' ? 'checked' : '' ?>><label for="rm_field_submit_field_align_center"><?php echo _e('Center', 'custom-registration-form-builder-with-submission-manager'); ?></label>

                                    <input type="radio" name="rm_field_submit_field_align" value="right" id="rm_field_submit_field_align_right"  <?php echo $btn_align == 'right' ? 'checked' : '' ?>><label for="rm_field_submit_field_align_right"><?php echo _e('Right', 'custom-registration-form-builder-with-submission-manager'); ?></label>

                                </div>

                                <div class="rm-field-submit-field-option-row rm-field-submit-ajax-loader" style="visibility: hidden">

<?php echo _e('Updating...', 'custom-registration-form-builder-with-submission-manager'); ?>

                                </div>

                            </div>

                        </div>

                        <div><input type="checkbox" id="rm_display_register" <?php echo $buttons['display_register'] == 1 ? 'checked' : ''; ?>/><?php echo _e('Display "Register" Button', 'custom-registration-form-builder-with-submission-manager'); ?></div>

                        <div class="rm-field-submit-field-hint"><?php echo RM_UI_Strings::get('EDIT_BUTTON_LABEL_DISC'); ?></div>

                    </div>

                    <!-- End: Submit Field -->

                </div>

            </div>

        </div>

        <!----Slab View---->

    </form>





    <!-- Widgets Pop up -->

    <div id="rm-widget-selector" class="rm-modal-view" style="display:none">

        <div class="rm-modal-overlay"></div> 



        <div class="rm-modal-wrap">

            <div class="rm-modal-titlebar">

                <div class="rm-modal-title"><?php echo _e('MagicWidgets', 'custom-registration-form-builder-with-submission-manager'); ?></div>

                <span class="rm-modal-close">×</span>

            </div>

            <div class="rm-modal-container">

                <div class="rmrow">

                    <div class="rm-widget-selector">

                        <ul class="rm-widget-selector-view">

                            <li title="<?php _e("Large size read only text useful for creating custom headings.", 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links">



                                <div class="rm-difl rm-widget-icon rm-widget-heading"><i class="fa fa-header" aria-hidden="true"></i></div>

                                <div class="rm-difl rm-widget-head"><a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=HTMLH'); ?>"><?php echo _e('Heading', 'custom-registration-form-builder-with-submission-manager'); ?></a>



                                </div>

                            </li>

                            <li title="<?php _e("This is a read only field which can be used to display formatted content inside the form. HTML is supported.", 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-paragraph"><i class="fa fa-paragraph" aria-hidden="true"></i></div> 

                                <div class="rm-difl rm-widget-head"><a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=HTMLP'); ?>"><?php echo _e('Paragraph', 'custom-registration-form-builder-with-submission-manager'); ?></a>

                                </div>

                            </li>

                            <li title="<?php _e("Divider for separating fields.", 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-divider"><i class="fa fa-arrows-h" aria-hidden="true"></i></div> 

                                <div class="rm-difl rm-widget-head"> <a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=Divider'); ?>"><?php echo _e('Divider', 'custom-registration-form-builder-with-submission-manager'); ?></a>



                                </div>

                            </li>



                            <li title="<?php _e("Useful for adding space between fields", 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-spacing"><i class="material-icons"></i></div> 

                                <div class="rm-difl rm-widget-head"> <a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=Spacing'); ?>"><?php echo _e('Spacing', 'custom-registration-form-builder-with-submission-manager'); ?></a>



                                </div>

                            </li>  

                            <li title="<?php _e("Allows you to display richly formatted text inside your form.", 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-richtext"><i class="material-icons"></i></div> 

                                <div class="rm-difl rm-widget-head"><a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=RichText'); ?>"><?php echo _e('Rich Text', 'custom-registration-form-builder-with-submission-manager'); ?></a>





                                </div>

                            </li>  



                            <li title="<?php _e("Allows you to display richly formatted text inside your form.", 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-richtext"><i class="material-icons"></i></div> 

                                <div class="rm-difl rm-widget-head"><a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=Timer'); ?>"><?php echo _e('Timer', 'custom-registration-form-builder-with-submission-manager'); ?></a>

                                </div>

                            </li> 



                            <li class="rm_button_like_links" title="<?php _e("Display link inside your form.", 'custom-registration-form-builder-with-submission-manager'); ?>">



                                <div class="rm-difl rm-widget-icon rm-widget-add-link">

                                    <i class="material-icons"></i>

                                </div>

                                <div class="rm-difl rm-widget-head"><a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=Link'); ?>"><?php echo _e('Link', 'custom-registration-form-builder-with-submission-manager'); ?></a>



                                </div>

                            </li>



                            <li class="rm_button_like_links" title="<?php _e("Insert a YouTube Video in your form.", 'custom-registration-form-builder-with-submission-manager'); ?>">

                                <div class="rm-difl rm-widget-icon rm-widget-youtube"><i class="fa fa-youtube-play" aria-hidden="true"></i></div> 

                                <div class="rm-difl rm-widget-head"><a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=YouTubeV'); ?>"><?php echo _e('YouTuve Video', 'custom-registration-form-builder-with-submission-manager'); ?></a>

                                </div>

                            </li> 



                            <li class="rm_button_like_links" title="<?php _e("Display an external webpage inside your form, using HTML iframe.", 'custom-registration-form-builder-with-submission-manager'); ?>">

                                <div class="rm-difl rm-widget-icon rm-widget-iframe-embed"><i class="material-icons"></i></div> 

                                <div class="rm-difl rm-widget-head"><a href="<?php echo admin_url('admin.php?page=rm_login_field_add&field_type=Iframe'); ?>"><?php echo _e('iFrame Embed', 'custom-registration-form-builder-with-submission-manager'); ?></a>





                                </div>

                            </li> 



                        </ul>



                        <!-- Upcoming Widget -->



                        <div class="dbfl rm-upcoming-widget-head"><?php echo _e('Upcoming MagicWidgets', 'custom-registration-form-builder-with-submission-manager'); ?></div>





                        <ul class="rm-widget-selector-view rm-upcoming-widget">



                            <li title="" class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-add-image"><i class="material-icons"></i></div> 

                                <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"> <?php echo _e('Image', 'custom-registration-form-builder-with-submission-manager'); ?></a>

                                </div>

                            </li>

                            <li class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-form-data"><i class="material-icons"></i></div> 

                                <div class="rm-difl rm-widget-head"> <a href="javascript:void(0)"><?php echo _e('Form Meta-Data', 'custom-registration-form-builder-with-submission-manager'); ?></a>



                                </div>

                            </li>



                            <li class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-form-date-chart"><i class="material-icons"></i></div> 

                                <div class="rm-difl rm-widget-head"> <a href="javascript:void(0)"><?php echo _e('Form Data Chart', 'custom-registration-form-builder-with-submission-manager'); ?></a>



                                </div>

                            </li>  

                            <li class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-map"><i class="material-icons"></i></div> 

                                <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php echo _e('Map', 'custom-registration-form-builder-with-submission-manager'); ?></a>





                                </div>

                            </li>  









                            <li class="rm_button_like_links">

                                <div class="rm-difl rm-widget-icon rm-widget-registration-feed"><i class="material-icons"></i></div> 

                                <div class="rm-difl rm-widget-head"><a href="javascript:void(0)"><?php echo _e('Registration Feed', 'custom-registration-form-builder-with-submission-manager'); ?></a>





                                </div>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Widget pop up ends -->



</div>
<div class="rm-formflow-top-bar">

    <!-- Step 1 -->
    <div class="rm-formflow-top-section" style="text-align: left">
        <div class="rm-formflow-top-action" >
            <span class="rm-formflow-top-left"><a href="<?php echo admin_url('admin.php?page=rm_form_manage'); ?>"><i class="material-icons">keyboard_arrow_left</i> <?php _e('All Forms', 'custom-registration-form-builder-with-submission-manager'); ?></a></span>
        </div>
    </div>
    <!-- Step 1 -->

    <!-- Step 2 -->
    <div class="rm-formflow-top-section" style="text-align: center">
        <div class="rm-formflow-top-action  rm-formflow-top-action-center" >

         <span >&nbsp;</span>
        </div>
    </div>
    <!-- Step 2 -->

    <!-- Step 3 -->
    <div class="rm-formflow-top-section" style="text-align: right">
        <div class="rm-formflow-top-action rm-formflow-top-action-right" >

            <span class="rm-formflow-top-right"><a href="<?php echo admin_url('admin.php?page=rm_login_sett_manage'); ?>"><?php _e('Form Dashboard', 'custom-registration-form-builder-with-submission-manager'); ?> <i class="material-icons">keyboard_arrow_right</i></a></span>
        </div>
    </div>
</div>


<script>
    jQuery(document).ready(function () {
        jQuery("#rm_display_register").change(function () {
            if (jQuery(this).is(':checked')) {
                jQuery("#rm_field_register_button").show();
            } else {
                jQuery("#rm_field_register_button").hide();
            }
        });
        jQuery("#rm_display_register").trigger('change');
    });

    function CallModalBox(ele) {

        jQuery(jQuery(ele).attr('href')).toggle();

    }

    jQuery(document).ready(function () {

        jQuery('.rm-modal-close, .rm-modal-overlay').click(function () {

            jQuery(this).parents('.rm-modal-view').hide();

        });

        rm_init_submit_field();

        jQuery('.rm-premium-option').on('click', function (e) {
            jQuery('.rm-premium-option-popup').toggle();
        });
    });



    function add_new_widget_to_page(widget_type) {

        var loc = "?page=rm_field_add_widget&rm_form_id=177&rm_form_page_no=" + curr_form_page + "&rm_field_type";

        if (widget_type !== undefined)
            loc += ('=' + widget_type);

        window.location = loc;

    }



    function rm_init_submit_field() {

        jQuery(".rm_field_btn").on("keydown", function (e) {

            if (e.keyCode === 13 || e.keyCode === 27) {

                jQuery(this).blur();

                window.getSelection().removeAllRanges();

            }

        })



        var last_label;



        jQuery(".rm_field_btn").on("focus", function (e) {

            var temp = jQuery(this).text().trim();

            if (temp.length)
                last_label = temp;

        })



        jQuery(".rm_field_btn").on("blur", function (e) {

            var temp = jQuery(this).text().trim();

            if (temp.length <= 0)
                jQuery(this).text(last_label);

            else
                rm_update_submit_field();

        })



        jQuery("input[name='rm_field_submit_field_align']").change(function (e) {

            var $btn_container = jQuery(".rm-field-submit-field-btn-container");

            $btn_container.removeClass("rm-field-btn-align-left rm-field-btn-align-center rm-field-btn-align-right");

            $btn_container.addClass("rm-field-btn-align-" + jQuery(this).val());

            rm_update_submit_field();

        })



    }



    jQuery("#rm_display_register").change(function () {

        rm_update_submit_field();

    });



    function rm_update_submit_field() {

        var data = {

            'register_btn_label': jQuery("#rm_field_register_button").text().trim(),

            'login_btn_label': jQuery("#rm_field_login_button").text().trim(),

            'btn_align': jQuery("[name='rm_field_submit_field_align']:checked").val(),

            'display_register': jQuery("#rm_display_register").is(':checked') ? 1 : 0

        };



        var data = {

            'action': 'rm_update_login_button',

            'data': data,

        };

        jQuery(".rm-field-submit-ajax-loader").css("visibility", "visible");

        jQuery.post(ajaxurl, data, function (response) {

            jQuery(".rm-field-submit-ajax-loader").css("visibility", "hidden");

        });

    }
</script>

<?php } ?>