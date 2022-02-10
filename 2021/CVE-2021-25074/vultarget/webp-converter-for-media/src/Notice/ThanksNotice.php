<?php

namespace WebpConverter\Notice;

use WebpConverter\Service\OptionsAccessManager;

/**
 * Supports notice displayed as thank you for using plugin.
 */
class ThanksNotice extends NoticeAbstract implements NoticeInterface {

	const NOTICE_OPTION     = 'webpc_notice_thanks';
	const NOTICE_OLD_OPTION = 'webpc_notice_hidden';
	const NOTICE_VIEW_PATH  = 'components/notices/thanks.php';

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
		return (string) strtotime( '+ 1 week' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available(): bool {
		return ( basename( $_SERVER['PHP_SELF'] ) === 'index.php' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_active(): bool {
		$option_value = OptionsAccessManager::get_option( $this->get_option_name() );
		return ( ( $option_value !== null ) && ( $option_value < time() ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_disable_value(): string {
		$is_permanent = ( isset( $_REQUEST['is_permanently'] ) && $_REQUEST['is_permanently'] ); // phpcs:ignore
		return (string) strtotime( ( $is_permanent ) ? '+6 months' : '+ 1 month' );
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
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_ajax_action_to_disable(): string {
		return 'webpc_notice';
	}
}
