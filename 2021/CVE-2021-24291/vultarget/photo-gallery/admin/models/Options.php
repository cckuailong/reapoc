<?php

/**
 * Class OptionsModel_bwg
 */
class OptionsModel_bwg {

  /**
   * Set or reset Instagram credentials and access token
   *
   * @param bool $reset
   * @return bool
   */
	function set_instagram_access_token( $reset = true ) {
		$row = new WD_BWG_Options();
    $row->instagram_access_token = $reset ? '' : WDWLibrary::get('wdi_access_token');
    $row->instagram_access_token_start_in = $reset ? '' : time();;
    $row->instagram_access_token_expires_in = $reset ? '' : WDWLibrary::get('expires_in');
    $row->instagram_user_id = $reset ? '' : WDWLibrary::get('user_id');
    $row->instagram_username = $reset ? '' : WDWLibrary::get('username');
		$upd = update_option('wd_bwg_options', json_encode($row));
		return $upd;
	}

    /**
     * Get images count.
     *
     * @return int $imgcount
     */
    public function get_image_count() {
        global $wpdb;
        $imgcount = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "bwg_image");
        return $imgcount;
    }

  /**
   * Update gallery options by key.
   *
   * @param $data_params
  */
  public function update_options_by_key( $data_params = array() ) {
      $options = get_option( 'wd_bwg_options' );
      if ($options) {
        $options = json_decode( $options );

        foreach( $data_params as $key => $value ) {
          $options->$key = $value;
        }
        update_option( 'wd_bwg_options', json_encode($options), 'yes' );
      }
    }

}