<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_user_roles_manager.php'); else {
//echo'<pre>';var_dump($data->roles);die;
?>

<div class="rmagic">

    <!-----Operations bar Starts----->
    <div class="operationsbar">
        <div class="rmtitle"><?php echo RM_UI_Strings::get("LABEL_USER_ROLES"); ?></div>
        <div class="icons">
            <a href="?page=rm_options_user"><img alt="" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/rm-user-accounts.png'; ?>"></a>
        </div>
        <div class="nav">
            <ul>
                <!--li><a href="?page=rm_paypal_field_add&rm_field_type"><?php echo RM_UI_Strings::get('LABEL_ADD_NEW');?></a></li-->
                <li id="rm-delete-user-role" class="rm_deactivated"  onclick="jQuery.rm_do_action('rm_user_role_mananger_form','rm_user_role_delete')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get('LABEL_REMOVE'); ?></a></li>
            </ul>
        </div>

    </div>
    <!--------Operationsbar Ends----->
    <!----Field Selector Starts---->
    <div class="rm-field-selector rm-user-role-form">
        <?php
        $form = new RM_PFBC_Form("rm_user_role_add_form");
        $form->configure(array(
        "prevent" => array("bootstrap", "jQuery"),
        "action" => ""
        ));
        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("HEADING_ADD_ROLE_FORM") . '</div>'));
        $form->addElement(new Element_Hidden("rm_slug", "rm_user_role_add"));
        $form->addElement(new Element_HTML(wp_nonce_field('rm_user_role_manage')));
        $form->addElement(new Element_Hidden("rm_submitted", "true"));
        $form->addElement(new Element_Textbox("<b>".RM_UI_Strings::get('LABEL_ROLE_NAME')."</b>", "rm_role_name", array("id" => "rm_role_name","required"=>"1","longDesc" => RM_UI_Strings::get('HELP_ROLE_KEY'))));
        $form->addElement(new Element_Textbox("<b>".RM_UI_Strings::get('LABEL_ROLE_DISPLAY_NAME')."</b>", "rm_display_name", array("id" => "rm_display_name","required"=>"1","longDesc" => RM_UI_Strings::get('HELP_ROLE_NAME'))));
        $form->addElement(new Element_Select("<b>".RM_UI_Strings::get('LABEL_PERMISSION_LEVEL')."</b>", "rm_user_capability", array_merge($data->roles->default,$data->roles->custom),array("longDesc" => RM_UI_Strings::get('HELP_ROLE_PERMISSION'))));
        $form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_IS_PAID_ROLE') . "</b>", "rm_role_is_paid", array(1 => ""), array('id'=>'rm_is_paid_cb','disabled'=>'disabled','readonly'=>'readonly' ,"value" => null,"longDesc" => RM_UI_Strings::get('HELP_IS_PAID_ROLE').RM_UI_Strings::get('MSG_BUY_PRO_INLINE'))));
        $form->addElement(new Element_HTML("<div id='rm_role_price_container' class='childfieldsrow' style='display:block;border: 1px solid transparent;'>"));
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_ROLE_PRICE') . "</b>", "rm_role_amt", array('id'=>'rm_role_price','disabled'=>'disabled','readonly'=>'readonly',"longDesc" => RM_UI_Strings::get('HELP_ROLE_PRICE'))));
        $form->addElement(new Element_HTML("</div>"));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "class" => "rm_btn", "name" => "submit")));
        $form->render();
        ?>
    </div>
    
    <ul class="rm-field-container rm-user-role-manager" id="rm_nonsortable_list">
        <?php
        if (is_array($data->roles->default) || is_object($data->roles->default))
        {
            foreach ($data->roles->default as $role => $role_name)
            {
                ?>
                <li id="<?php echo $role;?>">
                    <div class="rm-slab">
                        <div class="rm-slab-grabber">
                            <span class="rm_sortable_handle">
                                <img alt="" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/user_role.png'; ?>">
                            </span>
                        </div>
                        <div class="rm-slab-content">
                            <input type="checkbox" name="rm_role[]" value="<?php echo $role; ?>" id="checkbox_<?php echo $role; ?>" disabled>
                            <span><?php echo $role; ?></span>
                            <span><?php echo $role_name; ?></span>

                        </div>
                        <div class="rm-slab-buttons">
                            <a href="javascript:void(0)" class="rmdisabled"><?php echo RM_UI_Strings::get("LABEL_DELETE"); ?></a>
                        </div>
                    </div>
                </li>

                <?php
            }
        }
        ?>

    <!----Slab View---->
<form method="post" id="rm_user_role_mananger_form">
    <?php wp_nonce_field('rm_user_role_manage');?>
        <input type="hidden" name="rm_slug" value="" id="rm_slug_input_field">
        <?php
        if (is_array($data->roles->custom) || is_object($data->roles->custom))
        {
            foreach ($data->roles->custom as $role => $role_name)
            {
                ?>
                <li id="<?php echo $role;?>">
                    <div class="rm-slab">
                        <div class="rm-slab-grabber">
                            <span class="rm_sortable_handle">
                                <img alt="" src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'images/user_role.png'; ?>">
                            </span>
                        </div>
                        <div class="rm-slab-content">
                            <input type="checkbox" name="rm_roles[]" onclick="rm_on_user_role_deletion()" value="<?php echo $role; ?>" id="checkbox_<?php echo $role; ?>">
                            <span><?php echo $role; ?></span>
                            <span><?php echo $role_name; ?></span>

                        </div>
                        <div class="rm-slab-buttons" onclick="delete_role(this,'checkbox_<?php echo $role; ?>')">
                            <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("LABEL_DELETE"); ?></a>
                        </div>
                    </div>
                </li>

                <?php
            }
        } 
        ?>
</form>
    </ul>
    <?php     
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
    
</div>
  <pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
function rm_on_user_role_deletion()
 {
         var selected_user_roles = jQuery("input[name='rm_roles[]']:checked");
         if(selected_user_roles.length > 0) {   
              jQuery("#rm-delete-user-role").removeClass("rm_deactivated"); 
         }else
         {
                jQuery("#rm-delete-user-role").removeClass("rm_deactivated"); 
         }
        
    }
     
  </script></pre>
  
<?php } ?>