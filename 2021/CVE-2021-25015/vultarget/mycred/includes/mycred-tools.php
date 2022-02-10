<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! class_exists( 'myCRED_Tools' ) ) :
class myCRED_Tools {

	/**
	 * Construct
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'tools_sub_menu' ) );

		if( isset( $_GET['page'] ) && $_GET['page'] == 'mycred-tools' )
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		
	}

	public function admin_enqueue_scripts()
	{
		wp_enqueue_script( MYCRED_SLUG . '-select2-script' );

		wp_enqueue_style( MYCRED_SLUG . '-select2-style' );

		wp_enqueue_script( MYCRED_SLUG . '-tools-script', plugins_url( 'assets/js/mycred-tools.js', __DIR__ ), 'jquery', myCRED_VERSION, true );

		wp_enqueue_style( MYCRED_SLUG . '-buttons' );

		wp_localize_script( 
			MYCRED_SLUG . '-tools-script',
			'mycredTools',
			array(
				'awardConfirmText'		=>	__( 'Do you really want to bulk award?', 'mycred' ),
				'revokeConfirmText'		=>	__( 'Do you really want to bulk deduct?', 'mycred' ),
				'successfullyAwarded'	=>	__( 'Successfully Awarded.', 'mycred' ),
				'successfullyDeducted'	=>	__( 'Successfully Deducted.', 'mycred' ),
				'pointsRequired'		=>	__( 'Points field is required.', 'mycred' ),
				'logEntryRequired'		=>	__( 'Log Entry is requried.', 'mycred' ),
				'revokeConfirmText'		=>	__( 'Do you really want to bulk revoke?', 'mycred' ),
				'successfullyRevoked'	=>	__( 'Successfully Revoked.', 'mycred' ),
				'userOrRoleIsRequired'	=>	__( 'Username or Role field required.', 'mycred' ),
				'badgesFieldRequried'	=>	__( 'Badges field required.', 'mycred' )
			)
		);
	}

	/**
	 * Register tools menu
	 */
	public function tools_sub_menu() {
		mycred_add_main_submenu( 
			'Tools', 
			'Tools', 
			'manage_options', 
			'mycred-tools',
			array( $this, 'tools_page' ),
			2
		);
	}

	/**
	 * Tools menu callback
	 */
	public function tools_page() { 
		
		$import_export = get_mycred_tools_page_url('import-export');
		$logs_cleanup = get_mycred_tools_page_url('logs-cleanup');
		$reset_data = get_mycred_tools_page_url('reset-data');
		
		?>

		<div class="" id="myCRED-wrap">
			<div class="mycredd-tools">
				<h1>Tools</h1>
			</div>
			<div class="clear"></div>
			<div class="mycred-tools-main-nav">
				<h2 class="nav-tab-wrapper">
					<a href="<?php echo admin_url('admin.php?page=mycred-tools') ?>" class="nav-tab <?php echo !isset( $_GET['mycred-tools'] ) ? 'nav-tab-active' : ''; ?>">Bulk Assign</a>
					<!-- <a href="<?php //echo $import_export ?>" class="nav-tab <?php //echo ( isset( $_GET['mycred-tools'] ) && $_GET['mycred-tools'] == 'import-export' ) ? 'nav-tab-active' : ''; ?>">Import/Export</a>
					<a href="<?php //echo $logs_cleanup ?>" class="nav-tab <?php //echo ( isset( $_GET['mycred-tools'] ) && $_GET['mycred-tools'] == 'logs-cleanup' ) ? 'nav-tab-active' : ''; ?>">Logs Cleanup</a>
					<a href="<?php //echo $reset_data ?>" class="nav-tab <?php //echo ( isset( $_GET['mycred-tools'] ) && $_GET['mycred-tools'] == 'reset-data' ) ? 'nav-tab-active' : ''; ?>">Reset Data</a> -->
				</h2>
			</div>
		
		<?php

		if ( isset( $_GET['mycred-tools'] ) ) {
			if ( $_GET['mycred-tools'] == 'import-export' ) { ?>
				<h1>IMPORT/EXPORT</h1>
				<?php
			}
		}

		if ( isset( $_GET['mycred-tools'] ) ) {
			if ( $_GET['mycred-tools'] == 'logs-cleanup' ) { ?>
				<h1>LOGS-CLEANUP</h1>
				<?php
			}
		}

		if ( isset( $_GET['mycred-tools'] ) ) 
		{
			if ( $_GET['mycred-tools'] == 'reset-data' ) { ?>
				<h1>RESET-DATA</h1>
				<?php
			}
		}
		else
		{

			$mycred_tools_bulk_assign = new myCRED_Tools_Bulk_Assign();

			$mycred_tools_bulk_assign->get_page();

		}

		?>
		</div>
		<?php
	}

	public function get_all_users()
	{
		$users = array();

		$wp_users = get_users();

		foreach( $wp_users as $user )
            $users[$user->user_email] = $user->display_name;

		return $users;
	}

	public function get_users_by_email( $emails )
	{
		$ids = array();

		foreach( $emails as $email )
			$ids[] = get_user_by( 'email', $email )->ID;

		return $ids;
	}

	public function get_users_by_role( $roles )
	{
		$user_ids = array();

		foreach( $roles as $role )
		{
			$args = array(
				'role'	=>	$role
			);

			$user_query = new WP_User_Query( $args );

			if ( ! empty( $user_query->get_results() ) ) 
			{
				foreach ( $user_query->get_results() as $user ) 
					$user_ids[] = $user->ID;
			}
		}

		return $user_ids;
	}

