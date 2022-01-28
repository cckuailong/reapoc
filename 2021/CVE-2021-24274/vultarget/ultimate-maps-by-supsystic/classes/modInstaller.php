<?php
class modInstallerUms {
   static private $_current = array();
   static public function install($module, $path) {
      $exPlugDest = explode('plugins', $path);
      if (!empty($exPlugDest[1])) {
         $module['ex_plug_dir'] = str_replace(DS, '', $exPlugDest[1]);
      }
      $path = $path . DS . $module['code'];
      if (!empty($module) && !empty($path) && is_dir($path)) {
         if (self::isModule($path)) {
            $filesMoved = false;
            if (empty($module['ex_plug_dir'])) $filesMoved = self::moveFiles($module['code'], $path);
            else $filesMoved = true;
            if ($filesMoved) {
               if (frameUms::_()->getTable('modules')->exists($module['code'], 'code')) {
                  frameUms::_()->getTable('modules')->delete(array(
                     'code' => $module['code']
                  ));
               }
               if ($module['code'] != 'license') $module['active'] = 0;
               global $wpdb;
               $tableName = $wpdb->prefix . "ums_modules";
               $res = $wpdb->insert($tableName, $module);
               self::_runModuleInstall($module);
               self::_installTables($module);
               return true;
            }
            else {
               errorsUms::push(sprintf(__('Move files for %s failed') , $module['code']) , errorsUms::MOD_INSTALL);
            }
         }
         else errorsUms::push(sprintf(__('%s is not plugin module') , $module['code']) , errorsUms::MOD_INSTALL);
      }
      return false;
   }
   static protected function _runModuleInstall($module, $action = 'install') {
      $moduleLocationDir = UMS_MODULES_DIR;
      if (!empty($module['ex_plug_dir'])) $moduleLocationDir = utilsUms::getPluginDir($module['ex_plug_dir']);
      if (is_dir($moduleLocationDir . $module['code'])) {
         if (!class_exists($module['code'] . strFirstUp(UMS_CODE))) {
            importClassUms($module['code'], $moduleLocationDir . $module['code'] . DS . 'mod.php');
         }
         $moduleClass = toeGetClassNameUms($module['code']);
         $moduleObj = new $moduleClass($module);
         if ($moduleObj) {
            $moduleObj->$action();
         }
      }
   }
   static public function isModule($path) {
      return true;
   }
   static public function moveFiles($code, $path) {
      if (!is_dir(UMS_MODULES_DIR . $code)) {
         if (mkdir(UMS_MODULES_DIR . $code)) {
            utilsUms::copyDirectories($path, UMS_MODULES_DIR . $code);
            return true;
         }
         else errorsUms::push(__('Can not create module directory. Try to set permission to ' . UMS_MODULES_DIR . ' directory 755 or 777', UMS_LANG_CODE) , errorsUms::MOD_INSTALL);
      }
      else return true;
      return false;
   }
   static private function _getPluginLocations() {
      $locations = array();
      $plug = reqUms::getVar('plugin');
      if (empty($plug)) {
         $plug = reqUms::getVar('checked');
         $plug = $plug[0];
      }
      $locations['plugPath'] = plugin_basename(trim($plug));
      $locations['plugDir'] = dirname(WP_PLUGIN_DIR . DS . $locations['plugPath']);
      $locations['plugMainFile'] = WP_PLUGIN_DIR . DS . $locations['plugPath'];
      $locations['xmlPath'] = $locations['plugDir'] . DS . 'install.xml';
      $locations['extendModPath'] = $locations['plugDir'] . DS . 'install.php';
      return $locations;
   }
   static private function _getModulesFromXml($xmlPath) {
      if ($xml = utilsUms::getXml($xmlPath)) {
         if (isset($xml->modules) && isset($xml->modules->mod)) {
            $modules = array();
            $xmlMods = $xml->modules->children();
            foreach ($xmlMods->mod as $mod) {
               $modules[] = $mod;
            }
            if (empty($modules)) errorsUms::push(__('No modules were found in XML file', UMS_LANG_CODE) , errorsUms::MOD_INSTALL);
            else return $modules;
         }
         else errorsUms::push(__('Invalid XML file', UMS_LANG_CODE) , errorsUms::MOD_INSTALL);
      }
      else errorsUms::push(__('No XML file were found', UMS_LANG_CODE) , errorsUms::MOD_INSTALL);
      return false;
   }
   static private function _getExtendModules($locations) {
      $modules = array();
      $isExtendModPath = file_exists($locations['extendModPath']);
      $modulesList = $isExtendModPath ? include $locations['extendModPath'] : self::_getModulesFromXml($locations['xmlPath']);
      if (!empty($modulesList)) {
         foreach ($modulesList as $mod) {
            $modData = $isExtendModPath ? $mod : utilsUms::xmlNodeAttrsToArr($mod);
            array_push($modules, $modData);
         }
         if (empty($modules)) errorsUms::push(__('No modules were found in installation file', UMS_LANG_CODE) , errorsUms::MOD_INSTALL);
         else return $modules;
      }
      else errorsUms::push(__('No installation file were found', UMS_LANG_CODE) , errorsUms::MOD_INSTALL);
      return false;
   }
   static public function check($extPlugName = '') {
      $locations = self::_getPluginLocations();
      if ($modules = self::_getExtendModules($locations)) {
         foreach ($modules as $m) {
            if (!empty($m)) {
               if (frameUms::_()->getTable('modules')->exists($m['code'], 'code')) {
                  self::activate($m);
               }
               else {
                  if (!self::install($m, $locations['plugDir'])) {
                     errorsUms::push(sprintf(__('Install %s failed') , $m['code']) , errorsUms::MOD_INSTALL);
                  }
               }
            }
         }
      }
      else errorsUms::push(__('Error Activate module', UMS_LANG_CODE) , errorsUms::MOD_INSTALL);
      if (errorsUms::haveErrors(errorsUms::MOD_INSTALL)) {
         self::displayErrors();
         return false;
      }
      update_option(UMS_CODE . '_full_installed', 1);
      return true;
   }
   static public function checkActivationMessages() {
   }
   static public function deactivate() {
      $locations = self::_getPluginLocations();
      if ($modules = self::_getExtendModules($locations)) {
         foreach ($modules as $m) {
            if (frameUms::_()->moduleActive($m['code'])) {
               global $wpdb;
               $tableName = $wpdb->prefix . "ums_modules";
               $id = frameUms::_()->getModule($m['code'])->getID();
               $data = array(
                  'id' => $id,
                  'active' => 0,
               );
               $data_where = array(
                  'id' => $id
               );
               $res = $wpdb->update($tableName, $data, $data_where);
               if (!$res) {
                  errorsUms::push(__('Error Deactivation module', UMS_LANG_CODE) , errorsUms::MOD_INSTALL);
               }
            }
         }
      }
      if (errorsUms::haveErrors(errorsUms::MOD_INSTALL)) {
         self::displayErrors(false);
         return false;
      }
      return true;
   }
   static public function activate($modDataArr) {
      if (!empty($modDataArr['code']) && !frameUms::_()->moduleActive($modDataArr['code'])) {
         $res = frameUms::_()->getModule('options')->getModel('modules')->put(array(
            'code' => $modDataArr['code'],
            'active' => 1,
         ));
         if (!$res) {
            errorsUms::push(__('Error Activating module', UMS_LANG_CODE) , errorsUms::MOD_INSTALL);
         }
         else {
            $dbModData = frameUms::_()->getModule('options')->getModel('modules')->get(array(
               'code' => $modDataArr['code']
            ));
            if (!empty($dbModData) && !empty($dbModData[0])) {
               $modDataArr['ex_plug_dir'] = $dbModData[0]['ex_plug_dir'];
            }
            self::_runModuleInstall($modDataArr, 'activate');
         }
      }
   }
   static public function displayErrors($exit = true) {
      $errors = errorsUms::get(errorsUms::MOD_INSTALL);
      foreach ($errors as $e) {
         echo '<b style="color: red;">' . $e . '</b><br />';
      }
      if ($exit) exit();
   }
   static public function uninstall() {
      $locations = self::_getPluginLocations();
      if ($modules = self::_getExtendModules($locations)) {
         foreach ($modules as $m) {
            self::_uninstallTables($m);
            frameUms::_()->getModule('options')->getModel('modules')->delete(array(
               'code' => $m['code']
            ));
            utilsUms::deleteDir(UMS_MODULES_DIR . $m['code']);
         }
      }
   }
   static protected function _uninstallTables($module) {
      if (is_dir(UMS_MODULES_DIR . $module['code'] . DS . 'tables')) {
         $tableFiles = utilsUms::getFilesList(UMS_MODULES_DIR . $module['code'] . DS . 'tables');
         if (!empty($tableNames)) {
            foreach ($tableFiles as $file) {
               $tableName = str_replace('.php', '', $file);
               if (frameUms::_()->getTable($tableName)) frameUms::_()->getTable($tableName)->uninstall();
            }
         }
      }
   }
   static public function _installTables($module, $action = 'install') {
      $modDir = empty($module['ex_plug_dir']) ? UMS_MODULES_DIR . $module['code'] . DS : utilsUms::getPluginDir($module['ex_plug_dir']) . $module['code'] . DS;
      if (is_dir($modDir . 'tables')) {
         $tableFiles = utilsUms::getFilesList($modDir . 'tables');
         if (!empty($tableFiles)) {
            frameUms::_()->extractTables($modDir . 'tables' . DS);
            foreach ($tableFiles as $file) {
               $tableName = str_replace('.php', '', $file);
               if (frameUms::_()->getTable($tableName)) frameUms::_()->getTable($tableName)->$action();
            }
         }
      }
   }
}
