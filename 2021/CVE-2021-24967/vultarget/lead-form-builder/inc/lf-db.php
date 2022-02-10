<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!function_exists('lfb_plugin_activate')) {

 function lfb_plugin_activate() {
  $default_form = 0;
   global $wpdb;
   $lead_form = $wpdb->prefix . 'lead_form';
   $lead_form_data = $wpdb->prefix . 'lead_form_data';
   $lead_form_extension = $wpdb->prefix . 'lead_form_extension';
   $lead_form_options = $wpdb->prefix . 'lead_form_options';
   $charset_collate = $wpdb->get_charset_collate();
   if ($wpdb->get_var("SHOW TABLES LIKE '$lead_form'") != $lead_form) {
       $sql = "CREATE TABLE  $lead_form (
         id INT(10) NOT NULL AUTO_INCREMENT,
         form_title VARCHAR(255) NOT NULL,
         form_data text NOT NULL,
         date datetime NOT NULL,
         mail_setting text NOT NULL,
         usermail_setting text NOT NULL,
         multiData text NOT NULL,
         form_skin VARCHAR(255) DEFAULT 'default' NOT NULL,
         form_status VARCHAR(50) DEFAULT 'ACTIVE' NOT NULL,       
         captcha_status VARCHAR(255) DEFAULT 'OFF' NOT NULL,
         storeType ENUM('1','2','3') DEFAULT '2' NOT NULL,
       PRIMARY KEY (id)
       ) $charset_collate;";
       $wpdb->query($sql);
        $default_form = 1;
   }

// $column = $wpdb->get_row("SELECT * FROM $lead_form");
//     //Add column if not present.
//     if(!isset($column->multiData)){
//       $wpdb->query("ALTER TABLE $lead_form ADD multiData text NOT NULL");
//     }
   
   if ($wpdb->get_var("SHOW TABLES LIKE '$lead_form_data'") != $lead_form_data) {
       $sql = "CREATE TABLE $lead_form_data(
         id INT(10) NOT NULL AUTO_INCREMENT,
         form_id INT(10),
         form_data LONGTEXT,
         ip_address VARCHAR(100),
         server_request TEXT,
         date datetime,
       PRIMARY KEY (id)
       )$charset_collate;";
       $wpdb->query($sql);  
   }

   if ($wpdb->get_var("SHOW TABLES LIKE '$lead_form_extension'") != $lead_form_extension) {
       $esql = "CREATE TABLE  $lead_form_extension (
         eid INT(10) NOT NULL AUTO_INCREMENT,
         form_id INT(10) NOT NULL,
         ext_api text NOT NULL,
         ext_map text NOT NULL,
         active tinyint(4) DEFAULT 0 NOT NULL,
         ext_type INT(5) NOT NULL,
         edate datetime NOT NULL,
       PRIMARY KEY (eid)
       ) $charset_collate;";
       $wpdb->query($esql);
   }   

   if ($wpdb->get_var("SHOW TABLES LIKE '$lead_form_options'") != $lead_form_options) {
       $esql = "CREATE TABLE  $lead_form_options (
         fid INT(10) NOT NULL,
         colorData text NOT NULL,
       PRIMARY KEY (fid)
       ) $charset_collate;";
       $wpdb->query($esql);
   }  

       if ($default_form >= 1) {
       $now_date= date('Y-m-d g:i:s');
     $form_title ='Contact Us';
       $form_data ='a:5:{s:12:"form_field_1";a:6:{s:10:"field_name";s:4:"Name";s:10:"field_type";a:1:{s:4:"type";s:4:"name";}s:13:"default_value";s:4:"Name";s:19:"default_placeholder";s:1:"1";s:11:"is_required";s:1:"1";s:8:"field_id";s:1:"1";}s:12:"form_field_2";a:6:{s:10:"field_name";s:5:"Email";s:10:"field_type";a:1:{s:4:"type";s:5:"email";}s:13:"default_value";s:5:"Email";s:19:"default_placeholder";s:1:"1";s:11:"is_required";s:1:"1";s:8:"field_id";s:1:"2";}s:12:"form_field_3";a:6:{s:10:"field_name";s:10:"Contact No";s:10:"field_type";a:1:{s:4:"type";s:6:"number";}s:13:"default_value";s:14:"Contact number";s:19:"default_placeholder";s:1:"1";s:11:"is_required";s:1:"1";s:8:"field_id";s:1:"3";}s:12:"form_field_4";a:6:{s:10:"field_name";s:7:"Message";s:10:"field_type";a:1:{s:4:"type";s:7:"message";}s:13:"default_value";s:7:"Message";s:19:"default_placeholder";s:1:"1";s:11:"is_required";s:1:"1";s:8:"field_id";s:1:"4";}s:12:"form_field_0";a:6:{s:10:"field_name";s:6:"submit";s:10:"field_type";a:1:{s:4:"type";s:6:"submit";}s:13:"default_value";s:0:"";s:19:"default_placeholder";s:1:"0";s:11:"is_required";s:1:"1";s:8:"field_id";s:1:"0";}}';
       $default_insert = "INSERT INTO $lead_form (form_title, form_data, date) VALUES ( '$form_title', '$form_data', '$now_date' );";
     $wpdb->query($default_insert);
     }

   $th_popup = $wpdb->prefix . 'th_popup';

   if ($wpdb->get_var("SHOW TABLES LIKE '$th_popup'") != $th_popup) {
       $sql = "CREATE TABLE  $th_popup (
         ID INT(10) NOT NULL AUTO_INCREMENT,
         popupData text NOT NULL,
         settings text NOT NULL,
         popupDate datetime NOT NULL,
       PRIMARY KEY (ID)
       ) $charset_collate;";
       $wpdb->query($sql);
   }
}
 add_action( 'admin_init', 'lfb_plugin_activate' );

} 

