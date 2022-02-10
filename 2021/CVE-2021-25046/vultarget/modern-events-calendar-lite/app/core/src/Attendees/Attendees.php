<?php

namespace MEC\Attendees;

use MEC\Singleton;

class Attendees extends Singleton{

    public $tbl;

    public function __construct(){

        global $wpdb;
        $this->tbl = $wpdb->prefix.'mec_attendees';
    }

    private function get_where( $conditions = array()){

        $where = "1";

        if( !is_array($conditions) || empty($conditions) ){

            return $where;
        }

        global $wpdb;
        foreach($conditions as $k => $v){

            switch($k){

                case 'post_id':
                case 'event_id':
                case 'occurrence':
                case 'email':
                case 'first_name':
                case 'last_name':
                case 'count':
                case 'verification':
                case 'confirmation':
                    if($v){

                        if( is_array( $v ) && !isset( $v['compare'] ) ){

                            $v = is_array($v) ? "'" . implode("','",$v) . "'" : $v;
                            $where .= " AND `{$k}` IN (".$v.")";
                        }elseif( is_array( $v ) && isset( $v['compare'] ) ){

                            $compare = isset( $v['compare'] ) ? $v['compare'] : 'IN';
                            $v = isset( $v['value'] ) ? $v['value'] : false;
                            if( !$v ){

                                break;
                            }

                            $v = is_array($v) ? "'" . implode("','",$v) . "'" : $v;
                            $where .= " AND `{$k}` {$compare} (".$v.")";
                        } else {

                            $where .= $wpdb->prepare(
                                " AND `{$k}` = '%s'",
                                $v
                            );
                        }
                    }
                break;
            }
        }

        return $where;
    }

    public function get_rows( $conditions, $fields = '*' ){

        global $wpdb;
        $where = $this->get_where( $conditions );

        $fields = is_array($fields) && !empty($fields) ? "`" . implode( '`,`', $fields ) . "`" : $fields;
        $fields = !empty($fields) ? $fields : '*';

        $sql = "SELECT {$fields} FROM {$this->tbl} WHERE {$where}";

        return $wpdb->get_results($sql,ARRAY_A);
    }

    public function _get_attendees( $conditions = array(), $return_by_post_and_occurrence_data = false ){

        $rows = $this->get_rows( $conditions);

        $attendees = [];
        if(!empty($rows) && is_array($rows)){

            foreach( $rows as $row ){

                $attendee_id = $row['attendee_id'];
                $data = maybe_unserialize($row['data']);

                $attendee = [
                    'attendee_id' => $attendee_id,
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'name' => $row['first_name'] .' '.$row['last_name'],
                    'email' => $row['email'],
                    'count' => $row['count'],
                    'verification' => $row['verification'],
                    'confirmation' => $row['confirmation'],
                    'reg' => is_array($data) ? $data : [],
                ];

                if($return_by_post_and_occurrence_data){

                    $attendee['post_id'] = $row['post_id'];
                    $attendee['event_id'] = $row['event_id'];
                    $attendee['occurrence'] = $row['occurrence'];
                }

                $attendees[$attendee_id] = $attendee;
            }
        }

        return $attendees;
    }

    private function _insert( $attendee_data ){

        global $wpdb;
        $attendee = [
            'post_id' => (int)$attendee_data['post_id'],
            'event_id' => (int)$attendee_data['event_id'],
            'occurrence' => (int)$attendee_data['occurrence'],
            'email' => $attendee_data['email'],
            'first_name' => $attendee_data['first_name'],
            'last_name' => $attendee_data['last_name'],
            'data' => $attendee_data['data'],
            'count' => isset($attendee_data['count']) && $attendee_data['count'] > 0 ? (int)$attendee_data['count'] : 1,
            'verification' => isset($attendee_data['verification']) ? $attendee_data['verification'] : 0,
            'confirmation' => isset($attendee_data['confirmation']) ? $attendee_data['confirmation'] : 0,
        ];

        $v_type = ['%d','%d','%d','%s','%s','%s','%s','%d','%d','%d'];

        return $wpdb->insert($this->tbl, $attendee, $v_type );
    }

    private function _update( $attendee_data, $where ){

        if(empty($where)){

            return false;
        }

        global $wpdb;

        $attendee = [
            'post_id' => '',
            'event_id' => '',
            'occurrence' => '',
            'email' => '',
            'first_name' => '',
            'last_name' => '',
            'data' => '',
            'count' => '',
            'verification' => '',
            'confirmation' => '',

        ];

        $v_type = ['%d','%d','%d','%s','%s','%s','%s','%d','%d','%d'];

        foreach($attendee as $k => $v){

            switch($k){

                case 'post_id':
                case 'event_id':
                case 'occurrence':
                case 'email':
                case 'first_name':
                case 'last_name':
                case 'count':
                case 'verification':
                case 'confirmation':
                case 'data':
                    if(isset($attendee_data[$k])){

                        $attendee[$k] = $attendee_data[$k];
                    }else{

                        unset($attendee[$k]);
                    }
                break;
            }
        }

        return $wpdb->update($this->tbl, $attendee, $where, $v_type );
    }

