<?php
/**
 * CLass that will return HTML for template configuration step
 */
class Construct_Admin_Pages {
	private $page;
	public $html;
	public $channel;
	public $inputname;

	/**
         * Returns html template
	 */
	function set_page( $new_page ) {
		$file = dirname(dirname( __FILE__ )) . "/pages/admin/".$new_page.".php";

		if (file_exists( $file )){
			require $file;
		}
	}

	function get_page() {
		return $this->page;
	}
}
