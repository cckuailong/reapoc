<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_options_payment.php'); else {
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//$data [] = 
$curr_arr = array('USD' => __("US Dollars",'custom-registration-form-builder-with-submission-manager'),
    'EUR' => __("Euros",'custom-registration-form-builder-with-submission-manager'),
    'GBP' => __("Pounds Sterling",'custom-registration-form-builder-with-submission-manager'),
    'AUD' => __("Australian Dollars",'custom-registration-form-builder-with-submission-manager'),
    'BRL' => __("Brazilian Real",'custom-registration-form-builder-with-submission-manager'),
    'CAD' => __("Canadian Dollars",'custom-registration-form-builder-with-submission-manager'),
    'HRK' => __("Croatian Kuna",'custom-registration-form-builder-with-submission-manager'),
    'CZK' => __("Czech Koruna",'custom-registration-form-builder-with-submission-manager'),
    'DKK' => __("Danish Krone",'custom-registration-form-builder-with-submission-manager'),
    'HKD' => __("Hong Kong Dollar",'custom-registration-form-builder-with-submission-manager'),
    'HUF' => __("Hungarian Forint",'custom-registration-form-builder-with-submission-manager'),
    'ILS' => __("Israeli Shekel",'custom-registration-form-builder-with-submission-manager'),
    'JPY' => __("Japanese Yen",'custom-registration-form-builder-with-submission-manager'),
    'MYR' => __("Malaysian Ringgits",'custom-registration-form-builder-with-submission-manager'),
    'MXN' => __("Mexican Peso",'custom-registration-form-builder-with-submission-manager'),
    'NZD' => __("New Zealand Dollar",'custom-registration-form-builder-with-submission-manager'),
    'NOK' => __("Norwegian Krone",'custom-registration-form-builder-with-submission-manager'),
    'PHP' => __("Philippine Pesos",'custom-registration-form-builder-with-submission-manager'),
    'PLN' => __("Polish Zloty",'custom-registration-form-builder-with-submission-manager'),
    'SGD' => __("Singapore Dollar",'custom-registration-form-builder-with-submission-manager'),
    'SEK' => __("Swedish Krona",'custom-registration-form-builder-with-submission-manager'),
    'CHF' => __("Swiss Franc",'custom-registration-form-builder-with-submission-manager'),
    'TWD' => __("Taiwan New Dollars",'custom-registration-form-builder-with-submission-manager'),
    'THB' => __("Thai Baht",'custom-registration-form-builder-with-submission-manager'),
    'INR' => __("Indian Rupee",'custom-registration-form-builder-with-submission-manager'),
    'TRY' => __("Turkish Lira",'custom-registration-form-builder-with-submission-manager'),
    'RIAL' => __("Iranian Rial",'custom-registration-form-builder-with-submission-manager'),
    'RUB' => __("Russian Rubles",'custom-registration-form-builder-with-submission-manager'),
    'NGN' => __("Nigerian Naira",'custom-registration-form-builder-with-submission-manager'),
    'ZAR' => __("South African Rand",'custom-registration-form-builder-with-submission-manager'),
    'ZMW' => __("Zambian Kwacha",'custom-registration-form-builder-with-submission-manager'),
    'GHS' => __("Ghanaian cedi",'custom-registration-form-builder-with-submission-manager')
    );
    
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
//PFBC form
        $form = new RM_PFBC_Form("options_payment");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        $data['payment_gateway'] = ((is_array($data['payment_gateway']) && in_array('paypal',$data['payment_gateway']) ) || $data['payment_gateway']=='paypal')?array('paypal') : array();
        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get('GLOBAL_SETTINGS_PAYMENT')));
        $form->addElement(new Element_HTML('<div class="rm_payment_guide"><a target="_blank" href="https://registrationmagic.com/setup-payments-on-registrationmagic-form-using-products/"><span class="dashicons dashicons-book-alt"></span>'.RM_UI_Strings::get('LABEL_PAYMENTS_GUIDE'). '</a></div></div>'));
        $config_field = new Element_HTML('<a href=javascript:void(0) onclick="rm_open_payproc_config(this)">'.__('configure', 'custom-registration-form-builder-with-submission-manager').'</a>');
        $form->addElement(new Element_Checkbox(RM_UI_Strings::get('LABEL_PAYMENT_PROCESSOR'), "payment_gateway", $data['pay_procs_options'], array("value" => $data['payment_gateway'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_PROCESSOR')), array('exclass_row'=>'rm_pricefield_checkbox','sub_element'=>$config_field)));
        ////////////////// Payment Processor configuration popup /////////////////
        $form->addElement(new Element_HTML('<div id="rm_pproc_config_parent_backdrop" style="display:none" class="rm_config_pop_wrap">'));
        $form->addElement(new Element_HTML('<div id="rm_pproc_config_parent" style="display:block" class="rm_config_pop">'));
        foreach($data['pay_procs_configs'] as $pproc_name => $form_elems):
            $form->addElement(new Element_HTML('<div class="rm_pproc_config_single" id="rm_pproc_config_'.$pproc_name.'" style="display:none">'));
                $form->addElement(new Element_HTML("<div class='rm_pproc_config_single_titlebar'><div class='rm_pproc_title'>{$data['pay_procs_options'][$pproc_name]}</div><span onclick='jQuery(\"#rm_pproc_config_parent_backdrop\").hide();' class='rm-popup-close'>&times;</span></div>"));
                $form->addElement(new Element_HTML('<div class="rm_pproc_config_single_elems">'));
            foreach($form_elems as $elem):
                $form->addElement($elem);
            endforeach;
                $form->addElement(new Element_HTML('</div>'));
            $form->addElement(new Element_HTML('</div>'));
        endforeach;
        
        $form->addElement(new Element_HTML('</div>'));
        $form->addElement(new Element_HTML('</div>'));
        ////////////////// End: Payment Processor configuration popup ////////////        
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_CURRENCY'), "currency", $curr_arr, array("value" => $data['currency'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_CURRENCY'))));
        $form->addElement(new Element_Select(RM_UI_Strings::get('LABEL_CURRENCY_SYMBOL'), "currency_symbol_position", array("before" => __("Before amount (Eg.: $10)",'custom-registration-form-builder-with-submission-manager'), "after" => __("After amount (Eg.: 10$)",'custom-registration-form-builder-with-submission-manager')), array("value" => $data['currency_symbol_position'], "longDesc" => RM_UI_Strings::get("LABEL_CURRENCY_SYMBOL_HELP"))));

        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__("Cancel",'custom-registration-form-builder-with-submission-manager'), '?page=rm_options_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE')));

        $form->render();
        ?>

    </div>
    <?php 
    include RM_ADMIN_DIR.'views/template_rm_promo_banner_bottom.php';
    ?>
