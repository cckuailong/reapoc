<?php
/**
 * Class to set the order of FAQs, if enabled
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewdufaqOrderingTable' ) ) {
class ewdufaqOrderingTable {

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'register_menu_screen' ) );
	}

	/**
	 * Adds filtering form, controls and column titles
	 * @since 3.0.0
	 */
	public function register_menu_screen() {
		global $ewd_ufaq_controller;

		if ( $ewd_ufaq_controller->settings->get_setting( 'faq-order-by' ) != 'set_order' ) { return; }
		
		add_submenu_page(
			'edit.php?post_type=ufaq',
			esc_html__( 'Ordering Table', 'ultimate-faqs' ),
			esc_html__( 'Ordering Table', 'ultimate-faqs' ),
			$ewd_ufaq_controller->settings->get_setting( 'access-role' ),
			'ewd-ufaq-ordering-table',
			array( $this, 'display_admin_screen' )
		);
	}

	/**
	 * Diplays the admin screen where it's possible to set the order of FAQs
	 * @since 3.0.0
	 */
	public function display_admin_screen() { 
		
		$args = array(
			'post_type' => EWD_UFAQ_FAQ_POST_TYPE,
			'posts_per_page' => -1,
			'meta_query' => array(
			    'relation' => 'OR',
			    array(
			        'key' => 'ufaq_order', 
			        'compare' => 'EXISTS'
			    ),
			    array(
			        'key' => 'ufaq_order', 
			        'compare' => 'NOT EXISTS'
			    )
			),
			'orderby' => 'meta_value_num',
			'order' => 'ASC'
		);

		$faqs = get_posts( $args );

		?>

		<h2>FAQ Ordering Table</h2>

		<div id='ewd-ufaq-ordering-table-container'>

			<div id='ewd-ufaq-ordering-table-explanation'>
				<?php _e( 'Use the table below to set the order for your FAQs, either overall or within their categories depending on your selected settings.', 'ultimate-faqs' ); ?>
			</div>

			<table class='ewd-ufaq-ordering-table form-table wp-list-table widefat sorttable ewd-ufaq-list'>
				<thead>
					<tr>
						<th><?php _e( 'Question', 'ultimate-faqs' ); ?></th>
						<th><?php _e( 'Views', 'ultimate-faqs' ); ?></th>
						<th><?php _e( 'Categories', 'ultimate-faqs' ); ?></th>
						<th><?php _e( 'Tags', 'ultimate-faqs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $faqs as $faq ) { ?>

						<?php $faq_views = get_post_meta( $faq->ID, 'ufaq_view_count', true ); ?>
						<?php $faq_categories = get_the_term_list($faq->ID, 'ufaq-category', '', ', ', ''); ?>
						<?php $faq_tags = get_the_term_list($faq->ID, 'ufaq-tag', '', ', ', ''); ?>

						<tr id='ewd-ufaq-item-<?php echo $faq->ID; ?>' class='ewd-ufaq-item'>
							<td class='ewd-ufaq-title'><?php echo esc_html( $faq->post_title ); ?></td>
							<td class='ewd-ufaq-title'><?php echo esc_html( $faq_views ); ?></td>
							<td class='ewd-ufaq-title'><?php echo esc_html( strip_tags( $faq_categories ) ); ?></td>
							<td class='ewd-ufaq-title'><?php echo esc_html( strip_tags( $faq_tags ) ); ?></td>
						</tr>

					<?php } ?>
				</tbody>
			</table>

		</div>

	<?php }

}
} // endif;