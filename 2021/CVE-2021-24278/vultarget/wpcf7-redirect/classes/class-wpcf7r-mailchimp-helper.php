<?php
use MailchimpAPI\Mailchimp;

/**
 * Class WPCF7_mailchimp - The main class that manages this extension.
 */
defined( 'ABSPATH' ) || exit;


class WPCF7R_Mailchimp_Helper {
	public static $instance;

	public static $mailchimp;

	public static $api_key;

	public function __construct( $api_key ) {
		self::$instance = $this;

		self::$api_key = $api_key;

		self::$mailchimp = new Mailchimp( $api_key );
	}

	/**
	 * Get a singelton
	 *
	 * @param string $api_key
	 */
	public static function get_instance( $api_key = '' ) {

		if ( null === self::$instance ) {
			self::$instance = new self( $api_key );
		}

		return self::$instance;
	}

	/**
	 * Get a clean list to return to the browser
	 *
	 * @param $api_key
	 */
	public static function get_lists_clean( $api_key ) {
		$instance = self::get_instance( $api_key );

		$lists = self::get_lists( $api_key );

		$lists_array = array();

		if ( ! is_wp_error( $lists ) && $lists ) {
			foreach ( $lists as $list ) {

				$list_fields       = $instance->get_list_fields( $list->id );
				$list_fields_clean = array();

				if ( $list_fields ) {
					foreach ( $list_fields as $list_field ) {
						$list_fields_clean[ $list_field->tag ] = $list_field->name;
					}
				}

				$lists_array[ $list->id ] = array(
					'list_id'     => $list->id,
					'list_name'   => $list->name,
					'list_fields' => $list_fields_clean,
				);
			}
		} else {
			return $lists;
		}

		return $lists_array;
	}

	/**
	 * Create a user on the mailchimp list
	 *
	 * @param $list_id
	 * @param $api_key
	 * @param $post_params
	 */
	public static function create_mailchimp_user( $list_id, $api_key, $post_params ) {

		$instance = self::get_instance( $api_key );

		try {
			if ( isset( $post_params['merge_fields']['ADDRESS'] ) && $post_params['merge_fields']['ADDRESS'] ) {
				$post_params['ADDRESS']['addr1'] = $post_params['merge_fields']['ADDRESS'];
			}

			$results = self::$mailchimp
				->lists( $list_id )
				->members()
				->post( $post_params );

			if ( $results->wasFailure() ) {
				$res_object = json_decode( $results->getBody() );

				$response   = new WP_Error( 'create_mailchimp_user', $res_object->detail . " List:{$list_id}" );

				return $response;
			}
		} catch ( Exception $ex ) {
			$response = new WP_Error( 'create_mailchimp_user', $ex->message . " List:{$list_id}" );
			return $response;
		}

		$response = $results->deserialize();

		if ( '400' === $response->status ) {
			$response = new WP_Error( 'lists', $response->detail . " List:{$list_id}" );
		} else {
			$new_response = array(
				'id'              => $response->id,
				'unique_email_id' => $response->unique_email_id,
				'status'          => $response->status,
				'list_id'         => $response->list_id,
			);

			$response = $new_response;
		}

		return $response;
	}

	/**
	 * Create a new list
	 *
	 * @param  $api_key
	 * @param  $list_name
	 */
	public static function create_list( $api_key, $list_name ) {
		$instance = self::get_instance( $api_key );

		$response = '';

		try {
			$contact = new stdclass();

			$contact->company  = 'test';
			$contact->address1 = 'address1';
			$contact->city     = 'city';
			$contact->state    = 'state';
			$contact->zip      = 'zip';
			$contact->country  = 'country';
			$contact->phone    = 'phone';

			$campaign_defaults             = new stdclass();
			$campaign_defaults->from_name  = 'name';
			$campaign_defaults->from_email = 'test@gmail.com';
			$campaign_defaults->subject    = 'test';
			$campaign_defaults->language   = 'en';

			$post_params = array(
				'name'                => $list_name,
				'email_address'       => 'example@domain.com',
				'contact'             => $contact,
				'permission_reminder' => __( 'You are receiving this email because you asked for it.', 'wpcf7-redirect' ),
				'email_type_option'   => true,
				'campaign_defaults'   => $campaign_defaults,
			);

			$results = self::$mailchimp
				->lists()
				->post( $post_params );

		} catch ( Exception $ex ) {
			$response = new WP_Error( 'lists', $ex->message . ' Check that your API key is correct' );
			return $response;
		}

		$results_desirialized = $results->deserialize();

		if ( isset( $results_desirialized->errors ) && $results_desirialized->errors ) {
			$error_message = '';
			foreach ( $results_desirialized->errors as $error ) {
				$error_message .= ' ' . $error->message;
			}
			$response = new WP_Error( 'lists', $error_message );
		}

		return $response;
	}

	/**
	 * Get the lists from the API
	 *
	 * @param  [type] $api_key [description]
	 * @return [type]          [description]
	 */
	public static function get_lists( $api_key ) {
		$instance = self::get_instance( $api_key );

		try {
			$results = self::$mailchimp->lists()->get(
				array(
					'count' => '200',
				)
			);
		} catch ( Exception $ex ) {
			$response = new WP_Error( 'lists', $ex->message . ' Check that your API key is correct' );
			return $response;
		}

		if ( is_a( $results, 'MailchimpAPI\Responses\FailureResponse' ) ) {
			$class_name = get_class( $results );
			$methods    = get_class_methods( $class_name );

			$error_message = $results->getBody();

			$response = new WP_Error( 'get_lists', $error_message );

			return $response;
		}

		$results_desirialized = $results->deserialize();

		if ( isset( $results_desirialized->lists ) && $results_desirialized->lists ) {
			$response = $results_desirialized->lists;

		} else {
			$response = new WP_Error( 'lists', $ex->message . ' Check that your API key is correct' );
		}

		return $response;
	}

	/**
	 * Get merge tags from the relevant list
	 *
	 * @param  [type] $list_id [description]
	 * @return [type]          [description]
	 */
	private function get_list_fields( $list_id ) {
		try {
			$results = self::$mailchimp->lists( $list_id )->mergeFields()->get();

			$results = $results->deserialize();

			return $results->merge_fields;
		} catch ( Exception $ex ) {
			$response = new WP_Error( 'custom_fields', 'Check that your API key is correct' );
			return $response;
		}
	}

	/**
	 * Create an array from the raw list
	 *
	 * @param  [type] $lists_raw [description]
	 * @return [type]            [description]
	 */
	public static function get_lists_array( $lists_raw ) {
		$mailchimp_lists = array();

		if ( $lists_raw ) {
			$lists_raw = maybe_unserialize( $lists_raw );
			foreach ( $lists_raw as $list_raw ) {
				$mailchimp_lists[ $list_raw['list_id'] ] = $list_raw['list_name'];
			}
		}

		return $mailchimp_lists;
	}
}
