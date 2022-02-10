<?php

global $user_list_table;
// Query, filter, and sort the data.
$user_list_table = new PMPro_Members_List_Table();
$user_list_table->prepare_items();
require_once dirname( __DIR__ ) . '/adminpages/admin_header.php';

// Build CSV export link.
$csv_export_link = admin_url( 'admin-ajax.php' ) . '?action=memberslist_csv';
if ( isset( $_REQUEST['s'] ) ) {
	$csv_export_link .= '&s=' . esc_attr( sanitize_text_field( trim( $_REQUEST['s'] ) ) );
}
if ( isset( $_REQUEST['l'] ) ) {
	$csv_export_link .= '&l=' . sanitize_text_field( trim( $_REQUEST['l'] ) );
}

// Render the List Table.
?>
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Members List', 'paid-memberships-pro' ); ?></h1>
	<a target="_blank" href="<?php echo esc_url( $csv_export_link ); ?>" class="page-title-action"><?php esc_html_e( 'Export to CSV', 'paid-memberships-pro' ); ?></a>
	<hr class="wp-header-end">

	<?php do_action( 'pmpro_memberslist_before_table' ); ?>			
	<form id="member-list-form" method="get">
		<input type="hidden" name="page" value="pmpro-memberslist" />
		<?php
			$user_list_table->search_box( __( 'Search Members', 'paid-memberships-pro' ), 'paid-memberships-pro' );
			$user_list_table->display();
		?>
	</form>

	<?php if ( ! function_exists( 'pmprorh_add_registration_field' ) ) {
		$allowed_pmprorh_html = array (
			'a' => array (
				'href' => array(),
				'target' => array(),
				'title' => array(),
			),
		);
		echo '<p class="description">' . sprintf( wp_kses( __( 'Optional: Capture additional member profile fields using the <a href="%s" title="Paid Memberships Pro - Register Helper Add On" target="_blank">Register Helper Add On</a>.', 'paid-memberships-pro' ), $allowed_pmprorh_html ), 'https://www.paidmembershipspro.com/add-ons/pmpro-register-helper-add-checkout-and-profile-fields/?utm_source=plugin&utm_medium=pmpro-memberslist&utm_campaign=add-ons&utm_content=pmpro-register-helper-add-checkout-and-profile-fields' ) . '</p>';
	} ?>
	
<?php
	require_once dirname( __DIR__ ) . '/adminpages/admin_footer.php';
?>
