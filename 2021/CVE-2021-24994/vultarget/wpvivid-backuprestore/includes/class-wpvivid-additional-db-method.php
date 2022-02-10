<?php

class WPvivid_Additional_DB_Method
{
    private $dbuser;
    private $dbpass;
    private $dbhost;
    private $use_mysqli = false;
    private $dbh;
    private $has_connected = false;
    public $charset;
    public $collate;

    public function __construct($dbuser, $dbpass, $dbhost){
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbhost = $dbhost;

        if ( function_exists( 'mysqli_connect' ) ) {
            $this->use_mysqli = true;

            if ( defined( 'WP_USE_EXT_MYSQL' ) ) {
                $this->use_mysqli = ! WP_USE_EXT_MYSQL;
            }
        }
    }

    public function wpvivid_parse_db_host( $host ) {
        $port    = null;
        $socket  = null;
        $is_ipv6 = false;

        // First peel off the socket parameter from the right, if it exists.
        $socket_pos = strpos( $host, ':/' );
        if ( $socket_pos !== false ) {
            $socket = substr( $host, $socket_pos + 1 );
            $host   = substr( $host, 0, $socket_pos );
        }

        // We need to check for an IPv6 address first.
        // An IPv6 address will always contain at least two colons.
        if ( substr_count( $host, ':' ) > 1 ) {
            $pattern = '#^(?:\[)?(?P<host>[0-9a-fA-F:]+)(?:\]:(?P<port>[\d]+))?#';
            $is_ipv6 = true;
        } else {
            // We seem to be dealing with an IPv4 address.
            $pattern = '#^(?P<host>[^:/]*)(?::(?P<port>[\d]+))?#';
        }

        $matches = array();
        $result  = preg_match( $pattern, $host, $matches );

        if ( 1 !== $result ) {
            // Couldn't parse the address, bail.
            return false;
        }

        $host = '';
        foreach ( array( 'host', 'port' ) as $component ) {
            if ( ! empty( $matches[ $component ] ) ) {
                $$component = $matches[ $component ];
            }
        }

        return array( $host, $port, $socket, $is_ipv6 );
    }

    public function db_version() {
        if ( $this->use_mysqli ) {
            $server_info = mysqli_get_server_info( $this->dbh );
        } else {
            $server_info = mysql_get_server_info( $this->dbh );
        }
        return preg_replace( '/[^0-9.].*/', '', $server_info );
    }

    public function has_cap( $db_cap ) {
        $version = $this->db_version();

        switch ( strtolower( $db_cap ) ) {
            case 'collation':    // @since 2.5.0
            case 'group_concat': // @since 2.7.0
            case 'subqueries':   // @since 2.7.0
                return version_compare( $version, '4.1', '>=' );
            case 'set_charset':
                return version_compare( $version, '5.0.7', '>=' );
            case 'utf8mb4':      // @since 4.1.0
                if ( version_compare( $version, '5.5.3', '<' ) ) {
                    return false;
                }
                if ( $this->use_mysqli ) {
                    $client_version = mysqli_get_client_info();
                } else {
                    $client_version = mysql_get_client_info();
                }

                /*
                 * libmysql has supported utf8mb4 since 5.5.3, same as the MySQL server.
                 * mysqlnd has supported utf8mb4 since 5.0.9.
                 */
                if ( false !== strpos( $client_version, 'mysqlnd' ) ) {
                    $client_version = preg_replace( '/^\D+([\d.]+).*/', '$1', $client_version );
                    return version_compare( $client_version, '5.0.9', '>=' );
                } else {
                    return version_compare( $client_version, '5.5.3', '>=' );
                }
            case 'utf8mb4_520': // @since 4.6.0
                return version_compare( $version, '5.6', '>=' );
        }

        return false;
    }

