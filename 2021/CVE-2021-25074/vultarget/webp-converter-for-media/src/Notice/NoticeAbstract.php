<?php

namespace WebpConverter\Notice;

/**
 * Abstract class for class that supports data field in plugin settings.
 */
abstract class NoticeAbstract implements NoticeInterface {

	/**
	 * {@inheritdoc}
	 */
	public function get_ajax_action_to_disable() {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_vars_for_view(): array {
		return [];
	}
}
