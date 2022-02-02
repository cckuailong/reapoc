<?php

add_action( 'init', 'cp_ppp_init_ds', 0 );

function cp_ppp_init_ds()
	{
		if( isset( $_REQUEST[ 'cffaction' ] ) )
		{
			switch( $_REQUEST[ 'cffaction' ] )
			{
				case 'test_db_connection':
					global $cpcff_db_connect;
					
					$_REQUEST[ 'data_source' ] = 'database';
					$_REQUEST[ 'query' ] = 'SHOW tables';
					$result =  cp_ppp_ds( $_REQUEST );
					$err = mysqli_error( $cpcff_db_connect );
					if( !is_null( mysqli_connect_error() ) ) $err .= mysqli_connect_error();
					print( ( ( empty( $err ) ) ? 'Connection OK' : $err ) );
					exit;
				break;
				case 'test_db_query':
				    if( $_REQUEST[ 'active' ] == 'structure' )
					{
						_cp_ppp_check_for_variable( $_REQUEST[ 'table' ] );
						_cp_ppp_check_for_variable( $_REQUEST[ 'where' ] );
					}
					else
					{
						_cp_ppp_check_for_variable( $_REQUEST[ 'query' ] );
					}
				case 'get_data_from_database':
					global $cpcff_db_connect;
					
					$_REQUEST[ 'data_source' ] = 'database';
					if( $_REQUEST[ 'active' ] == 'structure' )
					{
						$_REQUEST[ 'query' ] = '';
					}
					
					$query_result =  cp_ppp_ds( $_REQUEST );
					$err = mysqli_error( $cpcff_db_connect );
					if( !is_null( mysqli_connect_error() ) ) $err .= mysqli_connect_error();
					if( $_REQUEST[ 'cffaction' ] == test_db_query )
					{
						print_r( ( ( empty( $err ) ) ? $query_result : $err ) );
					}
					else
					{
						$result_obj = new stdClass;
						if( !empty( $err ) )
						{
							$result_obj->error = $err;
						}
						else
						{
							$result_obj->data = $query_result;
						}
						print( json_encode( $result_obj ) );
					}	
					exit;
				break;
				case 'get_post_types':
					print json_encode( get_post_types( array('public' => true) ) );
					exit;
				break;
				case 'get_posts':
					$_REQUEST[ 'data_source' ] = 'post_type';
					$result_obj = new stdClass;
					$result_obj->data = cp_ppp_ds( $_REQUEST );
					print( json_encode( $result_obj ) );
					exit;
				break;
				case 'get_available_taxonomies':
					print json_encode( get_taxonomies( array('public' => true), 'objects' ) );
					exit;
				break;
				case 'get_taxonomies':
					$_REQUEST[ 'data_source' ] = 'taxonomy';
					$result_obj = new stdClass;
					$result_obj->data = cp_ppp_ds( $_REQUEST );
					print( json_encode( $result_obj ) );
					exit;
				break;
				case 'get_users':
					$_REQUEST[ 'data_source' ] = 'user';
					$result_obj = new stdClass;
					$result_obj->data = cp_ppp_ds( $_REQUEST );
					print( json_encode( $result_obj ) );
					exit;
				break;
			}
		}
		
	} // End cp_ppp_init_ds

function cp_ppp_ds( $data )
	{
		switch( $data[ 'data_source' ] )
		{
			case 'database':
				return cp_ppp_ds_db( $data );
			break;
			case 'csv':
				return cp_ppp_ds_csv( $data );
			break;
			case 'post_type':
				return cp_ppp_ds_post_type( $data );
			break;
			case 'taxonomy':
				return cp_ppp_ds_taxonomy( $data );
			break;
			case 'user':
				return cp_ppp_ds_user( $data );
			break;
		}
	}

/**
	Displays a text about the existence of variables in the query, and stops the script execution.
**/	
function _cp_ppp_check_for_variable( $str )
	{
		if( preg_match( '/<%fieldname\d+%>/', $str ) )
		{
			print 'Your query includes variables, so it cannot be tested from the form\'s edition';
			exit;
		}
	}
function _cp_ppp_set_attr( &$obj, $attr, $arr, $elem )
	{
		$arr = (array)$arr;
		if( !empty( $elem ) && !empty( $arr[ $elem ] ) )
		{
			$tmp = (array)$obj;
			$tmp[ $attr ] = $arr[ $elem ];
			$obj = (object)$tmp;
		}
	}
	
