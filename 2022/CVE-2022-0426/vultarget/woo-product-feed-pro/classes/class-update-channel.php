<?php
/**
 * This class is called to:
 * - retrieve channel settings and configuration, function: get_channel_data
 * - update project configuration during steps, function: update_project
 * - add project configuration to cron option and clear current project config, function: add_project_cron
 */
class WooSEA_Update_Project {

	public $channel_data;
	public $channel_update;
	private $project_config;
	private $project_hash;

	/**
 	 * Get generic channel information
	 */
        public static function get_channel_data($channel_hash) {
		$channel_statics = get_option( 'channel_statics' );

		foreach ($channel_statics as $key=>$val){		
			
			foreach ($val as $k=>$v){
				if ($channel_hash === $v['channel_hash']){	
					$channel_data = $v;
				}
			}        
		}
		return $channel_data;
	}

	/**
	 * Get project configuration
	 */
	public static function get_project_data($project_hash) {
		if(get_option( 'cron_projects' )){
			$cron_projects = get_option( 'cron_projects' );
			$project_config = array();		
	
			foreach ($cron_projects as $key=>$val){		
				//if(!empty($val)){
				if(!empty($val['project_hash'])){
					if($val['project_hash'] === $project_hash){
						$project_config = $val;
					}	
				}
			}
			return $project_config;
		}
	}

	/**
	 * Update individual project configuration
	 */
	public static function update_project_data($project) {

		if(get_option( 'cron_projects' )){
			$cron_projects = get_option( 'cron_projects' );
	
			foreach ($cron_projects as $key=>$val){		
				if(!empty($val)){
					if($val['project_hash'] === $project['project_hash']){
						$cron_projects[$key] = $project;
						update_option('cron_projects', $cron_projects);
					}	
				}
			}
		}
	}

	public static function update_project($project_data){

       		// Log some information to the WooCommerce logs
            	$add_woosea_logging = get_option ('add_woosea_logging');
            	if($add_woosea_logging == "yes"){
               		$logger = new WC_Logger();
                    	$logger->add('Product Feed Pro by AdTribes.io','<!-- Start processing new product -->');
                      	$logger->add('Product Feed Pro by AdTribes.io','In update_project function');
                     	$logger->add('Product Feed Pro by AdTribes.io','<!-- End processing product -->');
             	}

		if(!array_key_exists('project_hash', $project_data)){
                	$upload_dir = wp_upload_dir();
                	$external_base = $upload_dir['baseurl'];
                	$external_path = $external_base . "/woo-product-feed-pro/" . $project_data['fileformat'];
			$channel_statics = get_option( 'channel_statics' );

			foreach ($channel_statics as $key=>$val){		
			
				foreach ($val as $k=>$v){
					if ($project_data['channel_hash'] == $v['channel_hash']){
						$project_fill = array_merge($v, $project_data);

						// New code to create the project hash so dependency on openSSL is removed	
						$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						$pieces = [];
						$length = 32;
						$max = mb_strlen($keyspace, '8bit') - 1;
						for ($i = 0; $i < $length; ++$i) {
        						$pieces []= $keyspace[random_int(0, $max)];
    						}
    						$project_fill['project_hash'] = implode('', $pieces);

						//$project_fill['project_hash'] = bin2hex(openssl_random_pseudo_bytes(16));

				            	if($add_woosea_logging == "yes"){
               						$logger = new WC_Logger();
                    					$logger->add('Product Feed Pro by AdTribes.io','<!-- Start processing new product -->');
                      					$logger->add('Product Feed Pro by AdTribes.io',$project_fill['project_hash']);
                     					$logger->add('Product Feed Pro by AdTribes.io','<!-- End processing product -->');
             					}
						$project_fill['filename'] = $project_fill['project_hash'];
						$project_fill['external_file'] = $external_path . "/" . sanitize_file_name($project_fill['filename']) . "." . $project_fill['fileformat'];
						$project_fill['query_log'] = $external_base . "/woo-product-feed-pro/logs/query.log";
						$project_fill['query_output_log'] = $external_base . "/woo-product-feed-pro/logs/query_output.log";
					}
				}
			}	
                	update_option( 'channel_project',$project_fill,'' );
		} else {
	             	$project_temp = get_option( 'channel_project' );
                        if(is_array($project_temp)){
                                $project_fill = array_merge($project_temp, $project_data);
                        } else {
                                $project_fill = $project_data;
                        }
                        update_option( 'channel_project',$project_fill,'' );	
		}
		return $project_fill;
	}

