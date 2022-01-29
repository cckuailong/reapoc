<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_field_conditions.php'); else {
$excluded_fields= array('HTMLP','HTMLH','File','Image','Repeatable','Price','Multi-Dropdown','Time','Divider','Spacing','Shortcode','Rating','Map','Address','RichText','Timer',"Link","YouTubeV");
$fields_dd_options = addslashes(RM_Utilities::get_fields_dropdown(array('form_id' => $data->form_id,'ex_by_type'=>$excluded_fields)));
?>
<script type="text/javascript">
    /************* Conditional fields related *******************/
    
    function opChanged(obj){
        var element= jQuery(obj);
        var selectedVal= element.val();
        var valueElement= element.closest(".rm-field-condition-row").find("input[name='values[]']");
        if(selectedVal=="_blank" || selectedVal=="_not_blank"){
            valueElement.val('');
            valueElement.addClass('rm-block-input');
        } else{
            valueElement.removeClass('rm-block-input');
        }
    }
    function delete_dependency(element)
    {
        jQuery(element).closest(".rm-field-condition-row").remove();
        if(jQuery(element).closest(".rm-field-condition-row").siblings().length<2)
        { 
            jQuery(".rm-match-condition").hide();
        }
    }
    
    function delete_all(id){
        jQuery("#rm_condition_" + id + " .rm-field-condition-row").remove();
       // jQuery("#rm_condition_" + id).hide();
        jQuery("#rm-cond-form-"+id).submit();
    }
    function showConditionFormModal(fid)
    {
        if (fid === void 0) {fid = 0;}
        jQuery("#rm-conditional-modal.rm-modal-view, .rm-modal-overlay").toggle();
        if(fid>0)
        {
           jQuery(".rm-condition").hide(); 
           addField(fid);
        } 
    }
    function addConditionForm(fid)
    { 
        var select_options= '<?php echo $fields_dd_options; ?>';
        // Removing select field option from dropdown to avoid self condition
        var current_option = new RegExp('<option value="' + fid + '">(.*?)<\/option>');
        var html = '<div class="rm-field-condition-row"><div class="rm-controlling-atr"><div class="rminput"><select name="cfields[]"><option><?php _e('Select Field','custom-registration-form-builder-with-submission-manager'); ?></option><?php echo $fields_dd_options; ?></select></div></div><div class="rm-controlling-atr"><div class="rminput"><select onchange="opChanged(this)" name="op[]"><?php echo addslashes(RM_Utilities::get_cond_op_dd()); ?></select></div></div><div class="rm-controlling-atr"><div class="rminput"><input type="text" name="values[]" placeholder="Value" maxlength="50"></div></div><div class="rm-controlling-atr rm-controlling-btn"> <div class="rminput"><a onclick="delete_dependency(this)" href="javascript:void(0)"><?php _e('Remove','custom-registration-form-builder-with-submission-manager'); ?></a></div></div></div>';
        html= html.replace(current_option,'');
        jQuery("#rm-container-field-"+fid).append(html);
        show_combinator(fid);
    }
    
    function show_combinator(fid)
    {   
        if(jQuery("#rm_condition_"+fid+" .rm-field-condition-row").length>1){
            jQuery(".rm-match-condition").show();
        }
        else{
            jQuery(".rm-match-condition").hide();
        }
    }
    
    
    function addField(fid){
        if(fid===undefined)
            fid= jQuery("#new_field").val();
        jQuery("#rm_condition_"+fid).show();
        jQuery('.rm-modal-wrap').animate({scrollTop: jQuery("#rm_condition_"+fid).offset().top},'slow');
        jQuery("#selected_field").html(jQuery("#rm_condition_"+fid).data('field-name'));
        show_combinator(fid);
    }
    /************* Conditional fields logic ends here *******************/
