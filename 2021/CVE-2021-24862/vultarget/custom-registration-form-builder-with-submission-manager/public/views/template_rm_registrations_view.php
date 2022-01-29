<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'views/template_rm_registrations_view.php'); else {
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
                                        activeTabIndex: <?php echo $data->active_tab_index; ?>
                                    });
    });

    function get_tab_and_redirect(reqpagestr) {
        var tab_index = g_rm_customtab.getActiveTabIndex();
        var curr_url = "<?php echo get_permalink(); ?>";
        var sign = '&';
        if (curr_url.indexOf('?') === -1) {
            sign = '?';
        }
        window.location.href = curr_url + sign + reqpagestr + '&rm_tab=' + tab_index;
    }
</script></pre>

    <div class="rm-user-row dbfl" id="rm_my_sub_tab">
        <div class="rm-user-row rm-icon dbfl">
            <i class="material-icons rm-bg" data-rm_apply_acc_bgcolor='true' >assignment_turned_in</i>
        </div>
        <div class="rm-user-row dbfl">
            <h2><?php echo RM_UI_Strings::get('LABEL_MY_SUBS'); ?></h2>
        </div>
        <?php
        if ($data->submission_exists === true)
        {   $sub_page_id = get_option('rm_option_front_sub_page_id', null);
            ?>
            <table class="rm-user-data">
                <tr>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_SR'); ?></th>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_FORM'); ?></th>
                    <th class="rm-bg-lt rm-submission-unique-id"><?php _e('Unique ID','custom-registration-form-builder-with-submission-manager'); ?></th>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_DATE'); ?></th>
                    <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_DOWNLOAD'); ?></th>
                </tr>
                <?php
                $i = 0;
                if ($data->submissions):
                    foreach ($data->submissions as $data_single):
                        $submission= new RM_Submissions();
                        $submission->load_from_db($data_single->submission_id);
                        $token= $submission->get_unique_token();
                        $url = add_query_arg( 'submission_id',$data_single->submission_id,get_permalink($sub_page_id));
                        //$url = get_permalink($sub_page_id).
                        ?>  
                        <tr>
                            <td id="<?php echo $data_single->submission_id; ?>"><?php echo ++$i; ?></td>
                            <td><a href="<?php echo esc_url($url); ?>"><?php echo $data_single->form_name; ?></a></td>
                            <td class="rm-submission-unique-id"><?php echo !empty($token) ? $token : ''; ?></td>
                            <td><?php echo RM_Utilities::localize_time($data_single->submitted_on, $data->date_format); ?></td>
                            <td><i class="material-icons" onclick="document.getElementById('rmsubmissionfrontform<?php echo $data_single->submission_id; ?>').submit()">cloud_download</i></td>
                        <form id="rmsubmissionfrontform<?php echo $data_single->submission_id; ?>" method="post">
                            <input type="hidden" value="<?php echo $data_single->submission_id; ?>" name="rm_submission_id">
                            <input type="hidden" value="rm_submission_print_pdf" name="rm_slug">
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
                            <li onclick="get_tab_and_redirect('rm_reqpage_sub=<?php echo $i; ?>')"><a class="active"><?php echo $i; ?></a></li>
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
        ?>
    </div>
     

  <?php } ?>