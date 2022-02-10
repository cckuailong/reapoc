<?php
/**
 * Attendee Registration Modal class
 *
 * @since 4.11.0
 */
class Tribe__Tickets__Attendee_Registration__Modal {

	/**
	 * Setup Modal Cart Template.
	 *
	 * @since 4.11.0
	 */
	public function hook() {
		add_filter( 'tribe_events_tickets_attendee_registration_modal_content', [ $this, 'modal_cart_template' ], 10, 2 );
	}

	/**
	 * Return Cart Template for Modal.
	 *
	 * @since 4.11.0
	 *
	 * @param string                           $content a string of default content.
	 * @param Tribe__Tickets__Editor__Template $template_obj the Template object.
	 *
	 * @return string
	 */
	function modal_cart_template( $content, $template_obj ) {
		// If they're not using the new views, include v1 and bail.
		if ( ! tribe_tickets_new_views_is_enabled() ) {
			return $this->modal_cart_template_v1( $content, $template_obj );
		}

		return $content;
	}

	/**
	 * Render AR Template to Modal.
	 *
	 * @since 4.11.0
	 *
	 * @param string                           $unused_content The content string.
	 * @param Tribe__Tickets__Editor__Template $template_obj the Template object.
	 */
	function append_modal_ar_template( $unused_content, $template_obj ) {
		// If they're not using the new views, include v1 and bail.
		if ( ! tribe_tickets_new_views_is_enabled() ) {
			$this->append_modal_ar_template_v1( $unused_content, $template_obj );
		}
	}

	/**
	 * Add Footer Template to Modal content.
	 *
	 * @since 4.11.0
	 *
	 * @param string                           $content The content string.
	 * @param Tribe__Tickets__Editor__Template $template_obj the Template object.
	 *
	 * @return string The content with AR fields appended.
	 */
	function modal_footer_template( $content, $template_obj ) {
		$template = 'modal/footer.php';
		$file     = $this->locate_template( $template );

		$obj_tickets = $template_obj->get( 'tickets', [] );

		foreach ( $obj_tickets as $ticket ) {
			$ticket_data = array(
				'id'       => $ticket->ID,
				'qty'      => 1,
				'provider' => $ticket->provider,
			);

			$tickets[] = $ticket_data;
		}

		$template            = $template_obj;
		$post_id             = $template_obj->get( 'post_id' );
		$provider            = $template_obj->get( 'provider' );
		$provider_id         = $template_obj->get( 'provider_id' );
		$cart_url            = $template_obj->get( 'cart_url' );
		$tickets_on_sale     = $template_obj->get( 'tickets_on_sale' );
		$has_tickets_on_sale = $template_obj->get( 'has_tickets_on_sale' );
		$is_sale_past        = $template_obj->get( 'is_sale_past' );

		ob_start();

		include $file;

		$content .= ob_get_clean();

		return $content;
	}

	/**
	 * Template finder.
	 * Allows for overriding template in theme.
	 *
	 * @param string $template Relative path to template file.
	 *
	 * @return string The template file to use.
	 */
	function locate_template( $template ) {
		$main = Tribe__Tickets__Main::instance();

		$theme_file = locate_template( [ 'tribe-events/' . $template, 'tribe/tickets/' . $template ] );

		if ( $theme_file ) {
			$file = $theme_file;
		} else {
			$file = $main->plugin_path . 'src/views/' . $template;
		}

		/**
		 * Filter Modal Template
		 *
		 * @since 4.11.0
		 *
		 * @param string $template Relative path to template file.
		 * @param string $file The template location.
		 *
		 * @return string
		 */
		$file = apply_filters( 'tribe_events_tickets_template_' . $template, $file );

		return $file;
	}

	/**
	 * Return Cart Template for Modal (V1).
	 * Note: This will be deprecated when we remove support for V1 views.
	 * Make it private so we can erase it later.
	 *
	 * @since 5.0.3
	 *
	 * @param string                           $content a string of default content.
	 * @param Tribe__Tickets__Editor__Template $template_obj the Template object.
	 *
	 * @return string The cart template HTML.
	 */
	private function modal_cart_template_v1( $content, $template_obj ) {
		$template = 'modal/cart.php';
		$file     = $this->locate_template( $template );

		$post_id             = $template_obj->get( 'post_id' );
		$tickets             = $template_obj->get( 'tickets', [] );
		$provider            = $template_obj->get( 'provider' );
		$provider_id         = $template_obj->get( 'provider_id' );
		$cart_url            = $template_obj->get( 'cart_url' );
		$tickets_on_sale     = $template_obj->get( 'tickets_on_sale' );
		$has_tickets_on_sale = $template_obj->get( 'has_tickets_on_sale' );
		$is_sale_past        = $template_obj->get( 'is_sale_past' );

		ob_start();
		?>
		<form
			id="tribe-tickets__modal-form"
			action=""
			method="post"
			enctype='multipart/form-data'
			data-provider="<?php echo esc_attr( $provider->class_name ); ?>"
			autocomplete="off"
			data-provider-id="<?php echo esc_attr( $provider->orm_provider ); ?>"
			novalidate
		>
			<?php
			$template_obj->template( $template );
			$this->append_modal_ar_template_v1( $content, $template_obj );
			?>
		</form>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render AR Template to Modal (V1).
	 * Note: This will be deprecated when we remove support for V1 views.
	 * Make it private so we can erase it later.
	 *
	 * @since 5.0.3
	 *
	 * @param string                           $unused_content The content string.
	 * @param Tribe__Tickets__Editor__Template $template_obj the Template object.
	 */
	private function append_modal_ar_template_v1( $unused_content, $template_obj ) {
		$template    = 'modal/registration-js.php';
		$file        = $this->locate_template( $template );
		$obj_tickets = $template_obj->get( 'tickets', [] );
		$tickets     = [];

		foreach ( $obj_tickets as $ticket ) {
			$ticket_data = [
				'id'       => $ticket->ID,
				'qty'      => 1,
				'provider' => $ticket->get_provider(),
			];

			$tickets[] = $ticket_data;
		}

		include $file;
	}
}
