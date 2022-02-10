<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap wpbs-wrap wpbs-wrap-calendars">


	<!-- Page Heading -->
	<h1 class="wp-heading-inline"><?php echo __( 'Calendars', 'wp-booking-system' ); ?></h1>
	<a href="<?php echo add_query_arg( array( 'subpage' => 'add-calendar' ), $this->admin_url ); ?>" class="page-title-action"><?php echo __( 'Add New Calendar', 'wp-booking-system' ); ?></a>
	<hr class="wp-header-end" />


	<?php wpbs_show_update_screen(); ?>
	
	<!-- Calendars List Table -->
	<form method="get">

        <input type="hidden" name="page" value="wpbs-calendars" />
        <input type="hidden" name="paged" value="1">

		<?php
			$table = new WPBS_WP_List_Table_Calendars();
			$table->views();
			$table->search_box( __( 'Search Calendars' ), 'wpbs-search-calendars' );
			$table->display();
		?>
	</form>

	<a href="<?php echo add_query_arg( array( 'page' => 'wpbs-calendars', 'subpage' => 'upgrade-to-premium' ), $this->admin_url ); ?>" class="wpbs-wrap-upgrade-cta">
		<span class="wpbs-wrap-upgrade-cta-button">I'm interested</span>
		<span class="wpbs-wrap-upgrade-cta-heading">Missing anything? Discover more powerful features in the premium version now!</span>
	</a>

</div>