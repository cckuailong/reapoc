<?php
/**
 * Class WPCF7R_Action_Save_Lead file - handles send send to api process
 */

defined( 'ABSPATH' ) || exit;

register_wpcf7r_actions( 'save_lead', __( 'Save Lead', 'wpcf7-redirect' ), 'WPCF7R_Action_Save_Lead', 3 );

class WPCF7R_Action_Save_Lead extends WPCF7R_Action {

	/**
	 * Init the parent action class
	 */
	public function __construct( $post ) {
		parent::__construct( $post );
		$this->priority = 1;
	}

	/**
	 * Get the action admin fields
	 */
	public function get_action_fields() {

		$parent_fields = parent::get_default_fields();

		unset( $parent_fields['action_status'] );

		return array_merge(
			array(
				'tags_map_mapping_section' => array(
					'name'   => 'tags_map_mapping_section',
					'type'   => 'section',
					'title'  => __( 'Tags mapping', 'wpcf7-redirect' ),
					'fields' => array(
						array(
							'name'          => 'leads_map',
							'type'          => 'leads_map',
							'label'         => '',
							'sub_title'     => '',
							'placeholder'   => '',
							'show_selector' => '',
							'value'         => maybe_unserialize( $this->get( 'leads_map' ) ),
							'tags'          => WPCF7R_Form::get_mail_tags(),
						),
					),
				),
				'action_status'            => array(
					'name'          => 'action_status',
					'type'          => 'checkbox',
					'label'         => $this->get_action_status_label(),
					'sub_title'     => __( 'if this is off the rule will not be applied', 'wpcf7-redirect' ),
					'placeholder'   => '',
					'show_selector' => '',
					'toggle-label'  => json_encode(
						array(
							'.field-wrap-action_status .checkbox-label,.column-status a' => array(
								__( 'Enabled', 'wpcf7-redirect' ),
								__( 'Disabled', 'wpcf7-redirect' ),
							),
						)
					),
					'value'         => $this->get( 'action_status' ),
				),
			),
			$parent_fields
		);
	}

	/**
	 * Get an HTML of the
	 */
	public function get_action_settings() {

		$this->get_settings_template( 'html-action-redirect.php' );

	}

	/**
	 * Connected to manage_columns hooks.
	 *
	 * @param [string] $column - key of the column.
	 * @param [int]    $lead_id - the id of the relevant post.
	 * @return void
	 */
	public function display_action_column_content( $column, $lead_id ) {
		switch ( $column ) {
			case 'form':
				echo $this->get_cf7_link_html();
				break;
			case 'data_preview':
				echo $this->display_columns_values( $lead_id );
				break;
		}
	}

	/**
	 * Display the values that were selected on the action.
	 *
	 * @return void
	 */
	private function display_columns_values( $lead_id ) {
		$mapped_fields = maybe_unserialize( $this->get( 'leads_map' ) );
		$none_selected = true;
		if ( $mapped_fields ) {
			foreach ( $mapped_fields as $field_key => $mapped_field ) {
				if ( isset( $mapped_field['appear'] ) && $mapped_field['appear'] ) {
					$label = $mapped_field['tag'] ? $mapped_field['tag'] : $field_key;

					$string = $label . ': ' . get_post_meta( $lead_id, $field_key, true );

					echo sprintf( "<div class='preview-data'>%s</div>", $string );

					$none_selected = false;
				}
			}
		}

		if ( $none_selected ) {
			echo __( 'No preview defined', 'wpcf7-redirect' );
		}
	}

	/**
	 * Handle a simple redirect rule
	 *
	 * @param $submission
	 */
	public function process( $submission ) {
		$contact_form = $submission->get_contact_form();

		// insert the lead to the DB
		$files = $submission->uploaded_files();

		$submitted_files = array();

		$posted_values = $submission->get_posted_data();

		if ( $files ) {
			foreach ( $files as $file_key => $file_path ) {

				$type = pathinfo( $file_path, PATHINFO_EXTENSION );

				$submitted_files[ $file_key ] = array(
					'type'        => $type,
					'name'        => basename( $file_path ),
					'base64_file' => wpcf7r_base_64_file( $file_path ),
					'path'        => $file_path,
				);
				unset( $posted_values[ $file_key ] );
			}
		}

		$lead = WPCF7R_Leads_Manager::insert_lead( $contact_form->id(), $posted_values, $submitted_files, 'contact', $this->get_id() );

		self::set_lead_id( $lead->post_id );

		$response = array(
			'type' => 'save_lead',
			'data' => array(
				'lead_id' => $lead->post_id,
			),
		);

		return $response;
	}
}

