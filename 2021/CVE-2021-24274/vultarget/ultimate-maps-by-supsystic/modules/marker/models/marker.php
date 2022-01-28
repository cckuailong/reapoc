<?php
class markerModelUms extends modelUms {
   public static $tableObj;
   function __construct() {
      $this->_setTbl('marker');
      if (empty(self::$tableObj)) {
         self::$tableObj = frameUms::_()->getTable('marker');
      }
   }
   public function save($marker = array() , &$update = false) {
      $id = isset($marker['id']) ? (int)$marker['id'] : 0;
      $marker['title'] = isset($marker['title']) ? trim($marker['title']) : '';
      $marker['coord_x'] = isset($marker['coord_x']) ? (float)$marker['coord_x'] : 0;
      $marker['coord_y'] = isset($marker['coord_y']) ? (float)$marker['coord_y'] : 0;
      if (!empty($marker['period_date_from']) && strtotime($marker['period_date_from'])) {
         $marker['period_from'] = date('Y-m-d', strtotime($marker['period_date_from']));
      }
      if (!empty($marker['period_date_to']) && strtotime($marker['period_date_to'])) {
         $marker['period_to'] = date('Y-m-d', strtotime($marker['period_date_to']));
      }
      $update = (bool)$id;
      if (!empty($marker['title'])) {
         $marker = apply_filters('ums_before_marker_save', $marker);
         if (isset($marker['description'])) {
            //Replace site url in markers descriptions backend. Fixed site migration issue.
            $marker['description'] = str_replace(UMS_SITE_URL, 'UMS_SITE_URL', $marker['description']);
         }
         if (!isset($marker['marker_group_id'])) {
            $marker['marker_group_id'] = 0;
         }
         if (!isset($marker['icon']) || !frameUms::_()->getModule('icons')
            ->getModel()
            ->iconExists($marker['icon'])) {
            $marker['icon'] = 1;
         }
         $marker['map_id'] = isset($marker['map_id']) ? (int)$marker['map_id'] : 0;
         if (!$update) {
            $marker['create_date'] = date('Y-m-d H:i:s');
            if ($marker['map_id']) {
               //$maxSortOrder = (int)dbUms::get('SELECT MAX(sort_order) FROM @__markers WHERE map_id = "' . $marker['map_id'] . '"', 'one');
               global $wpdb;
               $maxSortOrder = $wpdb->get_var("SELECT MAX(sort_order) FROM {$wpdb->prefix}ums_markers WHERE " . $wpdb->prepare("map_id = %s", $marker['map_id']));
               $marker['sort_order'] = ++$maxSortOrder;
            }
         }
         //save first groups value in markers table to better compatibility
         $markerGroupIds = $marker['marker_group_id'];
         $first_value = is_array($markerGroupIds) ? reset($markerGroupIds) : 0;
         $marker['marker_group_id'] = $first_value;

         $marker['params'] = isset($marker['params']) ? utilsUms::serialize($marker['params']) : '';
         if ($update) {
            dispatcherUms::doAction('beforeMarkerUpdate', $id, $marker);
            global $wpdb;
            $tableName = $wpdb->prefix . "ums_markers";
            $data_update = array(
                'title' => $marker['title'],
                'description' => $marker['description'],
                'icon' => $marker['icon'],
                'address' => $marker['address'],
                'coord_x' => $marker['coord_x'],
                'coord_y' => $marker['coord_y'],
                'params' => $marker['params'],
                'id' => $marker['id'],
                'map_id' => $marker['map_id'],
                'marker_group_id' => $marker['marker_group_id'],
            );
            if (!empty($marker['create_date'])) {
              $data_update['create_date'] = $marker['create_date'];
            }
            if (!empty($marker['sort_order'])) {
              $data_update['sort_order'] = $marker['sort_order'];
            }
            $data_where = array(
               'id' => $id
            );
            $dbRes = $wpdb->update($tableName, $data_update, $data_where);
            if ($dbRes) {
               $dbResId = $id;
            }

            $tableName = $wpdb->prefix . "ums_marker_groups_relation";
            $data_where = array(
               'marker_id' => $marker['id']
            );
            $wpdb->delete($tableName, $data_where);

            if (!empty($markerGroupIds)) {
               foreach ($markerGroupIds as $markerId) {
                  global $wpdb;
                  $tableName = $wpdb->prefix . "ums_marker_groups_relation";
                  $dbRes = $wpdb->insert($tableName, array(
                     'marker_id' => $marker['id'],
                     'groups_id' => $markerId,
                  ));
                  if ($dbRes) {
                     $dbResId = $wpdb->insert_id;;
                  }

               }
            }
            dispatcherUms::doAction('afterMarkerUpdate', $id, $marker);
         }
         else {
            dispatcherUms::doAction('beforeMarkerInsert', $marker);

            global $wpdb;
            $tableName = $wpdb->prefix . "ums_markers";
            $dbRes = $wpdb->insert($tableName, array(
               'title' => $marker['title'],
               'description' => $marker['description'],
               'icon' => $marker['icon'],
               'address' => $marker['address'],
               'coord_x' => $marker['coord_x'],
               'coord_y' => $marker['coord_y'],
               'params' => $marker['params'],
               'map_id' => $marker['map_id'],
               'marker_group_id' => $marker['marker_group_id'],
               'create_date' => $marker['create_date'],
               'sort_order' => !empty($marker['sort_order']) ? $marker['sort_order'] : '',
            ));
            if ($dbRes) {
               $dbResId = $wpdb->insert_id;;
            }
            if ($dbRes) {
               $tableName = $wpdb->prefix . "ums_marker_groups_relation";
               $data_where = array(
                  'marker_id' => $dbResId
               );
               $wpdb->delete($tableName, $data_where);
               if (!empty($markerGroupIds) && is_array($markerGroupIds)) {
                  foreach ($markerGroupIds as $markerId) {
                     $tableName = $wpdb->prefix . "ums_marker_groups_relation";
                     $wpdb->insert($tableName, array(
                        'marker_id' => $dbResId,
                        'groups_id' => $markerId,
                     ));
                  }
               }
            }
            dispatcherUms::doAction('afterMarkerInsert', $dbResId, $marker);
         }
         if ($dbResId) {
            if (!$update) {
               $id = $dbResId;
            }
            do_action('ums_save_lang_data', array(
               'type' => 'markers',
               'marker_id' => $id,
               'map' => frameUms::_()->getModule('maps')
                  ->getModel()
                  ->getMapById($marker['map_id']) ,
            ));
            return $id;
         }
         else {
            $this->pushError(frameUms::_()
               ->getTable('marker')
               ->getErrors());
         }
      }
      else {
         $this->pushError(__('Please enter marker name', UMS_LANG_CODE) , 'marker_opts[title]');
      }
      return false;
   }
   public function existsId($id) {
      if ($id) {
        global $wpdb;
        $marker = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ums_markers WHERE " . $wpdb->prepare("id = %s", $id), ARRAY_A);
         // $marker = frameUms::_()->getTable('marker')
         //    ->get('*', array(
         //    'id' => $id
         // ) , '', 'row');
         if (!empty($marker)) {
            return true;
         }
      }
      return false;
   }
   public function getById($id) {
      global $wpdb;
      $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ums_markers WHERE " . $wpdb->prepare("id = %s", $id), ARRAY_A);
      return $this->_afterGet($row);
    //   return $this->_afterGet(
    //     frameUms::_()
    //      ->getTable('marker')
    //      ->get('*', array(
    //      'id' => $id
    //   ) , '', 'row')
    // );
   }
   public function getMarkerByTitle($title) {
     global $wpdb;
     $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ums_markers WHERE " . $wpdb->prepare("title = %s", $title), ARRAY_A);
     return $this->_afterGet($row);
      // return $this->_afterGet(frameUms::_()
      //    ->getTable('marker')
      //    ->get('*', array(
      //    'title' => $title
      // ) , '', 'row'));
   }
   public function _afterGet($marker, $widthMapData = false, $withoutIcons = false) {
      if (!empty($marker)) {
         if (!$withoutIcons) {
            $marker['icon_data'] = frameUms::_()->getModule('icons')
               ->getModel()
               ->getIconFromId($marker['icon']);
         }
         $marker['params'] = utilsUms::unserialize($marker['params']);

         if (isset($marker['params']['marker_title_link']) && !empty($marker['params']['marker_title_link']) && strpos($marker['params']['marker_title_link'], 'http') !== 0) {
            $marker['params']['marker_title_link'] = 'http://' . $marker['params']['marker_title_link'];
         }
         if (!isset($marker['params']['title_is_link'])) $marker['params']['title_is_link'] = false;

         $siteUrl = uriUms::isHttps() ? uriUms::makeHttps(UMS_SITE_URL) : UMS_SITE_URL;
         // Go to absolute path as "../wp-content/" will not work on frontend
         $marker['description'] = str_replace('../wp-content/', $siteUrl . 'wp-content/', $marker['description']);
         //Replace site url in markers descriptions frontend.
         $marker['description'] = str_replace('UMS_SITE_URL', $siteUrl, $marker['description']);

         $marker['description'] = str_replace('\&quot;', '', $marker['description']);
         $marker['description'] = str_replace('\\', '', $marker['description']);

         if ($widthMapData && !empty($marker['map_id'])) $marker['map'] = frameUms::_()->getModule('maps')
            ->getModel()
            ->getMapById($marker['map_id'], false);

         if ($widthMapData && !empty($marker['map_id'])) $marker['map'] = frameUms::_()->getModule('maps')
            ->getModel()
            ->getMapById($marker['map_id'], false);

         if (!empty($marker['id'])) {
            $marker['marker_group_ids'] = frameUms::_()->getModule('marker')
               ->getModel('marker_groups_relation')
               ->getRelationsByMarkerId($marker['id']);
         }
      }
      return $marker;
   }
   public function getMapMarkers($mapId, $withGroup = false, $userId = false) {
      $mapId = (int)$mapId;
      $params = array(
         'map_id' => $mapId
      );
      if ($userId) {
         $params['user_id'] = $userId;
      }
      global $wpdb;
      $markers = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ums_markers AS toe_mr WHERE map_id = %s ORDER BY sort_order ASC", $mapId), ARRAY_A);
      if (!empty($markers)) {
         $iconIds = array();
         foreach ($markers as $i => $m) {
            $markers[$i] = $this->_afterGet($markers[$i], false, true);
            // We need to do shortcode only on frontend
            if (isset($markers[$i]["description"])) {
               ob_start();
               echo do_shortcode($markers[$i]["description"]);
               $out = ob_get_contents();
               ob_end_clean();
               $markers[$i]["description"] = $out;
            }
            $iconIds[$m['icon']] = 1;
         }
         $usedIcons = frameUms::_()->getModule('icons')
            ->getModel()
            ->getIconsByIds(array_keys($iconIds));
         foreach ($markers as $i => $m) {
            $markers[$i]['icon_data'] = isset($usedIcons[$m['icon']]) ? $usedIcons[$m['icon']] : '';
         }
      }
      return $markers;
   }
   public function getMapMarkersIds($mapId) {
     global $wpdb;
     $markers = $wpdb->get_col($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ums_markers WHERE map_id= %s", $mapId), ARRAY_A);
     return $markers;
   }
   public function getMarkersByIds($ids) {
      if (!is_array($ids)) $ids = array(
         $ids
      );
      $ids = array_map('intval', $ids);
      global $wpdb;
      $ids = implode(',', array_map('absint', $ids));
      $markers = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ums_markers WHERE id IN (%1s)", $ids), ARRAY_A);
      if (!empty($markers)) {
         foreach ($markers as $i => $m) {
            $markers[$i] = $this->_afterGet($markers[$i]);
         }
      }
      return $markers;
   }
   public function removeMarker($markerId) {
      dispatcherUms::doAction('beforeMarkerRemove', $markerId);
      global $wpdb;
      $tableName = $wpdb->prefix . "ums_markers";
      $data_where = array(
         'id' => $markerId
      );
      return $res = $wpdb->delete($tableName, $data_where);
   }
   public function removeList($ids) {
      $ids = array_map('intval', $ids);
      global $wpdb;
      foreach ($ids as $id) {
         $tableName = $wpdb->prefix . "ums_markers";
         $data_where = array(
            'id' => $id
         );
         $res = $wpdb->delete($tableName, $data_where);
      }
      if ($res) {
         return true;
      }
   }
   public function findAddress($params) {
      if (!isset($params['addressStr']) || strlen($params['addressStr']) < 3) {
         $this->pushError(__('Address is empty or not match', UMS_LANG_CODE));
         return false;
      }
      $addr = $params['addressStr'];
      $getdata = http_build_query(array(
         'address' => $addr,
         'language' => 'en',
         'sensor' => 'false',
      ));
      $apiDomain = frameUms::_()->getModule('maps')
         ->getView()
         ->getApiDomain();
      $google_response = utilsUms::jsonDecode(file_get_contents($apiDomain . 'maps/api/geocode/json?' . $getdata));
      $res = array();
      foreach ($google_response['results'] as $response) {
         $res[] = array(
            'position' => $response['geometry']['location'],
            'address' => $response['formatted_address'],
         );
      }
      return $res;
   }
   public function removeMarkersFromMap($mapId) {
      global $wpdb;
      $tableName = $wpdb->prefix . "ums_markers";
      $data_where = array(
         'map_id' => $mapId
      );
      return $res = $wpdb->delete($tableName, $data_where);
   }
   public function getAllMarkers($d = array() , $widthMapData = false) {
      global $wpdb;
      $markerList = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_markers", ARRAY_A);
      foreach ($markerList as $i => & $m) {
         $markerList[$i] = $this->_afterGet($markerList[$i], $widthMapData);
      }
      return $markerList;
   }
   public function getTotalCountBySearch($search) {
      global $wpdb;
      if (!empty($search)) {
         $count = (int)$wpdb->get_var("SELECT COUNT(*) AS total FROM {$wpdb->prefix}ums_markers " . $wpdb->prepare("(id = %s OR label = %s)", $search, $search));
      }
      else {
         $count = (int)$wpdb->get_var("SELECT COUNT(*) AS total FROM {$wpdb->prefix}ums_markers ");
      }
      return $count;
   }
   public function getListForTblBySearch($search, $limitStart, $rowsLimit, $mapId) {
      global $wpdb;
      if (!empty($search)) {
         $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_markers WHERE " . $wpdb->prepare(" map_id = %s AND (id = %s OR label = %s) ORDER BY id ASC LIMIT %1s,%1s", $mapId, $search, $search, (int)$limitStart, (int)$rowsLimit) , ARRAY_A);
      }
      else {
         $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_markers WHERE " . $wpdb->prepare(" map_id = %s ORDER BY id ASC LIMIT %1s,%1s", $mapId, (int)$limitStart, (int)$rowsLimit) , ARRAY_A);
      }
      foreach ($data as $i => & $m) {
         $data[$i] = $this->_afterGet($data[$i]);
      }
      return $data;
   }
   public function setMarkersToMap($addMarkerIds, $mapId) {
      if (!is_array($addMarkerIds)) $addMarkerIds = array(
         $addMarkerIds
      );
      $addMarkerIds = array_map('intval', $addMarkerIds);
      foreach ($addMarkerIds as $addMarkerId) {
         $tableName = $wpdb->prefix . "ums_markers";
         $data_update = array(
            'map_id' => (int)$mapId,
         );
         $data_where = array(
            'id' => $addMarkerId
         );
         $dbRes = $wpdb->update($tableName, $data_update, $data_where);
      }
      if ($dbRes) {
         return true;
      }
   }
   public function getCount($d = array()) {
      global $wpdb;
      return $count = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ums_markers ");
   }
   public function updatePos($d = array()) {
      global $wpdb;
      $d['id'] = isset($d['id']) ? (int)$d['id'] : 0;
      if ($d['id']) {
         $tableName = $wpdb->prefix . "ums_markers";
         $data_update = array(
            'coord_x' => $d['lat'],
            'coord_y' => $d['lng'],
         );
         $data_where = array(
            'id' => $d['id']
         );
         return $dbRes = $wpdb->update($tableName, $data_update, $data_where);
      }
      else $this->pushError(__('Invalid Marker ID'));
      return false;
   }
   public function replaceDeletedIconIdToDefault($id) {
      if ($id) {
         $tableName = $wpdb->prefix . "ums_markers";
         $data_update = array(
            'icon' => '1',
         );
         $data_where = array(
            'icon' => $id,
         );
         return $dbRes = $wpdb->update($tableName, $data_update, $data_where);
      }
      else $this->pushError(__('Invalid ID'));
      return false;
   }
}
