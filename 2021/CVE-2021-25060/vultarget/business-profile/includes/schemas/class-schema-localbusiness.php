<?php
/**
 * Create a schema for a Local Business as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaLocalBusiness' ) ) :
	require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-organization.php';

	/**
	 * Local Business schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaLocalBusiness extends bpfwpSchemaOrganization {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'LocalBusiness';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Local Business';


		/**
		 * Load the schema's default fields
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function set_fields() {
			parent::set_fields();

			$additional_fields = array(
				new bpfwpSchemaField( array(
					'slug' 				=> 'openingHours', 
					'name' 				=> 'Opening Hours', 
					'input' 			=> 'scheduler',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'openingHours', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'paymentAccepted', 
					'name' 				=> 'Accepted Payment Types', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'paymentAccepted', $this->slug )
				) ),
				new bpfwpSchemaField( array(
					'slug' 				=> 'priceRange', 
					'name' 				=> 'Price Range (ex. $, $$, $$$)', 
					'input' 			=> 'text',
					'callback' 			=> apply_filters( 'bpfwp_schema_field_callback', null, 'priceRange', $this->slug )
				) )
			);

			$fields = apply_filters( 'bpfwp_schema_additional_fields', $additional_fields, $this->slug );

			array_splice($this->fields, 1, 0, $fields);
		}


		/**
		 * Load the schema's child classes
		 *
		 * @since  2.0.0
		 * @access public
		 * @return void
		 */
		public function initialize_children(  $depth ) {
			$depth--;

			$child_classes = array(
				'foodestablishment' => 'FoodEstablishment',
				'animalshelter' => 'AnimalShelter',
				'archiveorganization' => 'ArchiveOrganization',
				'automotivebusiness' => 'AutomotiveBusiness',
				'childcare' => 'ChildCare',
				'dentist' => 'Dentist',
				'drycleaningorlaundry' => 'DryCleaningOrLaundry',
				'emergencyservice' => 'EmergencyService',
				'employmentagency' => 'EmploymentAgency',
				'entertainmentbusiness' => 'EntertainmentBusiness',
				'financialservice' => 'FinancialService',
				'governmentoffice' => 'GovernmentOffice',
				'healthandbeautybusiness' => 'HealthAndBeautyBusiness',
				'homeandconstructionbusiness' => 'HomeAndConstructionBusiness',
				'internetcafe' => 'InternetCafe',
				'legalservice' => 'LegalService',
				'library' => 'Library',
				'lodgingbusiness' => 'LodgingBusiness',
				'medicalbusiness' => 'MedicalBusiness',
				'professionalservice' => 'ProfessionalService',
				'radiostation' => 'RadioStation',
				'realestateagent' => 'RealEstateAgent',
				'recyclingcenter' => 'RecyclingCenter',
				'selfstorage' => 'SelfStorage',
				'shoppingcenter' => 'ShoppingCenter',
				'sportsactivitylocation' => 'SportsActivityLocation',
				'store' => 'Store',
				'televisionstation' => 'TelevisionStation',
				'touristinformationcenter' => 'TouristInformationCenter',
				'travelagency' => 'TravelAgency',
			);

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;
