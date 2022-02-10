<?php

/**
 * Class to import bookings from a spreadsheet (not working atm)
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if (!class_exists('ComposerAutoloaderInit4618f5c41cf5e27cc7908556f031e4d4')) {require_once RTB_PLUGIN_DIR . '/lib/PHPSpreadsheet/vendor/autoload.php';}
use PhpOffice\PhpSpreadsheet\Spreadsheet;
class rtbImport {

	/**
	 * Hook suffix for the page
	 *
	 * @since 0.1
	 */
	public $hook_suffix;

	/**
	 * The success/error message content
	 *
	 * @since 0.1
	 */
	public $status;

	/**
	 * Whether the operation was a success or not
	 *
	 * @since 0.1
	 */
	public $message;

	public function __construct() {
		add_action( 'admin_menu', array($this, 'register_install_screen' ));

		if ( isset( $_POST['rtbImport'] ) ) { add_action( 'admin_init', array($this, 'import_bookings' )); }

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_import_scripts' ) );
	}

	public function register_install_screen() {
		$this->hook_suffix = add_submenu_page(
			'rtb-bookings',
			_x( 'Import', 'Title of the Imports page', 'restaurant-reservations' ),
			_x( 'Import', 'Title of Imports link in the admin menu', 'restaurant-reservations' ),
			'manage_options',
			'rtb-imports',
			array( $this, 'display_editor_page' )
		);

		// Print the error modal and enqueue assets
		add_action( 'load-' . $this->hook_suffix, array( $this, 'enqueue_admin_assets' ) );
	}

	public function display_import_screen() {
		global $rtb_controller;

		$import_permission = $rtb_controller->permissions->check_permission( 'import' );
		?>
		<div class='wrap'>
			<h2>Import</h2>
			<?php if ( $import_permission ) { ?> 
				<form method='post' enctype="multipart/form-data">
					<p>
						<label for="rtb_bookings_spreadsheet"><?php _e("Spreadsheet Containing Bookings", 'restaurant-reservations') ?></label><br />
						<input name="rtb_bookings_spreadsheet" type="file" value=""/>
					</p>
					<input type='submit' name='rtbImport' value='Import Bookings' class='button button-primary' />
				</form>
			<?php } else { ?>
				<div class='rtb-premium-locked'>
					<a href="https://www.fivestarplugins.com/license-payment/?Selected=RTB&Quantity=1" target="_blank">Upgrade</a> to the premium version to use this feature
				</div>
			<?php } ?>
		</div>
	<?php }

	public function import_bookings() {
		global $rtb_controller;

		$fields = $rtb_controller->settings->get_booking_form_fields();

		$update = $this->handle_spreadsheet_upload();

		if ( $update['message_type'] != 'Success' ) :
			$this->status = false;
			$this->message =  $update['message'];

			add_action( 'admin_notices', array( $this, 'display_notice' ) );

			return;
		endif;

		$excel_url = RTB_PLUGIN_DIR . '/user-sheets/' . $update['filename'];

	    // Build the workbook object out of the uploaded spreadsheet
	    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excel_url);
	
	    // Create a worksheet object out of the product sheet in the workbook
	    $sheet = $spreadsheet->getActiveSheet();
	
	    $allowable_fields = array();
	    foreach ($fields as $field) {$allowable_fields[] = $field->name;}
	    //List of fields that can be accepted via upload
	    //$allowed_fields = array("ID", "Title", "Description", "Price", "Sections");
	
	
	    // Get column names
	    $highest_column = $sheet->getHighestColumn();
	    $highest_column_index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highest_column);
	    for ($column = 1; $column <= $highest_column_index; $column++) {
	        /*if (trim($sheet->getCellByColumnAndRow($column, 1)->getValue()) == "ID") {$ID_column = $column;}
	        if (trim($sheet->getCellByColumnAndRow($column, 1)->getValue()) == "Title") {$title_column = $column;}
	        if (trim($sheet->getCellByColumnAndRow($column, 1)->getValue()) == "Description") {$description_column = $column;}
	        if (trim($sheet->getCellByColumnAndRow($column, 1)->getValue()) == "Price") {$price_column = $column;}
	        if (trim($sheet->getCellByColumnAndRow($column, 1)->getValue()) == "Sections") {$sections_column = $column;}*/
	
	        foreach ($fields as $key => $field) {
	            if (trim($sheet->getCellByColumnAndRow($column, 1)->getValue()) == $field->name) {$field->column = $column;}
	        }
	    }
	
	
	    // Put the spreadsheet data into a multi-dimensional array to facilitate processing
	    $highest_row = $sheet->getHighestRow();
	    for ($row = 2; $row <= $highest_row; $row++) {
	        for ($column = 1; $column <= $highest_column_index; $column++) {
	            $data[$row][$column] = $sheet->getCellByColumnAndRow($column, $row)->getValue();
	        }
	    }
	
	    // Create the query to insert the products one at a time into the database and then run it
	    foreach ($data as $booking) {
	        // Create an array of the values that are being inserted for each order,
	        // edit if it's a current order, otherwise add it
	        foreach ($booking as $col_index => $value) {
	            if ($col_index == $ID_column and $ID_column !== null) {$post['ID'] = esc_sql($value);}
	            if ($col_index == $title_column and $title_column !== null) {$post['post_title'] = esc_sql($value);}
	            if ($col_index == $description_column and $description_column !== null) {$post['post_content'] = esc_sql($value);}
	            if ($col_index == $price_column and $price_column !== null) {$post_prices = explode(",", esc_sql($value));}
	            if (isset($sections_column) and $col_index == $sections_column and $sections_column !== null) {$post_sections = explode(",", esc_sql($value));}
	        }
	
	        if (!is_array($post_prices)) {$post_prices = array();}
	        if (!is_array($post_sections)) {$post_sections = array();}
	
	        if ($post['post_title'] == '') {continue;}
	
	        $post['post_status'] = 'publish';
	        $post['post_type'] = RTB_BOOKING_POST_TYPE;
		
			if ( isset( $post['ID'] ) and $post['ID'] != '') { $post_id = wp_update_post($post); }
	        else { $post_id = wp_insert_post($post); }
	        
	        if ( $post_id != 0 ) {
	            foreach ( $post_sections as $section ) {
	                $menu_section = term_exists($section, 'fdm-menu-section');
	                if ( $menu_section !== 0 && $menu_section !== null ) { $menu_section_ids[] = (int) $menu_section['term_id']; }
	            }
	            if ( isset($menu_section_ids) and is_array($menu_section_ids) ) { wp_set_object_terms($post_id, $menu_section_ids, 'fdm-menu-section'); }

	            update_post_meta( $post_id, 'fdm_item_price', implode(",", $post_prices) );
	
	            $field_values = array();
	            foreach ( $fields as $field ) {
	                if (isset($field->column) and isset($menu_item[$field->column])) {
	                    $field_values[$field->slug] = esc_sql($menu_item[$field->column]);
	                    
	                }
	            }
	            update_post_meta($post_id, '_fdm_menu_item_custom_fields', $field_values);
	        }
	
	        unset($post);
	        unset($post_sections);
	        unset($menu_section_ids);
	        unset($post_prices);
	        unset($field_values);
	    }

	    $this->status = true;
		$this->message = __("Reservations added successfully.", 'restaurant-reservations');

		add_action( 'admin_notices', array( $this, 'display_notice' ) );
	}

	function handle_spreadsheet_upload() {
		  /* Test if there is an error with the uploaded spreadsheet and return that error if there is */
        if (!empty($_FILES['fdm_menu_items_spreadsheet']['error']))
        {
                switch($_FILES['fdm_menu_items_spreadsheet']['error'])
                {

                case '1':
                        $error = __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'restaurant-reservations');
                        break;
                case '2':
                        $error = __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'restaurant-reservations');
                        break;
                case '3':
                        $error = __('The uploaded file was only partially uploaded', 'restaurant-reservations');
                        break;
                case '4':
                        $error = __('No file was uploaded.', 'restaurant-reservations');
                        break;

                case '6':
                        $error = __('Missing a temporary folder', 'restaurant-reservations');
                        break;
                case '7':
                        $error = __('Failed to write file to disk', 'restaurant-reservations');
                        break;
                case '8':
                        $error = __('File upload stopped by extension', 'restaurant-reservations');
                        break;
                case '999':
                        default:
                        $error = __('No error code avaiable', 'restaurant-reservations');
                }
        }
        /* Make sure that the file exists */
        elseif (empty($_FILES['fdm_menu_items_spreadsheet']['tmp_name']) || $_FILES['fdm_menu_items_spreadsheet']['tmp_name'] == 'none') {
                $error = __('No file was uploaded here..', 'restaurant-reservations');
        }
        /* Move the file and store the URL to pass it onwards*/
        /* Check that it is a .xls or .xlsx file */ 
        if(!isset($_FILES['fdm_menu_items_spreadsheet']['name']) or (!preg_match("/\.(xls.?)$/", $_FILES['fdm_menu_items_spreadsheet']['name']) and !preg_match("/\.(csv.?)$/", $_FILES['fdm_menu_items_spreadsheet']['name']))) {
            $error = __('File must be .csv, .xls or .xlsx', 'restaurant-reservations');
        }
        else {
                        $filename = basename( $_FILES['fdm_menu_items_spreadsheet']['name']);
                        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
                        $filename = mb_ereg_replace("([\.]{2,})", '', $filename);

                        //for security reason, we force to remove all uploaded file
                        $target_path = RTB_PLUGIN_DIR . "/user-sheets/";

                        $target_path = $target_path . $filename;

                        if (!move_uploaded_file($_FILES['fdm_menu_items_spreadsheet']['tmp_name'], $target_path)) {
                              $error .= "There was an error uploading the file, please try again!";
                        }
                        else {
                                $excel_file_name = $filename;
                        }
        }

        /* Pass the data to the appropriate function in Update_Admin_Databases.php to create the products */
        if (!isset($error)) {
                $update = array("message_type" => "Success", "filename" => $excel_file_name);
        }
        else {
                $update = array("message_type" => "Error", "message" => $error);
        }
        return $update;
	}

	public function enqueue_import_scripts() {
		$screen = get_current_screen();
		if($screen->id == 'rtb-menu_page_rtb-import'){
			wp_enqueue_style( 'rtb-admin-css', RTB_PLUGIN_URL . '/assets/css/admin.css', array(), RTB_VERSION );
			wp_enqueue_script( 'rtb-admin-js', RTB_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery' ), RTB_VERSION, true );
		}
	}

	public function display_notice() {
		if ( $this->status ) {
			echo "<div class='updated'><p>" . $this->message . "</p></div>";
		}
		else {
			echo "<div class='error'><p>" . $this->message . "</p></div>";
		}
	}

}