</div>
<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">
    
    function rm_open_payproc_config(ele) {
        var jqele = jQuery(ele);
        
        if(jqele.closest(".rmrow").hasClass("rm_deactivated"))
            return;
        
        var jq_pproc = jqele.parents("li").children('span.rm-pricefield-wrap').children().val();
        
        if(typeof jq_pproc == 'undefined')
            return;
        
        jQuery("#rm_pproc_config_parent").children().hide();
        jQuery("#rm_pproc_config_parent").children("#rm_pproc_config_"+jq_pproc).show();
        jQuery("#rm_pproc_config_parent_backdrop").show();
    }
    
    jQuery(document).mouseup(function (e) {
        var container = jQuery("#rm_pproc_config_parent");
        if (!container.is(e.target) // if the target of the click isn't the container... 
                && container.has(e.target).length === 0 && container.is(":visible")) // ... nor a descendant of the container 
        {
            jQuery("#rm_pproc_config_parent_backdrop").hide();
        }
    });
    
    jQuery(document).ready(function () {
        jQuery('#options_payment-element-1-0').click(function () {
            checkbox_disable_elements(this, 'rm_pp_test_cb-0,rm_pp_email_tb,rm_pp_style_tb', 0);
        });
        jQuery('#options_payment-element-1-1').attr("disabled", true);
        
        var pgws_jqel = jQuery("input[name='payment_gateway[]']");
        
        pgws_jqel.each(function(){
            var cbox_jqel = jQuery(this);
            if(cbox_jqel.prop('checked'))
                cbox_jqel.parents("li").children('.rmrow').removeClass("rm_deactivated");
            else
                cbox_jqel.parents("li").children('.rmrow').addClass("rm_deactivated");
        });
        
        pgws_jqel.change(function(){
            var cbox_jqel = jQuery(this);
            var pproc_name = cbox_jqel.val(); 
            if(pproc_name == 'paypal'){
                if(cbox_jqel.prop('checked'))
                    cbox_jqel.parents("li").children('.rmrow').removeClass("rm_deactivated");
                else
                    cbox_jqel.parents("li").children('.rmrow').addClass("rm_deactivated");
            } else {
                cbox_jqel.prop('checked',false);
                jQuery("#rm_pproc_config_parent").children().hide();
                jQuery("#rm_pproc_config_parent").children("#rm_pproc_config_"+pproc_name).show();
                jQuery("#rm_pproc_config_parent_backdrop").show();
            }
        });
    });
</script></pre>

<?php   
}