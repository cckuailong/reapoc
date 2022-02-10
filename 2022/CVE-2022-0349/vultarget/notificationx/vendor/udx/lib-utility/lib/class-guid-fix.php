<?php
/**
 * Class CF_Guid_Fix
 *
 * Correct duplicate GUID values created with versions of WordPress that exhibited a <a href="http://core.trac.wordpress.org/ticket/15041">non-unique GUID bug</a>.
 *
 * @source http://crowdfavorite.com/wordpress/plugins/cf-guid-fix/
 *
 */
namespace UsabilityDynamics\Utility {

  if( !class_exists( 'UsabilityDynamics\Utility\Guid_Fix' ) ) {

    class Guid_Fix {

      function __construct() {

        if( is_admin() ) {
          $this->add_actions();
        }

        $this->menu_page_slug = 'cf_guid_fix';
        $this->page_url       = admin_url( 'tools.php?page=' . $this->menu_page_slug );
        $this->errors         = array();
        $this->error_msgs     = array(
          '1' => __( 'Could not create index on your posts table for the `guid` column', 'cf_guid_fix' ),
          '2' => __( 'Could not update a post&rsquo;s GUID', 'cf_guid_fix' ),
          '3' => __( 'Could not remove the index from `guid`', 'cf_guid_fix' ),
        );
      }

      function add_actions() {
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
      }

      function plugin_action_links( $links, $file ) {
        $plugin_file = basename( __FILE__ );
        if( basename( $file ) == $plugin_file ) {
          $tools_link = '<a href="' . $this->page_url . '">' . __( 'Run GUID-fix', 'cf_guid_fix' ) . '</a>';
          array_unshift( $links, $tools_link );
        }

        return $links;
      }

      function register_admin_menu() {
        add_submenu_page(
          'tools.php', // parent slug
          'GUID Fix', // page title
          'GUID Fix', // menu title
          'manage_options', // capability
          $this->menu_page_slug, // menu item slug
          array( $this, 'output_admin_page' ) // callback function
        );
      }

      function output_admin_page() {
        ?>
        <div class="wrap">
          <h2><?php _e( 'CF GUID-Fix', 'cf_guid_fix' ); ?></h2>
          <?php
          if( isset( $_GET[ 'success' ] ) ) {
            ?>
            <div class="updated"><p><?php _e( 'Post GUIDs are now unique! <b>You should now disable and remove this plugin.</b>', 'cf_guid_fix' ); ?></p></div>
          <?php
          } else if( isset( $_GET[ 'error' ] ) ) {
            $error_str  = $_GET[ 'error' ];
            $error_nums = explode( ',', $error_str );
            ?>
            <div class="error">
					<?php
          foreach( $error_nums as $num ) {
            echo '<p>' . esc_html( $this->error_msgs[ intval( $num ) ] ) . '</p>';
          }
          ?>
				</div>
          <?php
          }
          ?>
          <p class="help"><strong><?php _e( 'What does this plugin do?', 'cf_guid_fix' ); ?></strong> <?php _e( 'Corrects duplicate GUID values created with versions of WordPress that exhibited a <a href="http://core.trac.wordpress.org/ticket/15041">non-unique GUID bug</a>.', 'cf_guid_fix' ); ?></p>
			<p class="instructions"><?php _e( 'Clicking the button below runs the plugin to ensure your guids are unique.', 'cf_guid_fix' ); ?></p>
			<form name="cf_guid_fix_settings_form" action="<?php echo $this->page_url; ?>" method="post">
				<input type="hidden" name="cf_action" id="cf_action" value="guid_fix"/>
				<button type="submit" class="button-primary"><?php _e( 'Fix Your GUIDs', 'cf_guid_fix' ); ?></button>
        <?php wp_nonce_field( 'cf_guid_fix_run' ); ?>
			</form>
		</div>
      <?php
      }

      function admin_init() {
        if( isset( $_POST[ 'cf_action' ] ) ) {
          switch( $_POST[ 'cf_action' ] ) {
            case 'guid_fix':
              // Validate request and nonce
              if( !check_admin_referer( 'cf_guid_fix_run' ) ) {
                error_log( 'Unauthorized Request for CF GUID Fix Plugin' );
                wp_die( __( 'You should not be here.', 'cf_guid_fix' ) );
              }

              $this->fix_guids();
              if( empty( $this->errors ) ) {
                wp_redirect( add_query_arg( array( 'success' => '' ), $this->page_url ) );
              } else {
                wp_redirect( add_query_arg( array( 'error' => implode( ',', $this->errors ) ), $this->page_url ) );
              }
              exit;
              break;
          }
        }
      }

      private function fix_guids() {
        global $wpdb;

        // add index on GUID column
        $r = $wpdb->query( "
			ALTER TABLE $wpdb->posts
			ADD INDEX (guid)
		" );

        // Error Handling
        if( $r === false ) {
          $this->add_error( 1 );
        }

        // find non-unique GUID values

        // NOTE: not including revisions since that's a whole other mess to
        // try to get the numbering right if we change the GUID

        // this gets the GUIDs
        // 		SELECT guid
        // 		FROM $wpdb->posts
        // 		WHERE post_type != 'revision'
        // 		GROUP BY guid
        // 		HAVING COUNT(guid) > 1

        // WORKS - but is very slow
        // 		SELECT ID
        // 		FROM $wpdb->posts
        // 		WHERE guid IN (
        // 			SELECT guid
        // 			FROM $wpdb->posts
        // 			WHERE post_type != 'revision'
        // 			GROUP BY guid
        // 			HAVING COUNT(guid) > 1
        // 		)
        // 		AND post_type != 'revision'

        $non_unique_guids = $wpdb->get_col( $wpdb->prepare( "
			SELECT p1.ID
			FROM $wpdb->posts p1
			WHERE 1 < (
				SELECT COUNT(ID)
				FROM $wpdb->posts p2
				WHERE p1.post_type != 'revision'
				AND p1.guid = p2.guid
			)
			AND p1.post_type != 'revision'
		" ) );

        // make them unique
        if( count( $non_unique_guids ) ) {
          foreach( $non_unique_guids as $post_id ) {
            $url = site_url( '?p=' . $post_id );
            $r   = $wpdb->query( $wpdb->prepare( "
					UPDATE $wpdb->posts
					SET guid = %s
					WHERE ID = %d
				", $url, $post_id ) );

            // Error Handling
            if( $r === false ) {
              $this->add_error( 2 );
            }
          }
        }

        // remove index from GUID column
        $r = $wpdb->query( "
			ALTER TABLE $wpdb->posts
			DROP INDEX guid
		" );

        // Error Handling
        if( $r === false ) {
          $this->add_error( 3 );
        }

      }

      function add_error( $error_num ) {
        if( !in_array( $error_num, $this->errors ) ) {
          $this->errors[ ] = intval( $error_num );
        }
      }

    }
  }

}

