<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'views/template_rm_front_submission_data.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<!-- setup initial tab -->
<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
    var g_rm_customtab, g_rm_acc_color;
    jQuery(document).ready(function () {
        
        //get accent color from theme
        g_rm_acc_color = jQuery('#rm_dummy_link_for_primary_color_extraction').css('color');
        if(typeof g_rm_acc_color == 'undefined')
            g_rm_acc_color = '#000';
        
        var rmagic_jq = jQuery(".rmagic");
        rmagic_jq.find("[data-rm_apply_acc_color='true']").css('color',g_rm_acc_color);
        rmagic_jq.find("[data-rm_apply_acc_bgcolor='true']").css('background-color',g_rm_acc_color);
        
        g_rm_customtab = new RMCustomTabs({
                                        container: '#rm_front_sub_tabs',
                                        animation: 'fade',
                                        accentColor: g_rm_acc_color,
                                        activeTabIndex: 1
                                    });
    });
</script></pre>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<a id='rm_dummy_link_for_primary_color_extraction' style='display:none' href='#'></a>

<div class="rmagic" id="rm_front_sub_tabs" style="display:none;"> 

    <!-----Operationsbar Starts-->

    <div class="operationsbar">
        <!--        <div class="rmtitle">Submissions</div>-->
        <div class="nav">

                <div class="rm_back_button rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_BACK'); ?>" data-rmt-tabcontent="__rmt_noop" onclick="window.history.back()"><i class="material-icons">keyboard_arrow_left</i> <?php echo RM_UI_Strings::get('LABEL_BACK'); ?></div>
                <div class="rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_MY_SUB'); ?>" data-rmt-tabcontent="#rm_first_tab"><i class="material-icons">assignment_turned_in</i> <?php echo RM_UI_Strings::get('LABEL_MY_SUB'); ?></div>
                <?php
                if ($data->payment) {
                    ?>

                    <div class="rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_PAYMENT_DETAILS'); ?>" data-rmt-tabcontent="#rm_second_tab"><i class="material-icons">credit_card</i> <?php echo RM_UI_Strings::get('LABEL_PAYMENT_DETAILS'); ?></div>
                    <?php
                }
                if (!is_user_logged_in()) {
                    ?>
                    <div class="rm-form-toggle rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_LOG_OFF'); ?>" data-rmt-tabcontent="__rmt_noop" onclick="document.getElementById('rm_front_submissions_nav_form').submit()"><i class="material-icons">vpn_key</i> <?php echo RM_UI_Strings::get('LABEL_LOG_OFF'); ?></div>
                    <?php
                }else
                    {
                    ?>
                    <div class="rm-form-toggle rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_RESET_PASS'); ?>" data-rmt-tabcontent="__rmt_noop" onclick="document.getElementById('rm_front_submissions_respas_form').submit()"><i class="material-icons">vpn_key</i> <?php echo RM_UI_Strings::get('LABEL_RESET_PASS'); ?></div>
                    <?php
                    }
                ?>

            <form method="post" id="rm_front_submissions_nav_form">
                <input type="hidden" name="rm_slug" value="rm_front_log_off">
            </form>
            <form method="post" id="rm_front_submissions_respas_form">
                <input type="hidden" name="rm_slug" value="rm_front_reset_pass_page">
                <input type="hidden" name="RM_CLEAR_ERROR" value="true">
            </form>

        </div>


    </div>    
    <div class="rm-submission" id="rm_first_tab">     

<?php
if ($data->form_is_unique_token) {
    ?>
            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_UNIQUE_TOKEN_SHORT'); ?> :</div>
                <div class="rm-submission-value"><?php echo $data->submission->get_unique_token(); ?></div>
            </div>
    <?php
}
?>

        <div class="rm-submission-field-row">
            <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_ENTRY_ID'); ?></div>
            <div class="rm-submission-value"><?php echo $data->submission->get_submission_id(); ?></div>
        </div>

        <div class="rm-submission-field-row">
            <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_ENTRY_TYPE'); ?></div>
            <div class="rm-submission-value"><?php echo $data->form_type; ?></div>
        </div>
