<?php
/**
 * Block: Tickets
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/tickets.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.0.3
 *
 * @version 5.0.3
 *
 * @var Tribe__Tickets__Editor__Template   $this                        [Global] Template object.
 * @var Tribe__Tickets__Tickets            $provider                    [Global] The tickets provider class.
 * @var string                             $provider_id                 [Global] The tickets provider class name.
 * @var Tribe__Tickets__Ticket_Object[]    $tickets                     [Global] List of tickets.
 * @var array                              $cart_classes                [Global] CSS classes.
 * @var Tribe__Tickets__Ticket_Object[]    $tickets_on_sale             [Global] List of tickets on sale.
 * @var bool                               $has_tickets_on_sale         [Global] True if the event has any tickets on sale.
 * @var bool                               $is_sale_past                [Global] True if tickets' sale dates are all in the past.
 * @var bool                               $is_sale_future              [Global] True if no ticket sale dates have started yet.
 * @var Tribe__Tickets__Commerce__Currency $currency                    [Global] Tribe Currency object.
 * @var Tribe__Tickets__Tickets_Handler    $handler                     [Global] Tribe Tickets Handler object.
 * @var int                                $threshold                   [Global] The count at which "number of tickets left" message appears.
 * @var bool                               $show_original_price_on_sale [Global] Show original price on sale.
 * @var null|bool                          $is_mini                     [Global] If in "mini cart" context.
 * @var null|bool                          $is_modal                    [Global] Whether the modal is enabled.
 * @var string                             $submit_button_name          [Global] The button name for the tickets block.
 * @var string                             $cart_url                    [Global] Link to Cart (could be empty).
 * @var string                             $checkout_url                [Global] Link to Checkout (could be empty).
 * @var WP_Post                            $post                        The post object.
 * @var int                                $post_id                     The post ID.
 */

// We don't display anything if there is no provider or tickets.
if ( ! $is_sale_future && ( ! $provider || ! $tickets ) ) {
	return false;
}

$classes = [
	'tribe-common',
	'event-tickets',
	'tribe-tickets__tickets-wrapper',
];

?>
<div <?php tribe_classes( $classes ); ?>>
	<form
		id="tribe-tickets__tickets-form"
		action="<?php echo esc_url( $provider->get_cart_url() ); ?>"
		class="tribe-tickets__tickets-form tribe-tickets__form"
		method="post"
		enctype='multipart/form-data'
		data-provider="<?php echo esc_attr( $provider->class_name ); ?>"
		autocomplete="off"
		data-provider-id="<?php echo esc_attr( $provider->orm_provider ); ?>"
		data-post-id="<?php echo esc_attr( $post_id ); ?>"
		novalidate
	>

		<input type="hidden" name="tribe_tickets_saving_attendees" value="1"/>
		<input type="hidden" name="tribe_tickets_ar" value="1"/>
		<input type="hidden" name="tribe_tickets_ar_data" value="" id="tribe_tickets_block_ar_data"/>

		<?php $this->template( 'v2/tickets/commerce/fields' ); ?>

		<?php $this->template( 'v2/tickets/title' ); ?>

		<?php $this->template( 'v2/tickets/notice' ); ?>

		<?php $this->template( 'v2/tickets/items' ); ?>

		<?php $this->template( 'v2/tickets/footer' ); ?>

		<?php $this->template( 'v2/tickets/item/inactive' ); ?>

		<?php $this->template( 'v2/components/loader/loader' ); ?>

	</form>

	<?php
	/**
	 * Allows injection of additional markup after the form tag but within the div of this template.
	 *
	 * @since 5.0.3
	 *
	 * @see  Tribe__Template\do_entry_point()
	 * @link https://docs.theeventscalendar.com/reference/classes/tribe__template/do_entry_point/
	 */
	$this->do_entry_point( 'after_form' );
	?>
</div>