    public function determine_charset( $charset, $collate ) {
        if ( ( $this->use_mysqli && ! ( $this->dbh instanceof mysqli ) ) || empty( $this->dbh ) ) {
            return compact( 'charset', 'collate' );
        }

        if ( 'utf8' === $charset && $this->has_cap( 'utf8mb4' ) ) {
            $charset = 'utf8mb4';
        }

        if ( 'utf8mb4' === $charset && ! $this->has_cap( 'utf8mb4' ) ) {
            $charset = 'utf8';
            $collate = str_replace( 'utf8mb4_', 'utf8_', $collate );
        }

        if ( 'utf8mb4' === $charset ) {
            // _general_ is outdated, so we can upgrade it to _unicode_, instead.
            if ( ! $collate || 'utf8_general_ci' === $collate ) {
                $collate = 'utf8mb4_unicode_ci';
            } else {
                $collate = str_replace( 'utf8_', 'utf8mb4_', $collate );
            }
        }

        // _unicode_520_ is a better collation, we should use that when it's available.
        if ( $this->has_cap( 'utf8mb4_520' ) && 'utf8mb4_unicode_ci' === $collate ) {
            $collate = 'utf8mb4_unicode_520_ci';
        }

        return compact( 'charset', 'collate' );
    }

    public function init_charset() {
        $charset = '';
        $collate = '';

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {
            $charset = 'utf8';
            if ( defined( 'DB_COLLATE' ) && DB_COLLATE ) {
                $collate = DB_COLLATE;
            } else {
                $collate = 'utf8_general_ci';
            }
        } elseif ( defined( 'DB_COLLATE' ) ) {
            $collate = DB_COLLATE;
        }

        if ( defined( 'DB_CHARSET' ) ) {
            $charset = DB_CHARSET;
        }

        $charset_collate = $this->determine_charset( $charset, $collate );

        $this->charset = $charset_collate['charset'];
        $this->collate = $charset_collate['collate'];
    }

    public function wpvivid_do_connect($allow_bail = true){
        $new_link     = defined( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : true;
        $client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;

        $error_code = '';
        $error = 'Unknown Error.';

        if ( $this->use_mysqli ) {
            $this->dbh = mysqli_init();

            $host    = $this->dbhost;
            $port    = null;
            $socket  = null;
            $is_ipv6 = false;

            if ( $host_data = $this->wpvivid_parse_db_host( $this->dbhost ) ) {
                list( $host, $port, $socket, $is_ipv6 ) = $host_data;
            }

            if ( $is_ipv6 && extension_loaded( 'mysqlnd' ) ) {
                $host = "[$host]";
            }

            @mysqli_real_connect( $this->dbh, $host, $this->dbuser, $this->dbpass, null, $port, $socket, $client_flags );

            if ( $this->dbh->connect_errno ) {
                $error_code = $this->dbh->connect_errno;
                $error = $this->dbh->connect_error;
                $this->dbh = null;
                $attempt_fallback = true;

                if ( $this->has_connected ) {
                    $attempt_fallback = false;
                } elseif ( defined( 'WP_USE_EXT_MYSQL' ) && ! WP_USE_EXT_MYSQL ) {
                    $attempt_fallback = false;
                } elseif ( ! function_exists( 'mysql_connect' ) ) {
                    $attempt_fallback = false;
                }

                if ( $attempt_fallback ) {
                    $this->use_mysqli = false;
                    return $this->wpvivid_do_connect( $allow_bail );
                }
            }
        }
        else{
            $this->dbh = @mysql_connect( $this->dbhost, $this->dbuser, $this->dbpass, $new_link, $client_flags );
        }

        if($this->dbh){
            $this->has_connected = true;
            $ret['result'] = WPVIVID_SUCCESS;
        }
        else{
            $ret['result'] = WPVIVID_FAILED;
            $ret['error'] = $error_code.': '.$error;
        }

        return $ret;
    }

    public function wpvivid_show_additional_databases(){
        $query = 'SHOW DATABASES;';
        $result = '';
        if ( ! empty( $this->dbh ) && $this->use_mysqli ) {
            $result = mysqli_query( $this->dbh, $query );
        } elseif ( ! empty( $this->dbh ) ) {
            $result = mysql_query( $query, $this->dbh );
        }
        if ( $this->use_mysqli && $result instanceof mysqli_result ) {
            while ( $row = mysqli_fetch_object( $result ) ) {
                $database_array[] = $row;
            }
        } elseif ( is_resource( $result ) ) {
            while ( $row = mysql_fetch_object( $result ) ) {
                $database_array[] = $row;
            }
        }
        if(!empty($database_array)){
            foreach ($database_array as $key => $value){
                $last_result[] = $value->Database;
            }
        }
        return $last_result;
    }
}