<?php
/**
 * The template that displays the search type dropdown in the Attendees admin screen.
 *
 * @since 4.10.6
 */
?>

<select name="tribe_attendee_search_type" class="tribe-admin-search-type">
	<?php foreach ( $options as $option => $label ) : ?>
		<option value="<?php echo esc_attr( $option ); ?>"<?php selected( $selected, $option ); ?>>
			<?php echo esc_html( $label ); ?>
		</option>
	<?php endforeach; ?>
</select>
