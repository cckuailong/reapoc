<?php
class Sccp_Settings_Actions {
    private $plugin_name;

    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        // $this->check_setting_mailchimp();
    }

    public function store_data($data){
        global $wpdb;
        $settings_table = $wpdb->prefix . "ays_sccp_settings";
        if( isset($data["settings_action"]) && wp_verify_nonce( $data["settings_action"], 'settings_action' ) ){
            $success = 0;            
            $mailchimp_username = isset($data['ays_mailchimp_username']) ? $data['ays_mailchimp_username'] : '';
            $mailchimp_api_key = isset($data['ays_mailchimp_api_key']) ? $data['ays_mailchimp_api_key'] : '';
            $mailchimp = array(
                'username' => $mailchimp_username,
                'apiKey' => $mailchimp_api_key
            );
            // WP Editor height
            $sccp_wp_editor_height = (isset($data['ays_sccp_wp_editor_height']) && $data['ays_sccp_wp_editor_height'] != '') ? absint( sanitize_text_field($data['ays_sccp_wp_editor_height']) ) : 150 ;

            $sccp_disable_user_ip = (isset( $data['ays_sccp_disable_user_ip'] ) && sanitize_text_field( $data['ays_sccp_disable_user_ip'] ) == 'on') ? 'on' : 'off';


            $options = array(
                "sccp_wp_editor_height" => $sccp_wp_editor_height,
                "sccp_disable_user_ip" => $sccp_disable_user_ip,
            );

            $result = $this->ays_update_setting('mailchimp', json_encode($mailchimp));
            if ($result) {
                $success++;
            }
            $result = $this->ays_update_setting('options', json_encode($options));
            if ($result) {
                $success++;
            }

            $message = "saved";
            if($success > 0){
                $tab = "";
                if(isset($data['ays_sccp_tab'])){
                    $tab = "&ays_sccp_tab=".$data['ays_sccp_tab'];
                }
                $url = admin_url('admin.php') . "?page=secure-copy-content-protection-settings" . $tab . '&status=' . $message;
                wp_redirect( $url );
            }
        }

    }

    public function get_db_data(){
        global $wpdb;
        $settings_table = esc_sql($wpdb->prefix . "ays_sccp_settings");
        $sql = "SELECT * FROM ".$settings_table;
        
        $results = $wpdb->get_results($sql, 'ARRAY_A');
        if(count($results) > 0){
            return $results;
        }else{
            return array();
        }
    }

    public function check_setting_mailchimp(){
        global $wpdb;
        $settings_table = esc_sql($wpdb->prefix . "ays_sccp_settings");
        $mailchimp = esc_sql('mailchimp');
        $sql = "SELECT COUNT(*) FROM ".$settings_table." WHERE meta_key = %s";

        $result = $wpdb->get_var(
                    $wpdb->prepare( $sql, $mailchimp)
                  );

        if(intval($result) == 0){
            $this->ays_add_setting("mailchimp", "", "", "");
        }
        return false;
    }

    public function ays_get_setting($meta_key){
        global $wpdb;
        $settings_table = esc_sql($wpdb->prefix . "ays_sccp_settings");
        $key_meta = esc_sql($meta_key);

        $sql = "SELECT meta_value FROM ".$settings_table." WHERE meta_key = %s";

        $result = $wpdb->get_var(
                    $wpdb->prepare( $sql, $key_meta)
                  );

        if($result != ""){
            return $result;
        }
        return false;
    }

    public function ays_add_setting($meta_key, $meta_value, $note = "", $options = ""){
        global $wpdb;
        $settings_table = $wpdb->prefix . "ays_sccp_settings";
        $result = $wpdb->insert(
            $settings_table,
            array(
                'meta_key'    => $meta_key,
                'meta_value'  => $meta_value,
                'note'        => $note,
                'options'     => $options
            ),
            array( '%s', '%s', '%s', '%s' )
        );
        if($result >= 0){
            return true;
        }
        return false;
    }

    public function ays_update_setting($meta_key, $meta_value, $note = null, $options = null){
        global $wpdb;
        $settings_table = $wpdb->prefix . "ays_sccp_settings";
        $value = array(
            'meta_value'  => $meta_value,
        );
        $value_s = array( '%s' );
        if($note != null){
            $value['note'] = $note;
            $value_s[] = '%s';
        }
        if($options != null){
            $value['options'] = $options;
            $value_s[] = '%s';
        }
        $result = $wpdb->update(
            $settings_table,
            $value,
            array( 'meta_key' => $meta_key, ),
            $value_s,
            array( '%s' )
        );
        if($result >= 0){
            return true;
        }
        return false;
    }

    public function ays_delete_setting($meta_key){
        global $wpdb;
        $settings_table = $wpdb->prefix . "ays_sccp_settings";
        $wpdb->delete(
            $settings_table,
            array( 'meta_key' => $meta_key ),
            array( '%s' )
        );
    }

    public function sccp_settings_notices($status){

        if ( empty( $status ) )
            return;

        if ( 'saved' == $status )
            $updated_message = esc_html( __( 'Changes saved.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'SCCP attribute .', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'SCCP attribute deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }

}