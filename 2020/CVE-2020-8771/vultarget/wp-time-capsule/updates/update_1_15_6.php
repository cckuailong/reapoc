<?php

use AwsWPTC\S3\S3Client;

Class Wptc_Update_1_15_6{
	public function __construct($access_key, $secure_key, $bucket, $config){

		try {
			$client = S3Client::factory(array(
				'key'    => trim($access_key),
				'secret' => trim(str_replace(' ', '+', $secure_key)),
			));

			$region = $client->getBucketLocation(array('Bucket' => $bucket ));
			$location = $region->get('Location');
		}  catch (Exception $e) {
			$location = '';
		}

		$this->set_bucket_location($location, $config);
	}

	private function set_bucket_location($location, $config) {
		wptc_log($location, "--------" . __FUNCTION__ . "--------");
		$config->set_option('as3_bucket_region', $location);
	}
}