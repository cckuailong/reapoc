<?php
class iconsModelUms extends modelUms {
   /* public static $tableObj;
    function __construct() {
        if(empty(self::$tableObj)){
            self::$tableObj=  frameUms::_()->getTable("icons");
        }
    }*/
	public function checkDefIcons() {
		if(!get_option(UMS_CODE. '_def_icons_installed') ){
			$this->setDefaultIcons();
		}
	}
	public function getIconsByIds($ids) {
		//$icons = frameUms::_()->getTable('icons')->get('*', array('additionalCondition' => 'id IN ('. implode(',', $ids). ')'));
		global $wpdb;
		$ids = implode(',', array_map('absint', $ids));
		$icons = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ums_icons WHERE id IN (%1s)", $ids), ARRAY_A);
        if(empty($icons) ){
			return $icons ;
        }
		if(!empty($icons)) {
			$iconsArr = array();
			foreach($icons as $i => $icon){
				$icon['path'] = $this->getIconUrl($icon['path']);
				$iconsArr[$icon['id']] = $icon;
			}
		}
        return $iconsArr;
	}
    public function getIcons($params = array()) {
				global $wpdb;
				$res = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_icons AS ums_icons", ARRAY_A);
				if (empty($res) && !$res) {
					return $res;
				}
				$iconsArr = array();
				foreach($res as $icon){
            $icon['path'] = $this->getIconUrl($icon['path']);
            $iconsArr[$icon['id']] = $icon;
        }
        return $iconsArr;
    }
    public function saveNewIcon($params){
        if(!isset($params['url'])){
            $this->pushError(__("Icon no found", UMS_LANG_CODE));
            return false;
        }
        $url = $params['url'];
        //$exists = frameUms::_()->getTable('icons')->get("*", "`path`='".$url."'");
				global $wpdb;
        $exists = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_icons WHERE " . $wpdb->prepare("path = %s", $url), ARRAY_A);
        if(!empty($exists)){
            return $exists[0]['id'];
        }
				$tableName = $wpdb->prefix . "ums_icons";
				return $wpdb->insert($tableName, array(
						'path' => $url,
						'title' => $params['title'],
						'description' => $params['description'],
						'width' => $params['width'],
						'height' => $params['height'],
				));
        // return frameUms::_()->getTable('icons')->insert(array(
				// 	'path' => $url,
				// 	'title' => $params['title'],
				// 	'description' => $params['description'],
				// 	'width' => $params['width'],
				// 	'height' => $params['height'],
				// ));
    }
   /* public function getIconsPath(){
        return 'icons_files/def_icons/';
    }*/
	private function _getIconsDir() {
		return UMS_CODE. '_icons';
	}
	private function _getDefIconsDir() {
		return 'icons_files/def_icons/';
	}
    public function getIconsFullDir(){
        static $uplDir = '';
		if(empty($uplDir))
			$uplDir = wp_upload_dir();
        $modPath = $this->getModule()->getModPath();
        $path  = $modPath. $this->_getDefIconsDir();
        return $path;
    }

    public function getIconsFullPath(){
        $uplDir = wp_upload_dir();
        $path = $uplDir['basedir']. $this->_getIconsDir();
        return $path;
    }
    public function setDefaultIcons(){
		$jsonFile = frameUms::_()->getModule('icons')->getModDir(). 'icons_files/icons.json';
		$icons = utilsUms::jsonDecode(file_get_contents($jsonFile));
		$uplDir = wp_upload_dir();
		wp_mkdir_p($uplDir['basedir']. DS. $this->_getIconsDir());
        $qItems = array();
        foreach($icons as $icon){
					$size = $this->_getIconSize($this->_getIconPath($icon['img'], true));
					global $wpdb;
					$tableName = $wpdb->prefix . "ums_icons";
					$wpdb->insert($tableName, array(
							'title' => $icon['title'],
							'description' => $icon['description'],
							'path' => $icon['img'],
							'width' => (int)$size[0],
							'height' => (int)$size[1],
							'is_def' => 1,
					));
				}
		update_option(UMS_CODE. '_def_icons_installed', true);
    }
	private function _getIconPath($iconName, $isDef = false) {
		if($isDef) {
			return $this->getModule()->getModDir(). $this->_getDefIconsDir(). $iconName;
		}
	}
	private function _getIconSize($iconFile) {
		if(function_exists('getimagesize')) {
			return getimagesize($iconFile);
		}
		return array(0, 0);
	}


    public function downloadIconFromUrl($url){
        $filename = basename($url);
        if(empty($filename)){
            $this->pushError(__('File not found', UMS_LANG_CODE));
            return false;
        }
        $imageinfo = getimagesize ( $url,$imgProp );
        if(empty($imageinfo)){
            $this->pushError(__('Cannot get image', UMS_LANG_CODE));
            return false;
        }
        $fileExt = str_replace("image/","",$imageinfo['mime']);
        $filename = utilsUms::getRandStr(8).".".$fileExt;
        $dest = $this->getIconsFullPath().$filename;
        file_put_contents($dest, fopen($url, 'r'));
        $newIconId = frameUms::_()->getTable('icons')->store(array('path'=>$filename),"insert");
        if($newIconId){
           return array('id'=>$newIconId,'path'=>$this->getIconsFullDir().$filename);
        }else{
            $this->pushError(__('cannot insert to table', UMS_LANG_CODE));
            return false;
        }
    }

	public function getIconFromId($id){
		global $wpdb;
		$res = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_icons WHERE " . $wpdb->prepare("id = %s", $id), ARRAY_A);
		//$res = frameUms::_()->getTable('icons')->get('*', array('id' => $id));
		if(empty($res)){
			return $res;
		}
		$icon = $res[0];
		$icon['path'] = $this->getIconUrl($icon['path']);
		return $icon;
	}
	function getIconUrl($icon){
		if(!empty($icon)){
			$isUrl = strpos($icon, 'http');
			if($isUrl === false){
				$isWpContent = strpos($icon, 'wp-content/uploads');
				if ($isWpContent === false) {
					$icon = $this->getIconsFullDir(). $icon;
				} else {
					$icon = $icon;
				}
			}
			if(uriUms::isHttps()) {
				$icon = uriUms::makeHttps($icon);
			}
		}
		return $icon;
	}
	public function iconExists($iconId) {
		global $wpdb;
		return $res = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ums_icons WHERE " . $wpdb->prepare("id = %s", $iconId), ARRAY_A);
		//return frameUms::_()->getTable('icons')->exists($iconId, 'id');
	}
	public function remove($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if($d['id']) {
			if(frameUms::_()->getTable('icons')->delete(array('id' => $d['id']))) {
				$this->replaceDeletedIconIdToDefault($d['id']);
				return true;
			} else
				$this->pushError (frameUms::_()->getTable('icons')->getErrors());
		} else
			$this->pushError (__('Invalid ID', UMS_LANG_CODE));
		return false;
	}
	public function replaceDeletedIconIdToDefault($idIcon){
		if(frameUms::_()->getModule('marker')->getModel()->replaceDeletedIconIdToDefault($idIcon)) {
			return true;
		} else {
			$this->pushError (frameUms::_()->getTable('icons')->getErrors());
		}
		return false;
	}

}