	/**
	 * This function takes care of updating project settings whenever an user updates a project
	 */
	public static function reconfigure_project($project_data){
		$project_hash = $project_data['project_hash'];

		if(get_option( 'cron_projects' )){
			$cron_projects = get_option( 'cron_projects' );
		
			foreach ($cron_projects as $key=>$val){	
				if(!empty($val)){
					foreach ($val as $k=>$v){
						if(!is_array($v)){
							if (($v === $project_hash) AND ($k === "project_hash")){
								$project_config = $val;
								$remove_key = $key;
							}	
						}
					}
				}
			}
		}
	
		/**
		 * Update project hash with new values
		 */

		foreach ($project_data as $key=>$val){
			$project_config[$key] = $val;
		}

                /**
                * Update some project configs
                */
                $project_config['last_updated'] = date("d M Y H:i");
                $count_products = wp_count_posts('product', 'product_variation');
                $count_variation = wp_count_posts('product_variation');
                $count_single = wp_count_posts('product');
                $published_single = $count_single->publish;
                $published_variation = $count_variation->publish;
                $published_products = $published_single+$published_variation; 
		$project_config['nr_products'] = $published_products;
                $project_config['nr_products_processed'] = 0;

		/**
		 * We might have to change the file extension
		 */
		$upload_dir = wp_upload_dir();
               	$external_base = $upload_dir['baseurl'];
                $external_path = $external_base . "/woo-product-feed-pro/" . $project_config['fileformat'];
		$project_config['external_file'] = $external_path . "/" . sanitize_file_name($project_config['filename']) . "." . $project_config['fileformat'];

		if((array_key_exists('woosea_page', $project_data)) AND ($project_data['woosea_page'] == "analytics")){
			/**
			 * Did the Google Analytics UTM code part got disabled?
			 */
			if(!array_key_exists('utm_on', $project_data)) {
				unset($project_config['utm_on']);
			}
			/**
			 * Did the conversion tracking got disabled?
			 */
			if(!array_key_exists('adtribes_conversion', $project_data)) {
				unset($project_config['adtribes_conversion']);
			}
		}

		/**
		 * Did the product variations support got disabled?
		 */
		if(array_key_exists('fileformat', $project_data)){
			if(!array_key_exists('product_variations', $project_data)) {
				unset($project_config['product_variations']);
				unset($project_config['productname_append']);
			}
		}

		/**
		 * Did the default product variations got disabled?
		 */
		if(array_key_exists('fileformat', $project_data)){
			if(!array_key_exists('default_variations', $project_data)) {
				unset($project_config['default_variations']);
			}
		}

		/**
		 * Did the lowest price product variations got disabled?
		 */
		if(array_key_exists('fileformat', $project_data)){
			if(!array_key_exists('lowest_price_variations', $project_data)) {
				unset($project_config['lowest_price_variations']);
			}
		}

		/**
		 * Did the option to only update the feed when products changed got disabled?
		 */
		if(array_key_exists('fileformat', $project_data)){
			if(!array_key_exists('products_changed', $project_data)) {
				unset($project_config['products_changed']);
			}
		}

		/**
		 * Did all the filters got removed
	  	 */
                if((array_key_exists('woosea_page', $project_data)) AND ($project_data['woosea_page'] == "filters_rules")){
			if(!array_key_exists('rules', $project_data)) {
				unset($project_config['rules']);
			}
		}

		/**
		 * Did all the rules got removed
		 */
                if((array_key_exists('woosea_page', $project_data)) AND ($project_data['woosea_page'] == "filters_rules")){
			if(!array_key_exists('rules2', $project_data)) {
				unset($project_config['rules2']);
			}
		}

		/**
		 * Did all the field manipulations  got removed
		 */
                if((array_key_exists('woosea_page', $project_data)) AND ($project_data['woosea_page'] == "field_manipulation")){
			if(!array_key_exists('field_manipulation', $project_data)) {
				unset($project_config['field_manipulation']);
			}
		}
 
		/**
		 * Update cron with new project settings
		 */
		$add_to_cron = WooSEA_Update_Project::add_project_cron($project_config, $remove_key);

		return $project_config;
	}

	/**
 	 * This function add's a project configuration to the list of projects needed for the cron
	 * it also delete's the current project configuration from the channel_project option for a next project
	 */
	public static function add_project_cron($project_data, $key){
		if(get_option( 'cron_projects' )){
			$cron_projects = get_option( 'cron_projects' );
			if(is_int($key)){
				$cron_projects[$key] = $project_data;
			} else {
				array_push($cron_projects, $project_data);
			}
			update_option( 'cron_projects', $cron_projects);
		} else {
			$cron_projects = array (); // Create a new multidimensional array for the cron projects
			array_push($cron_projects, $project_data);
			update_option( 'cron_projects', $cron_projects);
		}
			
		// Clear channel_project option
		delete_option( 'channel_project' );
	}
}