    private function _delete( $conditions ){

        global $wpdb;
        return $wpdb->delete($this->tbl,$conditions);
    }

    private function _add_or_update( $attendee ){

        $post_id = isset($attendee['post_id']) ? (int)$attendee['post_id'] : 0;
        $event_id = isset($attendee['event_id']) ? (int)$attendee['event_id'] : 0;
        $occurrence = isset($attendee['occurrence']) ? (int)$attendee['occurrence'] : 0;
        $attendee['email'] =  isset($attendee['email']) ? sanitize_email($attendee['email']) : 0;
        $email = $attendee['email'];

        if( !$post_id || !$event_id || !$occurrence || !$email ){

            return false;
        }

        $attendee['data'] = isset($attendee['data']) ? serialize($attendee['data']) : '';
        $existed = $this->is_existed( $post_id, $event_id, $occurrence, $email );

        if( !$existed ){

            $r = $this->_insert($attendee);
        }elseif( $existed ){

            $where['attendee_id'] = $existed;
            $r = $this->_update($attendee, $where);
        }

        return $r;
    }

    public function get_attendees( $post_id, $event_id, $occurrence, $return_cached = true ){

        $cached = $return_cached ? $this->get_cache( $post_id, $event_id, $occurrence ) : false;
        if( $cached ){

            return $cached;
        }

        $conditions = [
            'post_id' => $post_id,
            'event_id' => $event_id,
            'occurrence' => $occurrence,
        ];

        $attendees = $this->_get_attendees( $conditions );
        $this->update_cache( $post_id, $event_id, $occurrence, $attendees );

        return $attendees;
    }

    public function is_existed( $post_id, $event_id, $occurrence, $email ){

        $conditions = [
            'post_id' => $post_id,
            'event_id' => $event_id,
            'occurrence' => $occurrence,
            'email' => $email,
        ];

        $r = $this->_get_attendees($conditions);

        if(count($r)){

            return key($r);
        }

        return false;
    }

    public function add_or_update( $post_id, $event_id, $occurrence, $attendees ){

        $success = null;
        if( !$post_id || !$event_id || !$occurrence || !is_array($attendees) ){

            return false;
        }

        $new_attendees = [];
        foreach( $attendees as $attendee ){

            $email = isset($attendee['email']) ? sanitize_email($attendee['email']) : 0;
            if(!$email){

                continue;
            }

            $data = isset($attendee['data']) ? (array)$attendee['data'] : [];
            $data = empty($data) && isset($attendee['reg']) ? (array)$attendee['reg'] : $data;
            unset($data['reg']);

            $attendee['post_id'] = $post_id;
            $attendee['event_id'] = $event_id;
            $attendee['occurrence'] = $occurrence;
            $attendee['data'] = $data;
            $attendee['count'] = 1;

            if(isset($new_attendees[$email])){

                $new_attendees[$email]['count']++;

                continue;
            }

            $new_attendees[$email] = $attendee;
        }

        foreach( $new_attendees as $attendee ){

            $s = $this->_add_or_update( $attendee );
            $success = !is_null($success) ? $success : true;
            $success = $success && $s;
        }

        $this->clear_cache( $post_id, $event_id, $occurrence );

        return $success;
    }

    public function update_attendees( $post_id, $event_id, $occurrence, $attendees ){

        if( !$post_id || !$event_id || !$occurrence || !is_array($attendees) ){

            return false;
        }

        $saved_attendees = $this->get_attendees( $post_id, $event_id, $occurrence, false );

        $saved_emails = array_column($saved_attendees,'email','attendee_id');
        $emails = array_column($attendees,'email');

        foreach( $saved_emails as $attendee_id => $saved_email ){

            if( false === array_search( $saved_email, $emails ) ){

                $conditions = [
                    'attendee_id' => $attendee_id,
                ];
                $this->_delete($conditions);
            }
        }

        return $this->add_or_update( $post_id, $event_id, $occurrence , $attendees );
    }

