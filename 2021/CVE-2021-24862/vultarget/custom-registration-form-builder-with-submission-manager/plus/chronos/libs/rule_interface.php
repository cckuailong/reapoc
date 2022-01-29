<?php

interface RM_Chronos_Rule_Interface {
    
    const RULE_TYPE_PAYMENT_GATEWAY = 1;
    const RULE_TYPE_PAYMENT_STATUS = 2;
    const RULE_TYPE_USER_STATE = 3;
    const RULE_TYPE_USER_META = 4;
    const RULE_TYPE_SUB_TIME = 5;
    const RULE_TYPE_FIELD_VALUE = 6;
}
