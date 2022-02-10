<?php
class Secure_Copy_Content_Protection_Subscribe_Actions {
    private $plugin_name;

    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
    }

    public function store_data( $data ) {
        global $wpdb;
        if (isset($data["subscribe_action"]) && wp_verify_nonce($data["subscribe_action"], 'subscribe_action')) {
            
            $blocksub_id = isset($data['sccp_blocksub_id']) ? $data['sccp_blocksub_id'] : false;
            $deleted_ids = isset($data['deleted_ids']) ? $data['deleted_ids'] : false;

                $deleted_ids = explode(',', $deleted_ids);
            if ($deleted_ids) {
                foreach ( $deleted_ids as $val ) {
                    $wpdb->delete( esc_sql(SCCP_BLOCK_SUBSCRIBE),
                            array(
                                'id' => esc_sql($val)
                            ),
                        array( '%d' )
                    );
                }
            }

            if ($blocksub_id) {
                foreach ( $blocksub_id as $key => $id_val ) {
                    $id    = absint( intval(esc_sql($id_val)));
                    $bs_table = esc_sql(SCCP_BLOCK_SUBSCRIBE);

                    $bs_result = $wpdb->get_row(
                        $wpdb->prepare( 'SELECT * FROM '. $bs_table .' WHERE id = %d',
                            $id
                        )
                    );

                    $enable_name_field = (isset($data["sccp_enable_block_sub_name_field"][$id_val])) ? "on" : "off";
                    $sccp_wpdb_id = isset($bs_result) ? absint( intval($bs_result->id)) : null;
                    $bs_options = array(
                        'require_verification' => $data['sub_require_verification'][$key],
                        "enable_name_field"    => $enable_name_field,
                    );
                    
                    $bs_options = json_encode($bs_options);

                    if ($sccp_wpdb_id != $id) {
                        $wpdb->insert( $bs_table,
                            array(                           
                                'options'   => $bs_options
                            ),
                            array( '%s' )
                        );
                    }else{
                        $wpdb->update( $bs_table,
                            array(
                                'options'   => $bs_options
                            ),
                            array( 'id' => $id ),
                            array( '%s' ),
                            array( '%d' )
                        );
                    }       
                }
            }

        }
    }

    public function get_data() {
        global $wpdb;

        $bs_table = esc_sql(SCCP_BLOCK_SUBSCRIBE);
        $sql = "SELECT * FROM " . $bs_table . " ORDER BY `id`";
        $block_subscribe = $wpdb->get_results($sql, ARRAY_A);
        $l_id = self::sccp_get_bs_last_id();
        if (!empty($block_subscribe)) {
            $block_subscribe_data = $block_subscribe;
        } else {
            $block_subscribe_data = array(
                array(
                    "id"   => $l_id->AUTO_INCREMENT,
                    "options"   => ""
                )
            );
        }

        return $block_subscribe_data;
    }   

    public function sccp_subscribe_notices($status){

        if ( empty( $status ) )
            return;

        if ( 'saved' == $status )
            $updated_message = esc_html( __( 'Changes saved.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'SCCP subscribe.', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'SCCP subscribe deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }

    public function sccp_get_bs_last_id(){
        global $wpdb;
        $bs_table = esc_sql(SCCP_BLOCK_SUBSCRIBE);

        $lastId = $wpdb->get_row(
            $wpdb->prepare( 'SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s',
                $wpdb->dbname, $bs_table
            )
        );
        return $lastId;
    }

    public function sccp_get_last_id_check(){
        global $wpdb;
        $bs_table = esc_sql(SCCP_BLOCK_SUBSCRIBE);

        $checker = "SELECT id FROM ". $bs_table;
        $results = $wpdb->get_results($checker, "ARRAY_A");

        return $results;
    }

}