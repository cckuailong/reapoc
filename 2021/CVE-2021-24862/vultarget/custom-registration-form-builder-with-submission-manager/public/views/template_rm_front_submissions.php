<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'views/template_rm_front_submissions.php'); else {
//echo "<pre>", var_dump($data);
/**
 * Plugin Template File[For Front End Submission Page]
 */
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- setup initial tab -->
<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
    var g_rm_customtab, g_rm_acc_color;
    jQuery(document).ready(function () {

        //get accent color from theme
        g_rm_acc_color = jQuery('#rm_dummy_link_for_primary_color_extraction').css('color');
        if (typeof g_rm_acc_color == 'undefined')
            g_rm_acc_color = '#000';

        var rmagic_jq = jQuery(".rmagic");
        rmagic_jq.find("[data-rm_apply_acc_color='true']").css('color', g_rm_acc_color);
        rmagic_jq.find("[data-rm_apply_acc_bgcolor='true']").css('background-color', g_rm_acc_color);
        g_rm_customtab = new RMCustomTabs({
            container: '#rm_front_sub_tabs',
            animation: 'fade',
            accentColor: g_rm_acc_color,
            activeTabIndex: <?php echo $data->active_tab_index; ?>
        });
    });

    function get_tab_and_redirect(reqpagestr) {
        //alert(reqpage);
        var tab_index = g_rm_customtab.getActiveTabIndex();//jQuery("#rm_tabbing_container_front_sub").tabs("option", "active");
        var curr_url = window.location.href;
        var sign = '&';
        if (curr_url.indexOf('?') === -1) {
            sign = '?';
        }
        window.location.href = curr_url + sign + reqpagestr + '&rm_tab=' + tab_index;
    }
</script></pre>
<a id='rm_dummy_link_for_primary_color_extraction' style='display:none' href='#'></a>
<?php
if (!$data->payments && !$data->submissions && $data->is_user !== true) {
    ?>

    <div class="rmnotice-container"><div class="rmnotice"><?php echo RM_UI_Strings::get('MSG_NO_DATA_FOR_EMAIL'); ?></div></div>
    <?php
}
?>
<div class="rmagic" id="rm_front_sub_tabs" style="display: none;"> 

    <!-----Operationsbar Starts-->

    <div class="operationsbar">
        <!--        <div class="rmtitle">Submissions</div>-->
        <div class="nav">

            <?php
            if ($data->is_user === true) {
                ?>
                <div class="rmtab-my-details rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_MY_DETAILS'); ?>" data-rmt-tabcontent="#rm_my_details_tab">
                    <i class="material-icons">account_box</i>
                    <?php echo RM_UI_Strings::get('LABEL_MY_DETAILS'); ?></div>
                <?php
            }
            ?>
            <div class="rmtab-registration rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_MY_SUBS'); ?>" data-rmt-tabcontent="#rm_my_sub_tab">
                <i class="material-icons">assignment_turned_in</i>
                <?php echo RM_UI_Strings::get('LABEL_MY_SUBS'); ?></div>

            <div class="rmtab-payment-details rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_PAY_HISTORY'); ?>" data-rmt-tabcontent="#rm_my_pay_tab">
                <i class="material-icons">credit_card</i>
                <?php echo RM_UI_Strings::get('LABEL_PAY_HISTORY'); ?>
            </div>
            <?php
            // Let the extensions add any menu before action buttons.
            $extended_tab_listings = apply_filters('rm_after_front_tabtitle_listing', array());
            foreach ($extended_tab_listings as $tab) {
                ?>
                <div class="rmtabs_head" title="<?php echo $tab['label'] ?>" data-rmt-tabcontent="#<?php echo $tab['id'] ?>"><?php echo $tab['icon'] . ' ' . $tab['label'] ?></a></div>
                <?php
            }
            if (!is_user_logged_in()) {
                ?>
                <div class="rmtab-log-off rm-form-toggle rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_LOG_OFF'); ?>" data-rmt-tabcontent="__rmt_noop" onclick="document.getElementById('rm_front_submissions_nav_form').submit()"><i class="material-icons">vpn_key</i><?php echo RM_UI_Strings::get('LABEL_LOG_OFF'); ?></div>
                <?php
            } else {
                ?>
                <div class="rmtab-reset-pass rm-form-toggle rmtabs_head" title="<?php echo RM_UI_Strings::get('LABEL_RESET_PASS'); ?>" data-rmt-tabcontent="__rmt_noop" onclick="document.getElementById('rm_front_submissions_respas_form').submit()"><i class="material-icons">vpn_key</i> <?php echo RM_UI_Strings::get('LABEL_RESET_PASS'); ?></div>
                <div class="rmtab-logout rmtabs_head" title="Logout"><i class="material-icons">exit_to_app</i><a href="<?php echo wp_logout_url(get_permalink()); ?>"><?php _e('Logout', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
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
    <!--------Operationsbar Ends----->

    <!-------Contentarea Starts----->

    <!----Table Wrapper---->

    <?php
    if ($data->is_user) {
        $editable_forms = array();
        ?>
        <div class="rm-submission" id="rm_my_details_tab">
            <div class="rm-user-details-card rm-wide-card">
                <div class="rm-user-image-container">
                    <div class="rm-user-row dbfl">
                        <div class="rm-user-card">
                            <?php 
                                $av = get_avatar_data($data->user->ID); 
                                $profile_image_url = apply_filters('rm_profile_image',$av['url'],$data->user->ID);
                            ?>
                            <img alt="" src="<?php echo $profile_image_url; ?>" class="rm-user" height="512" width="512">
                            <div class="rm-user-name-submission">
                                <div class="rm-user-name dbfl">
                                    <span data-rm_apply_acc_color='true'><?php echo RM_UI_Strings::get('LABEL_WELCOME'); ?>, </span> <?php echo $data->user->display_name; ?>
                                </div>
                                <div class="rm-user-name-subtitle dbfl">
                                    <span data-rm_apply_acc_color='true'><?php echo $data->total_submission_count; ?> </span> <?php echo RM_UI_Strings::get('LABEL_REGISTRATIONS'); ?>.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <hr>
                <div class="rm-user-row dbfl">
                    <?php
                    if ($data->user->first_name) {
                        ?>
                        <div class="rm-field-row dbfl">
                            <div class="rm-user-field-label"><?php echo RM_UI_Strings::get('FIELD_TYPE_FNAME'); ?>:</div>
                            <div class="rm-user-field-value"><?php echo $data->user->first_name; ?></div>
                        </div>
                        <?php
                    }
                    if ($data->user->last_name) {
                        ?>

                        <div class="rm-field-row dbfl">
                            <div class="rm-user-field-label"><?php echo RM_UI_Strings::get('FIELD_TYPE_LNAME'); ?>:</div>
                            <div class="rm-user-field-value"><?php echo $data->user->last_name; ?></div>
                        </div>
                        <?php
                    }
                    if ($data->user->description) {
                        ?>

                        <div class="rm-field-row dbfl">
                            <div class="rm-user-field-label"><?php echo RM_UI_Strings::get('LABEL_BIO'); ?>:</div>
                            <div class="rm-user-field-value"><?php echo $data->user->description; ?></div>
                        </div>
                        <?php
                    }
                    if ($data->user->user_email) {
                        ?>

                        <div class="rm-field-row dbfl">
                            <div class="rm-user-field-label"><?php echo RM_UI_Strings::get('LABEL_EMAIL'); ?>:</div>
                            <div class="rm-user-field-value"><?php echo $data->user->user_email; ?></div>
                        </div>
                        <?php
                    }
                    if ($data->user->sec_email) {
                        ?>

                        <div class="rm-field-row dbfl">
                            <div class="rm-user-field-label"><?php echo RM_UI_Strings::get('LABEL_SECEMAIL'); ?>:</div>
                            <div class="rm-user-field-value"><?php echo $data->user->sec_email; ?></div>
                        </div>
                        <?php
                    }
                    if ($data->user->nickname) {
                        ?>

                        <div class="rm-field-row dbfl">
                            <div class="rm-user-field-label"><?php echo RM_UI_Strings::get('FIELD_TYPE_NICKNAME'); ?>:</div>
                            <div class="rm-user-field-value"><?php echo $data->user->nickname; ?></div>
                        </div>
                        <?php
                    }
                    if ($data->user->user_url) {
                        ?>

                        <div class="rm-field-row dbfl">
                            <div class="rm-user-field-label"><?php echo RM_UI_Strings::get('FIELD_TYPE_WEBSITE'); ?>:</div>
                            <div class="rm-user-field-value"><?php echo $data->user->user_url; ?></div>
                        </div>
                        <?php
                    }
                    if (is_array($data->custom_fields) || is_object($data->custom_fields))
                        foreach ($data->custom_fields as $field_id => $sub) {
                            $key = $sub->label;
                            $meta = $sub->value;
                            if (!isset($sub->type)) {
                                $sub->type = '';
                            }

                            $sub_original = $sub;

                            $meta = RM_Utilities::strip_slash_array(maybe_unserialize($meta));
                            ?>
                            <div class="rm-field-row dbfl">

                                <div class="rm-user-field-label"><?php echo $key; ?></div>
                                <div class="rm-user-field-value">
                                    <?php
                                    if (is_array($meta) || is_object($meta)) {
                                        if (isset($meta['rm_field_type']) && $meta['rm_field_type'] == 'File') {
                                            unset($meta['rm_field_type']);

                                            foreach ($meta as $sub) {

                                                $att_path = get_attached_file($sub);
                                                $att_url = wp_get_attachment_url($sub);
                                                ?>
                                                <div class="rm-user-attachment">
                                                    <?php echo wp_get_attachment_link($sub, 'thumbnail', false, true, false); ?>
                                                    <div class="rm-user-attachment-field"><?php echo basename($att_path); ?></div>
                                                    <div class="rm-user-attachment-field"><a href="<?php echo $att_url; ?>"><?php echo RM_UI_Strings::get('LABEL_DOWNLOAD'); ?></a></div>
                                                </div>

                                                <?php
                                            }
                                        } elseif ($sub->type == 'Time') {
                                            echo $meta['time'] . ", Timezone: " . $meta['timezone'];
                                        } elseif ($sub->type == 'Checkbox') {
                                            echo implode(', ', RM_Utilities::get_lable_for_option($field_id, $meta));
                                        } else {
                                            $sub = implode(', ', $meta);
                                            echo $sub;
                                        }
                                    } else {
                                        if ($sub->type == 'Rating') {
                                            if(defined('REGMAGIC_ADDON'))
                                                echo RM_Utilities::enqueue_external_scripts('script_rm_rating', RM_ADDON_BASE_URL . 'public/js/rating3/jquery.rateit.js');
                                            echo '<div class="rateit" id="rateit5" data-rateit-min="0" data-rateit-max="5" data-rateit-value="' . $meta . '" data-rateit-ispreset="true" data-rateit-readonly="true"></div>';
                                        } elseif ($sub->type == 'Radio' || $sub->type == 'Select') {
                                            echo RM_Utilities::get_lable_for_option($field_id, $meta);
                                        } else
                                            echo $meta;
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            //check if any field is editable
                            if ($sub_original->is_editable == 1 && !in_array($sub_original->form_id, $editable_forms)) {
                                $editable_forms[] = $sub_original->form_id;
                            }
                        }
                    ?>
                </div>
            </div>
            <?php if (!empty($editable_forms)) { ?>
                <div id="rm_edit_sub_link">
                    <form method="post" name="rm_form" action="" id="rmeditsubmissions">
                        <input type="hidden" name="rm_edit_user_details" value="true">
                        <input type="hidden" name="form_ids" value='<?php echo json_encode($editable_forms); ?>'>
                    </form>
                    <a href="javascript:void(0)" onclick="document.getElementById('rmeditsubmissions').submit();"><?php echo RM_UI_Strings::get('MSG_EDIT_YOUR_SUBMISSIONS'); ?></a>
                </div> 
            <?php } ?>
        </div>
        <?php
    }
    ?>

    <div class="rm-user-row dbfl" id="rm_my_sub_tab">
        <div class="rm-user-row rm-icon dbfl">
            <i class="material-icons rm-bg" data-rm_apply_acc_bgcolor='true' >assignment_turned_in</i>
        </div>
        <div class="rm-user-row dbfl">
            <h2><?php echo RM_UI_Strings::get('LABEL_MY_SUBS'); ?></h2>
        </div>
        <?php
        if ($data->submission_exists === true) {
            ?>
            <table class="rm-user-data">
                <tr>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_SR'); ?></th>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_FORM'); ?></th>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_DATE'); ?></th>

                </tr>
                <?php
                $i = 0;
                if ($data->submissions):
                    foreach ($data->submissions as $data_single):
                        $submission = new RM_Submissions();
                        $submission->load_from_db($data_single->submission_id);
                        $token = $submission->get_unique_token();
                        $url = add_query_arg('submission_id', $data_single->submission_id);
                        ?>  
                        <tr>
                            <td id="<?php echo $data_single->submission_id; ?>"><?php echo ++$i; ?></td>
                            <td><a href="<?php echo esc_url($url);  ?>"><?php echo $data_single->form_name; ?></a></td>
                            <td><?php echo RM_Utilities::localize_time($data_single->submitted_on, $data->date_format); ?></td>

                        <form id="rmsubmissionfrontform<?php echo $data_single->submission_id; ?>" method="post">
                            <input type="hidden" value="<?php echo $data_single->submission_id; ?>" name="rm_submission_id">

                        </form>    
                        </tr>
                        <?php
                    endforeach;
                else:

                endif;
                ?>
            </table>
            <?php
            /*             * ********** Pagination Logic ************** */
            $max_pages_without_abb = 10;
            $max_visible_pages_near_current_page = 3; //This many pages will be shown on both sides of current page number.

            if ($data->total_pages_sub > 1):
                ?>
                <ul class="rmpagination">
                    <?php
                    if ($data->curr_page_sub > 1):
                        ?>
                        <li onclick="get_tab_and_redirect('rm_reqpage_sub=1')"><a><?php echo RM_UI_Strings::get('LABEL_FIRST'); ?></a></li>
                        <li onclick="get_tab_and_redirect('rm_reqpage_sub=<?php echo $data->curr_page_sub - 1; ?>')"><a><?php echo RM_UI_Strings::get('LABEL_PREVIOUS'); ?></a></li>
                        <?php
                    endif;
                    if ($data->total_pages_sub > $max_pages_without_abb):
                        if ($data->curr_page_sub > $max_visible_pages_near_current_page + 1):
                            ?>
                            <li><a> ... </a></li>
                            <?php
                            $first_visible_page = $data->curr_page_sub - $max_visible_pages_near_current_page;
                        else:
                            $first_visible_page = 1;
                        endif;

                        if ($data->curr_page_sub < $data->total_pages_sub - $max_visible_pages_near_current_page):
                            $last_visible_page = $data->curr_page_sub + $max_visible_pages_near_current_page;
                        else:
                            $last_visible_page = $data->total_pages_sub;
                        endif;
                    else:
                        $first_visible_page = 1;
                        $last_visible_page = $data->total_pages_sub;
                    endif;
                    for ($i = $first_visible_page; $i <= $last_visible_page; $i++):
                        if ($i != $data->curr_page_sub):
                            ?>
                            <li onclick="get_tab_and_redirect('rm_reqpage_sub=<?php echo $i; ?>')"><a><?php echo $i; ?></a></li>
                        <?php else:
                            ?>
                            <li onclick="get_tab_and_redirect('rm_reqpage_sub=<?php echo $i; ?>')"><a class="active"?><?php echo $i; ?></a></li>
                        <?php
                        endif;
                    endfor;
                    if ($data->total_pages_sub > $max_pages_without_abb):
                        if ($data->curr_page_sub < $data->total_pages_sub - $max_visible_pages_near_current_page):
                            ?>
                            <li><a> ... </a></li>
                            <?php
                        endif;
                    endif;
                    ?>
                    <?php
                    if ($data->curr_page_sub < $data->total_pages_sub):
                        ?>
                        <li onclick="get_tab_and_redirect('rm_reqpage_sub=<?php echo $data->curr_page_sub + 1; ?>')"><a><?php echo RM_UI_Strings::get('LABEL_NEXT'); ?></a></li>
                        <li onclick="get_tab_and_redirect('rm_reqpage_sub=<?php echo $data->total_pages_sub; ?>')"><a><?php echo RM_UI_Strings::get('LABEL_LAST'); ?></a></li>
                        <?php
                    endif;
                    ?>
                </ul>
                <?php
            endif;
        } else
            echo RM_UI_Strings::get('MSG_NO_SUBMISSION_FRONT');
        do_action('rm_extend_front_registrations_view');
        
        ?>
    </div>
    
    <div class="rmagic-table" id="rm_my_pay_tab">

        <div class="rm-user-row rm-icon dbfl">
            <i class="material-icons rm-bg" data-rm_apply_acc_bgcolor="true">credit_card</i>
        </div>
        <div class="rm-user-row dbfl">
            <h2><?php echo RM_UI_Strings::get('LABEL_PAY_HISTORY'); ?></h2>
        </div>
        <?php
        if (empty($data->payments)) {
            _e('You have not made any payment transactions yet.', 'custom-registration-form-builder-with-submission-manager');
        }
        ?>
        <?php if ($data->payments): ?>
            <table class="rm-user-data">
                <tr>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_DATE'); ?></th>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_FORM'); ?></th>
                    <th class="rm-bg-lt"><?php _e('Unique ID', 'custom-registration-form-builder-with-submission-manager'); ?></th>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_AMOUNT'); ?></th>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_INVOICE_SHORT'); ?></th>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_STATUS'); ?></th>
                </tr>
                <?php
                for ($i = $data->offset_pay; $i < $data->end_offset_this_page; $i++):
                    $submission = new RM_Submissions();
                    $submission->load_from_db($data->payments[$i]->submission_id);
                    $token = $submission->get_unique_token();
                    $url = add_query_arg('submission_id', $data->payments[$i]->submission_id);
                    ?>
                    <tr>
                        <td><?php echo RM_Utilities::localize_time($data->payments[$i]->posted_date, $data->date_format); ?></td>
                        <td><a href="<?php echo esc_url($url); ?>"><?php echo $data->form_names[$data->payments[$i]->submission_id]; ?></a></td>
                        <td><?php echo!empty($token) ? $token : ''; ?></td>
                        <td><?php echo $data->payments[$i]->total_amount; ?></td>
                        <td><?php echo $data->payments[$i]->invoice; ?></td>
                        <td><?php echo $data->payments[$i]->status; ?></td>
                    </tr>
                    <?php
                endfor;
                ?>
            </table>

            <?php
            /*             * ********** Pagination Logic ************** */
            $max_pages_without_abb = 10;
            $max_visible_pages_near_current_page = 3; //This many pages will be shown on both sides of current page number.

            if ($data->total_pages_pay > 1):
                ?>
                <ul class="rmpagination">
                    <?php
                    if ($data->curr_page_pay > 1):
                        ?>
                        <li onclick="get_tab_and_redirect('rm_reqpage_pay=1')"><a><?php echo RM_UI_Strings::get('LABEL_FIRST'); ?></a></li>
                        <li onclick="get_tab_and_redirect('rm_reqpage_pay=<?php echo $data->curr_page_pay - 1; ?>')"><a><?php echo RM_UI_Strings::get('LABEL_PREVIOUS'); ?></a></li>
                        <?php
                    endif;
                    if ($data->total_pages_pay > $max_pages_without_abb):
                        if ($data->curr_page_pay > $max_visible_pages_near_current_page + 1):
                            ?>
                            <li><a> ... </a></li>
                            <?php
                            $first_visible_page = $data->curr_page_pay - $max_visible_pages_near_current_page;
                        else:
                            $first_visible_page = 1;
                        endif;

                        if ($data->curr_page_pay < $data->total_pages_pay - $max_visible_pages_near_current_page):
                            $last_visible_page = $data->curr_page_pay + $max_visible_pages_near_current_page;
                        else:
                            $last_visible_page = $data->total_pages_pay;
                        endif;
                    else:
                        $first_visible_page = 1;
                        $last_visible_page = $data->total_pages_pay;
                    endif;
                    for ($i = $first_visible_page; $i <= $last_visible_page; $i++):
                        if ($i != $data->curr_page_pay):
                            ?>
                            <li onclick="get_tab_and_redirect('rm_reqpage_pay=<?php echo $i; ?>')"><a><?php echo $i; ?></a></li>
                        <?php else:
                            ?>
                            <li onclick="get_tab_and_redirect('rm_reqpage_pay=<?php echo $i; ?>')"><a class="active"><?php echo $i; ?></a></li>
                            <?php
                            endif;
                        endfor;
                        if ($data->total_pages_pay > $max_pages_without_abb):
                            if ($data->curr_page_pay < $data->total_pages_pay - $max_visible_pages_near_current_page):
                                ?>
                            <li><a> ... </a></li>
                            <?php
                        endif;
                    endif;
                    ?>
                    <?php
                    if ($data->curr_page_pay < $data->total_pages_pay):
                        ?>
                        <li onclick="get_tab_and_redirect('rm_reqpage_pay=<?php echo $data->curr_page_pay + 1; ?>')"><a><?php echo RM_UI_Strings::get('LABEL_NEXT'); ?></a></li>
                        <li onclick="get_tab_and_redirect('rm_reqpage_pay=<?php echo $data->total_pages_pay; ?>')"><a><?php echo RM_UI_Strings::get('LABEL_LAST'); ?></a></li>
                        <?php
                    endif;
                    ?>
                </ul>
            <?php endif; ?>
            <!-- Pagination Ends    -->
        <?php endif; ?>
    </div>   

    <?php // Let the extensions add any menu before action buttons.
                if(isset($data->user,$data->user->ID))
                        echo apply_filters('rm_after_front_tabcontent_listing', '',$data->user->ID);?>
</div>

<?php } ?>