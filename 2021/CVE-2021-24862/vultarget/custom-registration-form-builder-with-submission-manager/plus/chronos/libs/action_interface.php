<?php

interface RM_Chronos_Action_Interface {
    
    const ACTION_TYPE_DEACTIVATE_USER = 'at_deactivate';
    const ACTION_TYPE_ACTIVATE_USER = 'at_activate';
    const ACTION_TYPE_DELETE_USER = 'at_delete';
    const ACTION_TYPE_APPLY_STATUS = 'at_apply';
    const ACTION_TYPE_REMOVE_STATUS = 'at_remove';
    const ACTION_TYPE_SEND_EMAIL = 'at_send_email';
}