// database classs
Class LFB_SAVE_DB{
  private $thdb;
  private  $leadform = 'lead_form';
  private  $lf_ext = 'lead_form_extension';
  private  $lfb_options = 'lead_form_options';


 function __construct($nwpdb=''){
  global $wpdb;
 $this->thdb = $wpdb;
 $this->tbl_leadform =  $this->thdb->prefix.$this->leadform; 
 $this->tbl_extension =  $this->thdb->prefix.$this->lf_ext; 
 $this->tbl_options =  $this->thdb->prefix.$this->lfb_options; 
    }
 function lfb_get_form_content($get_form_query){
  return $this->thdb->get_results($get_form_query);
 }
 function lfb_delete_form($deletequery){
  return $this->thdb->query($deletequery); 
  //return $this->thdb->query($this->thdb->prepare($deletequery)); 
 }
 function lfb_update_form_data($updatequery){
 $update_data = $this->thdb->query($updatequery);
 //$update_data = $this->thdb->query($this->thdb->prepare($updatequery));
 return $update_data;
}
 function lfb_insert_form_data($insertquery){
 //$this->thdb->query($this->thdb->prepare($insertquery));
 $this->thdb->query($insertquery);
}

// get form data
function lfb_get_form_data($formid){
  $query = $this->thdb->prepare("SELECT * FROM $this->tbl_leadform WHERE id = %d and form_status = %s LIMIT 1",$formid, 'ACTIVE' );
  $form = $this->lfb_get_form_content($query);
  return $form;
}

public function lfb_get_all_form_title(){
  $return = $this->thdb->prepare("SELECT id,form_title FROM $this->tbl_leadform WHERE form_status = %s ",'ACTIVE');
  return $this->thdb->get_results($return);
}

public function lfb_get_ext_data($fid,$ext){
  $return = $this->thdb->prepare("SELECT * FROM $this->tbl_extension WHERE form_id = %d AND ext_type = %d ",$fid,$ext);
  return $this->thdb->get_results($return);
}

//get lead form

function get_lead_form(){  
    $query = $this->thdb->prepare("SELECT * FROM $this->tbl_leadform WHERE form_status = %s ORDER BY id DESC ", 'ACTIVE' );
    $return = $this->lfb_get_form_content($query);

    return $return;
}
/*
 * Mailchimp api update
 */
public function lfb_mcpi_insert_update_api($fid,$api,$ext){
  $get_data = $this->lfb_get_ext_data($fid,$ext);
  if(empty($get_data)):
    $this->lfb_mcpi_insert_extension($fid,$api,$ext);
  else:
      $this->lfb_mcpi_update_api($fid,$api,$ext);
  endif; 
}

public function lfb_mcpi_insert_extension($fid,$api,$ext){
   $insert_leads = $this->thdb->query( $this->thdb->prepare( 
     "INSERT INTO $this->tbl_extension ( form_id, ext_api, ext_type, edate ) 
     VALUES ( %d, %s,%d,  %s)",
      $fid, $api, $ext, date('Y-m-d g:i:s') ) );
}

public function lfb_mcpi_update_api($fid,$api,$ext){
  $this->thdb->query( $this->thdb->prepare( " UPDATE $this->tbl_extension 
  SET ext_api = %s WHERE form_id = %d AND ext_type = %d ",$api,$fid,$ext ) );
}
/*
 * Mailchimp list update
 */
public function lfb_mcpi_update_lists($fid,$list,$ext){
    $get_data = $this->lfb_get_ext_data($fid,$ext);
  if(!empty($get_data)):
      $this->lfb_mcpi_update_db_list($fid,$list,$ext);
  endif;
}
public function lfb_mcpi_update_db_list($fid,$list,$ext){
   $this->thdb->query( $this->thdb->prepare( " UPDATE $this->tbl_extension 
  SET ext_map = %s, active = %d  WHERE form_id = %d AND ext_type = %d ",$list,1,$fid,$ext ) );
}

/*
 * Mailchimp on/off update
 */
public function lfb_mcpi_update_onoff($fid,$extname,$onoff){

  $query = $this->thdb->query( $this->thdb->prepare( " UPDATE $this->tbl_extension 
  SET active = %s WHERE form_id = %d AND ext_type = %d ",$onoff,$fid,$extname ) );
  return $fid;
}

/*
 * Color Options
 */

public function lfb_colors_insert_update($fid,$data){
  $return = false;
  $color_stting = $this->lfb_get_colors_data($fid);
  if(empty($color_stting)):
    $return = $this->lfb_colors_insert($fid,$data);
  else:
     $return =  $this->lfb_colors_update($fid,$data);
  endif;
  return $return; 
}

public function lfb_get_colors_data($fid){
  $return = $this->thdb->prepare(" SELECT * FROM $this->tbl_options WHERE fid = %d ",$fid);
  return $this->thdb->get_results($return);
}

public function lfb_colors_insert($fid,$data){
  return $insert_leads = $this->thdb->query( $this->thdb->prepare( 
     "INSERT INTO $this->tbl_options ( fid, colorData ) 
     VALUES ( %d, %s)",
      $fid, $data ) );
}

public function lfb_colors_update($fid,$data){
 return $this->thdb->query( $this->thdb->prepare( "UPDATE $this->tbl_options 
  SET colorData = %s WHERE fid = %d ", $data,$fid ) );
}

public function lfb_reset_colors_data($fid){
  $this->thdb->query( $this->thdb->prepare( "UPDATE $this->tbl_options 
  SET colorData = %s WHERE fid = %d ", '',$fid ) );
}

/*
* next,previous lead show and show all leads
*
*/

// lead table
function lfb_lead_form_value($form_data,$fieldIdNew,$fieldData,$leadscount){
    $i = 0;
    $table_row = '';
    $table_popup = '';
    $count = 1;


            foreach ($fieldIdNew as $key => $value) {
                if(isset($form_data[$value]) && is_array($form_data[$value])){
                  if(strstr($value, 'upload_')){

                    $upload_filename = isset($form_data[$value]['filename'])?$form_data[$value]['filename']:$form_data[$value]['error'];

                  $upload = isset($form_data[$value]['url'])?'<a target="_blank" href="'.$form_data[$value]["url"].'">'.$upload_filename.'</a>':$upload_filename;

                  if($leadscount >= $count){
                    $table_row  .= '<td>'.$upload.'</td>';
                  }
                    $table_popup .='<tr><td> '.$fieldData[$value].'</td><td>'.$upload.'</td></tr>';

                } else {
                  $fieldVal = implode(", ",$form_data[$value]);

                 if($leadscount >= $count){
                 $table_row  .= '<td>'.$fieldVal.'</td>';
                  }
                 $table_popup .='<tr><td> '.$fieldData[$value].'</td><td>'.$fieldVal.'</td></tr>';
               }
                } else {

                if($leadscount >= $count){
               $table_row  .= (isset($form_data[$value]))?'<td>'.$form_data[$value].'</td>':'<td> - </td>';
                }

               $table_popup .=(isset($form_data[$value]))?'<tr><td> '.$fieldData[$value].'</td><td>'.$form_data[$value].'</td></tr>':'<tr><td> '.$fieldData[$value].'</td><td> - </td></tr>';
               }
               $count++;
            }

            $return = array('table_row'=>$table_row, 'table_popup' => $table_popup);
             return $return;
    }

function lfb_form_field_filter($form_data){
         $filterForm  = maybe_unserialize($form_data[0]->form_data);
        $arrayForm = array();
        foreach($filterForm as $field){
                $fieldArr   =  isset($field['field_type'])?$field['field_type']:'-';
                $fieldName  =  isset($field['field_name'])?$field['field_name']:'-';
                $fieldId    = $fieldArr['type'].'_'.$field['field_id'];

                if($fieldArr['type']=='submit') continue;

                $arrayForm[$fieldId] = $fieldName ; 
            }

            return $arrayForm;
    }

    function lfb_post_count($form_id){
            global $wpdb;
            $table_name = LFB_FORM_DATA_TBL;

    $rows = $wpdb->get_var(" SELECT COUNT(*) FROM $table_name WHERE form_id =  $form_id");

    return $rows;

    }

function lfb_get_all_view_leads_db($form_id,$start){
         global $wpdb;
         $table_name = LFB_FORM_DATA_TBL;
         $limit = 10;

         // form field filter
          $form_data  = $this->lfb_get_form_data($form_id);
          $fieldArr = $this->lfb_form_field_filter($form_data);

         $prepare_19 = $wpdb->prepare(" SELECT * FROM $table_name WHERE form_id = %d ORDER BY id DESC LIMIT $start , $limit ", $form_id);

         $posts = $this->lfb_get_form_content($prepare_19);
         $rows  = $this->lfb_post_count($form_id);
              $return = array('posts'=>$posts,'rows'=>$rows, 
                 'limit' => $limit, 'fieldId'=> $fieldArr);
        return $return;
    }

function lfb_get_all_view_date_leads_db($form_id,$leadtype,$start=0){
        global $wpdb;
        $table_name = LFB_FORM_DATA_TBL;
        $limit = 10;

        // form field filter
            $form_data  = $this->lfb_get_form_data($form_id);
            $fieldArr = $this->lfb_form_field_filter($form_data);

        if($leadtype=="total_leads"){
        $prepare_19 = $wpdb->prepare(" SELECT * FROM $table_name WHERE form_id = %d ORDER BY id DESC LIMIT $start , $limit ", $form_id);
        $prepare_20 = $wpdb->prepare(" SELECT * FROM $table_name WHERE form_id = %d ", $form_id);
        $posts = $this->lfb_get_form_content($prepare_19);
        $rows = $this->lfb_get_form_content($prepare_20); 

        } else if($leadtype=="today_leads"){

        $today_date= date('Y/m/d');
        $newDate = date("Y/m/d H:i:s", strtotime($today_date));

        $prepare_21 = $wpdb->prepare("SELECT * FROM $table_name WHERE date > %s and form_id = %d ORDER BY id DESC LIMIT $start , $limit ", $newDate, $form_id );
        $prepare_22 = $wpdb->prepare("SELECT * FROM $table_name WHERE date > %s and form_id = %d ", $newDate, $form_id );
        $posts = $this->lfb_get_form_content($prepare_21);
        $rows = $this->lfb_get_form_content($prepare_22); 
        }
             $return = array('posts'=>$posts,'rows'=>$rows, 
                'limit' => $limit, 'fieldId'=> $fieldArr);
        return $return;
    }

   function lfb_admin_email_send($form_id){
     // form field filter
          $form_data  = $this->lfb_get_form_data($form_id);
          $fieldArr = $this->lfb_form_field_filter($form_data);
          return $fieldArr; 

   } 

   function lfb_mail_store_type($form_id){
          $table_name = LFB_FORM_FIELD_TBL;

    $query =  $this->thdb->prepare( "SELECT storeType, mail_setting, usermail_setting FROM $table_name WHERE id= %d LIMIT 1",$form_id );
      $posts = $this->lfb_get_form_content($query);

      return $posts;
   }

// xml form data and color options import
function lfb_save_xml_formdata($form){

   $query = $this->thdb->query( $this->thdb->prepare( 
    "INSERT INTO $this->tbl_leadform ( form_title, form_data, date, multiData, storeType ) VALUES ( %s, %s, %s, %s, %d  )",
    $form['form_title'], $form['form_data'] ,date('Y-m-d g:i:s'), $form['multiData'], $form['storeType']));

   return $this->thdb->insert_id;
}

function lfb_save_xml_colordata($formid,$colorData){
  $query = $this->thdb->query( $this->thdb->prepare( "INSERT INTO $this->tbl_options ( fid, colorData) VALUES ( %d, %s )",$formid,$colorData));
}

}