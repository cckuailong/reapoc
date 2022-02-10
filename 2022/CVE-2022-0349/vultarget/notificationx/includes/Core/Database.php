<?php

/**
 * Extension Factory
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Core;

use NotificationX\GetInstance;

/**
 * ExtensionFactory Class
 */
class Database {
    /**
     * Instance of Database
     *
     * @var Database
     */
    use GetInstance;

    /**
     * WordPress database abstraction object.
     *
     * @var \wpdb
     */
    protected $wpdb;
    public static $version = '2.1';
    public static $table_entries;
    public static $table_posts;
    public static $table_stats;

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb          = $wpdb;
        self::$table_entries = $wpdb->prefix . 'nx_entries';
        self::$table_posts   = $wpdb->prefix . 'nx_posts';
        self::$table_stats   = $wpdb->prefix . 'nx_stats';
    }


    public function Create_DB() {
        $charset_collate = $this->wpdb->get_charset_collate();
        $table_posts     = self::$table_posts;
        $table_entries   = self::$table_entries;
        $table_stats     = self::$table_stats;

        $sql = "CREATE TABLE {$table_entries} (
                entry_id bigint(20) unsigned NOT NULL auto_increment,
                nx_id bigint(20) unsigned NULL,
                source varchar(55) default NULL,
                entry_key varchar(255) default NULL,
                data longtext,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                PRIMARY KEY (entry_id),
                KEY source (source),
                KEY nx_id (nx_id)
            ) $charset_collate ;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $entries_db = dbDelta( $sql );

        $sql     = "CREATE TABLE {$table_posts} (
                nx_id bigint(20) unsigned NOT NULL auto_increment,
                title text default NULL,
                type varchar(55) default NULL,
                source varchar(55) default NULL,
                theme varchar(55) default NULL,
                is_inline varchar(255) default NULL,
                global_queue BOOLEAN default false,
                enabled BOOLEAN default false,
                data longtext,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,
                PRIMARY KEY  (nx_id),
                KEY type (type),
                KEY source (source),
                KEY theme (theme)
            ) $charset_collate ;";
        $post_db = dbDelta( $sql );

        $sql      = "CREATE TABLE {$table_stats} (
                stat_id bigint(20) unsigned NOT NULL auto_increment,
                nx_id bigint(20) unsigned default NULL,
                views varchar(55) default 0,
                clicks varchar(55) default 0,
                created_at DATE NOT NULL,
                PRIMARY KEY (stat_id),
                KEY nx_id (nx_id)
            ) $charset_collate ;";
        $stats_db = dbDelta( $sql );

    }

    public function update_analytics( $col, $id, $date, $data = null ) {
        $table_name = self::$table_stats;
        $_data = is_null( $data ) ? 1 : $data;

        return $this->wpdb->query( $this->wpdb->prepare( '
            UPDATE %1$s
            SET %2$s = %3$s + %4$s
            WHERE nx_id = "%5$s"
            AND created_at = "%6$s"',
            $table_name, $col, $col, $_data, intval( $id ), $date
        )
        );
    }

    public function insert_post( $table_name, $post ) {
        $post = $this->serialize_data( $post );
        $this->wpdb->insert( $table_name, $post );
        return $this->wpdb->insert_id;
    }

    public function insert_posts( $table_name, $posts ) {
        if ( ! empty( $posts[0] ) ) {
            $values        = array();
            $place_holders = array();
            $_column       = array_keys( $posts[0] );
            $columns       = implode( ', ', $_column );
            $query         = "INSERT INTO $table_name ($columns) VALUES ";
            foreach ( $posts as $key => $entry ) {
                if ( ! empty( $entry['data'] ) ) {
                    $entry['data'] = maybe_serialize( $entry['data'] );
                }
                reset( $_column );
                foreach ( $_column as $col ) {
                    $values[] = isset( $entry[ $col ] ) ? $entry[ $col ] : '';
                }
                // $values = array_merge($values, array_values($entry));
                $place_holders[] = '(' . implode( ', ', array_fill( 0, count( $entry ), "'%s'" ) ) . ')'; /* In my case, i know they will always be integers */
            }
            $query .= implode( ', ', $place_holders );
            $query  = $this->wpdb->prepare( "$query", $values );
            $this->wpdb->query( $query );
        }
    }

    public function update_post( $table_name, $post, $where__or_pid ) {
        $post = $this->serialize_data( $post );
        if ( ! is_array( $where__or_pid ) ) {
            $id            = $this->get_primary_col( $table_name );
            $where__or_pid = [ $id => $where__or_pid ];
        }
        return $this->wpdb->update( $table_name, $post, $where__or_pid );
    }

    public function get_post( $table_name, $where__or_pid, $select = '*' ) {
        if ( ! is_array( $where__or_pid ) ) {
            $id            = $this->get_primary_col( $table_name );
            $where__or_pid = [ $id => $where__or_pid ];
        }
        $posts = $this->get_posts( $table_name, $select, $where__or_pid );
        return ! empty( $posts[0] ) ? $posts[0] : null;
    }

    public function get_posts( $table_name, $select = '*', $wheres = [], $join_table = '', $group_by_col = '', $join_type = 'LEFT JOIN', $extra_query = '' ) {
        $query = "SELECT $select FROM $table_name";
        if ( ! empty( $join_table ) ) {
            $query .= " AS a $join_type `$join_table` AS b ON a.nx_id = b.nx_id";
        }
        $query .= $this->get_where_query( $wheres );
        if ( ! empty( $group_by_col ) ) {
            $query .= " GROUP BY $group_by_col";
        }
        $posts = $this->wpdb->get_results( "$query $extra_query", ARRAY_A );
        $posts = array_map( [ $this, 'unserialize_data' ], $posts );
        return $posts;
    }

    public function get_col( $table_name, $col, $wheres, $distinct = 'DISTINCT' ) {
        $posts = $this->get_posts( $table_name, "$distinct $col", $wheres );
        return array_column( $posts, $col );
    }

    // public function get_count($table_name, $col, $wheres) {
    // $posts = $this->get_posts($table_name, "$col", $wheres);
    // return array_column($posts, $col, 0);
    // }

    public function get_source_count( $table_name, $col, $wheres = [] ) {
        $results = [];
        $posts   = $this->get_posts( $table_name, "$col, count(*)", $wheres, '', $col );
        foreach ( $posts as $key => $value ) {
            $results[ $value[ $col ] ] = $value['count(*)'];
        }
        return $results;
    }

    public function delete_post( $table_name, $post_id ) {
        $id = $this->get_primary_col( $table_name );
        return $this->delete_posts( $table_name, [ $id => $post_id ] );
    }

    public function delete_posts( $table_name, $wheres, $limit = 0 ) {
        if ( $limit ) {
            return $this->delete_posts_limit( $table_name, $wheres, $limit );
        }
        return $this->wpdb->delete( $table_name, $wheres );
    }

    public function delete_posts_limit( $table_name, $wheres, $limit ) {
        $query  = "DELETE FROM $table_name ";
        $query .= $this->get_where_query( $wheres );
        $query .= " LIMIT $limit";
        return $this->wpdb->query( $query );
    }

    public function serialize_data( $post ) {
        if ( ! empty( $post['data'] ) ) {
            $post['data'] = maybe_serialize( $post['data'] );
        }
        return $post;
    }

    public function unserialize_data( $post ) {
        if ( ! empty( $post['data'] ) ) {
            $post['data'] = maybe_unserialize( $post['data'] );
        }
        return $post;
    }

    public function get_primary_col( $table_name ) {
        if ( $table_name == self::$table_posts ) {
            return 'nx_id';
        } elseif ( $table_name == self::$table_entries ) {
            return 'entry_id';
        } elseif ( $table_name == self::$table_stats ) {
            return 'stat_id';
        }
    }

    public function get_where_query( $wheres ) {
        $query = '';
        if ( ! empty( $wheres ) && is_string( $wheres ) ) {
            return " $wheres ";
        } elseif ( ! empty( $wheres ) ) {
            $query .= ' WHERE true=true';
            foreach ( $wheres as $key => $value ) {
                $compare = '=';
                if ( is_array( $value ) ) {
                    $compare = $value[0];
                    $value   = $value[1];
                } else {
                    $value = "'" . esc_sql( $value ) . "'"; // is_bool($value) ? $value :.
                }
                $query .= " AND $key $compare $value";
            }
        }
        return $query;
    }

    public function update_option( $key, $value, $autoload = 'no' ) {
        $is_exists = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->prefix}options WHERE option_name=%s LIMIT 1", $key ) );
        if ( $is_exists ) {
            if ( $is_exists->option_value == $value ) {
                return;
            }
            $this->wpdb->update( "{$this->wpdb->options}", [ 'option_value' => $value ], [ 'option_name' => $key ] );
        } else {
            $this->wpdb->insert( "{$this->wpdb->options}", [
                'option_name'  => $key,
                'option_value' => $value,
			]
            );
        }
    }
    public function get_option( $key, $default = false ) {
        $results = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->options} WHERE option_name=%s LIMIT 1", $key ) );
        if ( $results ) {
            return ! empty( $results->option_value ) ? $results->option_value : $default;
        }
        return $default;
    }
}