	public function tools_assign_award()
	{
		if( isset( $_REQUEST['selected_type'] ) ):

		$selected_type = sanitize_text_field( $_REQUEST['selected_type'] );

		$award_to_all_users = sanitize_text_field( $_REQUEST['award_to_all_users'] ) == 'true' ? true : false;
		$users = sanitize_text_field( $_REQUEST['users'] );
		$user_roles = sanitize_text_field( $_REQUEST['user_roles'] );

		//Gathering users
		$users_to_award = array();
		if( $award_to_all_users )
		{
			$users = $this->get_all_users();

			foreach( $users as $email => $user_name )
			{
				$users_to_award[] = $email;
			}

			$users_to_award = $this->get_users_by_email( $users_to_award );
		}
		else
		{
			$users = json_decode( stripslashes( $users ) );

			$roles = json_decode( stripslashes( $user_roles ) ); 

			if( empty( $users ) && empty( $roles ) )
			{
				$response = array( 'success' => 'userOrRoleIsRequired' );
				
				wp_send_json( $response );
				
				die;
			}
			
			$users_to_award = $this->get_users_by_email( $users );

			if( $user_roles )
			{
				$users_by_role = $this->get_users_by_role( $roles );

				$users_to_award = array_merge( $users_by_role, $users_to_award );

				$users_to_award = array_unique( $users_to_award );
			}
		}

		//Awarding Points
		if( $selected_type == 'points' )
		{
			$response = '';
			$log_entry_text = '';
			$points_to_award = sanitize_text_field( $_REQUEST['points_to_award'] );
			$point_type = sanitize_text_field( $_REQUEST['point_type'] );
			$log_entry = sanitize_text_field( $_REQUEST['log_entry'] ) == 'true' ? true : false;
			
			if( empty( $points_to_award ) )
			{
				$response = array( 'success' => 'pointsRequired' );

				wp_send_json( $response );

				die;
			}

			$mycred = mycred( $point_type );

			foreach( $users_to_award as $user_id )
			{
				//Entries with log
				if( $log_entry )
				{
					$log_entry_text = sanitize_text_field( $_REQUEST['log_entry_text'] );

					if( empty( $log_entry_text ) )
					{
						$response = array( 'success' => 'logEntryRequired' );
				
						wp_send_json( $response );
						
						die;
					}

					mycred_add(
						'bulk_assign',
						$user_id,
						$points_to_award,
						$log_entry_text,
						'',
						'',
						$point_type
					);

				}

				//Entries with log
				if( !$log_entry )
				{
					$new_balance = $mycred->update_users_balance( $user_id, $points_to_award, $point_type );
				}

				$response = array( 'success' => true );
			}

			wp_send_json( $response );

			die;
		}

		//Awarding Ranks
		if( $selected_type == 'ranks' )
		{
			$rank_to_award = sanitize_text_field( $_REQUEST['rank_to_award'] );

			foreach( $users_to_award as $user_id )
			{
				if( class_exists( 'myCRED_Ranks_Module' ) && mycred_manual_ranks() )
                {
					$rank_pt = mycred_get_rank_pt( $rank_to_award );
                    mycred_save_users_rank( $user_id, $rank_to_award, $rank_pt );
					$response = array( 'success' => true );
                }
			}
			
			wp_send_json( $response );

			die;
		}

		//Awarding/ Revoking Badges
		if( $selected_type == 'badges' )
		{
			//Awarding Badges
			if( $_REQUEST['action'] == 'mycred-tools-assign-award' && !isset( $_REQUEST['revoke'] ) )
			{
				$badges_to_award = sanitize_text_field( $_REQUEST['badges_to_award'] );

				$badges_to_award = json_decode( stripslashes( $badges_to_award ) );

				if( empty( $badges_to_award ) )
				{
					$response = array( 'success' => 'badgesFieldRequried' );
				
					wp_send_json( $response );
					
					die;
				}

				foreach( $badges_to_award as $badge_id )
				{
					foreach( $users_to_award as $user_id )
					{
						$badge_id = (int)$badge_id;

						mycred_assign_badge_to_user( $user_id, $badge_id );
					}
				}

				$response = array( 'success' => true );

			}
			
			//Revoking Badges
			if( $_REQUEST['action'] == 'mycred-tools-assign-award' && isset( $_REQUEST['revoke'] ) && $_REQUEST['revoke'] == 'revoke' )
			{
				$badges_to_revoke = sanitize_text_field( $_REQUEST['badges_to_revoke'] );

				$badges_to_revoke = json_decode( stripslashes( $badges_to_revoke ) );

				if( empty( $badges_to_revoke ) )
				{
					$response = array( 'success' => 'badgesFieldRequried' );
				
					wp_send_json( $response );
					
					die;
				}

				foreach( $badges_to_revoke as $badge_id )
				{
					foreach( $users_to_award as $user_id )
					{
						$badge = mycred_get_badge( $badge_id );

                		$badge->divest( $user_id );
					}
				}

				$response = array( 'success' => true );
				
			}

			wp_send_json( $response );

			die;
		}

		endif;
	}
}
endif;

$mycred_tools = new myCRED_Tools();

if ( ! function_exists( 'get_mycred_tools_page_url' ) ) :
	function get_mycred_tools_page_url( $urls ) {
		
		$args = array(
			'page'         => MYCRED_SLUG . '-tools',
			'mycred-tools' =>  $urls,
		);

		return esc_url( add_query_arg( $args, admin_url( 'admin.php' ) ) );

	}
endif;