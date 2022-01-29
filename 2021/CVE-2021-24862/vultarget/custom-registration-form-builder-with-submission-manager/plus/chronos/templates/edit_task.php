<?php

//var_dump($data->init_field_config);
$rule_data = $data->init_field_config->rule_data;
$action_data = $data->init_field_config->action_data;
?>

    
<div class="rmagic">
    
    <div class="rm-task-setting-wrap">
        

        <div class="rmheader"><?php _e('Task Settings','custom-registration-form-builder-with-submission-manager'); ?></div>
   
        <form name="rmc_task_settings_form" id="rmc_task_settings_form" action="" method="post" style="display:none">
            <input type="hidden" name="rmc-task-edit-form-subbed" value="yes">
 
            <input type="radio" name="rm-task-slide" class="rm-slide-radio1 rm-task-slide-radio" checked id="rm-task-slide-1"> 
            <input type="radio" name="rm-task-slide" class="rm-slide-radio2 rm-task-slide-radio" id="rm-task-slide-2">
            <input type="radio" name="rm-task-slide" class="rm-slide-radio3 rm-task-slide-radio" id="rm-task-slide-3">
    
            <div class="rm-task-slide rm-task-slide1">     

                <div class="rmrow">
                    <div class="rmfield" for="rmc_task_name">
                        <label><b><?php _e('Task Name','custom-registration-form-builder-with-submission-manager'); ?></b><sup class="required">* </sup></label>
                    </div>
                    <div class="rminput">
                        <input type="text" name="rmc_task_name" id="rmc_task_name" required value="<?php echo $data->init_field_config->name; ?>">
                    </div>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php _e('Name of the task. Should be unique.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#httaskname'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                    </div>
                </div>

                <div class="rmrow">
                    <div class="rmfield" for="rmc_task_description">
                        <label><b><?php _e('Description','custom-registration-form-builder-with-submission-manager'); ?></b></label>
                    </div>
                    <div class="rminput">
                        <textarea rows="5" name="rmc_task_description" id="rmc_task_description"><?php echo $data->init_field_config->desc; ?></textarea>
                    </div>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php _e('Optional task description for your reference.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htdescription'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                    </div>
                </div>   

            </div>

            <div class="rm-task-slide rm-task-slide2">
                
                <div class="rmrow">
                    <div class="rmfield" for="">
                        <label><b><?php _e('User Account Rule','custom-registration-form-builder-with-submission-manager'); ?></b></label>
                    </div>
                    <div class="rminput">
                        <ul class="rmradio" >
                            <li>
                                <input id="rmc_enable_user_account_rule" class="rmc_control_toggle" type="checkbox" name="rmc_enable_user_account_rule" value="1" <?php echo $rule_data['g_user_account_rule']['state']; ?>>
                                <span>  </span>
                            </li>
                        </ul>
                    </div>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php _e('Select users depending on the account state.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htuserrule'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                    </div>              
                </div>

                <div class="childfieldsrow" id="rmc_enable_user_account_rule_ctrl_subpanel">
                    <div class="rmrow">
                        <div class="rmfield" for=""><label><b><?php _e('User Account State','custom-registration-form-builder-with-submission-manager'); ?></b></label></div>
                        <div class="rminput">
                            <ul class="rmradio">
                                <li> 
                                    <input id="rm_-0" type="radio" name="rmc_rule_user_account" class="rm_"  required="" value="active" <?php echo $rule_data['g_user_account_rule']['active_option_state']; ?>><?php _e('Activated User Accounts','custom-registration-form-builder-with-submission-manager'); ?></li>
                                <li> 
                                    <input id="rm_-1" type="radio" name="rmc_rule_user_account" class="rm_"  required="" value="inactive" <?php echo $rule_data['g_user_account_rule']['inactive_option_state']; ?>> <?php _e('Deactivated User accounts','custom-registration-form-builder-with-submission-manager'); ?> </li> 
                            </ul>
                            <div id="rm_user_state_rule_error"  class="rm-rule-error" style="display:none">
                                &#9888; <?php _e('Select a user state','custom-registration-form-builder-with-submission-manager'); ?>
                            </div>
                        </div>
                        <div class="rmnote"><div class="rmprenote"></div>
                            <div class="rmnotecontent"><?php _e('Select the type of the users account.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htuserrule'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        </div>
                    </div>
                </div>
                
                <div class="rmrow">
                    <div class="rmfield" for="">
                        <label><b><?php _e('Submission age Rule','custom-registration-form-builder-with-submission-manager'); ?></b></label>
                    </div>
                    <div class="rminput">
                        <ul class="rmradio" >
                            <li>
                                <input id="rmc_enable_sub_time_rule" class="rmc_control_toggle" type="checkbox" name="rmc_enable_sub_time_rule" value="1" <?php echo $rule_data['g_sub_time_rule']['state']; ?>>
                                <span>  </span>
                            </li>
                        </ul>
                    </div>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php _e('Select submissions based on the time elapsed after submission.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htagerule'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                    </div>              
                </div>

                <div class="childfieldsrow" id="rmc_enable_sub_time_rule_ctrl_subpanel">
                    <div class="rmrow">
                        <div class="rmfield" for=""><label><b><?php _e('Define Period','custom-registration-form-builder-with-submission-manager'); ?></b></label></div>
                        <div class="rminput">
                            <ul class="rmradio">
                                <li> 
                                    <input id="rmc_enable_sub_time_rule_older_than" type="checkbox" name="rmc_enable_sub_time_rule_older_than" class="rm_" value="1" <?php echo $rule_data['g_sub_time_rule']['older_than_state']; ?>> <?php _e('Submissions older than','custom-registration-form-builder-with-submission-manager'); ?>
                                    <input id="rmc_rule_sub_time_older_than_age" type="number" name="rmc_rule_sub_time_older_than_age" class="rm_" step="1" min="0" value="<?php echo $rule_data['g_sub_time_rule']['older_than_age']; ?>"> <?php _e('days','custom-registration-form-builder-with-submission-manager'); ?>
                                </li>
                                <li> 
                                    <input id="rmc_enable_sub_time_rule_younger_than" type="checkbox" name="rmc_enable_sub_time_rule_younger_than" class="rm_" value="1" <?php echo $rule_data['g_sub_time_rule']['younger_than_state']; ?>> <?php _e('Submissions younger than','custom-registration-form-builder-with-submission-manager'); ?>
                                    <input id="rmc_rule_sub_time_younger_than_age" type="number" name="rmc_rule_sub_time_younger_than_age" class="rm_" step="1" min="0" value="<?php echo $rule_data['g_sub_time_rule']['younger_than_age']; ?>"> <?php _e('days','custom-registration-form-builder-with-submission-manager'); ?>
                                </li> 
                            </ul>
                            <div id="rm_sub_time_rule_error" class="rm-rule-error" style="display:none">
                                    <?php _e('Error Message!','custom-registration-form-builder-with-submission-manager'); ?>
                            </div>
                        </div>
                        <div class="rmnote"><div class="rmprenote"></div>
                            <div class="rmnotecontent"><?php _e('Define the selection parameters for submissions.','custom-registration-form-builder-with-submission-manager'); ?><a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htagerule'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        </div>
                    </div>
                </div>
                

                <div class="rmrow">
                    <div class="rmfield" for="">
                        <label><b><?php _e('Field Value Rule','custom-registration-form-builder-with-submission-manager'); ?></b></label>
                    </div>
                    <div class="rminput">
                        <ul class="rmradio">
                            <li>
                                <input id="rmc_enable_field_value_rule" class="rmc_control_toggle" type="checkbox" name="rmc_enable_field_value_rule" value="1" <?php echo $rule_data['g_field_val_rule']['state']; ?>>
                                <span>  </span>
                            </li>
                        </ul>
                    </div>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php _e("Selection Submissions based on matching field values. Separate multiple values with pipe '|'.",'custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#httfieldvalrule'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                    </div>
                </div>

                <div class="childfieldsrow" id="rmc_enable_field_value_rule_ctrl_subpanel">
                    <div class="rmrow">
                        <div class="rminput rm-field-value-rule">
                            <ul class="rm-field-value-rule-wrap" id="rm_field_value_rule_wrap">
                                <?php if(count($rule_data['g_field_val_rule']['rmc_rule_fv_fids']) > 0): ?>
                                <?php foreach($rule_data['g_field_val_rule']['rmc_rule_fv_fids'] as $index => $fid): ?>
                                <li class="appendable_options rm-deletable-options">
                                    <select name="rmc_rule_fv_fids[]">
                                        <option value=""><?php _e('Select a Field','custom-registration-form-builder-with-submission-manager'); ?></option>
                                        <?php foreach($data->fields as $field): 
                                                echo '<option value="',$field->field_id,'"';
                                                if($field->field_id == $fid)
                                                    echo " selected";
                                                echo '>',$field->field_label,'</option>';
                                              endforeach; ?>
                                    </select>
                                    <input type="text" placeholder="Values" name="rmc_rule_fv_fvals[]" id="id_placeholder_no" class="" required value="<?php echo $rule_data['g_field_val_rule']['rmc_rule_fv_fvals'][$index]; ?>">
                                    <div class="rm_actions" onclick="rm_delete_appended_field(this,'rm_field_value_rule_wrap')">
                                        <a href="javascript:void(0)"><?php _e('Delete','custom-registration-form-builder-with-submission-manager'); ?></a>
                                    </div>
                                </li>   
                                <?php endforeach; ?>
                                <?php else: ?>
                                <li class="appendable_options rm-deletable-options">
                                    <select name="rmc_rule_fv_fids[]">
                                        <option value=""><?php _e('Select a Field','custom-registration-form-builder-with-submission-manager'); ?></option>
                                        <?php foreach($data->fields as $field): 
                                                echo '<option value="',$field->field_id,'"';
                                                echo '>',$field->field_label,'</option>';
                                              endforeach; ?>
                                    </select>
                                    <input type="text" placeholder="Values" name="rmc_rule_fv_fvals[]" id="id_placeholder_no" class="" required value="">
                                    <div class="rm_actions" onclick="rm_delete_appended_field(this,'rm_field_value_rule_wrap')">
                                        <a href="javascript:void(0)"><?php _e('Delete','custom-registration-form-builder-with-submission-manager'); ?></a>
                                    </div>
                                </li>
                                <?php endif; ?>
                            </ul>
                            <div class="rm_action_container" id="rm_action_container_id">
                                <div id="rm_field_value_rule_error"  class="rm-rule-error" style="display: none">
                                    <?php _e('Error Message!','custom-registration-form-builder-with-submission-manager'); ?>
                                </div>
                              <div class="rm_action" id="rm_action_field_container" onclick="rm_append_field('li',this)">
                                  <a href="javascript:void(0)"><?php _e('+ Add more field value rules','custom-registration-form-builder-with-submission-manager'); ?></a>
                              </div>
                            </div>                              
                        </div>
                        <div class="rmnote">
                            <div class="rmprenote"></div>
                            <div class="rmnotecontent"><?php _e("Create rules for submitted field values. Separate multiple values with pipe '|'.",'custom-registration-form-builder-with-submission-manager'); ?></div>
                        </div>
                    </div>  
                </div>  
                
                <div class="rmrow">
                    <div class="rmfield" for="">
                        <label><b><?php _e('Payment processor Rule','custom-registration-form-builder-with-submission-manager'); ?></b></label>
                    </div>
                    <div class="rminput">
                        <ul class="rmradio" >
                            <li>
                                <input id="rmc_enable_pay_proc_rule" class="rmc_control_toggle" type="checkbox" name="rmc_enable_pay_proc_rule" value="1" <?php echo $rule_data['g_pay_proc_rule']['state']; ?>>
                                <span>  </span>
                            </li>
                        </ul>
                    </div>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php _e('Select submissions depending upon the payment processor used for submission checkout.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htpaymentprocessrule'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?> </a></div>
                    </div>            
                </div>
                
                <div class="childfieldsrow" id="rmc_enable_pay_proc_rule_ctrl_subpanel">
                    <div class="rmrow rm_pricefield_checkbox">
                        <div class="rmfield" for="">
                            <label><?php _e('Payment Processor','custom-registration-form-builder-with-submission-manager'); ?></label>
                        </div>
                        <div class="rminput">
                            <ul class="rmradio">
                                <?php $sppcount = count($rule_data['g_pay_proc_rule']['pprocs']); ?>
                                <?php foreach($data->pay_procs_options as $pproc => $pproc_label): ?>
                                <li>
                                    <input id="options_payment-element-1-0" type="checkbox" name="rmc_rule_pay_procs[]" value="<?php echo $pproc;?>"
                                           <?php if(in_array($pproc, $rule_data['g_pay_proc_rule']['pprocs']))
                                                   echo "checked";
                                           ?>
                                    >
                                    <span><?php echo $pproc_label;?></span>
                                </li>
                                <?php endforeach; ?>                            
                            </ul>
                        </div>
                        <div class="rmnote">
                            <div class="rmprenote"></div>
                            <div class="rmnotecontent"><?php _e('Only those submissions which have received payments through selected gateways will be selected.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htpaymentprocessrule'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>    
                        </div>
                    </div>
                </div>
                
                <div class="rmrow">
                    <div class="rmfield" for="">
                        <label><b><?php _e('Payment status Rule','custom-registration-form-builder-with-submission-manager'); ?></b></label>
                    </div>
                    <div class="rminput">
                        <ul class="rmradio" >
                            <li>
                                <input id="rmc_enable_pay_status_rule" class="rmc_control_toggle" type="checkbox" name="rmc_enable_pay_status_rule" value="1" <?php echo $rule_data['g_pay_status_rule']['state']; ?>>
                                <span>  </span>
                            </li>
                        </ul>
                    </div>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php _e('Select submissions depending upon the status of payment.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htpaymentstatusrule'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                    </div>              
                </div>
                
                <div class="childfieldsrow" id="rmc_enable_pay_status_rule_ctrl_subpanel">
                    <div class="rmrow rm_pricefield_checkbox">
                        <div class="rmfield" for="">
                            <label><?php _e('Payment Status','custom-registration-form-builder-with-submission-manager'); ?></label>
                        </div>
                        <div class="rminput">
                            <ul class="rmradio">
                                <?php $spscount = count($rule_data['g_pay_status_rule']['pay_statuses']); ?>                            
                                <li>
                                    <?php if(in_array('completed',$rule_data['g_pay_status_rule']['pay_statuses']))
                                            $temp_state = "checked";
                                    else $temp_state = ""
                                    ?>            
                                    <input id="options_payment-element-1-0" type="checkbox" name="rmc_rule_pay_status[]" value="completed" <?php echo $temp_state; ?>>
                                    <span> <?php _e('Completed','custom-registration-form-builder-with-submission-manager'); ?> </span>
                                </li>
                                <li>
                                    <?php if(in_array('pending',$rule_data['g_pay_status_rule']['pay_statuses']) || $spscount == 0)
                                            $temp_state = "checked";
                                    else $temp_state = ""
                                    ?>
                                    <input id="options_payment-element-1-1" type="checkbox" name="rmc_rule_pay_status[]" value="pending" <?php echo $temp_state; ?>>
                                    <span> <?php _e('Pending','custom-registration-form-builder-with-submission-manager'); ?> </span>
                                </li>
                                <li>
                                    <?php if(in_array('canceled',$rule_data['g_pay_status_rule']['pay_statuses']) || $spscount == 0)
                                            $temp_state = "checked";
                                    else $temp_state = ""
                                    ?>
                                    <input id="options_payment-element-1-2" type="checkbox" name="rmc_rule_pay_status[]" value="canceled" <?php echo $temp_state; ?>>
                                    <span> <?php _e('Canceled','custom-registration-form-builder-with-submission-manager'); ?> </span>
                                </li>
                            </ul>
                        </div>
                        <div class="rmnote">
                            <div class="rmprenote"></div>
                            <div class="rmnotecontent"><?php _e('Only those submissions with selected payment status will be selected.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htpaymentstatusrule'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        </div>
                    </div>
                </div>
                <?php
                //echo '<pre>';print_r($rule_data);echo '</pre>';
                ?>
            </div>

            <div class="rm-task-slide rm-task-slide3">

                <div class="rmrow rm-account-action">
                    <div class="rmfield" for="">
                        <label><?php _e('User Account Activation','custom-registration-form-builder-with-submission-manager'); ?></label>
                    </div>
                    <div class="rminput">
                        <ul class="rmradio">
                            <li>
                                <input id="options_payment-element-1-0" type="radio" name="rmc_action_user_acc" value="do_nothing" <?php if("do_nothing" == $action_data['g_user_acc_action']['type']) echo "checked";?>>
                                <span> <?php _e('Do Nothing','custom-registration-form-builder-with-submission-manager'); ?> </span>
                            </li>
                            <li>
                                <input id="options_payment-element-1-1" type="radio" name="rmc_action_user_acc" value="activate" <?php if("activate" == $action_data['g_user_acc_action']['type']) echo "checked";?>>
                                <span> <?php _e('Activate Account','custom-registration-form-builder-with-submission-manager'); ?> </span>
                            </li>
                            <li>
                                <input id="options_payment-element-1-2" type="radio" name="rmc_action_user_acc" value="deactivate" <?php if("deactivate" == $action_data['g_user_acc_action']['type']) echo "checked";?>>
                                <span> <?php _e('Deactivate Account','custom-registration-form-builder-with-submission-manager'); ?> </span>
                            </li>
                            <li>
                                <input id="options_payment-element-1-2" type="radio" name="rmc_action_user_acc" value="delete" <?php if("delete" == $action_data['g_user_acc_action']['type']) echo "checked";?>>
                                <span> <?php _e('Delete Account!','custom-registration-form-builder-with-submission-manager'); ?> </span>
                            </li>
                        </ul>
                    </div>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php _e('Select the action to be taken on the user accounts associated with selected submissions.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htuseraccaction'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                    </div>
                </div> 

                <div class="rmrow">
                    <div class="rmfield" for="">
                        <label><b><?php _e('Send Email','custom-registration-form-builder-with-submission-manager'); ?></b></label>
                    </div>
                    <div class="rminput">
                        <ul class="rmradio">
                            <li>
                                <?php if(defined('REGMAGIC_ADDON')) { ?>
                                    <input id="rmc_enable_send_mail_action" class="rmc_control_toggle" type="checkbox" name="rmc_enable_send_mail_action" value="1" <?php echo $action_data['g_send_email_action']['state'];?>>
                                <?php } else { ?>
                                    <input id="rmc_enable_send_mail_action" class="rmc_control_toggle" type="checkbox" name="buy_pro" value="1" disabled>
                                <?php } ?>
                                <span>  </span>
                            </li>
                        </ul>
                    </div>
                    <div class="rmnote">
                        <div class="rmprenote"></div>
                        <div class="rmnotecontent"><?php _e("Send an email to the user accounts associated with selected submissions.",'custom-registration-form-builder-with-submission-manager') ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htsendemail'><?php _e("More",'custom-registration-form-builder-with-submission-manager') ?></a><br/><br/><?php if(!defined('REGMAGIC_ADDON')) echo RM_UI_Strings::get('MSG_BUY_PRO_INLINE');?></div>
                    </div>
                </div>

                <div class="childfieldsrow" id="rmc_enable_send_mail_action_ctrl_subpanel">                
                    <div class="rmrow">
                        <div class="rmfield" for="rmc_action_send_mail_sub">
                            <label><?php _e('Subject','custom-registration-form-builder-with-submission-manager'); ?><sup class="required">*</sup></label>
                        </div>
                        <div class="rminput">
                            <input type="text" name="rmc_action_send_mail_sub" required="" id="rmc_action_send_mail_sub" value="<?php echo $action_data['g_send_email_action']['sub'];?>">
                        </div>
                        <div class="rmnote">
                            <div class="rmprenote"></div>
                            <div class="rmnotecontent"><?php _e('Subject for the message you are sending to the users.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htsubject'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        </div>
                    </div>

                    <div class="rmrow">
                        <div class="rmfield" for="rmc_action_send_mail_body">
                            <label><?php _e('Body (HTML and Mail-merge supported)','custom-registration-form-builder-with-submission-manager'); ?></label>
                        </div>
                        <div class="rminput">
                            <?php echo wp_editor($action_data['g_send_email_action']['body'], "rmc_action_send_mail_body_{$data->form_id}", array('textarea_name' => 'rmc_action_send_mail_body','editor_class' => 'rm_TinyMCE', 'editor_height' => '100px')); ?>
                        </div>
                        <div class="rmnote">
                            <div class="rmprenote"></div>
                            <div class="rmnotecontent"><?php _e('Content of the message your are sending to the users of selected submissions. You can use values from form fields filled by the users from "Add Fields" dropdown for personalized message.','custom-registration-form-builder-with-submission-manager'); ?> <a target='_blank' class='rm-more' href='https://registrationmagic.com/knowledgebase/task-settings/#htbody'><?php _e('More','custom-registration-form-builder-with-submission-manager'); ?></a></div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="buttonarea rm-task-setting-footer">              
                <div class="rm-previous rm-control">
                    <label for="rm-task-slide-1" class="rm-numb0" onclick="window.history.back()">&larr; &nbsp; Back</label>
                    <label for="rm-task-slide-1" class="rm-numb1">&larr; &nbsp; <?php _e('Previous','custom-registration-form-builder-with-submission-manager'); ?></label>
                    <label for="rm-task-slide-2" class="rm-numb2">&larr; &nbsp; <?php _e('Previous','custom-registration-form-builder-with-submission-manager'); ?></label>
                    <label for="rm-task-slide-3" class="rm-numb3">&larr; &nbsp; <?php _e('Previous','custom-registration-form-builder-with-submission-manager'); ?></label>
                </div>           
                <div class="rm-next rm-control">

                    <label for="rm-task-slide-2" class="rm-numb2" onclick="rmc_validate_task_form('basic_details')"><?php _e('Next','custom-registration-form-builder-with-submission-manager'); ?></label>
                    <label for="rm-task-slide-3" class="rm-numb3" onclick="rmc_validate_task_form('rule_config')"><?php _e('Next','custom-registration-form-builder-with-submission-manager'); ?></label>
                    <label for="rm-task-slide-3" class="rm-numb4" onclick="rmc_validate_task_form('action_config')"><?php _e('Save','custom-registration-form-builder-with-submission-manager'); ?></label>
                </div>            
            </div> 
        </form>
    </div> 
</div> 

