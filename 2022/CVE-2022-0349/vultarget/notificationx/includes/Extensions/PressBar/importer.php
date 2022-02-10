<?php
namespace NotificationX\Extensions\PressBar;

use Elementor\TemplateLibrary\Source_Local;
use Elementor\TemplateLibrary\Classes\Images;
use Elementor\Core\Settings\Page\Model;
use Elementor\Plugin as ElementorPlugin;

class Importer extends Source_Local {
    public function get_template_content( $args ){
		$output = json_decode( file_get_contents( __DIR__ . "/jsons/" . $args[ 'theme' ] . '.json' ), true );
		return $output;
    }
	/**
	 * Get template data.
	 *
	 * @inheritDoc
	 *
	 * @param array       $args    Custom template arguments.
	 * @param string      $context Optional. The context. Default is `display`.
	 *
	 * @return array Remote Template data.
	 */
	public function get_data( array $args, $context = 'display' ) {
		$data = $this->get_template_content( $args );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		ElementorPlugin::$instance->editor->set_edit_mode( true );

		$data['content'] = $this->replace_elements_ids( $data['content'] );
		$data['content'] = $this->process_export_import_content( $data['content'], 'on_import' );

		$post_id  = isset( $args['editor_post_id'] ) ? $args['editor_post_id'] : false;
		if( $post_id ) {
			$document = ElementorPlugin::$instance->documents->get( $post_id );
			if ( $document ) {
				$data['content'] = $document->get_elements_raw_data( $data['content'], true );
			}
		}
		return $data;
	}

	public function create_nx( $args ){
		$template_data = $this->get_data( $args );

		$page_settings = [];

		if ( ! empty( $template_data['page_settings'] ) ) {
			$page = new Model( [
				'id' => 0,
				'settings' => $template_data['page_settings'],
			] );

			$page_settings_data = $this->process_element_export_import_content( $page, 'on_import' );

			if ( ! empty( $page_settings_data['settings'] ) ) {
				$page_settings = $page_settings_data['settings'];
			}
		}

		$defaults = [
			'post_title'   => isset( $template_data['page_title'] ) ? $template_data['page_title'] : 'NxBar: ' . $template_data['title'],
			'page_settings' => $page_settings,
			'status' => current_user_can( 'publish_posts' ) ? 'publish' : 'pending',
		];

		$template_data = wp_parse_args( $template_data, $defaults );

		$document = ElementorPlugin::$instance->documents->create(
			$template_data['type'],
			[
				'post_title' => $template_data['post_title'],
				'post_status' => $template_data['status'],
				'post_type' => 'nx_bar',
			]
		);

		if ( is_wp_error( $document ) ) {
			/**
			 * @var \WP_Error $document
			 */
			return $document;
		}

		$document->save( [
			'elements' => $template_data['content'],
			'settings' => $page_settings,
		] );

		return $document->get_main_id();
	}

}
