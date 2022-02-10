<?php
/*
	PMPro Report
	Title: Logins
	Slug: login
	
	For each report, add a line like:
	global $pmpro_reports;
	$pmpro_reports['slug'] = 'Title';
	
	For each report, also write two functions:
	* pmpro_report_{slug}_widget()   to show up on the report homepage.
	* pmpro_report_{slug}_page()     to show up when users click on the report page widget.
*/
global $pmpro_reports;
$pmpro_reports['login'] = __('Visits, Views, and Logins', 'paid-memberships-pro');

function pmpro_report_login_widget() {
	global $wpdb;
	$now = current_time('timestamp');
	
	$visits = pmpro_reports_get_all_values('visits');
	$views = pmpro_reports_get_all_values('views');
	$logins = pmpro_reports_get_all_values('logins');
?>
<span id="pmpro_report_login" class="pmpro_report-holder">
	<table class="wp-list-table widefat fixed striped">
	<thead>
		<tr>
			<th scope="col">&nbsp;</th>
			<th scope="col"><?php _e('Visits','paid-memberships-pro'); ?></th>
			<th scope="col"><?php _e('Views','paid-memberships-pro'); ?></th>
			<th scope="col"><?php _e('Logins','paid-memberships-pro'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th scope="row"><?php _e('Today','paid-memberships-pro'); ?></th>
			<td><?php echo number_format_i18n($visits['today']); ?></td>
			<td><?php echo number_format_i18n($views['today']); ?></td>
			<td><?php echo number_format_i18n($logins['today']);?></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('This Week','paid-memberships-pro'); ?></th>
			<td><?php echo number_format_i18n($visits['week']); ?></td>
			<td><?php echo number_format_i18n($views['week']); ?></td>
			<td><?php echo number_format_i18n($logins['week']); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('This Month','paid-memberships-pro'); ?></th>
			<td><?php echo number_format_i18n($visits['month']); ?></td>
			<td><?php echo number_format_i18n($views['month']); ?></td>
			<td><?php echo number_format_i18n($logins['month']); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('Year to Date','paid-memberships-pro'); ?></th>
			<td><?php echo number_format_i18n($visits['ytd']); ?></td>
			<td><?php echo number_format_i18n($views['ytd']); ?></td>
			<td><?php echo number_format_i18n($logins['ytd']);?></td>
		</tr>
		<tr>
			<th scope="row"><?php _e('All Time','paid-memberships-pro'); ?></th>
			<td><?php echo number_format_i18n($visits['alltime']); ?></td>
			<td><?php echo number_format_i18n($views['alltime']);?></td>
			<td><?php echo number_format_i18n($logins['alltime']); ?></td>
		</tr>
	</tbody>
	</table>
	<?php if ( function_exists( 'pmpro_report_login_page' ) ) { ?>
		<p class="pmpro_report-button">
			<a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=pmpro-reports&report=login' ); ?>"><?php _e('Details', 'paid-memberships-pro' );?></a>
		</p>
	<?php } ?>	
</span>
<?php
}

function pmpro_report_login_page()
{
	global $wpdb;
	
	//vars
	if(!empty($_REQUEST['s']))
		$s = sanitize_text_field( $_REQUEST['s'] );
	else
		$s = "";
		
	if(!empty($_REQUEST['l'])) {
		if($_REQUEST['l'] == 'all')
			$l = 'all';
		else
			$l = intval($_REQUEST['l']);
	} else {
		$l = "";
	}
?>
	<form id="posts-filter" method="get" action="">	
	<h1>
		<?php _e('Visits, Views, and Logins Report', 'paid-memberships-pro');?>
	</h1>		
	<ul class="subsubsub">
		<li>			
			<?php _e( 'Show', 'Dropdown label, e.g. Show All Users', 'paid-memberships-pro' )?> <select name="l" onchange="jQuery('#posts-filter').submit();">
				<option value="" <?php if(!$l) { ?>selected="selected"<?php } ?>><?php _e('All Users', 'paid-memberships-pro')?></option>
				<option value="all" <?php if($l == "all") { ?>selected="selected"<?php } ?>><?php _e('All Levels', 'paid-memberships-pro')?></option>
				<?php
					$levels = $wpdb->get_results("SELECT id, name FROM $wpdb->pmpro_membership_levels ORDER BY name");
					$levels = pmpro_sort_levels_by_order( $levels );
					foreach($levels as $level)
					{
				?>
					<option value="<?php echo $level->id?>" <?php if($l == $level->id) { ?>selected="selected"<?php } ?>><?php echo $level->name?></option>
				<?php
					}
				?>
			</select>			
		</li>
	</ul>
	<p class="search-box">
		<label class="hidden" for="post-search-input"><?php _e( 'Search', 'Search form label', 'paid-memberships-pro')?> <?php if(empty($l)) echo "Users"; else echo "Members";?>:</label>
		<input type="hidden" name="page" value="pmpro-reports" />		
		<input type="hidden" name="report" value="login" />		
		<input id="post-search-input" type="text" value="<?php echo esc_attr($s)?>" name="s"/>
		<input class="button" type="submit" value="Search Members"/>
	</p>
	<?php 
		//some vars for the search					
		if(isset($_REQUEST['pn']))
			$pn = intval($_REQUEST['pn']);
		else
			$pn = 1;
			
		if(isset($_REQUEST['limit']))
			$limit = intval($_REQUEST['limit']);
		else
			$limit = 15;
		
		$end = $pn * $limit;
		$start = $end - $limit;				
					
		if($s)
		{
			$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, UNIX_TIMESTAMP(CONVERT_TZ(u.user_registered, '+00:00', @@global.time_zone)) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(CONVERT_TZ(mu.startdate, '+00:00', @@global.time_zone)) as startdate, UNIX_TIMESTAMP(CONVERT_TZ(mu.enddate, '+00:00', @@global.time_zone)) as enddate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->usermeta um ON u.ID = um.user_id LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id AND mu.status = 'active' LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id WHERE (u.user_login LIKE '%" . esc_sql($s) . "%' OR u.user_email LIKE '%" . esc_sql($s) . "%' OR um.meta_value LIKE '%" . esc_sql($s) . "%') ";
		
			if($l == "all")
				$sqlQuery .= " AND mu.status = 'active' AND mu.membership_id > 0 ";
			elseif($l)
				$sqlQuery .= " AND mu.membership_id = '" . esc_sql($l) . "' ";					
				
			$sqlQuery .= "GROUP BY u.ID ORDER BY user_registered DESC LIMIT $start, $limit";
		}
		else
		{
			$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, UNIX_TIMESTAMP(CONVERT_TZ(u.user_registered, '+00:00', @@global.time_zone)) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(CONVERT_TZ(mu.startdate, '+00:00', @@global.time_zone)) as startdate, UNIX_TIMESTAMP(CONVERT_TZ(mu.enddate, '+00:00', @@global.time_zone)) as enddate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id AND mu.status = 'active' LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id";
			$sqlQuery .= " WHERE 1=1 ";
			
			if($l == "all")
				$sqlQuery .= " AND mu.membership_id > 0  AND mu.status = 'active' ";
			elseif($l)
				$sqlQuery .= " AND mu.membership_id = '" . esc_sql($l) . "' ";
			$sqlQuery .= "GROUP BY u.ID ORDER BY user_registered DESC LIMIT $start, $limit";
		}

		$sqlQuery = apply_filters("pmpro_members_list_sql", $sqlQuery);
		
		$theusers = $wpdb->get_results($sqlQuery);
		$totalrows = $wpdb->get_var("SELECT FOUND_ROWS() as found_rows");
		
		if($theusers)
		{
		?>
		<p class="clear"><?php echo strval($totalrows)?> <?php if(empty($l)) echo "users"; else echo "members";?> found.	
		<?php		
		}		
	?>
	<table class="widefat">
		<thead>
			<tr class="thead">
				<th><?php _e('ID', 'paid-memberships-pro')?></th>
				<th><?php _e('Username', 'paid-memberships-pro')?></th>	
				<th><?php _e('Name', 'paid-memberships-pro')?></th>
				<th><?php _e('Level', 'paid-memberships-pro')?></th>	
				<th><?php _e('Joined', 'paid-memberships-pro')?></th>
				<th><?php _e('Expires', 'paid-memberships-pro')?></th>
				<th><?php _e('Last Visit', 'paid-memberships-pro')?></th>
				<th><?php _e('Visits This Week', 'paid-memberships-pro')?></th>
				<th><?php _e('Visits This Month', 'paid-memberships-pro')?></th>				
				<th><?php _e('Visits This Year', 'paid-memberships-pro')?></th>
				<th><?php _e('Visits All Time', 'paid-memberships-pro')?></th>
				<th><?php _e('Views This Week', 'paid-memberships-pro')?></th>
				<th><?php _e('Views This Month', 'paid-memberships-pro')?></th>				
				<th><?php _e('Views This Year', 'paid-memberships-pro')?></th>
				<th><?php _e('Views All Time', 'paid-memberships-pro')?></th>
				<th><?php _e('Last Login', 'paid-memberships-pro')?></th>
				<th><?php _e('Logins This Week', 'paid-memberships-pro')?></th>
				<th><?php _e('Logins This Month', 'paid-memberships-pro')?></th>				
				<th><?php _e('Logins This Year', 'paid-memberships-pro')?></th>
				<th><?php _e('Logins All Time', 'paid-memberships-pro')?></th>
			</tr>
		</thead>
		<tbody id="users" class="list:user user-list">	
			<?php	
				$count = 0;							
				foreach($theusers as $auser)
				{
					//get meta																					
					$theuser = get_userdata($auser->ID);			
					$visits = pmpro_reports_get_values_for_user("visits", $auser->ID);
					$views = pmpro_reports_get_values_for_user("views", $auser->ID);
					$logins = pmpro_reports_get_values_for_user("logins", $auser->ID);
					
					if(empty($logins))
						$logins = array("last"=>"N/A", "week"=>"N/A", "month"=>"N/A", "ytd"=>"N/A", "alltime"=>"N/A");
					?>
						<tr <?php if($count++ % 2 == 0) { ?>class="alternate"<?php } ?>>
							<td><?php echo $theuser->ID?></td>
							<td>
								<?php echo get_avatar($theuser->ID, 32)?>
								<strong>
									<?php
										$userlink = '<a href="user-edit.php?user_id=' . $theuser->ID . '">' . $theuser->user_login . '</a>';
										$userlink = apply_filters("pmpro_members_list_user_link", $userlink, $theuser);
										echo $userlink;
									?>																		
								</strong>
							</td>										
							<td>
								<?php echo $theuser->display_name;?>
							</td>
							<td><?php echo $auser->membership?></td>												
							<td><?php echo date_i18n( 'm/d/Y', strtotime( get_date_from_gmt( $theuser->user_registered ), current_time( 'timestamp' ) ) ); ?></td>

							<td>
								<?php 									
									if($auser->enddate) 
										echo date_i18n(get_option('date_format'), $auser->enddate);
									else
										echo "Never";
								?>
							</td>
							<td><?php if(!empty($visits['last'])) echo $visits['last'];?></td>
							<td><?php if(!empty($visits['week']) && pmpro_isDateThisWeek($visits['last'])) echo $visits['week'];?></td>
							<td><?php if(!empty($visits['month']) && pmpro_isDateThisMonth($visits['last'])) echo $visits['month'];?></td>
							<td><?php if(!empty($visits['ytd']) && pmpro_isDateThisYear($visits['last'])) echo $visits['ytd'];?></td>
							<td><?php if(!empty($visits['alltime'])) echo $visits['alltime'];?></td>							
							<td><?php if(!empty($views['week']) && pmpro_isDateThisWeek($views['last'])) echo $views['week'];?></td>
							<td><?php if(!empty($views['month']) && pmpro_isDateThisMonth($views['last'])) echo $views['month'];?></td>
							<td><?php if(!empty($views['ytd']) && pmpro_isDateThisYear($views['last'])) echo $views['ytd'];?></td>
							<td><?php if(!empty($views['alltime'])) echo $views['alltime'];?></td>
							<td><?php if(!empty($logins['last'])) echo $logins['last'];?></td>
							<td><?php if(!empty($logins['week']) && pmpro_isDateThisWeek($logins['last'])) echo $logins['week'];?></td>
							<td><?php if(!empty($logins['month']) && pmpro_isDateThisMonth($logins['last'])) echo $logins['month'];?></td>
							<td><?php if(!empty($logins['ytd']) && pmpro_isDateThisYear($logins['last'])) echo $logins['ytd'];?></td>
							<td><?php if(!empty($logins['alltime'])) echo $logins['alltime'];?></td>
						</tr>
					<?php
				}
				
				if(!$theusers)
				{
				?>
				<tr>
					<td colspan="9"><p><?php _e('No members found.', 'paid-memberships-pro')?> <?php if($l) { ?><a href="?page=pmpro-memberslist&s=<?php echo esc_attr($s)?>"><?php _e('Search all levels', 'paid-memberships-pro')?></a>.<?php } ?></p></td>
				</tr>
				<?php
				}
			?>		
		</tbody>
	</table>
	</form>

	<?php
	echo pmpro_getPaginationString($pn, $totalrows, $limit, 1, admin_url( "/admin.php?page=pmpro-reports&report=login&s=" . urlencode($s)), "&l=$l&limit=$limit&pn=");
	?>
<?php
}

/*
	Other code required for your reports. This file is loaded every time WP loads with PMPro enabled.
*/
//get values for a user
function pmpro_reports_get_values_for_user($type, $user_id = NULL) {
	//default to current user
	if(empty($user_id))
	{
		global $current_user;
		$user_id = $current_user->ID;
	}

	//need a type and user
	if(empty($type) || empty($user_id))
		return false;

	//get values from user meta
	$values = get_user_meta($user_id, "pmpro_" . $type, true);

	//clean them up
	if(empty($values))
		$values = array("last"=>"N/A", "thisdate"=>NULL, "week"=>0, "thisweek"=>NULL, "month"=>0, "thismonth"=>NULL, "ytd"=>0, "thisyear"=>NULL, "alltime"=>0);
	else
	{
		//check if we should reset any of the values
		$now = current_time('timestamp');
		$thisdate = date("Y-d-m", $now);
		$thisweek = date("W", $now);
		$thismonth = date("n", $now);
		$thisyear = date("Y", $now);

		if(!isset($values['thisdate']) || $thisdate != $values['thisdate'])
		{
			$values['today'] = 0;
			$values['thisdate'] = $thisdate;
			$update = true;
		}

		if(!isset($values['thisweek']) || $thisweek != $values['thisweek'])
		{
			$values['week'] = 0;
			$values['thisweek'] = $thisweek;
			$update = true;
		}

		if(!isset($values['thismonth']) || $thismonth != $values['thismonth'])
		{
			$values['month'] = 0;
			$values['thismonth'] = $thismonth;
			$update = true;
		}
				
		if(!isset($values['thisyear']) || $thisyear != $values['thisyear'])
		{
			$values['ytd'] = 0;
			$values['thisyear'] = $thisyear;
			$update = true;
		}

		if(!empty($update))
			update_user_meta($user_id, 'pmpro_' . $type, $values);
	}

	return $values;
}

//get values for a user
function pmpro_reports_get_all_values($type) {
	//need a type and user
	if(empty($type))
		return false;

	$allvalues = get_option("pmpro_" . $type);	
	if(empty($allvalues))
		$allvalues = array("today"=>0, "thisdate"=>NULL, "week"=>0, "thisweek"=>NULL, "month"=>0, "thismonth"=> NULL, "ytd"=>0, "thisyear"=>NULL, "alltime"=>0);
	else
	{
		//check if we should reset any of the values
		$now = current_time('timestamp');
		$thisdate = date("Y-d-m", $now);
		$thisweek = date("W", $now);
		$thismonth = date("n", $now);
		$thisyear = date("Y", $now);

		if(!isset($allvalues['thisdate']) || $thisdate != $allvalues['thisdate'])
		{
			$allvalues['today'] = 0;
			$allvalues['thisdate'] = $thisdate;
			$update = true;
		}

		if(!isset($allvalues['thisweek']) || $thisweek != $allvalues['thisweek'])
		{
			$allvalues['week'] = 0;
			$allvalues['thisweek'] = $thisweek;
			$update = true;
		}

		if(!isset($allvalues['thismonth']) || $thismonth != $allvalues['thismonth'])
		{
			$allvalues['month'] = 0;
			$allvalues['thismonth'] = $thismonth;
			$update = true;
		}
				
		if(!isset($allvalues['thisyear']) || $thisyear != $allvalues['thisyear'])
		{
			$allvalues['ytd'] = 0;
			$allvalues['thisyear'] = $thisyear;
			$update = true;
		}

		if(!empty($update))
			update_option('pmpro_' . $type, $allvalues);
	}

	return $allvalues;
}

//track visits, views, and logins and save to user meta
function pmpro_report_track_values($type, $user_id = NULL) {
	//don't track admin
	if(is_admin())
		return false;

	//default to current user
	if(empty($user_id))
	{
		global $current_user;
		$user_id = $current_user->ID;
	}

	//need a type
	if(empty($type))
		return false;

	//check for cookie for visits
	if( $type === 'visits' && !empty( $_COOKIE['pmpro_visit'] ) ) {
		return false;
	}

	//set cookie for visits
	if( $type === 'visits' && empty( $_COOKIE['pmpro_visit'] ) ) {
		// The secure parameter is set to is_ssl(), true if HTTPS.
        setcookie( 'pmpro_visit', '1', null, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
    }

	//some vars for below
	$now = current_time('timestamp');
	$thisdate = date("Y-d-m", $now);
	$thisweek = date("W", $now);
	$thismonth = date("n", $now);
	$thisyear = date("Y", $now);

	//track user stats if we have one
	if(!empty($user_id))
	{
		//get values
		$values = pmpro_reports_get_values_for_user($type, $user_id);

		if($values !== false)
		{
			//track for user
			$values['last'] = date(get_option("date_format"), $now);
			$values['alltime'] = $values['alltime'] + 1;
			
			if($thisweek == $values['thisweek'])
				$values['week'] = $values['week'] + 1;
			else
			{
				$values['week'] = 1;
				$values['thisweek'] = $thisweek;
			}
					
			if($thismonth == $values['thismonth'])
				$values['month'] = $values['month'] + 1;
			else
			{
				$values['month'] = 1;
				$values['thismonth'] = $thismonth;
			}
					
			if($thisyear == $values['thisyear'])
				$values['ytd'] = $values['ytd'] + 1;
			else
			{
				$values['ytd'] = 1;
				$values['thisyear'] = $thisyear;
			}		
				
			//update user data
			update_user_meta($user_id, "pmpro_" . $type, $values);
		}
	}

	//track cumulative stats
	$allvalues = pmpro_reports_get_all_values($type);

	$allvalues['alltime'] = $allvalues['alltime'] + 1;	
	
	if($thisdate == $allvalues['thisdate'])
		$allvalues['today'] = $allvalues['today'] + 1;
	else
	{
		$allvalues['today'] = 1;
		$allvalues['thisdate'] = $thisdate;
	}
	
	if($thisweek == $allvalues['thisweek'])
		$allvalues['week'] = $allvalues['week'] + 1;
	else
	{
		$allvalues['week'] = 1;
		$allvalues['thisweek'] = $thisweek;
	}
	
	if($thismonth == $allvalues['thismonth'])
		$allvalues['month'] = $allvalues['month'] + 1;
	else
	{
		$allvalues['month'] = 1;
		$allvalues['thismonth'] = $thismonth;
	}
	
	if($thisyear == $allvalues['thisyear'])
		$allvalues['ytd'] = $allvalues['ytd'] + 1;
	else
	{
		$allvalues['ytd'] = 1;
		$allvalues['thisyear'] = $thisyear;
	}

	update_option('pmpro_' . $type, $allvalues);
}

//track visits
function pmpro_report_login_wp_visits() {
	pmpro_report_track_values("visits");	
}
add_action("wp", "pmpro_report_login_wp_visits");

//we want to clear the pmpro_visit cookie on login/logout
function pmpro_report_login_clear_visit_cookie() {
	if(isset($_COOKIE['pmpro_visit']))
		unset($_COOKIE['pmpro_visit']);
}
add_action("wp_login", "pmpro_report_login_clear_visit_cookie");
add_action("wp_logout", "pmpro_report_login_clear_visit_cookie");

//track views
function pmpro_report_login_wp_views() {
	pmpro_report_track_values("views");
}
add_action("wp_head", "pmpro_report_login_wp_views");

//track logins
function pmpro_report_login_wp_login($user_login, $user) {
	pmpro_report_track_values("logins", $user->ID);	
}
add_action("wp_login", "pmpro_report_login_wp_login", 10 ,2);
