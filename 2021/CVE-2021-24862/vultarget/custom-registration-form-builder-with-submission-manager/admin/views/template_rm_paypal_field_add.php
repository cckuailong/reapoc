<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_paypal_field_add.php'); else {
/**
 * @internal Plugin Template File [Add Text Type Field]
 * 
 * This view generates the form for adding text type field to the form
 */

$price_field_type = array(
    "fixed" => __("Fixed",'custom-registration-form-builder-with-submission-manager'),
    "multisel" => __("Multi-Select", 'custom-registration-form-builder-with-submission-manager'),
    "dropdown" => __("Dropdown", 'custom-registration-form-builder-with-submission-manager'),
    "userdef" => __("User Defined", 'custom-registration-form-builder-with-submission-manager')
);

$fixed_class = "class = 'rm_hidden_element'";
$dd_class = "class = 'rm_hidden_element'";
        
if($data->selected_field == 'fixed'){
        $fixed_class = "";
        $dd_class = "class = 'rm_hidden_element'";
    }

?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">
        
        <?php
        
$form = new RM_PFBC_Form("add-paypal-field");

$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));

if (isset($data->model->field_id))
{
    $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_EDIT_PAYPAL_FIELD_PAGE")));
    $form->addElement(new Element_HTML('<div class="rm_payment_guide"><a target="_blank" href="https://registrationmagic.com/setup-payments-on-registrationmagic-form-using-products/"><span class="dashicons dashicons-book-alt"></span>'.RM_UI_Strings::get('LABEL_PAYMENTS_GUIDE'). '</a></div></div>'));
    $form->addElement(new Element_Hidden("field_id", $data->model->field_id));
}
else{
    $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_NEW_PAYPAL_FIELD_PAGE")));
    $form->addElement(new Element_HTML('<div class="rm_payment_guide"><a target="_blank" href="https://registrationmagic.com/setup-payments-on-registrationmagic-form-using-products/">'.RM_UI_Strings::get('LABEL_PAYMENTS_GUIDE'). '</a></div></div>'));
}

$form->addElement(new Element_Select("<b>" . RM_UI_Strings::get('LABEL_SELECT_PRICING_TYPE') . "</b>", "type", $price_field_type, array("value" => $data->selected_field, "id"=>"id_paypal_field_type_dd", "required" => "1", "onchange" => "rm_toggle_visiblity_pricing_fields(this)", "longDesc"=>RM_UI_Strings::get('HELP_ADD_PRICE_FIELD_SELECT_TYPE'))));
$form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_PRODUCT_NAME') . "</b>", "name", array("id"=>"id_paypal_field_name_tb", /*"required" => "0",*/  "required" => "1", "value" => $data->model->name, "longDesc"=>RM_UI_Strings::get('HELP_ADD_PRICE_FIELD_LABEL'))));

$form->addElement(new Element_HTML("<div id='id_block_fields_for_fixed' $fixed_class>"));
$form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_PRICE') . "</b>", "value", array("id"=>"id_paypal_field_value_no", /*"required" => "0",*/  "step"=>"0.01", "min"=>"0.01", "value" => $data->model->value, "longDesc" => RM_UI_Strings::get('HELP_PRICE_FIELD'))));
$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_SHOW_ON_FORM') . "</b>", "show_on_form", array(1 => ""), array("id"=>"id_paypal_field_visible_cb",  "value" => $data->show_on_form, "longDesc" => RM_UI_Strings::get('HELP_SHOW_ON_FORM'))));
$form->addElement(new Element_HTML("</div>"));

$form->addElement(new Element_HTML("<div id='id_allow_quantity_container'>"));
$form->addElement(new Element_Checkbox("<b>" . RM_UI_Strings::get('LABEL_ALLOW_QUANTITY') . "</b>", "allow_quantity", array(1 => ""), array("id" => "id_allow_quantity", "class" => "rm-static-field", "value" => $data->allow_quantity, "longDesc" => RM_UI_Strings::get('HELP_PRICE_FIELD_ALLOW_QUANTITY'))));
$form->addElement(new Element_HTML("</div>"));
$form->addElement(new Element_HTML('<div id="rm_product_notice">'.RM_UI_Strings::get('MSG_RM_PRODUCT_NOTICE').'</div>'));


$form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_paypal_field_manage', array('class' => 'cancel')));
$form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE'), "submit", array("id" => "rm_submit_btn", "onClick" => "jQuery.prevent_field_add(event, '".RM_UI_Strings::get('MSG_REQUIRED_FIELD') ."')", "class" => "rm_btn", "name" => "submit")));
 
$form->render();

?>
         
    </div>
    <?php     
    $rm_promo_banner_title = __("Unlock multiple pricing configurations by upgrading",'custom-registration-form-builder-with-submission-manager');
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
    
</div>

<?php }