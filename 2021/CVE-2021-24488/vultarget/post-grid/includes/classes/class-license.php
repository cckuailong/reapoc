<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

class class_post_grid_license{
	
	public function __construct(){
		
		//add_action('admin_notices', array( $this, 'admin_notices_please_activate' ));
		
		}
	
	public function license_status(){
					
		$post_grid_license = get_option('post_grid_license');	
	
	}
	
	

	function days_remaining($date_expiry) {

		$gmt_offset = get_option('gmt_offset');
		$today = date('Y-m-d h:i:s', strtotime('+'.$gmt_offset.' hour'));
		$today = strtotime($today);
		//var_dump(strtotime($today));
		$response = array();
		$response['class'] = 'active';
		
		
		$date_expiry = strtotime($date_expiry);
		//$today = time();		
		
		//var_dump($today);
		
		
		$diff = $date_expiry-$today;
		
		if($diff<0){
			
			$response['diff'] = $diff;			
			$response['text'] = __('Expired', 'post-grid');
			$response['class'] = 'expired';
			}
		else{
			
			$minute = floor(($diff % 3600)/60);
			$hour = floor(($diff % 86400)/3600);
			$day = floor(($diff % 2592000)/86400);
			$month = floor($diff/2592000);
			$year = floor($diff/(86400*365));		
			
			if($year>0){
				$response['text'] =  number_format_i18n($year) .' '.__('year', 'post-grid');
				}
					
			elseif($month > 0 && $day<=12 ){
				$response['text'] = number_format_i18n($month) .' '.__('month', 'post-grid');
				}
				
			elseif($day > 0 && $day<=30){
				$response['text'] = number_format_i18n($day).' '.__('day', 'post-grid');
				}
				
			elseif($hour > 0 && $hour<=24){
				$response['text'] = number_format_i18n($hour).' '.__('hour', 'post-grid');
				}		
				
			elseif($minute > 0 && $minute<60){
				$response['text'] = number_format_i18n($minute).' '.__('minute', 'post-grid');
				}	
					
			else{
				$response['text'] = $diff.' '.__('second', 'post-grid');;
				}
			
			}
		
		

			
			
		return $response;	
			
			
			
			
		
	}	
	
	
	
	
	
	
	
	
	
	
	
	
	public function admin_notices_please_activate(){
					
		$post_grid_license = get_option('post_grid_license');
		
		if(!empty($post_grid_license['status'])){
			
			$license_status = $post_grid_license['status'];
			}
		else{
			
			$license_status = '';
			}

	// var_dump($license_status);




		$html= '';

		if(empty($license_status)){
			
				$admin_url = get_admin_url();
				
				$html.= '<div class="update-nag">';
				
				$html.= sprintf(__('Please activate your license for <b>%s &raquo; <a href="%sedit.php?post_type=post_grid&page=license">License</a></b>', 'post-grid'), post_grid_plugin_name, $admin_url);
				
				
				$html.= '</div>';	
			}
		else
			{

			}

		echo $html;


		}	
	


	}
	
new class_post_grid_license();