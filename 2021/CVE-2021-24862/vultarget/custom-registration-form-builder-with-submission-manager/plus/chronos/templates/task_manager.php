<?php

?>

<div class="rmagic">
    
    <!-- Joyride Magic begins -->
    <ol id="rm-task-man-joytips" style="display:none">
        <li data-button="Done">
            <h2>
                <?php _e('Welcome to Automation','custom-registration-form-builder-with-submission-manager'); ?>
            </h2>
            <br/>
            <p><?php _e('This new section will allow you to create tasks which run in the background and process actions on form submissions. Each task is attached to a specific form and requires setting rule(s). The tasks are scheduled using WordPress native cron system. Please note, scheduling too many tasks simultaneously may stress resources of shared or moderately powered servers. Automation is another step in our plan to keep providing you greater control over your forms. You will keep seeing new automation rules and actions in coming months. Good luck!','custom-registration-form-builder-with-submission-manager'); ?></p>
        </li>
    </ol>
    
<!-----Operations bar Starts----->

    <div class="operationsbar">
        <div class="rmtitle"><?php echo RM_Chronos_UI_Strings::get("LABEL_TASK_MANAGER"); ?></div>
        <div class="icons">
<!--            <a href="?page=rm_options_payment"><img alt="" src="<?php //echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/rm-payments.png'; ?>"></a>-->
        </div>
        <div class="nav">
            <ul class="rm-automation">
                <li onclick="window.history.back()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_BACK"); ?></a></li>                
                <li><a href="?page=rm_ex_chronos_edit_task&rm_form_id=<?php echo $data->form_id; ?>"><?php echo RM_Chronos_UI_Strings::get('LABEL_NEW_TASK');?></a></li>
                <li id="rm-duplicate-task" class="rm_deactivated" onclick="rmc_duplicate_tasks_batch()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get('LABEL_DUPLICATE');?></a></li>
                <li id="rm-enable-task" class="rm_deactivated" onclick="rmc_set_state_tasks_batch('enable')"><a href="javascript:void(0)"><?php echo RM_Chronos_UI_Strings::get('LABEL_ENABLE');?></a></li>
                <li id="rm-disable-task" class="rm_deactivated" onclick="rmc_set_state_tasks_batch('disable')"><a href="javascript:void(0)"><?php echo RM_Chronos_UI_Strings::get('LABEL_DISABLE');?></a></li>
                <li id="rm-delete-task" class="rm_deactivated" onclick="rmc_delete_tasks_batch()"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get('LABEL_REMOVE'); ?></a></li>
                <li><a href="javascript:void(0)" onclick="rm_start_joyride()"><?php echo RM_Chronos_UI_Strings::get('LABEL_HELP'); ?></a></li>
                <li class="rm-form-toggle">
                    <?php if (count($data->forms) !== 0)
                    {
                        echo RM_UI_Strings::get('LABEL_TOGGLE_FORM');
                        ?>
                        <select id="rm_form_dropdown" name="form_id" onchange="on_form_change()">
                            <?php
                            foreach ($data->forms as $form_id => $form)
                                if ($data->form_id == $form_id)
                                    echo "<option value=$form_id selected>$form</option>";
                                else
                                    echo "<option value=$form_id>$form</option>";
                            ?>
                        </select>
                        <?php
                    } 
                    ?>
                </li>
            </ul>
        </div>
    </div>
    <!--------Operationsbar Ends----->
    
    <!-----  Show Notice if WP-Cron is disabled    ----->
    <?php
    if(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON)
        echo "<div class='rmnotice'>".RM_Chronos_UI_Strings::get('CRON_DISABLED_WARNING')."</div>";
    ?>
    
    <!----Field Selector Starts---->

    <!----Slab View---->
    <ul class="rm-field-container" id="rmc_sortable_tasks_list">
        <?php
        if ($data->tasks_data)
        {$i=0;
            foreach ($data->tasks_data as $task_data)
            {
                ?>
                <li id="<?php echo $task_data->task_id?>">
                    <div class="rm-slab">
                        <div class="rm-slab-grabber">
                            <span class="rmc_task_sortable_handle rm_handle">
                                <img alt="" src="<?php echo RM_BASE_URL . 'images/rm-drag.png'; ?>">
                            </span>
                        </div>
                        <div class="rm-slab-content">
                            <input type="checkbox" name="rm_selected[]"  onclick="rm_on_field_selection_change()" value="<?php echo $task_data->task_id; ?>">
                            <span><?php echo $task_data->name; ?></span>
                        </div>
                        <?php $task_state = $task_data->is_active == 1?'Enabled':'Disabled'; ?>
                            <div class="rmc-taskslab-info-task-state"><?php echo $task_state; ?></div>
                        <div class="rm-slab-buttons">                            
                            <a href="javascript:void(0)" id="id_rmc_run_task_now_<?php echo $task_data->task_id;?>" onclick="rmc_run_task_now(<?php echo $task_data->task_id;?>)"><?php echo RM_Chronos_UI_Strings::get("LABEL_RUN_NOW"); ?></a>
                            <a href="<?php echo "?page=rm_ex_chronos_edit_task&rm_form_id={$data->form_id}&rmc_task_id={$task_data->task_id}"; ?>"><?php echo RM_UI_Strings::get("LABEL_EDIT"); ?></a>
                            <a href="<?php echo "?page=rm_ex_chronos_manage_tasks&rm_form_id={$data->form_id}&rmc_task_id={$task_data->task_id}&rmc_action=delete"; ?>"><?php echo RM_UI_Strings::get("LABEL_DELETE"); ?></a>
                        </div>
                    </div>
                </li>
                <?php
            }
        } else
        {
            echo "<div class='rmnotice'>".RM_Chronos_UI_Strings::get('NO_TASKS_MSG')."</div>";
        }
        ?>
    </ul>
