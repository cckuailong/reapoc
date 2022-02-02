<?php

Class WPTC_Activities{

	private $list_table;
	private $type;
	private $uri;

	function __construct(){
		$this->include_files();
		$this->init();
		$this->set_type();
		$this->add_type();
		$this->list_table->display();
	}

	private function include_files(){
		wp_enqueue_script('wptc-activity', plugins_url() . '/' . WPTC_TC_PLUGIN_NAME . '/Views/wptc-activity.js', array(), WPTC_VERSION);
		require_once ( WP_PLUGIN_DIR.'/wp-time-capsule/Classes/ActivityLog.php' );
	}

	private function init(){
		$this->list_table = new WPTC_List_Table();
		$this->list_table->prepare_items();
		$this->uri = network_admin_url() . 'admin.php?page=wp-time-capsule-activity';
	}

	private function set_type(){
		$this->type = isset( $_GET['type'] ) ? $_GET['type'] : 'all';
	}

	private function print_header(){
		return "<h2> Activity Log & Report </h2>";
	}

	private function add_type(){
		$html  = '';
		$html .= $this->print_header();
		$html .= '<div class="tablenav"> 	<ul class="subsubsub">';

		foreach ($this->list_table->all_types as $type => $value) {
			$class = ( $this->type === $type ) ? 'current' : '';
			$html .= "<li>
				<a href=" . $this->uri . "&type=" . $type . " id=" . $type . " class=" . $class . "> " . $value['title'] ." <span class='count'></span></a> |
			</li>";
			# code...
		}

		$html .= '</ul> <ul class="subsubsub" style="float: right; margin-right: 20px; cursor: pointer;"> <li>
					<a id="wptc_clear_log">Clear Logs</a> </li> </ul> </div>';

		echo $html;
	}

}

new WPTC_Activities();