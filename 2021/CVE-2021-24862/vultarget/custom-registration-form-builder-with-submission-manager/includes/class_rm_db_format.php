<?php
class RM_DB_FORMAT
{
    public function get_db_table_field_type($identifier,$field) 
    {
        $functionname = 'get_field_format_type_'.$identifier;
        if (method_exists('RM_DB_FORMAT',$functionname)) {
            $format = $this->$functionname($field);
        }
        else
        {
            return false;
        }
        return $format;
    }
    
    public function get_field_format_type_FORMS($field)
    {
        switch ($field) 
        {
                case 'form_id':
                        $format = '%d';
                        break;
                 case 'form_type':
                        $format = '%d';
                        break;
                case 'form_should_send_email':
                        $format = '%d';
                        break;
                case 'form_should_auto_expire':
                        $format = '%d';
                        break;
                case 'created_by':
                        $format = '%d';
                        break;
                case 'modified_by':
                        $format = '%d';
                        break;
                default:
                        $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_FIELDS($field)
    {
        switch ($field) 
        {
                case 'field_id':
                        $format = '%d';
                        break;
                 case 'form_id':
                        $format = '%d';
                        break;
                case 'page_no':
                        $format = '%d';
                        break;
                case 'field_order':
                        $format = '%d';
                        break;
                case 'field_show_on_user_page':
                        $format = '%d';
                        break;
                case 'is_field_primary':
                        $format = '%d';
                        break;
                case 'field_is_editable':
                        $format = '%d';
                        break;
                case 'is_deletion_allowed':
                        $format = '%d';
                        break;
                default:
                        $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_SUBMISSIONS($field)
    {
        switch ($field) 
        {
                case 'submission_id':
                        $format = '%d';
                        break;
                 case 'form_id':
                        $format = '%d';
                        break;
                case 'child_id':
                        $format = '%d';
                        break;
                case 'last_child':
                        $format = '%d';
                        break;
                case 'is_read':
                        $format = '%d';
                        break;
       
                default:
                        $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_SUBMISSION_FIELDS($field)
    {
        switch ($field) 
        {
                case 'sub_field_id':
                        $format = '%d';
                        break;
                 case 'submission_id':
                        $format = '%d';
                        break;
                case 'field_id':
                        $format = '%d';
                        break;
                case 'form_id':
                        $format = '%d';
                        break;
                case 'value':
                        $format = '%s';
                        break;
                default:
                        $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_PAYPAL_FIELDS($field)
    {
        switch ($field) 
        {
                case 'field_id':
                    $format = '%d';
                    break;
                case 'order':
                    $format = '%d';
                    break;
                default:
                    $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_PAYPAL_LOGS($field)
    {
        switch ($field) 
        {
                case 'id':
                    $format = '%d';
                    break;
                case 'submission_id':
                    $format = '%d';
                    break;
                case 'form_id':
                    $format = '%d';
                    break;
                case 'total_amount':
                    $format = '%f';
                    break;
                default:
                    $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_FRONT_USERS($field)
    {
        switch ($field) 
        {
                case 'id':
                    $format = '%d';
                    break;
                default:
                    $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_STATS($field)
    {
        switch ($field) 
        {
                case 'stat_id':
                    $format = '%d';
                    break;
                case 'form_id':
                    $format = '%d';
                    break;
                case 'time_taken':
                    $format = '%d';
                    break;
                case 'submission_id':
                    $format = '%d';
                    break;
                default:
                    $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_NOTES($field)
    {
        switch ($field) 
        {
                case 'note_id':
                    $format = '%d';
                    break;
                case 'submission_id':
                    $format = '%d';
                    break;
                case 'published_by':
                    $format = '%d';
                    break;
                case 'last_edited_by':
                    $format = '%d';
                    break;
                default:
                    $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_WP_USERS($field)
    {
        return '%s';
    }
    public function get_field_format_type_WP_USERS_META($field)
    {
        return '%s';
    }
    public function get_field_format_type_SENT_EMAILS($field)
    {
        switch ($field) 
        {
                case 'mail_id':
                    $format = '%d';
                    break;
                case 'type':
                    $format = '%d';
                    break;
                case 'form_id':
                    $format = '%d';
                    break;
                case 'is_read_by_user':
                    $format = '%d';
                    break;
                case 'was_sent_success':
                    $format = '%d';
                    break;
                default:
                    $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_LOGIN_LOG($field)
    {
        switch ($field) 
        {
                case 'id':
                    $format = '%d';
                    break;
                case 'status':
                    $format = '%d';
                    break;
                case 'ban':
                    $format = '%d';
                    break;
                
                default:
                    $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_LOGIN($field)
    {
        switch ($field) 
        {
                case 'id':
                    $format = '%d';
                    break;
                default:
                    $format = '%s';
                    break;
        }
        return $format;
    }
    public function get_field_format_type_SESSIONS($field)
    {
        switch ($field) 
        {
                case 'timestamp':
                    $format = '%d';
                    break;
                default:
                    $format = '%s';
                    break;
        }
        return $format;
    }
}