<?php

/**
 * WPDB query logger
 *
 * @since 3.5.0
 *
 * Log wpdb query using the stop and end methods. Log queries between
 * two points of execution. Start logging with `wpuf()->log->wpdb_query->start( 'some_id ).
 * Then stop logging using `wpuf()->log->wpdb_query->stop( 'some_id ). Log will be dumped
 * in debug.log.
 */
class WPUF_Log_WPDB_Query {

    /**
     * Log store
     *
     * @var array
     */
    protected static $store = [];

    /**
     * Handle dynamic filter hook for log_query_custom_data tag
     *
     * @since 3.5.0
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return array|void
     */
    public static function __callStatic( $name, $arguments ) {
        if ( 0 === strpos( $name, 'log_query_callback_' ) ) {
            $id = str_replace( 'log_query_callback_', '', $name );
            self::log_query( $id, ...$arguments );
            return $arguments[0];
        }
    }

    /**
     * Store the query log
     *
     * @since 3.5.0
     *
     * @param string $id
     * @param array  $query_data
     * @param string $query
     * @param float  $query_time
     * @param string $query_callstack
     * @param float  $query_start
     *
     * @return void
     */
    protected static function log_query( $id, $query_data, $query, $query_time, $query_callstack, $query_start ) {
        self::$store[ $id ][] = [
			'sql'       => $query,
			'time'      => $query_time,
			'callstack' => explode( ', ', $query_callstack ),
			'start'     => $query_start,
			'data'      => $query_data,
        ];
    }

    /**
     * Start logging wpdb query
     *
     * @since 3.5.0
     *
     * @param string $id
     *
     * @return void
     */
    public function start( $id ) {
        if ( isset( self::$store[ $id ] ) ) {
            throw new Exception( 'Error starting wpdb query log. $id already exists.' );
        }

        if ( ! defined( 'SAVEQUERIES' ) || ! SAVEQUERIES ) {
            define( 'SAVEQUERIES', true );
        }

        self::$store[ $id ] = [];

        add_filter( 'log_query_custom_data', [ self::class, 'log_query_callback_' . $id ], 10, 5 );
    }

    /**
     * Stop or finish logging wpdb query
     *
     * @since 3.5.0
     *
     * @param string $id
     *
     * @return void
     */
    public function stop( $id ) {
        if ( ! isset( self::$store[ $id ] ) ) {
            throw new Exception( 'Error starting wpdb query log. $id does not exists.' );
        }

        remove_filter( 'log_query_custom_data', [ self::class, 'log_query_callback_' . $id ] );

        $store      = print_r( self::$store[ $id ], true );
        $total_time = 0;

        if ( ! empty( $store ) ) {
            foreach( self::$store[ $id ] as $item ) {
                $total_time += $item['time'];
            }
        }

        error_log( sprintf(
            "[WPUF] wpdb query log for `%s`:\nTotal Time: %s\n%s",
            $id,
            $total_time,
            $store
        ) );
        unset( self::$store[ $id ] );
    }
}