</div>
<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
    
    jQuery(document).ready(function(){

        jQuery('#rmc_sortable_tasks_list').sortable({
            axis: 'y',
            opacity: 0.7,
            handle: '.rmc_task_sortable_handle',
            update: function (event, ui) {
                var list_sortable = jQuery(this).sortable('toArray');

                var data = {
                    action: 'rm_chronos_ajax',
                    rm_chronos_ajax_action: 'update_task_order',
                    ordered_task_ids: list_sortable
                };

                jQuery.post(ajaxurl, data, function (response) {
                    void(0);
                });
            }
        });
        
        <?php if($data->autostart_tour): ?>
       /*jQuery("#rm-task-man-joytips").joyride({tipLocation: 'top',
                                               autoStart: true,
                                               postRideCallback: rm_joyride_tour_taken});*/
        <?php else: ?>
           /* jQuery("#rm-task-man-joytips").joyride({tipLocation: 'top',
                                               autoStart: false,
                                               postRideCallback: rm_joyride_tour_taken});*/
        <?php endif; ?>
    });
    
    function rm_start_joyride(){
       jQuery("#rm-task-man-joytips").joyride();
    }
    
    function rm_joyride_tour_taken(){
        var data = {
			'action': 'joyride_tour_update',
			'tour_id': 'task_manager_tour',
                        'state': 'taken'
		};

        jQuery.post(ajaxurl, data, function(response) {});
    }
        
    function on_form_change() {
        var new_form_id = jQuery("#rm_form_dropdown").val();
        window.location = "<?php echo $data->page_url_base;?>"+"&rm_form_id="+new_form_id;        
    }
    
    function rm_on_field_selection_change(){
        var selected_fields = jQuery("input[name='rm_selected[]']:checked");
        if(selected_fields.length > 0) {   
            jQuery("#rm-delete-task, #rm-duplicate-task, #rm-enable-task, #rm-disable-task").removeClass("rm_deactivated");
        } else {
            jQuery("#rm-delete-task, #rm-duplicate-task, #rm-enable-task, #rm-disable-task").addClass("rm_deactivated");
        }
    }
    
</script></pre>