</script>
<!----Slab View---->
<div class="rm-modal-view" id="rm-conditional-modal" style="display:none">
    <div class="rm-modal-overlay" style="display:none" onClick="showConditionFormModal()"></div>
        <div class="rm-modal-wrap">
            <div class="rm-modal-titlebar">
                <div class="rm-modal-title">  <?php echo RM_UI_Strings::get('LABEL_CONDITIONS'); ?> for <span id="selected_field"></span></div>
                <span class="rm-modal-close" onClick="showConditionFormModal()">&times;</span>
              
                
            </div>
            <div class="rm-conditional">
                <div class="rm-conditions">
                    <div class="rm-add-field" style="display:none">
                        <a href="javascript:void(0)" onclick="addField()"><?php _e('Add','custom-registration-form-builder-with-submission-manager'); ?></a>
                     <?php echo RM_Utilities::get_fields_dropdown(array('form_id' => $data->form_id, 'name' => 'new_field','inc_by_type'=>  RM_Utilities::get_allowed_conditional_fields(),'ex_by_type'=>$excluded_fields)); ?>
                    </div>

                    <?php
                    foreach ($data->fields_data as $field):
                        $options = maybe_unserialize($field->field_options);
                        if(empty($options->conditions))
                        {
                            $options = new StdClass();
                            $options->conditions= array("settings"=>array('combinator'=>'OR'));
                        }
                        $display= empty($options->conditions['rules'])?'display:none':'';
                        ?>   
                    <form method="post" id="rm-cond-form-<?php echo $field->field_id; ?>">
                        <div style="<?php echo $display; ?>" class="rm-condition" id="rm_condition_<?php echo $field->field_id; ?>" data-field-name="<?php echo ucwords(esc_attr($field->field_label)); ?>"> 
                                <input type="hidden" name="dfield" value="<?php echo $field->field_id; ?>" />
                                
                                <div class="rm-conditions-field-container">
                                <div class="rm-field-conditions-wrap">
                                    <div class="rm-field-conditions-title">
                                        <span class="rm-conditions-field-label">
                                        <?php echo $field->field_label; ?>
                                        </span>
                                        <div class="rm-field-conditions-delete"><a href="javascript:void(0)" onclick="delete_all(<?php echo $field->field_id; ?>)"><?php _e('Delete','custom-registration-form-builder-with-submission-manager'); ?></a> </div>
                                         
                                    </div> 
                                    <div  class="rm-field-condition-container rm-combinator-container" id="rm-container-field-<?php echo $field->field_id; ?>">
                                    <div class="rm-match-condition-row">
                                                <div class="rm-match-condition"><input type="radio" name="combinator" value="OR" <?php echo @$options->conditions['settings']['combinator'] != 'AND' ? 'checked' : '' ?>><label for="rm-match-all-condition" ><?php _e('Match Any Condition','custom-registration-form-builder-with-submission-manager'); ?></label></div>     
                                                <div class="rm-match-condition"><input  type="radio" name="combinator" value="AND" <?php echo @$options->conditions['settings']['combinator'] == 'AND' ? 'checked' : '' ?>><label for="rm-match-one-condition" ><?php _e('Match All Conditions','custom-registration-form-builder-with-submission-manager'); ?></label></div> 
                                    </div>
                                    <?php
                                    if(!empty($options->conditions['rules'])):
                                    foreach ($options->conditions['rules'] as $key => $condition):
                                        $cfield = new RM_Fields();
                                        $cfield->load_from_db($condition['controlling_field']);
                                        $conditional_settings = json_encode($condition);
                                        ?>   
                                            <div class="rm-field-condition-row">
                                                <div class="rm-controlling-atr">
                                                    <div class="rminput">
                                                            <?php echo RM_Utilities::get_fields_dropdown(array('form_id' => $data->form_id,'full' => true, 'name' => 'cfields[]','def'=>$condition['controlling_field'],'exclude'=>array($field->field_id),'ex_by_type'=>$excluded_fields)); ?>
                                                    </div>
                                                </div>
                                                <div class="rm-controlling-atr">
                                                    <div class="rminput">
                                                        <select name="op[]" onchange="opChanged(this)">
                                                        <?php echo RM_Utilities::get_cond_op_dd(array('def' => $condition['op'])); ?>
                                                        </select>    
                                                    </div>

                                                </div>
                                                <div class="rm-controlling-atr">
                                                    <div class="rminput">
                                                        <?php 
                                                            $values='';
                                                            if(is_array($condition['values']) && !empty($condition['values'])){
                                                                $values= implode(',', $condition['values']);
                                                            }
                                                        ?>
                                                        <input type="text" class="<?php echo ($condition['op']=='_blank' || $condition['op']=='_not_blank')?'rm-block-input':''; ?>" value="<?php echo htmlspecialchars($values); ?>" name="values[]" placeholder="Value"  maxlength="50" />
                                                    </div>
                                                </div>
                        
                                                <div class="rm-controlling-atr rm-controlling-btn"> 
                                                    <div class="rminput"><a onclick='delete_dependency(this)' href="javascript:void(0)">Remove</a></div>
                                                </div>
                                            </div>
                                        
                                     
                                        
                                    <?php endforeach;
                                        else:
                                            echo '<script>addConditionForm('.$field->field_id.')</script>';
                                        endif;
                                    ?> 
                                           
                                    </div>
                                    
                                </div> 
                                    
                                   <div class="rm-add-condition-row">  <a onclick="addConditionForm(<?php echo $field->field_id; ?>)" href="javascript:void(0)"><?php _e('Add','custom-registration-form-builder-with-submission-manager'); ?></a></div> 
                                   <div class="rm-save-condition-row"> <input type="submit" value="<?php _e('Save','custom-registration-form-builder-with-submission-manager'); ?>" /></div>
                                </div>
                        </div></form>
                        <?php
                    endforeach;
                    ?>
                </div>    
            </div> 
        </div>
</form>
</div>

<?php 
if(isset($data->show_conditions) && $data->show_conditions)
{
   // echo '<script>window.onload= showConditionFormModal();</script>';
}
}
?>
