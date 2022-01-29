<?php

class RM_Chronos_Rule_Factory {
    
    public function create_rule($rule_id) {
        $rule_model = new RM_Chronos_Rule_Model;
        if($rule_model->load_from_db($rule_id)) {
            $class_name = null;            
            switch($rule_model->type) {
                case RM_Chronos_Rule_Interface::RULE_TYPE_USER_STATE:
                    $class_name = 'RM_Chronos_Rule_UserState';
                    break;
                
                case RM_Chronos_Rule_Interface::RULE_TYPE_FIELD_VALUE:
                    $class_name = 'RM_Chronos_Rule_FieldValue';
                    break;
                
                case RM_Chronos_Rule_Interface::RULE_TYPE_SUB_TIME:
                    $class_name = 'RM_Chronos_Rule_SubTime';
                    break;
                
                case RM_Chronos_Rule_Interface::RULE_TYPE_PAYMENT_GATEWAY:
                    $class_name = 'RM_Chronos_Rule_PaymentGateway';
                    break;
                
                case RM_Chronos_Rule_Interface::RULE_TYPE_PAYMENT_STATUS:
                    $class_name = 'RM_Chronos_Rule_PaymentStatus';
                    break;
            }

            if($class_name)
                return new $class_name($rule_model);
        }
        return null;
    }
    
}