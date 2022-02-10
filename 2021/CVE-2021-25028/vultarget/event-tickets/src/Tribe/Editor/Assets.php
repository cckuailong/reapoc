<?php
/**
 * Events Gutenberg Assets
 *
 * @since 4.9
 */
class Tribe__Tickets__Editor__Assets {
	/**
	 * Registers and Enqueues the assets
	 *
	 * @since 4.9
	 */
	public function register() {
		$plugin = Tribe__Tickets__Main::instance();

		tribe_asset(
			$plugin,
			'tribe-tickets-gutenberg-data',
			'app/data.js',
			/**
			 * @todo revise this dependencies
			 */
			[
				'react',
				'react-dom',
				'thickbox',
				'wp-components',
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-editor',
			],
			'enqueue_block_editor_assets',
			[
				'in_footer'    => false,
				'localize'     => [],
				'conditionals' => tribe_callback( 'tickets.editor', 'current_type_support_tickets' ),
				'priority'     => 200,
			]
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-gutenberg-icons',
			'app/icons.js',
			/**
			 * @todo revise this dependencies
			 */
			[],
			'enqueue_block_editor_assets',
			[
				'in_footer'    => false,
				'localize'     => [],
				'conditionals' => tribe_callback( 'tickets.editor', 'current_type_support_tickets' ),
				'priority'     => 201,
			]
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-gutenberg-elements',
			'app/elements.js',
			/**
			 * @todo revise this dependencies
			 */
			[],
			'enqueue_block_editor_assets',
			[
				'in_footer'    => false,
				'localize'     => [],
				'conditionals' => tribe_callback( 'tickets.editor', 'current_type_support_tickets' ),
				'priority'     => 202,
			]
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-gutenberg-blocks',
			'app/blocks.js',
			[
				'react',
				'react-dom',
				'wp-components',
				'wp-api',
				'wp-api-request',
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-editor',
			],
			'enqueue_block_editor_assets',
			[
				'in_footer'    => false,
				'localize'     => [],
				'conditionals' => tribe_callback( 'tickets.editor', 'current_type_support_tickets' ),
				'priority'     => 203,
			]
		);

		tribe_asset(
			$plugin,
			'tribe-tickets-gutenberg-blocks-styles',
			'app/blocks.css',
			[],
			'enqueue_block_editor_assets',
			[
				'in_footer'    => false,
				'localize'     => [],
				'conditionals' => tribe_callback( 'tickets.editor', 'current_type_support_tickets' ),
				'priority'     => 15,
			]
		);
	}
}
