<?php

namespace WebpConverter\Plugin\Uninstall;

use WebpConverter\Error\ErrorDetectorAggregator;
use WebpConverter\Notice\ThanksNotice;
use WebpConverter\Notice\WelcomeNotice;
use WebpConverter\Plugin\Update;
use WebpConverter\Repository\TokenRepository;
use WebpConverter\Service\OptionsAccessManager;
use WebpConverter\Settings\SettingsSave;

/**
 * Removes options saved by plugin.
 */
class PluginSettings {

	/**
	 * Removes options from wp_options table.
	 *
	 * @return void
	 */
	public static function remove_plugin_settings() {
		OptionsAccessManager::delete_option( ThanksNotice::NOTICE_OLD_OPTION );
		OptionsAccessManager::delete_option( ThanksNotice::NOTICE_OPTION );
		OptionsAccessManager::delete_option( WelcomeNotice::NOTICE_OPTION );
		OptionsAccessManager::delete_option( ErrorDetectorAggregator::ERRORS_CACHE_OPTION );
		OptionsAccessManager::delete_option( SettingsSave::SETTINGS_OPTION );
		OptionsAccessManager::delete_option( Update::VERSION_OPTION );
		OptionsAccessManager::delete_option( TokenRepository::TOKEN_OPTION );
	}
}
