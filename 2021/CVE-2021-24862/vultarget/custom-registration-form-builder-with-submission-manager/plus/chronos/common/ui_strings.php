<?php

class RM_Chronos_UI_Strings
{
    public static function get($identifier)
    {
        switch($identifier)
        {
            case 'LABEL_TASK_MANAGER':
                return __('Automation','custom-registration-form-builder-with-submission-manager');      
                
            case 'LABEL_NEW_TASK':
                return __('New Task','custom-registration-form-builder-with-submission-manager');      
                
            case 'NO_TASKS_MSG':
                return __('You haven&apos;t created any task for this form yet.','custom-registration-form-builder-with-submission-manager');      
                
            case 'LABEL_RUN_NOW':
                return __('Run Now','custom-registration-form-builder-with-submission-manager');      
                
            case 'LABEL_RM_AUTO_MENU':
                return __('Automation','custom-registration-form-builder-with-submission-manager');      
                
            case 'LABEL_ENABLE':
                return __('Enable','custom-registration-form-builder-with-submission-manager');      
                
            case 'LABEL_DISABLE':
                return __('Disable','custom-registration-form-builder-with-submission-manager');      
                
            case 'CRON_DISABLED_WARNING':
                return sprintf(__('Wordpress cron is disabled. Automatic task execution will not work. <a target="__blank" href="%s">More info.</a>','custom-registration-form-builder-with-submission-manager'),'https://codex.wordpress.org/Editing_wp-config.php#Disable_Cron_and_Cron_Timeout');
                
            case 'LABEL_HELP':
                return __('Help','custom-registration-form-builder-with-submission-manager');   
                
            case 'LABEL_TASKS':
                return __('Automation Tasks','custom-registration-form-builder-with-submission-manager');   
            
            default:
                return __("NO STRING FOUND (rmchrono)", 'custom-registration-form-builder-with-submission-manager');
        }
    }
}
