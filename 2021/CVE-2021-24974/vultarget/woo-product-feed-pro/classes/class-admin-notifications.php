<?php
/**
 * Class holding the notification messages and type of notices
 * Returns the message and type of message (info, error, success)
 */
class WooSEA_Get_Admin_Notifications {

	public function __construct() {
		$this->notification_details = array();
	}

	public function woosea_debug_informations ($versions, $product_numbers, $order_rows, $cron_objects) {
                $upload_dir = wp_upload_dir();
		$filename = "debug";

                $base = $upload_dir['basedir'];
                $path = $base . "/woo-product-feed-pro/logs";
                $file = $path . "/". $filename ."." ."log";

		// Remove the previous file, preventing the file from becoming to big
		if ( file_exists ( $file ) ){
			unlink($file);
		}

                // External location for downloading the file   
                $external_base = $upload_dir['baseurl'];
                $external_path = $external_base . "/woo-product-feed-pro/logs";
                $external_file = $external_path . "/" . $filename ."." ."log";

                // Check if directory in uploads exists, if not create one      
                if ( ! file_exists( $path ) ) {
                        wp_mkdir_p( $path );
                }

                // Log timestamp
                $today = "\n";
                $today .= date("F j, Y, g:i a");                 // March 10, 2001, 5:16 pm
                $today .= "\n";

                $fp = fopen($file, 'a+');
                fwrite($fp, $today);
                fwrite($fp, print_r($versions, TRUE));
                fwrite($fp, print_r($product_numbers, TRUE));
                fwrite($fp, print_r($cron_objects, TRUE));
                fwrite($fp, print_r($order_rows, TRUE));
                fclose($fp);

		return $this->notification_details = $external_file;
	}
	
	public function get_admin_notifications ( $step, $error ) {

		$domain = $_SERVER['HTTP_HOST'];
	
		switch($step){
			case 0:
				$message = __( 'Please select the country and channel for which you would like to create a new product feed. The channel drop-down will populate with relevant country channels once you selected a country. Filling in a project name is mandatory.','woo-product-feed-pro' );	
				$message_type = "notice notice-info";
				break;
			case 1:
				$message = __( 'Map your products or categories to the categories of your selected channel. For some channels adding their categorisation in the product feed is mandatory. Even when category mappings are not mandatory it is likely your products will get better visibility and higher conversions when mappings have been added.','woo-product-feed-pro' );		
				$message_type = "notice notice-info";
				break;
			case 2:
				$message = __( 'Please drag and drop the attributes you want to be in your product feed from left to right.','woo-product-feed-pro' );	
				$message_type = "notice notice-info is-dismissible";
				break;
			case 3:
				$message = __( 'Mapping your product categories to the channel categories will increase changes of getting all your products listed correctly, thus increase your conversion rates.','woo-product-feed-pro' );	
				$message_type = "notice notice-info is-dismissible";
				break;
			case 4:
				$message = __( 'Create filter and rules so exactly the right products end up in your product feed. These filters and rules are only eligable for the current product feed you are configuring and will not be used for other feeds.<br/><br/><strong>Filters:</strong> Exclude or include products that meet certain conditions. [<strong><i><a href="https://adtribes.io/how-to-create-filters-for-your-product-feed/" target="_blank">Detailed information and filter examples</a></i></strong>] or [<strong><i><a href="https://adtribes.io/create-a-product-feed-for-one-specific-category/" target="_blank">Create a product feed for just 1 category</a></i></strong>]<br/><strong>Rules:</strong> Change attribute values based on other attribute values or conditions.[<strong><i><a href="https://adtribes.io/how-to-create-rules/" target="_blank">Detailed information about rules and some examples</a></i></strong>]<br/><br/>Order of execution: the filters and rules will be executed in the order of creation.','woo-product-feed-pro' );	
				$message_type = "notice notice-info";
				break;
			case 5:
				$message = __( '<strong>Google Analytics UTM codes:</strong><br/>Adding Google Analytics UTM codes is not mandatory, it will however enable you to get detailed insights into how your products are performing in Google Analytics reporting and allow you to tweak and tune your campaign making it more profitable. We strongly advise you to add the Google Analytics tracking. When enabled the plugin will append the Google Analytics UTM parameters to your landingpage URL\'s.','woo-product-feed-pro' );
				$message_type = "notice notice-info";
				break;
			case 6:
				$message = __( 'Your product feed is now being created, please be patient. Your feed details will be displayed when generation of the product feed has been finished.','woo-product-feed-pro' );
				$message_type = "notice notice-info is-dismissible";
				break;
			case 7:
				$message = __( 'For the selected channel the attributes shown below are mandatory, please map them to your product attributes. We\'ve already pre-filled a lot of mappings so all you have to do is check those and map the ones that are left blank or add new ones by hitting the \'Add field mapping\' button.<br/><br/>[<strong><i><a href="https://adtribes.io/how-to-use-static-values-and-create-fake-content-for-your-product-feed/" target="_blank">Learn how to use static values</a></i></strong>]','woo-product-feed-pro' );
				$message_type = "notice notice-info";
				break;
			case 8:
				$message = __( 'Manage your projects, such as the mappings and filter rules, below. Hit the refresh icon for the project to run with its new settings or just to refresh the product feed. When a project is being processed it is not possible to make changes to its configuration.','woo-product-feed-pro' );
				$message_type = "notice notice-info";
				break;
			case 9:
				$message = __( 'You cannot create product feeds yet, please install WooCommerce first.','woo-product-feed-pro' );
				$message_type = "notice notice-error";
				break;
			case 10:
				$message = __( 'The graph shows the amount of products in this product feed, measured after every scheduled and/or manually triggered refresh.','woo-product-feed-pro' );
				$message_type = "notice notice-info is-dismissible";
				break;
			case 11:
				$message = __( 'You are running an old PHP version. This plugin might not work or be really slow. Please upgrade to PHP version 7.0 or newer.','woo-product-feed-pro' );
				$message_type = "notice notice-error is-dismissible";
				break;
			case 12:
				$message = __( 'We are sorry but it seems you have disabled your WP CRON. This plugin creates product feeds in batches and needs the WP CRON to be active for doing so. Please enable the WP CRON in your wp-config.php file and re-activate this plugin before creating a product feed.','woo-product-feed-pro' );
				$message_type = "notice notice-error is-dismissible";
				break;
			case 13:
				$message = __( 'We are sorry but it seems you are running an old version of WooCommerce. This plugin requires WooCommerce version 3.0 at least. Please upgrade to the latest version of WooCommerce before creating a product feed.','woo-product-feed-pro' );
				$message_type = "notice notice-error is-dismissible";
				break;
			case 14:
				$message = __( 'Add important attributes, such as Brand, GTIN, condition and many more to create a perfect Google Shopping feed or fix the WooCommerce structured data bug so less products get disapproved in Google\'s Merchant Center.','woo-product-feed-pro' );
				$message_type = "notice notice-info";
				break;
			case 15:
				$message = __( 'Manipulate your product data to improve the quality of your product feeds and online marketing campaigns. Manipulating your product data is an extremely powerfull feature. Check out an example we have created in our blog post: <b><u><a href="https://adtribes.io/feature-product-data-manipulation/" target="_blank">Manipulating product data</a></u></b>','woo-product-feed-pro' );
				$message_type = "notice notice-info";
				break;
		}
		
		$this->notification_details['message'] = $message;
		$this->notification_details['message_type'] = $message_type;
		return $this->notification_details;
	}
}
