<?php if( !empty( $post_types ) ) { ?>
<ul class="subsubsub">
	<li>Jump to: </li>
	<?php foreach( $post_types as $key => $post_type ) { ?>
	<li><a href="#post_type-<?php echo $key; ?>"><?php echo $post_type->label; ?></a> |</li>
	<?php } ?>
</ul>
<?php } ?>
<br class="clear">
<h3><?php _e( 'Post Types', 'woocommerce-store-toolkit' ); ?></h3>
<?php if( !empty( $post_types ) ) { ?>
<table class="widefat striped wp-list-table fixed posts">
	<thead>
		<tr>
			<th>Label</th>
			<th style="width:85%;">Object</th>
			<th>Count</th>
			<th>Posts</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach( $post_types as $key => $post_type ) { ?>
		<tr id="post_type-<?php echo $key; ?>">
			<td><strong><?php echo $post_type->label; ?></strong></td>
			<td style="font-family:monospace; text-align:left; width:100%;"><?php print_r( $post_type ); ?></td>
			<td><?php echo ( isset( $post_counts[$key] ) ? $post_counts[$key] : '-' ); ?></td>
			<td>
		<?php if( isset( $post_ids[$key] ) ) { ?>
			<?php if( !empty( $post_ids[$key] ) ) { ?>
				<?php foreach( $post_ids[$key] as $post_id ) { ?>
					<a href="<?php echo get_edit_post_link( $post_id ); ?>" target="_blank">#<?php echo $post_id; ?></a><br />
				<?php } ?>
			<?php } ?>
		<?php } else { ?>
				-
		<?php } ?>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<hr />

<?php } else { ?>
<p><?php _e( 'No Post Types were detected, weird.', 'woocommerce-store-toolkit' ); ?></p>
<?php } ?>