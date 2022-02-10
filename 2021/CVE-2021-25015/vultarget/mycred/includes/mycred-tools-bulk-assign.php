<?php
if( !class_exists( 'myCRED_Tools_Bulk_Assign' ) ):
class myCRED_Tools_Bulk_Assign extends myCRED_Tools
{

    private static $_instance;

    public static function get_instance()
    {
        if (self::$_instance == null)
            self::$_instance = new self();

        return self::$_instance;
    }

    public function __construct()
    {
		add_action( 'wp_ajax_mycred-tools-assign-award', array( $this, 'tools_assign_award' ) );
    }

    public function get_page()
    {
        // Points
        $award_type = array(
            'points'	=>	__( 'Points', 'mycred' )
        );

        if( class_exists( 'myCRED_Badge_Module' ) ) $award_type['badges'] =	__( 'Badges', 'mycred' );

        if( class_exists( 'myCRED_Ranks_Module' ) ) $award_type['ranks'] = __( 'Ranks', 'mycred' );

        $award_args = array(
            'class'	=>	'bulk-award-type',
            'name'	=>	'bulk_award_type',
            'id'	=>	'bulk-award-type'
        );

        $point_types = mycred_get_types();

        $pt_args = array(
            'name'	=> 'bulk_award_pt', 
            'id'	=>	'bulk-award-pt', 
            'class'	=>	'bulk-award-pt'
        );

        $user_args = array(
            'users'	=>	array(
                'name'	=>	'bulk_users',
                'class'	=>	'bulk-users',
                'id'	=>	'bulk-users'
            ),
            'roles'	=>	array(
                'name'	=>	'bulk_roles',
                'class'	=>	'bulk-roles',
                'id'	=>	'bulk-roles'
            ),
        );

        //Badges
        $badges_args = array(
            'name'		=> 	'bulk_badges', 
            'id'		=>	'bulk-badges', 
            'class'		=>	'bulk-badges',
            'multiple'	=>	'multiple'
        );

        $badges = array();
        if (class_exists('myCRED_Badge')){
            
            $badge_ids = mycred_get_badge_ids();

            foreach( $badge_ids as $id )
                $badges[$id] = get_the_title( $id );
        }

        //Ranks
        $ranks_args = array(
            'name'		=> 	'bulk_ranks', 
            'id'		=>	'bulk-ranks', 
            'class'		=>	'bulk-ranks'
        );

        $ranks = array();

        foreach( $point_types as $key => $pt )
        {
            $mycred_ranks = '';
            
            if( class_exists( 'myCRED_Ranks_Module' ) && mycred_manual_ranks( $key ) )
            {
                $mycred_ranks = mycred_get_ranks( 'publish', '-1', 'ASC', $key );

                foreach( $mycred_ranks as $key => $value )
                {
                    $ranks[$value->post->ID] = "{$value->post->post_title} ({$pt})";
                }
            }
        }

        ?>
        <h1>Award/ Revoke</h1>
        <form class="mycred-tools-ba-award-form">
            <table width="" class="mycred-tools-ba-award-table" cellpadding="10">
                <thead>
                    <tr>
                        <td><label for=""><?php _e( 'Select Type', 'mycred' ) ?></label></td>
                        <td><?php echo mycred_create_select2( $award_type, $award_args ); ?></td>
                    </tr>
                </thead>

                <tbody class="bulk-award-point">

                    <tr>
                        <td><label for=""><?php _e( 'Points to Award/ Revoke', 'mycred' ) ?></label></td>
                        <td>
                            <input type="number" name="bulk_award_point">
                        </td>
                    </tr>

                    <tr>
                        <td class="tb-zero-padding"></td>
                        <td class="tb-zero-padding">
                            <p><i>
                                <?php _e( 'Either set points are Positive to award or in Negative to deduct.', 'mycred' ); ?>
                            </i></p>
                            <p><i>
                                <?php _e( 'eg. 10 or -100 ', 'mycred' ); ?>
                            </i></p>
                        </td>
                    </tr>

                    <tr>
                        <td><label for=""><?php _e( 'Select Point Type', 'mycred' ) ?></label></td>
                        <td><?php echo mycred_create_select2( $point_types, $pt_args ); ?></td>
                    </tr>

                    <tr>
                        <td><label for=""><?php _e( 'Enable to Log Entry', 'mycred' ) ?></label></td>
                        <td>
                            <label class="mycred-switch1">
                                <input type="checkbox" value="1" class="log-entry">
                                <span class="slider round"></span>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <td class="tb-zero-padding"></td>
                        <td class="tb-zero-padding">
                            <p><i>
                                <?php _e( 'Check if you want to create log of this entry.', 'mycred' ) ?>
                            </i></p>
                        </td>
                    </tr>

                    <tr class="log-entry-row">
                        <td><label for=""><?php _e( 'Log Entry', 'mycred' ) ?></label></td>
                        <td>
                            <input type="text" name="log_entry_text">
                            <p><i>
                                <?php _e( 'Enter Text for log entry.', 'mycred' ) ?>
                            </i></p>
                        </td>
                    </tr>
                    
                </tbody>
                

                <tbody class="bulk-award-badge" style="display: none;">
                    <tr>
                        <td><label for=""><?php _e( 'Select Badge(s)', 'mycred' ) ?></label></td>
                        <td><?php echo mycred_create_select2( $badges, $badges_args ); ?></td>
                    </tr>
                </tbody>

                <tbody class="bulk-award-rank" style="display: none;">
                    <tr>
                        <td><label for=""><?php _e( 'Select Rank', 'mycred' ) ?></label></td>
                        <td>
                            <?php echo mycred_create_select2( $ranks, $ranks_args ); ?>
                        </td>
                    </tr>
                    <tr class="bulk-award-rank">
                        <td class="tb-zero-padding"></td>
                        <td class="tb-zero-padding">
                            <p>
                                <i>Rank Behaviour should be set to Manual Mode.</i>
                            </p>
                        </td>
                    </tr>
                </tbody>

                <!-- User fields -->
                <?php echo $this->users_fields( $user_args ) ?>

                <!-- Award Button -->
                <tbody>
                <tr>
                    <td>
                        <button class="button button-large large button-primary tools-bulk-assign-award-btn award-points">
                            <span class="dashicons dashicons-update mycred-button1"></span> 
                            Update
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php
    }