function cp_ppp_ds_db( $data )
	{
		global $wpdb, $cpcff_db_connect;

		if( !empty( $data[ 'query' ] ) )
		{
			$query = $data[ 'query' ];
		}
		else
		{
			$separator = '';
			$select = '';
			if( !empty( $data[ 'value' ] ) )
			{
				$separator = ',';
				$select .= $data[ 'value' ] . ' AS value';
			}
			
			if( !empty( $data[ 'text' ] ) )
			{
				$select .= $separator . $data[ 'text' ] . ' AS text';
			}
			
			$query = 'SELECT DISTINCT ' . $select . ' FROM ' . $data[ 'table' ] . ( ( !empty( $data[ 'where' ] ) ) ? ' WHERE ' . $data[ 'where' ] : '' ) . ( ( !empty( $data[ 'orderby' ] ) ) ? ' ORDER BY ' . $data[ 'orderby' ] : '' ).( ( !empty( $data[ 'limit' ] ) ) ? ' LIMIT ' . $data[ 'limit' ] : '' );
			
		}
		$query = stripcslashes( $query );
		
		if( !empty( $data[ 'connection' ] ) && !empty( $data[ 'form' ] ) )
		{
			$connection_data = unserialize( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, cp_ppp_get_option( 'form_structure', CP_PPP_DEFAULT_form_structure, $data[ 'form' ] ), base64_decode( $data[ 'connection' ] ), MCRYPT_MODE_ECB ) );
			foreach( $connection_data as $key => $val )
			{
				$data[ $key ] = $val;
			}
		}
		
		if( !empty( $data[ 'host' ] ) ) // External database
		{
			$results = array();
			$cpcff_db_connect = mysqli_connect( $data[ 'host' ], $data[ 'user' ], $data[ 'pass' ], $data[ 'database' ] );

			if( $cpcff_db_connect !== false )
			{
				$query_result = mysqli_query( $cpcff_db_connect, $query );
				while( $query_result && $row = mysqli_fetch_object( $query_result ) )
				{
					$results[] = $row;
				}
			}	
			return $results;
		}
		else // Local database
		{
			return $wpdb->get_results( $query, ARRAY_A );
		}
	} // End cp_ppp_ds_db
	
function cp_ppp_ds_csv( $data )
	{
	}
	
function cp_ppp_ds_post_type( $data )
	{

		$posts = array();
		if( !empty( $data[ 'id' ] ) )
		{
			$result = get_post( $data[ 'id' ], ARRAY_A );
			if( !is_null( $result ) )
			{
				$tmp = new stdClass;
				_cp_ppp_set_attr( $tmp, 'value', $result, $data[ 'value' ] );
				_cp_ppp_set_attr( $tmp, 'text',  $result, $data[ 'text' ] );
				array_push( $posts, $tmp );
			}
		}
		else
		{
			$args = array(
				'post_status'      => 'publish',
				'orderby'          => 'post_date',
				'order'            => 'DESC'
			);
			
			if( !empty( $data[ 'posttype' ] ) )
			{
				$args[ 'post_type' ] = $data[ 'posttype' ];
			}
			
			if( !empty( $data[ 'last' ] ) )
			{
				$args[ 'posts_per_page' ] = $data[ 'last' ];
			}
			
			$results = get_posts( $args );
			
			foreach ( $results as $result )
			{
				$tmp = new stdClass;
				_cp_ppp_set_attr( $tmp, 'value', $result, $data[ 'value' ] );
				_cp_ppp_set_attr( $tmp, 'text',  $result, $data[ 'text' ] );
				array_push( $posts, $tmp );
			}
		}
		return $posts;
	}
	
function cp_ppp_ds_taxonomy( $data )
	{
		$taxonomies = array();
		if( !empty( $data[ 'id' ] ) || !empty( $data[ 'slug' ] ) )
		{
			if( !empty( $data[ 'taxonomy' ] ) )
			{
				if( !empty( $data[ 'id' ] ) )
				{
					$result = get_term( $data[ 'id' ], $data[ 'taxonomy' ], ARRAY_A );
				}
				else
				{
					$result = get_term_by( 'slug', $data[ 'slug' ], $data[ 'taxonomy' ], ARRAY_A );
				}
				
				if( !is_null( $result ) )
				{
					$tmp = new stdClass;
					_cp_ppp_set_attr( $tmp, 'value', $result, $data[ 'value' ] );
					_cp_ppp_set_attr( $tmp, 'text',  $result, $data[ 'text' ] );
					array_push( $taxonomies, $tmp );
				}
			}	
		}
		else
		{
			if( !empty( $data[ 'taxonomy' ] ) )
			{
				$results = get_terms( $data[ 'taxonomy' ], array( 'hide_empty' => 0 ) );

				foreach ( $results as $result )
				{
					$tmp = new stdClass;
					_cp_ppp_set_attr( $tmp, 'value', $result, $data[ 'value' ] );
					_cp_ppp_set_attr( $tmp, 'text',  $result, $data[ 'text' ] );
					array_push( $taxonomies, $tmp );
				}
			}
		}
		return $taxonomies;
	}
	
	
function cp_ppp_ds_user( $data )
	{
		$users = array();
		if( !empty( $data[ 'logged' ] ) && $data[ 'logged' ] !== 'false' )
		{
			$result = wp_get_current_user();
			if( !empty( $result ) )
			{
				$tmp = new stdClass;
				_cp_ppp_set_attr( $tmp, 'value', $result->data, $data[ 'value' ] );
				$users[] = $tmp;
			}
		}
		elseif( !empty( $data[ 'id' ] ) || !empty( $data[ 'login' ] ) )
		{
			if( !empty( $data[ 'id' ] ) )
			{
				$tmp = new stdClass;
				$result = get_user_by( 'id', $data[ 'id' ] );
			}
			else
			{
				$tmp = new stdClass;
				$result = get_user_by( 'login', $data[ 'login' ] );
			}
			
			if( !empty( $result ) )
			{
				$tmp = new stdClass;
				_cp_ppp_set_attr( $tmp, 'value', $result->data, $data[ 'value' ] );
				$users[] = $tmp;
			}
		}
		else
		{
		
			$results = get_users();
			foreach( $results as $result )
			{
				$tmp = new stdClass;
				_cp_ppp_set_attr( $tmp, 'value', $result->data, $data[ 'value' ] );
				_cp_ppp_set_attr( $tmp, 'text', $result->data, $data[ 'text' ] );
				$users[] = $tmp;
			}
		}
		
		return $users;
	}
	
?>