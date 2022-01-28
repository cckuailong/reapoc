<?php
/**
 * Render a block of rules
 */

defined( 'ABSPATH' ) || exit;

if ( ! conditional_logic_enabled() ) {
	return;
}
?>

<div class="conditional-logic-blocks <?php echo $field['has_conditional_logic'] ? 'active' : ''; ?>">
	<div class="conditional-groups-wrap">
		<div class="conditional-groups-tabs">
			<div class="conditional-group-blocks">
				<div class="qs-row">
					<div class="qs-col qs-col-12">
						<div class="wpcfr-rule-groups">
							<?php
							$active_tab = 'active';
							foreach ( $field['blocks'] as $group_block ) :
								WPCF7R_Html::get_block_html( 'block_1', $group_block, $active_tab, true, $prefix );
								$active_tab = false;
								endforeach;
							?>
						</div>
					</div>
					<div class="qs-col qs-col-12">
						<div class="groups-actions">
							<a href="#" class="button-primary wpcfr-add-group"><?php _e( 'OR', 'wpcf7-redirect' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
