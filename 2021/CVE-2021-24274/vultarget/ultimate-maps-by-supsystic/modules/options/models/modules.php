<?php
class modulesModelUms extends modelUms {
   public function get($d = array()) {
      global $wpdb;
      $res = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ums_modules AS sup_m WHERE code = %s", $d['code']) , ARRAY_A);
      return $res;
   }
   public function put($d = array()) {
      $res = new responseUms();
      $id = $this->_getIDFromReq($d);
      $d = prepareParamsUms($d);
      if (is_numeric($id) && $id) {
         if (isset($d['active'])) $d['active'] = ((is_string($d['active']) && $d['active'] == 'true') || $d['active'] == 1) ? 1 : 0;
         global $wpdb;
         $tableName = $wpdb->prefix . "ums_modules";
         $data_where = array(
            'id' => $id
         );
         $res = $wpdb->update($tableName, $d, $data_where);
         if ($res) {
            $mod = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_modules WHERE " . $wpdb->prepare("id = %s", $id) , ARRAY_A);
            $mod = !empty($mod) ? $mod : false;
            if (is_array($mod) && !isset($mod['type_id'])) {
               $mod = $mod[0];
            }
            if ($mod) {
               $newType = $wpdb->get_results("SELECT label FROM {$wpdb->prefix}ums_modules_type WHERE " . $wpdb->prepare("id = %s", $mod['type_id']) , ARRAY_A);
               if (is_array($newType) && !isset($newType['label'])) {
                  $newType = $newType[0];
               }
               $newType = $newType['label'];
            }
         }
         else {
            if ($tableErrors = frameUms::_()->getTable('modules')->getErrors()) {
               $res->errors = array_merge($res->errors, $tableErrors);
            }
            else $res->errors[] = __('Module Update Failed', UMS_LANG_CODE);
         }
      }
      else {
         $res->errors[] = __('Error module ID', UMS_LANG_CODE);
      }
      return $res;
   }
   protected function _getIDFromReq($d = array()) {
      $id = 0;
      if (isset($d['id'])) $id = $d['id'];
      elseif (isset($d['code'])) {
         $fromDB = $this->get(array(
            'code' => $d['code']
         ));
         if (isset($fromDB[0]) && $fromDB[0]['id']) $id = $fromDB[0]['id'];
      }
      return $id;
   }
}
