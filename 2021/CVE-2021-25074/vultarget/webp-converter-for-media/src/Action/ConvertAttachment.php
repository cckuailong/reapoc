<?php

namespace WebpConverter\Action;

use WebpConverter\Conversion\Media\Attachment;
use WebpConverter\HookableInterface;
use WebpConverter\PluginData;

/**
 * Initializes conversion of all image sizes for attachment.
 */
class ConvertAttachment implements HookableInterface {

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	public function __construct( PluginData $plugin_data ) {
		$this->plugin_data = $plugin_data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'webpc_convert_attachment', [ $this, 'convert_files_by_attachment' ] );
	}

	/**
	 * Converts all sizes of attachment to output formats.
	 *
	 * @param int $post_id ID of attachment.
	 *
	 * @return void
	 * @internal
	 */
	public function convert_files_by_attachment( int $post_id ) {
		$attachment = new Attachment( $this->plugin_data );

		do_action( 'webpc_convert_paths', $attachment->get_attachment_paths( $post_id ) );
	}
}
