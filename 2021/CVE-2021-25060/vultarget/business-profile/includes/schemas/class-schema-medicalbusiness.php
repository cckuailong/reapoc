<?php
/**
 * Create a schema for a Medical Business as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaMedicalBusiness' ) ) :
	require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-localbusiness.php';

	/**
	 * Medical Business schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaMedicalBusiness extends bpfwpSchemaLocalBusiness {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'MedicalBusiness';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Medical Business';


		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_fields() {
			parent::set_fields();
		}


		/**
		 * Load the schema's child classes
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function initialize_children( $depth ) {
			$depth--;

			$child_classes = array(
				'communityhealth' => 'CommunityHealth',
				'dentist' => 'Dentist',
				'dermatology' => 'Dermatology',
				'dietnutrition' => 'DietNutrition',
				'emergency' => 'Emergency',
				'geriatric' => 'Geriatric',
				'gynecologic' => 'Gynecologic',
				'medicalclinic' => 'MedicalClinic',
				'midwifery' => 'Midwifery',
				'nursing' => 'Nursing',
				'obstetric' => 'Obstetric',
				'oncologic' => 'Oncologic',
				'optician' => 'Optician',
				'optometric' => 'Optometric',
				'otolaryngologic' => 'Otolaryngologic',
				'pediatric' => 'Pediatric',
				'pharmacy' => 'Pharmacy',
				'physician' => 'Physician',
				'physiotherapy' => 'Physiotherapy',
				'plasticsurgery' => 'PlasticSurgery',
				'podiatric' => 'Podiatric',
				'primarycare' => 'PrimaryCare',
				'psychiatric' => 'Psychiatric',
				'publichealth' => 'PublicHealth',
			);

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;
