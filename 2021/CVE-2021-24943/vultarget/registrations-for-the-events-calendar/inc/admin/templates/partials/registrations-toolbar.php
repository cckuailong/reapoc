<?php
$dismiss_new_needed = ((int)get_transient( 'rtec_new_registrations' ) > 0);
$settings = $this->settings;
?>
<div class="rtec-toolbar wp-filter">
	<div class="rtec-toolbar-secondary">
		<form id="rtec-toolbar-form" action="" method="get" style="margin-bottom: 0;">
			<input type="hidden" name="post_type" value="tribe_events">
			<input type="hidden" name="page" value="<?php echo RTEC_MENU_SLUG; ?>">
			<input type="hidden" name="v" value="<?php echo esc_attr( $settings['v'] ); ?>">
			<div class="view-switch rtec-grid-view-switch">
				<a href="<?php $this->the_toolbar_href( 'v', 'list' ); ?>" class="view-list<?php if( $settings['v'] === 'list' ) echo ' current'; ?>">
					<span class="screen-reader-text"><?php _e( 'List View', 'registrations-for-the-events-calendar' ); ?></span>
				</a>
				<a href="<?php $this->the_toolbar_href( 'v', 'grid' ); ?>" class="view-grid<?php if( $settings['v'] === 'grid' ) echo ' current'; ?>">
					<span class="screen-reader-text"><?php _e( 'Grid View', 'registrations-for-the-events-calendar' ); ?></span>
				</a>
			</div>
			<label for="rtec-registrations-date" class="screen-reader-text"><?php _e( 'Filter by start date', 'registrations-for-the-events-calendar' ); ?></label>
			<select id="rtec-registrations-date" name="qtype" class="registrations-filters">
                <option value="upcoming" <?php if ( $settings['qtype'] === 'upcoming' ) echo 'selected'; ?>><?php _e( 'View Upcoming', 'registrations-for-the-events-calendar' ); ?></option>
                <option value="cur" <?php if ( $settings['qtype'] === 'cur' ) echo 'selected'; ?>><?php _e( 'View Current', 'registrations-for-the-events-calendar' ); ?></option>
                <option value="past" <?php if ( $settings['qtype'] === 'past' ) echo 'selected'; ?>><?php _e( 'View Past', 'registrations-for-the-events-calendar' ); ?></option>
                <option value="hid" <?php if ( $settings['qtype'] === 'hid' ) echo 'selected'; ?>><?php _e( 'View Hidden from Listing', 'registrations-for-the-events-calendar' ); ?></option>
                <option value="start" <?php if ( $settings['qtype'] === 'start' ) echo 'selected'; ?>><?php _e( 'Select Start Date', 'registrations-for-the-events-calendar' ); ?></option>
                <option value="all" <?php if ( $settings['qtype'] === 'all' ) echo 'selected'; ?>><?php _e( 'View All', 'registrations-for-the-events-calendar' ); ?></option>
            </select>
			<label for="rtec-registrations-start" class="screen-reader-text"><?php _e( 'Filter by event start date', 'registrations-for-the-events-calendar' ); ?></label>
			<input type="text" id="rtec-date-picker" name="start" value="<?php echo date( "m/d/Y", strtotime( $settings['start'] ) ); ?>" class="rtec-date-picker" style="vertical-align: middle;<?php if ( $settings['qtype'] !== 'start' ) echo 'display: none;'; ?>"/>
			<label for="rtec-registrations-reg" class="screen-reader-text"><?php _e( 'Filter by registrations', 'registrations-for-the-events-calendar' ); ?></label>
			<select id="rtec-registrations-reg" name="with" class="registrations-filters">
				<option value="with" <?php if ( $settings['with'] === 'with' ) echo 'selected'; ?>><?php _e( 'With registrations enabled', 'registrations-for-the-events-calendar' ); ?></option>
				<option value="either" <?php if ( $settings['with'] === 'either' ) echo 'selected'; ?>><?php _e( 'With/without registrations', 'registrations-for-the-events-calendar' ); ?></option>
			</select>
			<button id="rtec-filter-go" type="button" class="button rtec-toolbar-button" data-rtec-view-settings="<?php echo esc_attr( json_encode( $settings ) ); ?>"><?php _e( 'Go', 'registrations-for-the-events-calendar' ); ?></button>
		</form>
	</div>
	<div class="rtec-toolbar-primary search-form"><label for="rtec-search-input" class="screen-reader-text"><?php _e( 'Search Registrants', 'registrations-for-the-events-calendar' ); ?></label>
		<input type="search" placeholder="<?php _e( 'Search Registrants', 'registrations-for-the-events-calendar' ); ?>" id="rtec-search-input" class="search">
	</div>
	<?php if ( $dismiss_new_needed ) : ?>
        <div class="rtec-clear"></div>
        <a id="rtec-new-dismiss" href="JavaScript:void(0);" class="rtec-email-creator-send"><span class="rtec-notice-new"><i class="fa fa-tag" aria-hidden="true"></i></span> <?php _e( 'dismiss notices', 'registrations-for-the-events-calendar' ); ?></a>
	<?php endif; ?>
</div>