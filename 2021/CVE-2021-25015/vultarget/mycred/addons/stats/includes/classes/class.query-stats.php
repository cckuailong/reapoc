<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Query Statistics
 * @see http://codex.mycred.me/classes/mycred_query_stats/ 
 * @since 1.7
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Query_Stats' ) ) :
	class myCRED_Query_Stats {

		protected $db = '';

		/**
		 * Construct
		 */
		public function __construct() {

			global $mycred;

			$this->db = $mycred->log_table;

		}

	}
endif;
