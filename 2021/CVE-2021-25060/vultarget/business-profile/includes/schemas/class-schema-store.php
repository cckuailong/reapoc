<?php
/**
 * Create a schema for a Store as listed on schema.org.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2019, Five Star Plugins
 * @license   GPL-2.0+
 * @since     2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpSchemaStore' ) ) :
	require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-localbusiness.php';

	/**
	 * Store schema for Business Profile
	 *
	 * @since 2.0.0
	 */
	class bpfwpSchemaStore extends bpfwpSchemaLocalBusiness {

		/**
		 * The name used by Schema.org
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $slug = 'Store';

		/**
		 * The display name for this schema
		 *
		 * @since  2.0.0
		 * @access public
		 * @var    string
		 */
		public $name = 'Store';


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
				'autopartsstore' => 'AutoPartsStore',
				'bikestore' => 'BikeStore',
				'bookstore' => 'BookStore',
				'clothingstore' => 'ClothingStore',
				'computerstore' => 'ComputerStore',
				'conveniencestore' => 'ConvenienceStore',
				'departmentstore' => 'DepartmentStore',
				'electronicsstore' => 'ElectronicsStore',
				'florist' => 'Florist',
				'furniturestore' => 'FurnitureStore',
				'gardenstore' => 'GardenStore',
				'grocerystore' => 'GroceryStore',
				'hardwarestore' => 'HardwareStore',
				'hobbyshop' => 'HobbyShop',
				'homegoodsstore' => 'HomeGoodsStore',
				'jewelrystore' => 'JewelryStore',
				'liquorstore' => 'LiquorStore',
				'mensclothingstore' => 'MensClothingStore',
				'mobilephonestore' => 'MobilePhoneStore',
				'movierentalstore' => 'MovieRentalStore',
				'musicstore' => 'MusicStore',
				'officeequipmentstore' => 'OfficeEquipmentStore',
				'outletstore' => 'OutletStore',
				'pawnshop' => 'PawnShop',
				'petstore' => 'PetStore',
				'shoestore' => 'ShoeStore',
				'sportinggoodsstore' => 'SportingGoodsStore',
				'tireshop' => 'TireShop',
				'toystore' => 'ToyStore',
				'wholesalestore' => 'WholesaleStore',
			);

			foreach ( $child_classes as $slug => $name ) {
				require_once BPFWP_PLUGIN_DIR . '/includes/schemas/class-schema-' . $slug . '.php';

				$class_name = 'bpfwpSchema' . $name;
				$this->children[$slug] = new $class_name( array( 'depth' => $depth ) );
			}
		}

	}
endif;
