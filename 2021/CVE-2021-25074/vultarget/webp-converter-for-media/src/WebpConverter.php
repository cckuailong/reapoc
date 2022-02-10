<?php

namespace WebpConverter;

use WebpConverter\Action;
use WebpConverter\Conversion;
use WebpConverter\Conversion\Cron;
use WebpConverter\Conversion\Endpoint;
use WebpConverter\Conversion\Media;
use WebpConverter\Error\ErrorDetectorAggregator;
use WebpConverter\Notice;
use WebpConverter\Plugin;
use WebpConverter\Repository\TokenRepository;
use WebpConverter\Settings\Page;

/**
 * Class initializes all plugin actions.
 */
class WebpConverter {

	public function __construct( PluginInfo $plugin_info ) {
		$plugin_data      = new PluginData();
		$token_repository = new TokenRepository();

		( new Action\ConvertAttachment( $plugin_data ) )->init_hooks();
		( new Action\ConvertDir() )->init_hooks();
		( new Action\ConvertPaths( $plugin_data ) )->init_hooks();
		( new Action\DeletePaths() )->init_hooks();
		( new Action\RegenerateAll( $plugin_data, $token_repository ) )->init_hooks();
		( new Conversion\Directory\DirectoryFactory() )->init_hooks();
		( new Conversion\DirectoryFiles( $plugin_data ) )->init_hooks();
		( new Endpoint\EndpointIntegration( new Endpoint\ImagesCounterEndpoint( $plugin_data, $token_repository ) ) )->init_hooks();
		( new Endpoint\EndpointIntegration( new Endpoint\PathsEndpoint( $plugin_data, $token_repository ) ) )->init_hooks();
		( new Endpoint\EndpointIntegration( new Endpoint\RegenerateEndpoint( $plugin_data ) ) )->init_hooks();
		( new Conversion\SkipConvertedPaths( $plugin_data ) )->init_hooks();
		( new Conversion\SkipExcludedPaths() )->init_hooks();
		( new Cron\Event( $plugin_data ) )->init_hooks();
		( new Cron\Schedules() )->init_hooks();
		( new ErrorDetectorAggregator( $plugin_info, $plugin_data ) )->init_hooks();
		( new Notice\NoticeFactory( $plugin_info ) )->init_hooks();
		( new Loader\LoaderIntegration( new Loader\HtaccessLoader( $plugin_info, $plugin_data ) ) )->init_hooks();
		( new Loader\LoaderIntegration( new Loader\PassthruLoader( $plugin_info, $plugin_data ) ) )->init_hooks();
		( new Media\Delete() )->init_hooks();
		( new Media\Upload( $plugin_data ) )->init_hooks();
		( new Plugin\Activation( $plugin_info ) )->init_hooks();
		( new Plugin\Deactivation( $plugin_info ) )->init_hooks();
		( new Plugin\Deactivation\Modal( $plugin_info, $plugin_data ) )->init_hooks();
		( new Plugin\Links( $plugin_info ) )->init_hooks();
		( new Plugin\Uninstall( $plugin_info ) )->init_hooks();
		( new Plugin\Update( $plugin_info ) )->init_hooks();
		( new Page\PageIntegration( $plugin_info ) )
			->set_page_integration( new Page\SettingsPage( $plugin_info, $plugin_data, $token_repository ) )
			->set_page_integration( new Page\DebugPage( $plugin_info, $plugin_data ) )
			->init_hooks();
	}
}
