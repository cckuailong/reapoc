<?php

class RM_LMS_UI_Strings
{
    public static function get($identifier)
    {
        switch($identifier)
        {
            case 'LABEL_RM_GLOBAL_SETTING_MENU':
                return __('LeadMagic','custom-registration-form-builder-with-submission-manager');      
                
            case 'SUBTITLE_RM_GLOBAL_SETTING_MENU':
                return __('Create Landing/ Squeeze pages with your forms','custom-registration-form-builder-with-submission-manager'); 
            
            default:
                return __("NO STRING FOUND (rmlms)", 'custom-registration-form-builder-with-submission-manager');
        }
    }
}

