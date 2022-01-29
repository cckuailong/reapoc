<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'views/template_rm_user_payments_view.php'); else {
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
        var tab_index = g_rm_customtab.getActiveTabIndex();
        var curr_url = "<?php echo get_permalink(); ?>";
        var sign = '&';
        if (curr_url.indexOf('?') === -1) {
            sign = '?';
        }
        window.location.href = curr_url + sign + reqpagestr + '&rm_tab=' + tab_index;
    }
</script></pre>
<?php 

    ?>
    <div class="rmagic-table" id="rm_my_pay_tab">
        <div class="rm-user-row rm-icon dbfl">
            <i class="material-icons rm-bg" data-rm_apply_acc_bgcolor="true">credit_card</i>
        </div>
        <div class="rm-user-row dbfl">
            <h2><?php echo RM_UI_Strings::get('LABEL_PAY_HISTORY'); ?></h2>
        </div>
        <?php 
                if(empty($data->payments)){
                    _e('You have not made any payment transactions yet.','custom-registration-form-builder-with-submission-manager');
                }
        ?>
        <?php if ($data->payments): ?>
        <table class="rm-user-data">
            <tr>
                <th  class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_DATE'); ?></th>
                <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_FORM'); ?></th>
                <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_AMOUNT'); ?></th>
                <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_TXN_ID'); ?></th>
                <th class="rm-bg-lt"><?php echo RM_UI_Strings::get('LABEL_STATUS'); ?></th>
            </tr>
    <?php
    for ($i = $data->offset_pay; $i < $data->end_offset_this_page; $i++):
        ?>
                <tr>
                    <td><?php echo RM_Utilities::localize_time($data->payments[$i]->posted_date, $data->date_format); ?></td>
                    <td><a href="<?php echo esc_url(add_query_arg('submission_id', $data->payments[$i]->submission_id)); ?>"><?php echo $data->form_names[$data->payments[$i]->submission_id]; ?></a></td>
                    <td><?php echo $data->payments[$i]->total_amount; ?></td>
                    <td><?php echo $data->payments[$i]->invoice; ?></td>
                    <td><?php echo $data->payments[$i]->status; ?></td>
                </tr>
        <?php
    endfor;
    ?>
        </table>

    <?php
    /*     * ********** Pagination Logic ************** */
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
        <?php endif; ?>
    </div>   

       
<?php } ?>