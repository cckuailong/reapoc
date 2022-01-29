<?php

require_once plugin_dir_path(__FILE__) . 'interface_rm_exporter.php';

/**
 * Description of class_rm_export_post
 *
 * @author CMSHelplive
 */
class RM_Export_POST implements RM_Exporter
{

    public $xurl;
    public $data_prepared;

    public function __construct($url)
    {
        $this->xurl = new RM_Xurl($url);
        $this->data_prepared = array();
    }
    
    public function prepare_data($data_raw)
    {
        foreach ($data_raw as $data_row)
        {
            if (is_array($data_row->value))
            {
                if (isset($data_row->value['rm_field_type']) && $data_row->value['rm_field_type'] == 'File')
                {
                    unset($data_row->value['rm_field_type']);
                    if (count($data_row->value) == 0)
                        $data_row->value = null;
                    else
                    {
                        $file = array();
                        foreach ($data_row->value as $a)
                            $file[] = wp_get_attachment_url($a);

                        $data_row->value = implode(',', $file);
                    }
                }elseif (isset($data_row->value['rm_field_type']) && $data_row->value['rm_field_type'] == 'Address'){
                       unset($data_row->value['rm_field_type']);
                       foreach($data_row->value as $in =>  $value){
                           if(empty($value))
                               unset($data_row->value[$in]);
                       }
                    $data_row->value = implode(',', $data_row->value);   
                }
                elseif(isset($data_row->type) && $data_row->type == 'Price'){
                    if (count($data_row->value) == 0)
                        $data_row->value = null;
                    else
                    {
                        $values = array();
                        foreach ($data_row->value as $value){
                            $tmp = array();
                            $tmp = explode('&times;', $value);
                            $values[] = implode('quantity',$tmp);
                        }
                        $data_row->value = implode(',',$values);
                    }
                }
                else
                    $data_row->value = implode(',', $data_row->value);
            }
            else{
                if(isset($data_row->type) && $data_row->type == 'Price'){
                    $tmp = array();
                    $tmp = explode('&times;', $data_row->value);
                    $data_row->value = implode('quantity',$tmp);
                    
                }
            }
            
            $this->data_prepared[$data_row->label] = $data_row->value;
            
        }
        
    }

    public function send_data()
    {
        return $this->xurl->post($this->data_prepared);
    }

}