<?php
if ($data->form_type_status == "1" && !empty($data->user)) {
    $user_roles_dd = RM_Utilities::user_role_dropdown();
    ?>
            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_USER_NAME'); ?></div>
                <div class="rm-submission-value"><?php echo $data->user->display_name; ?></div>
            </div>

            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_USER_ROLES'); ?></div>
                <div class="rm-submission-value">
                    <?php
                    if(isset($data->user->roles[0],$user_roles_dd[$data->user->roles[0]]))
                        echo $user_roles_dd[$data->user->roles[0]];
                    else
                        echo "<em>".RM_UI_Strings::get('MSG_USER_ROLE_NOT_ASSIGNED')."<em>";
                    ?>
                </div>
            </div>

    <?php
}
?>
        <?php
        $submission_data = $data->submission->get_data();
        if (is_array($submission_data) || $submission_data)
            foreach ($submission_data as $field_id => $sub):

                $sub_key = $sub->label;
                $sub_data = $sub->value;
                if(!isset($sub->type)){
                                $sub->type = '';
                            }
                ?>

                <!--submission row block-->


                <div class="rm-submission-field-row">
                    <div class="rm-submission-label"><?php echo $sub_key; ?></div>
                    <div class="rm-submission-value">
        <?php
        //if submitted data is array print it in more than one row.

        if (is_array($sub_data)) {

            $i = 0;

            //If submitted data is a file.

            if (isset($sub_data['rm_field_type']) && $sub_data['rm_field_type'] == 'File') {
                unset($sub_data['rm_field_type']);

                foreach ($sub_data as $sub) {

                    $att_path = get_attached_file($sub);
                    $att_url = wp_get_attachment_url($sub);
                    ?>
                                    <div class="rm-submission-attachment">
                                    <?php echo wp_get_attachment_link($sub, 'thumbnail', false, true, false); ?>
                                        <div class="rm-submission-attachment-field"><?php echo basename($att_path); ?></div>
                                        <div class="rm-submission-attachment-field"><a href="<?php echo $att_url; ?>"><?php echo RM_UI_Strings::get('LABEL_DOWNLOAD'); ?></a></div>
                                    </div>

                                    <?php
                                }
                            } elseif (isset($sub_data['rm_field_type']) && $sub_data['rm_field_type'] == 'Address') {
                                $sub = $sub_data['original'] . '<br/>';
                                if (count($sub_data) === 8) {
                                    $sub .= '<b>'.__('Street Address','custom-registration-form-builder-with-submission-manager').'</b> : ' . $sub_data['st_number'] . ', ' . $sub_data['st_route'] . '<br/>';
                                    $sub .= '<b>'.__('City','custom-registration-form-builder-with-submission-manager').'</b> : ' . $sub_data['city'] . '<br/>';
                                    $sub .= '<b>'.__('State','custom-registration-form-builder-with-submission-manager').'</b> : ' . $sub_data['state'] . '<br/>';
                                    $sub .= '<b>'.__('Zip Code','custom-registration-form-builder-with-submission-manager').'</b> : ' . $sub_data['zip'] . '<br/>';
                                    $sub .= '<b>'.__('Country','custom-registration-form-builder-with-submission-manager').'</b> : ' . $sub_data['country'];
                                }
                                echo $sub;
                            }  elseif ($sub->type == 'Time') {
                                echo $sub_data['time'] . ", Timezone: " . $sub_data['timezone'];
                            }  elseif ($sub->type == 'Checkbox') {   
                                echo implode(', ',RM_Utilities::get_lable_for_option($field_id, $sub_data));
                            } else {
                                $sub = implode(', ', $sub_data);
                                echo $sub;
                            }
                        } else {
                            if ($sub->type == 'Rating') {
                                if(defined('REGMAGIC_ADDON'))
                                    echo RM_Utilities::enqueue_external_scripts('script_rm_rating', RM_ADDON_BASE_URL . 'public/js/rating3/jquery.rateit.js');
                                echo '<div class="rateit" id="rateit5" data-rateit-min="0" data-rateit-max="5" data-rateit-value="' . $sub_data . '" data-rateit-ispreset="true" data-rateit-readonly="true"></div>';
                            } elseif ($sub->type == 'Radio' || $sub->type == 'Select') {   
                                echo RM_Utilities::get_lable_for_option($field_id, $sub_data);                                
                            } else {
                                echo $sub_data;
                            }                        }
                        ?>
                    </div>
                </div><!-- End of one submission block-->
                        <?php
                    endforeach;
                if($data->is_editable == true){
                ?>
                <form id="rmeditsubmission" method="post" action="">
                    <input type="hidden" name="rm_slug" value="rm_user_form_edit_sub">
                    <input type="hidden" name="form_id" value="<?php echo $data->submission->get_form_id(); ?>">
                </form>
                <div id="rm_edit_sub_link">
                    <a href="javascript:void(0)" onclick="document.getElementById('rmeditsubmission').submit();"><?php echo RM_UI_Strings::get('MSG_EDIT_SUBMISSION'); ?></a>
                </div> 
                <?php } ?>
    </div>
                <?php
                if ($data->payment) {
                    ?>
        <div class="rm-submission" id="rm_second_tab"> 
    <?php
    if ($data->payment->log):
        ?>
                <div class="rm-submission-field-row">
                    <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_PAYER_NAME'); ?></div>
                    <div class="rm-submission-value"><?php
            if (isset($data->payment->log['first_name']))
                echo $data->payment->log['first_name'];
            if (isset($data->payment->log['last_name']))
                echo ' ' . $data->payment->log['last_name'];
            ?></div>
                </div>
                <div class="rm-submission-field-row">
                    <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_PAYER_EMAIL'); ?></div>
                    <div class="rm-submission-value"><?php if (isset($data->payment->log['payer_email'])) echo $data->payment->log['payer_email']; ?></div>
                </div>
                        <?php
                    endif;
                    ?>

            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_INVOICE'); ?></div>
                <div class="rm-submission-value"><?php if (isset($data->payment->invoice)) echo $data->payment->invoice; ?></div>
            </div>
            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_TAXATION_ID'); ?></div>
                <div class="rm-submission-value"><?php if (isset($data->payment->txn_id)) echo $data->payment->txn_id; ?></div>
            </div>
            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_STATUS_PAYMENT'); ?></div>
                <div class="rm-submission-value"><?php if (isset($data->payment->status)) echo $data->payment->status; ?></div>
            </div>
            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_PAID_AMOUNT'); ?></div>
                <div class="rm-submission-value"><?php if (isset($data->payment->total_amount)) echo $data->payment->total_amount; ?></div>
            </div>
            <div class="rm-submission-field-row">
                <div class="rm-submission-label"><?php echo RM_UI_Strings::get('LABEL_DATE_OF_PAYMENT'); ?></div>
                <div class="rm-submission-value"><?php if (isset($data->payment->posted_date)) echo RM_Utilities::localize_time($data->payment->posted_date, get_option('date_format')); ?></div>
            </div>
        </div>
    <?php
}
?>


<?php
if ($data->notes && (is_object($data->notes) || is_array($data->notes))) {
    ?>
        <div class="rmsubtitle"><?php echo RM_UI_Strings::get('LABEL_ADMIN_NOTES'); ?></div>
    <?php
    foreach ($data->notes as $note) {
        ?>
            <div class="rm-submission-note" style="border-left: 4px solid #<?php echo maybe_unserialize($note->note_options)->bg_color; ?>">
                <div class="rm-submission-note-text"><?php echo $note->notes; ?></div>
                <div class="rm-submission-note-attribute">

            <?php
            echo RM_UI_Strings::get('LABEL_CREATED_BY') . " <b>" . $note->author . "</b> <em>" . RM_Utilities::localize_time($note->publication_date) . "</em>";
            if ($note->editor)
                echo " (" . RM_UI_Strings::get('LABEL_EDITED_BY') . " <b>" . $note->editor . "</b> <em>" . RM_Utilities::localize_time($note->last_edit_date) . "</em>";
            ?>
                </div>
            </div>
                    <?php
                }
                ?>
        <?php
    }
    ?>
</div>

<?php } ?>