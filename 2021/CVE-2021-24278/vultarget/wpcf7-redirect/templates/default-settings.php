<?php
/**
 * Displays the list of actions
 */

defined( 'ABSPATH' ) || exit;

$rule_id = 'default';
$actions = $this->get_actions( $rule_id );
?>

<h2>
	<?php _e( 'Submission Actions', 'wpcf7-redirect' ); ?>
</h2>

<legend>
	<?php _e( 'You can add actions that will be fired on submission. For details and support check', 'wpcf7-redirect' ); ?> <a href="https://redirection-for-contact-form7.com/" target="_blank"><?php _e( 'official website', 'wpcf7-redirect' ); ?></a>.
</legend>

<div class="actions-list">
	<div class="actions">
		<table class="wp-list-table widefat fixed striped pages" data-wrapid="<?php echo $rule_id; ?>">
			<thead>
				<tr>
					<th class="manage-column check-column">
						<a href="#"><?php _e( 'No.', 'wpcf7-redirect' ); ?></a>
					</th>
					<th class="manage-column column-title column-primary sortable desc">
						<a href="#"><?php _e( 'Title', 'wpcf7-redirect' ); ?></a>
					</th>
					<th class="manage-column column-primary sortable desc">
						<a href="#"><?php _e( 'Type', 'wpcf7-redirect' ); ?></a>
					</th>
					<th class="manage-column column-primary sortable desc">
						<a href="#"><?php _e( 'Active', 'wpcf7-redirect' ); ?></a>
					</th>
					<th class="manage-column check-column">
					</th>
				</tr>
			</thead>
			<tbody id="the_list">
				<?php if ( $actions ) : ?>
					<?php foreach ( $actions as $action ) : ?>
						<?php echo $action->get_action_row(); ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<div class="add-new-action-wrap">
		<select class="new-action-selector" name="new-action-selector">
			<option value="" selected="selected"><?php _e( 'Choose Action', 'wpcf7-redirect' ); ?></option>
			<?php foreach ( wpcf7r_get_available_actions() as $available_action_key => $available_action_label ) : ?>
				<option value="<?php echo $available_action_key; ?>" <?php echo $available_action_label['attr']; ?>><?php echo $available_action_label['label']; ?></option>
			<?php endforeach; ?>

			<?php foreach ( wpcf7_get_extensions() as $extension ) : ?>
				<?php if ( ! isset( $extension['active'] ) || ! $extension['active'] ) : ?>
					<option value="<?php echo WPCF7_PRO_REDIRECT_PLUGIN_PAGE_URL . $extension['name']; ?>" data-action="purchase">
						<?php echo $extension['title']; ?> (<?php _e( 'Purchase', 'wpcf7-redirect' ); ?>)
					</option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
		<a type="button" name="button" class="button-primary wpcf7-add-new-action" data-ruleid="<?php echo $rule_id; ?>" data-id="<?php echo $this->get_id(); ?>">
			<?php _e( 'Add Action', 'wpcf7-redirect' ); ?>
		</a>
	</div>
</div>