    public function users_fields( $args )
	{
        $users = $this->get_all_users();
        
        $users_args = array(
            'name'		=>	$args['users']['name'],
            'id'		=>	$args['users']['id'],
            'class'		=>	$args['users']['class'],
            'multiple'	=>	'multiple'
        );

        $wp_roles = wp_roles();

        $roles = array();

        foreach( $wp_roles->roles as $role => $name )
        {
            $roles[$role] = $name['name'];
        }

        $roles_args = array(
            'name'		=>	$args['roles']['name'],
            'id'		=>	$args['roles']['id'],
            'class'		=>	$args['roles']['class'],
            'multiple'	=>	'multiple'
        );

		$content = '';
		
		$content .= 
		'<tr>
			<td><label for="">Award/ Revoke to All Users</label></td>
			<td>
				<label class="mycred-switch1">
					<input type="checkbox" name="" class="award-to-all">
					<span class="slider round"></span>
				</label>
			</td>
		</tr>

        <tr class="users-row">
            <td class="tb-zero-padding">
            </td>
            <td class="tb-zero-padding">
                <p><i>
					Check if you want to award to all users.
				</i></p>
            </td>
        </tr>
		
		<tr class="users-row">
			<td><label for="">Users to Award/ Revoke</label></td>
			<td>';

		$content .= mycred_create_select2( $users, $users_args );

		$content .='
			</td>
		</tr>

        <tr class="users-row">
            <td class="tb-zero-padding">
            </td>
            <td class="tb-zero-padding">
            <p><i>
                Choose users to award.
            </i></p>
            </td>
        </tr>
		
		<tr class="users-row">
			<td><label for="">Roles to Award/ Revoke</label></td>
			<td>';

		$content .= mycred_create_select2( $roles, $roles_args );

		$content .= '
			</td>
		</tr>
        <tr class="users-row">
            <td class="tb-zero-padding">
            </td>
            <td class="tb-zero-padding">
                <p><i>
                    Choose roles to award.
                </i></p>
            </td>
        </tr>
        ';

		return $content;
	}
}
endif;

myCRED_Tools_Bulk_Assign::get_instance();