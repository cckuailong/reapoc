<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_user_manager.php'); else {
?>
<!-----Operationsbar Starts----->

<div class="rmagic">

    <!-----Operations bar Starts----->

    <div class="operationsbar">
        <div class="rmtitle"><?php echo RM_UI_Strings::get("TITLE_USER_MANAGER"); ?></div>
        <div class="icons">
            <a href="admin.php?page=rm_options_user"><img alt="" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/rm-user-accounts.png'; ?>"></a>
        </div>
        <div class="nav">
            <ul>
                <li><a href="user-new.php"><?php echo RM_UI_Strings::get("NEW_USER"); ?></a></li>
                <li  id="rm-activation" class="rm_deactivated" onclick="jQuery.rm_do_action('rm_user_manager_form', 'rm_user_activate')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get('ACTIVATE'); ?></a></li>
                <li  id="rm-deactivation" class="rm_deactivated"  onclick="jQuery.rm_do_action('rm_user_manager_form', 'rm_user_deactivate')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get('DEACTIVATE'); ?></a></li>
                <li  id="rm-delete" class="rm_deactivated"  onclick="jQuery.rm_do_action('rm_user_manager_form', 'rm_user_delete')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get('LABEL_DELETE'); ?></a></li>
            </ul>
        </div>

    </div>
    <!--------Operationsbar Ends----->

    <!-------Contentarea Starts----->
    <div class="rmagic-table">

        <!----Sidebar---->

        <div class="sidebar">

            <form id="rm_user_manager_sideform"  action="<?php echo esc_url(add_query_arg('rm_reqpage', '1')); ?>" method="GET">

                <div class="sb-filter"><?php _e('Search', 'custom-registration-form-builder-with-submission-manager'); ?>
                    <input type="text" class="sb-search" name="rm_to_search" value="<?php echo $data->filter->filters['rm_to_search']; ?>">
                </div>

                <div class="sb-filter">
                    <?php echo RM_UI_Strings::get("LABEL_TIME"); ?>
                    <div class="filter-row"><input type="radio" onclick="document.getElementById('rm_user_manager_sideform').submit()" name="rm_interval" value="all"   <?php if ($data->filter->filters['rm_interval'] == "all") echo "checked"; ?>><?php echo RM_UI_Strings::get("LABEL_ALL"); ?> </div>
                    <div class="filter-row"><input type="radio" onclick="document.getElementById('rm_user_manager_sideform').submit()" name="rm_interval" value="today" <?php if ($data->filter->filters['rm_interval'] == "today") echo "checked"; ?>><?php echo RM_UI_Strings::get("LABEL_TODAY"); ?> </div>
                    <div class="filter-row"><input type="radio" onclick="document.getElementById('rm_user_manager_sideform').submit()" name="rm_interval" value="week"  <?php if ($data->filter->filters['rm_interval'] == "week") echo "checked"; ?>><?php echo RM_UI_Strings::get("LABEL_THIS_WEEK"); ?></div>
                    <div class="filter-row"><input type="radio" onclick="document.getElementById('rm_user_manager_sideform').submit()" name="rm_interval" value="month" <?php if ($data->filter->filters['rm_interval'] == "month") echo "checked"; ?>><?php echo RM_UI_Strings::get("LABEL_THIS_MONTH"); ?></div>
                    <div class="filter-row"><input type="radio" onclick="document.getElementById('rm_user_manager_sideform').submit()" name="rm_interval" value="year"  <?php if ($data->filter->filters['rm_interval'] == "year") echo "checked"; ?>><?php echo RM_UI_Strings::get("LABEL_THIS_YEAR"); ?></div>

                </div>

                <div class="sb-filter">
                    <?php echo RM_UI_Strings::get("LABEL_STATUS"); ?>
                    <div class="filter-row"><input type="radio" onclick="document.getElementById('rm_user_manager_sideform').submit()" name="rm_status" value="all"     <?php if ($data->filter->filters['rm_status'] == "all") echo "checked"; ?>><?php echo RM_UI_Strings::get("LABEL_ALL"); ?></div>
                    <div class="filter-row"><input type="radio" onclick="document.getElementById('rm_user_manager_sideform').submit()" name="rm_status" value="active"  <?php if ($data->filter->filters['rm_status'] == "active") echo "checked"; ?>><?php echo RM_UI_Strings::get("LABEL_ACTIVE"); ?></div>
                    <div class="filter-row"><input type="radio" onclick="document.getElementById('rm_user_manager_sideform').submit()" name="rm_status" value="pending" <?php if ($data->filter->filters['rm_status'] == "pending") echo "checked"; ?>><?php echo RM_UI_Strings::get("LABEL_PENDING"); ?></div>
                </div>


                <div class="filter-row"><a href="?page=rm_user_manage"><input type="button" name="Reset" value="<?php _e('Reset', 'custom-registration-form-builder-with-submission-manager') ?>"></a><input type="submit" name="Search" value="<?php _e('Search', 'custom-registration-form-builder-with-submission-manager') ?>"></div>
                <input type="hidden" name="page" value="rm_user_manage" />
            </form>

        </div>

        <form method="POST" name="rm_user_manage" id="rm_user_manager_form">
            <?php wp_nonce_field('rm_user_manage'); ?>
            <input type="hidden" name="rm_slug" value="" id="rm_slug_input_field">
            <table>
                <tr>
                    <th>&nbsp;</th>
                    <th><?php echo RM_UI_Strings::get("IMAGE"); ?></th>
                    <th><?php echo RM_UI_Strings::get("LABEL_NAME"); ?></th>
                    <th><?php echo RM_UI_Strings::get("LABEL_EMAIL"); ?></th>
                    <th><?php echo RM_UI_Strings::get("LABEL_STATUS"); ?></th>
                    <th><?php echo RM_UI_Strings::get("ACTION"); ?></th>
                </tr>
                <!--********************************-->

                <?php
                if (is_array($data->users) || is_object($data->users))
                    foreach ($data->users as $user):
                        ?>
                        <tr>
                            <td><input class="rm_checkbox_group rm_user_cb" type="checkbox" onclick="rm_on_users_selection()" data-email="<?php echo $user->user_email; ?>" <?php echo get_current_user_id() == $user->ID ? 'disabled' : ''; ?> value="<?php echo $user->ID; ?>" name="rm_users[]"></td>
                            <td><div class="tableimg">
                                    <a href="?page=rm_user_view&user_id=<?php echo $user->ID; ?>">
                                        <?php echo get_avatar($user->ID); ?>
                                    </a>

                                </div></td>

                            <td><?php echo $user->first_name; ?></td>
                            <td><?php echo $user->user_email; ?></td>
                            <td><?php echo $user->user_status; ?></td>
                            <td><a href="?page=rm_user_view&user_id=<?php echo $user->ID; ?>"><?php echo RM_UI_Strings::get("VIEW"); ?></a></td>
                        </tr>

                        <?php
                    endforeach;
                ?>

            </table>
            <div id="rm_user_delete_popup" class="rm-modal-view" style="display: none;">
                <div class="rm-modal-overlay"></div>
                <div class="rm-modal-wrap rm-user-delete-popup">

                    <div class="rm-modal-titlebar ">
                        <div class="rm-modal-title">
                            <?php _e("Delete Users", 'custom-registration-form-builder-with-submission-manager'); ?>
                        </div>
                        <span class="rm-modal-close">&times;</span>
                    </div>
                    <div class="rm-modal-container">
                        <div class="rm-user-delete-wrap">
                            <span class="user_msg1"></span>    
                            <div class="rm_user_datails"></div><br>
                            <span class="user_msg2"></span>
                            <div class="rm_delete_options">
                                <ul style="list-style:none;">
                                    <li>
                                        <label>
                                            <input type="radio" name="rm_delete_option" value="delete">
                                            <?php _e("Delete all content", 'custom-registration-form-builder-with-submission-manager'); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <input checked type="radio" name="rm_delete_option" value="reassign">
                                        <label for="delete_option"><?php _e("Attribute all content to:", 'custom-registration-form-builder-with-submission-manager'); ?></label> 
                                        <?php $users = get_users(); ?>
                                        <select name="rm_reassign_user" id="rm_reassign_user" class="">
                                            <?php foreach ($users as $single_user): ?>
                                                <option value="<?php echo $single_user->ID; ?>"><?php echo $single_user->email . ' (' . $single_user->display_name . ')'; ?></option>
                                            <?php endforeach; ?>
                                        </select>    
                                    </li>    
                                </ul>                
                            </div>
                        </div>
                    </div>

                    <div class="rm-modal-footer rm-dbfl">
                        <div class="rm-difl rm_user_delete-cancel-bt"><a href="javascript:void(0)" class="rm-model-cancel">← &nbsp;<?php _e("Cancel", 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        <div class="rm-difl rm_user_delete-bt">
                            <a onclick="jQuery.rm_user_deletion_confirmed()"><?php _e("Confirm Deletion", 'custom-registration-form-builder-with-submission-manager'); ?></a>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>


    <?php
    echo $data->filter->render_pagination();
    include RM_ADMIN_DIR . 'views/template_rm_promo_banner_bottom.php';
    ?>


</div>
<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">

        function rm_on_users_selection() {
            var selected_users = jQuery("input.rm_checkbox_group:checked");
            if (selected_users.length > 0) {
                jQuery("#rm-activation").removeClass("rm_deactivated");
                jQuery("#rm-deactivation").removeClass("rm_deactivated");
                jQuery("#rm-delete").removeClass("rm_deactivated");
            } else {
                jQuery("#rm-activation").addClass("rm_deactivated");
                jQuery("#rm-deactivation").addClass("rm_deactivated");
                jQuery("#rm-delete").addClass("rm_deactivated");
            }
        }


        jQuery('.rm-modal-close, .rm-modal-overlay, .rm-model-cancel').click(function () {
            jQuery(this).parents('#rm_user_delete_popup').hide();
        });

 </script></pre>
<?php } ?>