    public function update_attendee_status( $status_key, $status, $post_id, $email = null, $event_id = null, $occurrence = null ){

        if( !in_array( $status_key, [ 'confirmation', 'verification' ] ) ){

            return;
        }

        $conditions = [
            'post_id' => $post_id,
            'event_id' => $event_id,
            'occurrence' => $occurrence,
            'email' => $email,
        ];

        $attendees = $this->_get_attendees( $conditions, true );

        foreach( $attendees as $k => $attendee ){

            $attendee_id = $attendee['attendee_id'];

            if(!$attendee_id){

                continue;
            }

            $saved_status = $attendee[$status_key];
            if( $status === $saved_status ){

                unset($attendees[$k]);
                continue;
            }

            $attendee[$status_key] = (int)$status;
            $where = array(
                'attendee_id' => $attendee_id
            );

            $this->_update( $attendee , $where );
        }
    }

    public function update_confirmation_status( $status, $post_id, $email = null, $event_id = null, $occurrence = null ){

        return $this->update_attendee_status( 'confirmation', $status, $post_id, $email, $event_id, $occurrence );
    }

    public function update_verification_status( $status, $post_id, $email = null, $event_id = null, $occurrence = null ){

        return $this->update_attendee_status( 'verification', $status, $post_id, $email, $event_id, $occurrence );
    }

    public function delete( $post_id, $email = null, $event_id = null, $occurrence = null ){

        $conditions['post_id'] = $post_id;

        if( !is_null($email) ){

            $conditions['email'] = $email;
        }

        if( !is_null($event_id) ){

            $conditions['event_id'] = $event_id;
        }

        if( !is_null($occurrence) ){

            $conditions['occurrence'] = $occurrence;
        }

        $this->_delete( $conditions );
    }

    public function get_attendees_emails( $post_id = null, $event_id = null, $occurrence = null, $confirmation = null, $verification = null ){

        $conditions = [];

        if( !is_null($post_id) ){

            $conditions['post_id'] = $post_id;
        }

        if( !is_null($event_id) ){

            $conditions['event_id'] = $event_id;
        }

        if( !is_null($occurrence) ){

            $conditions['occurrence'] = $occurrence;
        }

        if( !is_null($confirmation) ){

            $conditions['confirmation'] = $confirmation;
        }

        if( !is_null($verification) ){

            $conditions['verification'] = $verification;
        }

        $emails = $this->get_rows($conditions,'email');
        $emails = array_column( $emails, 'email' );

        $emails = array_unique($emails);

        return $emails;
    }

    /**
     * @param string $email
     * @param int $post_id
     * @param int $event_id
     * @param int $occurrence
     * @return array|int
     */
    public function get_total_attendees_group_by_email( $email = null, $post_id = null, $event_id = null, $occurrence = null, $exclude_post_ids = [], $confirmation = 1, $verification = 1 ){

        $conditions = [];

        if( !is_null($email) ){

            $conditions['email'] = $email;
        }

        if( !is_null($post_id) ){

            $conditions['post_id'] = $post_id;
        }

        if( !is_null($event_id) ){

            $conditions['event_id'] = $event_id;
        }

        if( !is_null($occurrence) ){

            $conditions['occurrence'] = $occurrence;
        }

        if($exclude_post_ids){

            $exclude_post_ids = is_array($exclude_post_ids) ? $exclude_post_ids : explode(',',$exclude_post_ids);
            $conditions['post_id'] = [
                'compare' => 'NOT IN',
                'value' => $exclude_post_ids,
            ];
        }

        if( !is_null($confirmation) ){

            $conditions['confirmation'] = $confirmation;
        }

        if( !is_null($verification) ){

            $conditions['verification'] = $verification;
        }

        $total_used_emails = [];
        $rows = $this->get_rows($conditions,'`email`,`count`');

        foreach($rows as $row){

            $r_email = $row['email'];
            $r_count = $row['count'];

            if( !isset( $total_used_emails[ $r_email ] ) ){
                $total_used_emails[ $r_email ] = 0;
            }

            $total_used_emails[ $r_email ] += $r_count;
        }

        if(empty($email)){

            return $total_used_emails;
        }

        return isset($total_used_emails[$email]) ? (int)$total_used_emails[$email] : 0;
    }

    public function get_cache( $post_id, $event_id, $occurrence ){

        $cache_key = 'mec-attendees-'.$post_id.'-'.$event_id.'-'.$occurrence;
        return get_transient( $cache_key );
    }

    public function update_cache( $post_id, $event_id, $occurrence, $attendees ){

        $cache_key = 'mec-attendees-'.$post_id.'-'.$event_id.'-'.$occurrence;
        set_transient( $cache_key, $attendees, 3600 );
    }

    public function clear_cache( $post_id, $event_id, $occurrence ){

        $cache_key = 'mec-attendees-'.$post_id.'-'.$event_id.'-'.$occurrence;
        delete_transient( $cache_key );
    }
}