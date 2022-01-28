<?php
class installerUms {
	static public $update_to_version_method = '';
	static public function init() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$current_version = get_option($wpPrefix. UMS_DB_PREF. 'db_version', 0);
		$installed = (int) get_option($wpPrefix. UMS_DB_PREF. 'db_installed', 0);
		/**
		 * modules
		 */
		// if (!dbUms::exist($wpPrefix.UMS_DB_PREF."modules")) {
		// 	dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.UMS_DB_PREF."modules` (
		// 	  `id` int(11) NOT NULL AUTO_INCREMENT,
		// 	  `code` varchar(64) NOT NULL,
		// 	  `active` tinyint(1) NOT NULL DEFAULT '0',
		// 	  `type_id` smallint(3) NOT NULL DEFAULT '0',
		// 	  `params` text,
		// 	  `has_tab` tinyint(1) NOT NULL DEFAULT '0',
		// 	  `label` varchar(128) DEFAULT NULL,
		// 	  `description` text,
		// 	  `ex_plug_dir` varchar(255) DEFAULT NULL,
		// 	  PRIMARY KEY (`id`),
		// 	  UNIQUE INDEX `code` (`code`)
		// 	) DEFAULT CHARSET=utf8;");
		if (!dbUms::exist("ums_modules")) {
			$charset_collate = $wpdb->get_charset_collate();
			$table_name = $wpdb->prefix . 'ums_modules';
			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
				 `id` int(11) NOT NULL AUTO_INCREMENT,
	 			 `code` varchar(64) NOT NULL,
	 			 `active` tinyint(1) NOT NULL DEFAULT '0',
	 			 `type_id` smallint(3) NOT NULL DEFAULT '0',
	 			 `params` text,
	 			 `has_tab` tinyint(1) NOT NULL DEFAULT '0',
	 			 `label` varchar(128) DEFAULT NULL,
	 			 `description` text,
	 			 `ex_plug_dir` varchar(255) DEFAULT NULL,
	 			 PRIMARY KEY (`id`),
	 			 UNIQUE INDEX `code` (`code`)
			) $charset_collate";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			$tableName = $wpdb->prefix . "ums_modules";
			$wpdb->insert($tableName, array(
					'code' => 'adminmenu',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 0,
					'label' => 'Admin Menu',
					'description' => '',
			));
			$wpdb->insert($tableName, array(
					'code' => 'options',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 0,
					'label' => 'Options',
					'description' => '',
			));
			$wpdb->insert($tableName, array(
					'code' => 'user',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 0,
					'label' => 'Users',
					'description' => '',
			));
			$wpdb->insert($tableName, array(
					'code' => 'templates',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 0,
					'label' => 'Templates for Plugin',
					'description' => '',
			));
			$wpdb->insert($tableName, array(
					'code' => 'maps',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 0,
					'label' => 'maps',
					'description' => 'maps',
			));
			$wpdb->insert($tableName, array(
					'code' => 'marker',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 0,
					'label' => 'Markers',
					'description' => 'Maps Markers',
			));
			$wpdb->insert($tableName, array(
					'code' => 'marker_groups',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 0,
					'label' => 'Marker Groups',
					'description' => 'Marker Groups',
			));
			$wpdb->insert($tableName, array(
					'code' => 'supsystic_promo',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 0,
					'label' => 'Promo',
					'description' => 'Promo',
			));
			$wpdb->insert($tableName, array(
					'code' => 'icons',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 1,
					'label' => 'Marker Icons',
					'description' => 'Marker Icons',
			));
			$wpdb->insert($tableName, array(
					'code' => 'csv',
					'active' => 1,
					'type_id' => 1,
					'params' => '',
					'has_tab' => 0,
					'label' => 'csv',
					'description' => '',
			));
		}
		/**
		 *  modules_type
		 */
		if(!dbUms::exist("ums_modules_type")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.UMS_DB_PREF."modules_type` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(64) NOT NULL,
			  PRIMARY KEY (`id`)
			) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;");
			$tableName = $wpdb->prefix . "ums_modules_type";
			$wpdb->insert($tableName, array(
					'id' => 1,
					'label' => 'system',
			));
			$wpdb->insert($tableName, array(
					'id' => 4,
					'label' => 'widget',
			));
			$wpdb->insert($tableName, array(
					'id' => 6,
					'label' => 'addons',
			));
			$wpdb->insert($tableName, array(
					'id' => 7,
					'label' => 'template',
			));
		}
		/**
		 * options
		 */
		if(!dbUms::exist("ums_options")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.UMS_DB_PREF."options` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) CHARACTER SET latin1 NOT NULL,
			  `value` text NULL,
			  `label` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
			  `description` text CHARACTER SET latin1,
			  `htmltype_id` smallint(2) NOT NULL DEFAULT '1',
			  `params` text NULL,
			  `cat_id` mediumint(3) DEFAULT '0',
			  `sort_order` mediumint(3) DEFAULT '0',
			  `value_type` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `id` (`id`),
			  UNIQUE INDEX `code` (`code`)
			) DEFAULT CHARSET=utf8");

			$tableName = $wpdb->prefix . "ums_options";
			$wpdb->insert($tableName, array(
					'code' => 'save_statistic',
					'value' => '0',
					'label' => 'Send statistic',
			));
			$wpdb->insert($tableName, array(
					'code' => 'infowindow_size',
					'value' => utilsUms::serialize(array('width'=>'100','height'=>'100')),
					'label' => 'Info Window Size',
			));
		}
		/* options categories */
		if(!dbUms::exist("ums_option_categories")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.UMS_DB_PREF."options_categories` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(128) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `id` (`id`)
			) DEFAULT CHARSET=utf8");

			$tableName = $wpdb->prefix . "ums_options_categories";
			if (empty($wpdb->get_var("SELECT ID FROM {$wpdb->prefix}ums_options_categories WHERE label = 'General'"))) {
				$wpdb->insert($tableName, array(
						'id' => 1,
						'label' => 'General',
				));
			}
			if (empty($wpdb->get_var("SELECT ID FROM {$wpdb->prefix}ums_options_categories WHERE label = 'Template'"))) {
				$wpdb->insert($tableName, array(
						'id' => 2,
						'label' => 'Template',
				));
			}
			if (empty($wpdb->get_var("SELECT ID FROM {$wpdb->prefix}ums_options_categories WHERE label = 'Subscribe'"))) {
				$wpdb->insert($tableName, array(
						'id' => 3,
						'label' => 'Subscribe',
				));
			}
			if (empty($wpdb->get_var("SELECT ID FROM {$wpdb->prefix}ums_options_categories WHERE label = 'Social'"))) {
				$wpdb->insert($tableName, array(
						'id' => 4,
						'label' => 'Social',
				));
			}
		}
		/*
		* Create table for map
		*/
        if(!dbUms::exist("ums_maps")) {
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.UMS_DB_PREF."maps` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`title` varchar(125) CHARACTER SET utf8  NOT NULL,
				`engine` varchar(32),
				`params` text NULL,
				`html_options` text NOT NULL,
				`create_date` datetime,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `id` (`id`)
			  ) DEFAULT CHARSET=utf8");
		}
		/**
		 * Create table for markers
		 */
		if(!dbUms::exist("ums_markers")){
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.UMS_DB_PREF."markers"."` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`title` varchar(125) CHARACTER SET utf8 NOT NULL,
					`description` text CHARACTER SET utf8 NULL,
					`coord_x` varchar(30) CHARACTER SET utf8 NOT NULL,
					`coord_y` varchar(30) CHARACTER SET utf8 NOT NULL,
					`icon` int(11),
					`map_id` int(11),
					`marker_group_id` int(11),
					`address` text CHARACTER SET utf8,
					`animation` int(1),
					`create_date` datetime,
					`params` text  CHARACTER SET utf8 NOT NULL,
					`sort_order` INT(11) NOT NULL DEFAULT '0',
					`user_id` int(11),
					PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
		}
		// if(!dbUms::exist('ums_markers', 'description')) {
		// 	$tableName = '`'.$wpdb->prefix . "ums_markers".'`';
		// 	$prepareQuery = $wpdb->prepare("ALTER TABLE %1s ADD COLUMN `description` text CHARACTER SET utf8 NULL", $tableName);
		// 	$wpdb->query($prepareQuery);
		// }
		/**
		 * Create table for marker Icons
		 */
		if(!dbUms::exist("ums_icons")){
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.UMS_DB_PREF."icons"."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`title` varchar(100) CHARACTER SET utf8,
				`description` text CHARACTER SET utf8,
				`path` varchar(250) CHARACTER SET utf8,
				`width` MEDIUMINT(5) NOT NULL DEFAULT '0',
				`height` MEDIUMINT(5) NOT NULL DEFAULT '0',
				`is_def` tinyint(1) NOT NULL DEFAULT '0',
				 PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8");
		}

		/**
		 * Create table for marker groups
		 */
		if(!dbUms::exist("ums_marker_groups")){
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.UMS_DB_PREF."marker_groups"."` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`title` varchar(250) CHARACTER SET utf8,
					`description` text CHARACTER SET utf8,
					`params` text CHARACTER SET utf8,
					`parent` tinyint(1) NOT NULL DEFAULT '0',
					`sort_order` tinyint(1) NOT NULL DEFAULT '0',
				 PRIMARY KEY (`id`)
				  ) DEFAULT CHARSET=utf8");
		}
		/**
		* Plugin usage statistics
		*/
		if(!dbUms::exist("ums_usage_stat")) {
			dbDelta("CREATE TABLE `".$wpPrefix.UMS_DB_PREF."usage_stat` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `code` varchar(64) NOT NULL,
			  `visits` int(11) NOT NULL DEFAULT '0',
			  `spent_time` int(11) NOT NULL DEFAULT '0',
			  `modify_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			  UNIQUE INDEX `code` (`code`),
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8");
			$tableName = $wpdb->prefix . "ums_usage_stat";
			$wpdb->insert($tableName, array(
					'code' => 'installed',
					'visits' => 1,
			));
		}
		/**
		 * Create table for marker groups
		 */
		if(!dbUms::exist("ums_marker_groups_relation")){
			dbDelta("CREATE TABLE IF NOT EXISTS `".$wpPrefix.UMS_DB_PREF."marker_groups_relation"."` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`marker_id` int(11) NOT NULL,
					`groups_id` int(11) NOT NULL,
				 PRIMARY KEY (`id`)
				  ) DEFAULT CHARSET=utf8");
		}
        update_option($wpPrefix. UMS_DB_PREF. 'db_version', UMS_VERSION_PLUGIN);
		add_option($wpPrefix. UMS_DB_PREF. 'db_installed', 1);

        installerDbUpdaterUms::runUpdate();
	}
	static public function setUsed() {
		update_option(UMS_DB_PREF. 'plug_was_used', 1);
	}
	static public function isUsed() {
		// No welcome page for now
		return true;
		return (bool)get_option(UMS_DB_PREF. 'plug_was_used');
	}
	static public function delete() {
		self::_checkSendStat('delete');
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$deleteOptions = false;
		if((bool)$deleteOptions){
		   $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ums_modules`");
		   $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ums_icons`");
		   $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ums_maps`");
		   $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ums_options`");
		   $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ums_markers`");
		   $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ums_marker_groups`");
		   $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ums_options_categories`");
		   $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ums_modules_type`");
		   $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}ums_usage_stat`");

		   delete_option(UMS_DB_PREF. 'db_version');
		   delete_option($wpPrefix.UMS_DB_PREF.'db_installed');
		   //delete_option(UMS_DB_PREF. 'plug_was_used');
		}
	}
	static public function deactivate() {
		self::_checkSendStat('deactivate');
	}
	static private function _checkSendStat($statCode) {
		if(class_exists('frameUms')
			&& frameUms::_()->getModule('supsystic_promo')
			&& frameUms::_()->getModule('options')
		) {
			frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat( $statCode );
			frameUms::_()->getModule('supsystic_promo')->getModel()->checkAndSend( true );
		}
	}
	static public function update() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$currentVersion = get_option($wpPrefix. UMS_DB_PREF. 'db_version', 0);
		$installed = (int) get_option($wpPrefix. UMS_DB_PREF. 'db_installed', 0);
		if(!$currentVersion || version_compare(UMS_VERSION_PLUGIN, $currentVersion, '>')) {
			self::init();
			update_option($wpPrefix. UMS_DB_PREF. 'db_version', UMS_VERSION_PLUGIN);
		}
	}
}
