<?php
class modulesModelCfs extends modelCfs {
	public function __construct() {
		$this->_setTbl('modules');
	}
    public function get($d = array()) {
        if(isset($d['id']) && $d['id'] && is_numeric($d['id'])) {
            $fields = frameCfs::_()->getTable('modules')->fillFromDB($d['id'])->getFields();
            $fields['types'] = array();
            $types = frameCfs::_()->getTable('modules_type')->fillFromDB();
            foreach($types as $t) {
                $fields['types'][$t['id']->value] = $t['label']->value;
            }
            return $fields;
        } elseif(!empty($d)) {
            $data = frameCfs::_()->getTable('modules')->get('*', $d);
            return $data;
        } else {
            return frameCfs::_()->getTable('modules')
                ->innerJoin(frameCfs::_()->getTable('modules_type'), 'type_id')
                ->getAll(frameCfs::_()->getTable('modules')->alias().'.*, '. frameCfs::_()->getTable('modules_type')->alias(). '.label as type');
        }
    }
		public function put($d = array()) {
        $res = new responseCfs();
        $id = $this->_getIDFromReq($d);
        $d = prepareParamsCfs($d);
        if(is_numeric($id) && $id) {
            if(isset($d['active']))
                $d['active'] = ((is_string($d['active']) && $d['active'] == 'true') || $d['active'] == 1) ? 1 : 0;           //mmm.... govnokod?....)))
           /* else
                 $d['active'] = 0;*/
						global $wpdb;
				 		$tableName = $wpdb->prefix . "cfs_modules";
				 		$data_where = array('id' => $id);
				 		$res = $wpdb->update($tableName , $d, $data_where);
            if($res) {
                //$res->messages[] = __('Module Updated', UMS_LANG_CODE);
								$mod = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cfs_modules WHERE ". $wpdb->prepare("id = %s", $id), ARRAY_A);
								$mod = !empty($mod) ? $mod : false;
								if (is_array($mod) && !isset($mod['type_id'])) {
									$mod = $mod[0];
								}
								if ($mod) {
									$newType = $wpdb->get_results("SELECT label FROM {$wpdb->prefix}cfs_modules_type WHERE ". $wpdb->prepare("id = %s", $mod['type_id']), ARRAY_A);
									if (is_array($newType) && !isset($newType['label'])) {
										$newType = $newType[0];
									}
									$newType = $newType['label'];
								}
								// $newType = frameCfs::_()->getTable('modules_type')->supGetById($mod['type_id'], 'label');
								// $newType = $newType['label'];
                //$mod = frameCfs::_()->getTable('modules')->supGetById($id);
                // $res['data'] = array(
                //     'id' => $id,
                //     'label' => $mod['label'],
                //     'code' => $mod['code'],
                //     'type' => $newType,
                //     'active' => $mod['active'],
                // );
            } else {
                if($tableErrors = frameCfs::_()->getTable('modules')->getErrors()) {
                    $res->errors = array_merge($res->errors, $tableErrors);
                } else
                    $res->errors[] = __('Module Update Failed', CFS_LANG_CODE);
            }
        } else {
            $res->errors[] = __('Error module ID', CFS_LANG_CODE);
        }
        return $res;
    }
    protected function _getIDFromReq($d = array()) {
        $id = 0;
        if(isset($d['id']))
            $id = $d['id'];
        elseif(isset($d['code'])) {
						global $wpdb;
						$res = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}cfs_modules WHERE code = %s", $d['code']));
						if ($res) {
							$id = $res;
						}
        }
        return $id;
    }
}
