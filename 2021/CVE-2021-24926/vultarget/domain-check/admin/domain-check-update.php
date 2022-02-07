<?php

class DomainCheckUpdate {

	public static function init($old_version = null) {
		global $wpdb;

		$current_version = get_option(DomainCheckConfig::OPTIONS_PREFIX . 'version');
		//better be sure you know what you're doing
		if ($old_version !== null) {
			$current_version = $old_version;
		}

		if ($current_version != DomainCheck::PLUGIN_VERSION && version_compare($current_version, DomainCheck::PLUGIN_VERSION) === (-1) ) {
			//error_log('upgrading version ' . $current_version . ' to ' . DomainCheck::PLUGIN_VERSION);

			//1.0.8 releaase
			if (version_compare($current_version, '1.0.8') === (-1)) {
				if (!self::column_exists(DomainCheck::$db_prefix . '_domains', 'owner')) {
					//column does not exist...
					$sql = 'ALTER TABLE ' . DomainCheck::$db_prefix . '_domains ADD COLUMN owner varchar(255) DEFAULT NULL AFTER domain_expires';
					$wpdb->query($sql);
				}
				if (!self::column_exists(DomainCheck::$db_prefix . '_ssl', 'owner')) {
					//column does not exist...
					$sql = 'ALTER TABLE ' . DomainCheck::$db_prefix . '_ssl ADD COLUMN owner varchar(255) DEFAULT NULL AFTER domain_expires';
					$wpdb->query($sql);
				}
			}

			//1.0.15 release
			if ( version_compare($current_version, '1.0.15') === (-1) ) {
				if ( !self::column_exists( DomainCheck::$db_prefix . '_domains', 'domain_extension' ) ) {
					//column does not exist...
					$sql = 'ALTER TABLE ' . DomainCheck::$db_prefix . '_domains ADD COLUMN domain_extension varchar(255) DEFAULT null AFTER domain_url';
					$wpdb->query($sql);
				}
				if ( !self::column_exists( DomainCheck::$db_prefix . '_domains', 'registrar' ) ) {
					//column does not exist...
					$sql = 'ALTER TABLE ' . DomainCheck::$db_prefix . '_domains ADD COLUMN registrar int(11) DEFAULT 0 AFTER domain_expires';
					$wpdb->query($sql);
				}
				if ( !self::column_exists( DomainCheck::$db_prefix . '_domains', 'nameserver' ) ) {
					//column does not exist...
					$sql = 'ALTER TABLE ' . DomainCheck::$db_prefix . '_domains ADD COLUMN nameserver varchar(255) DEFAULT NULL AFTER registrar';
					$wpdb->query($sql);
				}
				if ( !self::column_exists( DomainCheck::$db_prefix . '_domains', 'autorenew' ) ) {
					//column does not exist...
					$sql = 'ALTER TABLE ' . DomainCheck::$db_prefix . '_domains ADD COLUMN autorenew TINYINT DEFAULT 0 NOT NULL AFTER nameserver';
					$wpdb->query($sql);
				}
			}

			update_option(DomainCheckConfig::OPTIONS_PREFIX . 'version', DomainCheck::PLUGIN_VERSION);

			DomainCheckAdmin::admin_notices_add(
				'Successfully updated <a href="http://domaincheckplugin.com" target="_blank">Domain Check</a> from version ' . $current_version . ' to version ' . DomainCheck::PLUGIN_VERSION . '!',
				'updated',
				null,
				'circle-check'
			);

		} else {
			if (version_compare($current_version, DomainCheck::PLUGIN_VERSION) === 1) {
				DomainCheckAdmin::admin_notices_add(
					'Your plugin is out of date! Your plugin code is version <strong>' . DomainCheck::PLUGIN_VERSION .'</strong> but your database requires at least version <strong>' . $current_version . '</strong><br><br>Get the latest version at <a href="http://domaincheckplugin.com" target="_blank">http://domaincheckplugin.com</a>.',
					'error',
					null,
					'circle-x'
				);
			}
		}
	}

	public static function column_exists($db_table, $db_column) {
		global $wpdb;

		$sql = 'SHOW COLUMNS FROM ' . $db_table . ' LIKE \'' . $db_column . '\'';
		$result = $wpdb->get_results($sql);

		if (!count($result)) {
			return false;
		} else {
			return true;
		}
	}

}