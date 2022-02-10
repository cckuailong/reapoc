<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class RTEC_Admin_Event
 */
class RTEC_Admin_Event {

	/**
	 * @var
	 */
	public $form_obj;

	/**
	 * @var
	 */
	public $event_meta;

	/**
	 * @var array
	 */
	public $registrants_data = array();

	/**
	 * @var int
	 */
	private $records_to_retrieve = 2000;

	/**
	 * @var bool
	 */
	public $pagination_needed = false;

	/**
	 * @var string
	 */
	public $venue = '(unassigned)';

	/**
	 * @var
	 */
	public $tz_offset;

	/**
	 * @var
	 */
	public $labels;

	/**
	 * @var
	 */
	public $columns;

	/**
	 * @var
	 */
	public $column_label;

	/**
	 * @var
	 */
	public $view_type;

	/**
	 * Creates the admin event object depending on what view and other parameters are being used
	 *
	 * @param   $event_id   int
	 * @param   $view_type  string
	 * @param   $mvt        string
	 * @param   $form_obj   mixed
	 *
	 * @since 2.0
	 */
	public function build_admin_event( $event_id, $view_type, $mvt = '', $form_obj = false ) {
		$this->view_type = $view_type;
		$this->event_meta = rtec_get_event_meta( $event_id );

		if ( false !== $form_obj ) {
			$this->form_obj = $form_obj;
		} else {
			$this->form_obj = $this->get_form_field_data( 0, 3 );
		}

		if ( $view_type === 'grid' ) {
			$this->records_to_retrieve = 11;
			$this->registrants_data = $this->get_registrations( 'simple' );
			if ( is_array( $this->registrants_data ) ) {
				$this->pagination_needed = ( count( $this->registrants_data ) === $this->records_to_retrieve );
			} else {
				$this->pagination_needed = false;
			}
		} elseif ( $view_type === 'single' ) {
			$this->records_to_retrieve = 2000;
			$this->registrants_data = $this->get_registrations( 'normal' );
		} elseif ( $view_type === 'csv' ) {
			$this->records_to_retrieve = 2000;
			$this->registrants_data = $this->get_registrations( 'normal' );
		}

		$this->set_venue();

		$this->set_tz_offset();
	}

	/**
	 * @since 2.0
	 */
	public function set_venue() {
		$this->venue = $this->event_meta['venue_title'];
	}

	/**
	 * Sets the timezone offset for use in displaying dates and times
	 * @since 2.0
	 */
	public function set_tz_offset() {
		global $rtec_options;
		$timezone = isset( $rtec_options['timezone'] ) ? $rtec_options['timezone'] : 'America/New_York';
		// use php DateTimeZone class to handle the date formatting and offsets
		$date_obj = new DateTime( date( 'm/d g:i a' ), new DateTimeZone( "UTC" ) );
		$date_obj->setTimeZone( new DateTimeZone( $timezone ) );
		$this->tz_offset = $date_obj->getOffset();
	}

	/**
	 * Gets registrations based on the event object attributes
	 *
	 * @param $type             string  simple returns just the first 3 fields
	 *
	 * @return $registrations   array
	 * @since 2.0
	 */
	public function get_registrations( $type = 'simple' ) {
		$rtec = RTEC();
		$db = $rtec->db_frontend->instance();

		if ( $type === 'simple' ) {
			$field_atts = $this->form_obj;
		} else {
			$field_atts = $this->form_obj->get_field_attributes();
		}

		$event_meta = $this->event_meta;
		$columns = array( 'registration_date' );
		$labels = array( __( 'Registration Date', 'registrations-for-the-events-calendar' ) );
		$column_label = array();
		$no_backend_label_fields = rtec_get_no_backend_column_fields();

		if ( $type === 'simple' ) {
			foreach ( $field_atts as $field => $atts ) {
				if ( !in_array( $field, $no_backend_label_fields, true ) ) {
					$columns[] = $field;
					$labels[] = __( $atts['label'], 'registrations-for-the-events-calendar' );
					$column_label[$field] = $atts['label'];
				}
			}
		} else {
			if ( $this->view_type === 'single' ) {
				$columns = array_merge( $columns, $this->form_obj->get_column_keys() );
				$labels = array_merge( $labels, $this->form_obj->get_field_labels() );
				$column_label = $this->form_obj->get_custom_fields_label_name_pairs();
			} else {
				foreach ( $field_atts as $field => $atts ) {
					if ( !in_array( $field, $no_backend_label_fields, true ) ) {
						$columns[] = $field;
						$labels[] = str_replace( '&#42;', '',  __( stripslashes( $atts['label'] ), 'registrations-for-the-events-calendar' ) );
						$column_label[$field] = str_replace( '&#42;', '', stripslashes( $atts['label'] ) );
					}
				}
			}

		}

		$retrieve_columns = $columns;
		$retrieve_columns[] = 'status';
		$retrieve_columns[] = 'event_id';
		$retrieve_columns[] = 'id';
		$retrieve_columns[] = 'user_id';

			$args = array(
				'fields' => $retrieve_columns,
				'where' => array(
					array( 'event_id', $event_meta['post_id'], '=', 'int' )
				),
				'order_by' => 'registration_date'
			);


		$records_to_retrieve = $this->records_to_retrieve;
		$registrations = $db->retrieve_entries( $args, false, $records_to_retrieve );

		$this->columns = $columns;
		$this->labels = $labels;
		$this->column_label = $column_label;

		return $registrations;
	}

	/**
	 * Sets registrants data back to an empty array
	 *
	 * @since 2.0
	 */
	public function reset_registrations() {
		$this->registrants_data = array();
	}

	/**
	 * Sets classes to alter styling for an event
	 *
	 * @since 2.0
	 */
	public function the_single_event_classes() {
		$classes = '';

		$classes .= ' rtec-single-event-no-mvt';

		echo $classes;
	}

	/**
	 * Gets registration text based on event settings
	 *
	 * @param $mvt_obj      string
	 * @param $num_registered   int
	 *
	 * @return  string
	 * @since 2.0
	 */
	public function get_registration_text( $mvt_obj = '', $num_registered = 0 ) {
		$event_meta = $this->event_meta;

		$max_registrations_text = $event_meta['limit_registrations'] ? $event_meta['max_registrations'] : '&#8734;';
		$num_registered_text = isset( $event_meta['num_registered'] ) ? max( (int)$event_meta['num_registered'], 0 ) : 0;

		return $num_registered_text . ' &#47; ' . $max_registrations_text;
	}

	/**
	 * Gets singe event wrapper class
	 *
	 * @since 2.0
	 */
	public function get_single_event_wrapper_classes() {
		$classes = '';

		return $classes;
	}

	/**
	 * Sets classes to alter styling for an event
	 *
	 * @param $form_id      int
	 * @param $max_fields   int
	 *
	 * @return array
	 * @since 2.0
	 */
	protected function get_form_field_data( $form_id = 1, $max_fields = 100 )
	{
		$rtec = RTEC();
		$form = $rtec->form->instance();
		$form->build_form();
		$results = $form->get_form_field_data_from_db();
		$no_template = rtec_get_no_template_fields();
		$fields_data = array();

		foreach( $results as $result ) {

			if ( ! in_array( $result['field_name'], $no_template, true ) ) {
				$fields_data[ $result['field_name'] ][ 'label' ] = $result['label'];
			}

		}

		return array_slice( $fields_data, 0, $max_fields );
	}
}
