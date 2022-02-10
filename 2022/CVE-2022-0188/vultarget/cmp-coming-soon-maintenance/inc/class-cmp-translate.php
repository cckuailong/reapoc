<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Create a new table class that will extend the WP_List_Table
 */
class cmp_translate_table extends WP_List_Table {

	var $translate_list;

	function __construct(){

        if ( get_option('niteoCS_translation') ) {
            $this->translate_list = json_decode( get_option('niteoCS_translation'), true );

        } 

		parent::__construct( array(
			'singular'  => __( 'Translation string', 'cmp-coming-soon-maintenance' ),     //singular name of the listed records
            'plural'    => __( 'Tranlation String', 'cmp-coming-soon-maintenance' ),   //plural name of the listed records
        ));

	}
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    function prepare_items() {

        $columns 	= $this->get_columns();
        $hidden 	= $this->get_hidden_columns();
        $this->process_bulk_action();
        $data 		= $this->table_data();        
        $this->_column_headers = array($columns, $hidden);
        $this->items = $data;
    }



    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    function get_columns() {
        $columns = array(
            'string'        => __('String', 'cmp-coming-soon-maintenance'),
            'translation'   => __('Translation', 'cmp-coming-soon-maintenance'),


        );
        return $columns;
    }
    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    function get_hidden_columns() {
        return array();
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    function table_data() {       
    	return $this->translate_list;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'string':
                return $item[ $column_name ];
            case 'translation':
                return '<input type="text" name="niteoCS_translate_'.$item['id'].'" value="'.stripslashes( $item[ $column_name ] ).'" class="regular-text code">';
  
            default:
                return print_r( $item, true ) ;
        }
    }

	function no_items() {
	  _e( 'No Translation Variables!', 'cmp-coming-soon-maintenance' );
	}
}