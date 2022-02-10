<?php

namespace WebpConverter\Notice;

use WebpConverter\Service\OptionsAccessManager;
use WebpConverter\Settings\Page\PageIntegration;

/**
 * Supports notice displayed after plugin installation.
 */
class WelcomeNotice extends NoticeAbstract implements NoticeInterface {

	const NOTICE_OPTION    = 'webpc_is_new_installation';
	const NOTICE_VIEW_PATH = 'components/notices/welcome.php';

	/**
	 * {@inheritdoc}
	 */
	public function get_option_name(): string {
		return self::NOTICE_OPTION;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value(): string {
		return '1';
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available(): bool {
		return ( ! isset( $_GET['page'] ) || ( $_GET['page'] !== PageIntegration::ADMIN_MENU_PAGE ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_active(): bool {
		return ( OptionsAccessManager::get_option( $this->get_option_name() ) === $this->get_default_value() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_disable_value(): string {
		return '0';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_output_path(): string {
		return self::NOTICE_VIEW_PATH;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_vars_for_view(): array {
		return [
			'settings_url' => PageIntegration::get_settings_page_url(),
		];
	}
}
