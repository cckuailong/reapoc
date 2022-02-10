<?php
$is_admin = tribe_is_truthy( tribe_get_request_var( 'is_admin', is_admin() ) );

if ( ! isset( $post_id ) ) {
	$post_id = get_the_ID();
}

$provider       = null;
$provider_class = null;
$ticket         = null;

$datepicker_format = Tribe__Date_Utils::datepicker_formats( Tribe__Date_Utils::get_datepicker_format_index() );

if ( ! isset( $ticket_id ) ) {
	$ticket_id = null;

	if ( ! $is_admin ) {
		$provider_class = Tribe__Tickets__Tickets::get_default_module();
	}
} else {
	$provider = tribe_tickets_get_ticket_provider( $ticket_id );

	if ( ! empty( $provider ) ) {
		$provider_class = $provider->class_name;

		$ticket = $provider->get_ticket( $post_id, $ticket_id );
	}

	if ( ! empty( $ticket->start_date ) ) {
		$start_date = Tribe__Date_Utils::date_only( $ticket->start_date, false, $datepicker_format );
	} else {
		$start_date = null;
	}

	if ( ! empty( $ticket->end_date ) ) {
		$end_date = Tribe__Date_Utils::date_only( $ticket->end_date, false, $datepicker_format );
	} else {
		$end_date = null;
	}
}

$modules = Tribe__Tickets__Tickets::modules();

$default_module_class = empty( $default_module ) ? '' : $default_module->class_name;

$timepicker_step = 30;

if ( class_exists( 'Tribe__Events__Main' ) ) {
	$timepicker_step = (int) tribe( 'tec.admin.event-meta-box' )->get_timepicker_step( 'start' );
}

$timepicker_round = '00:00:00';

$start_date_errors = [
	'is-required'         => __( 'Start sale date cannot be empty.', 'event-tickets' ),
	'is-less-or-equal-to' => __( 'Start sale date cannot be greater than End Sale date', 'event-tickets' ),
];

$ticket_name        = $ticket ? $ticket->name : '';
$ticket_description = $ticket ? $ticket->description : '';
$ticket_start_time  = $ticket ? $ticket->start_time : '';
$ticket_end_time    = $ticket ? $ticket->end_time : '';

$msg_rsvp_type_required = sprintf(
	// Translators: %s: dynamic 'RSVP' text.
	_x(
		'%s type is a required field',
		'admin edit ticket panel error',
		'event-tickets'
	),
	tribe_get_rsvp_label_singular( 'admin_edit_ticket_panel_error' )
);

$msg_ticket_start_time_aria = sprintf(
// Translators: %s: dynamic 'Ticket' text.
	_x(
		'%s start date',
		'input start time ARIA label',
		'event-tickets'
	),
	tribe_get_ticket_label_singular( 'input_start_time_aria_label' )
);

$msg_ticket_start_date = sprintf(
	// Translators: %s: dynamic 'tickets' text.
	_x(
		'If you do not set a start sale date, %s will be available immediately.',
		'input start time help text title',
		'event-tickets'
	),
	tribe_get_ticket_label_plural_lowercase( 'input_start_time_help_text_title' )
);

$msg_ticket_end_date_aria = sprintf(
	// Translators: %s: dynamic 'Ticket' text.
	_x(
		'%s end date',
		'input end time ARIA label',
		'event-tickets'
	),
	tribe_get_ticket_label_singular( 'input_end_time_aria_label' )
);

$msg_ticket_end_date_title_if_tec = sprintf(
	// Translators: %s: dynamic 'tickets' text.
	_x(
		'If you do not set an end sale date, %s will be available until the event begins.',
		'input end time help text title',
		'event-tickets'
	),
	tribe_get_ticket_label_plural_lowercase( 'input_end_time_help_text_title' )
);

$msg_ticket_end_date_title_if_not_tec = sprintf(
	// Translators: %s: dynamic 'tickets' text.
	_x(
		'If you do not set an end sale date, %s will be available forever.',
		'input end time help text title',
		'event-tickets'
	),
	tribe_get_ticket_label_plural_lowercase( 'input_end_time_help_text_title' )
);

$msg_ticket_form_save = sprintf(
	// Translators: %s: dynamic 'tickets' text.
	_x(
		'Save %s',
		'meta box ticket form button text',
		'event-tickets'
	),
	tribe_get_ticket_label_singular_lowercase( 'meta_box_ticket_form_button_text' )
);

$msg_rsvp_form_save = sprintf(
// Translators: %s: dynamic 'RSVP' text.
	_x(
		'Save %s',
		'RSVP form save value',
		'event-tickets'
	),
	tribe_get_rsvp_label_singular( 'form_save_value' )
);
?>

<div id="tribe_panel_edit" class="ticket_panel panel_edit tribe-validation" aria-hidden="true" data-default-provider="<?php echo esc_attr( $default_module_class ); ?>">
	<?php
	/**
	 * Allows for the insertion of additional elements into the main ticket edit panel.
	 *
	 * @since 4.6
	 *
	 * @param int Post ID
	 * @param int Ticket ID
	 */
	do_action( 'tribe_events_tickets_pre_edit', $post_id, $ticket_id );
	?>

	<div id="ticket_form" class="ticket_form tribe_sectionheader tribe-validation">
		<div id="ticket_form_table" class="eventtable ticket_form">
			<div
				class="tribe-dependent"
				data-depends="#Tribe__Tickets__RSVP_radio"
				data-condition-is-not-checked
			>
				<h4
					id="ticket_title_add"
					class="ticket_form_title tribe-dependent"
					data-depends="#ticket_id"
					data-condition-is-empty
				>
					<?php
					echo esc_html(
						sprintf(
							// Translators: %s: dynamic 'ticket' text.
							_x(
								'Add new %s',
								'admin add new ticket panel heading',
								'event-tickets'
							),
							tribe_get_ticket_label_singular_lowercase( 'admin_add_new_ticket_panel_heading' )
						)
					);
					?>
				</h4>
				<h4
					id="ticket_title_edit"
					class="ticket_form_title tribe-dependent"
					data-depends="#ticket_id"
					data-condition-is-not-empty
				>
					<?php
					echo esc_html(
						sprintf(
							// Translators: %s: dynamic 'ticket' text.
							_x(
								'Edit %s',
								'admin edit ticket panel heading',
								'event-tickets'
							),
							tribe_get_ticket_label_singular_lowercase( 'admin_edit_ticket_panel_heading' )
						)
					); ?>
				</h4>
			</div>
			<div
				class="tribe-dependent"
				data-depends="#Tribe__Tickets__RSVP_radio"
				data-condition-is-checked
			>
				<h4
					id="rsvp_title_add"
					class="ticket_form_title tribe-dependent"
					data-depends="#ticket_id"
					data-condition-is-empty
				>
					<?php
					echo esc_html(
						sprintf(
							// Translators: %s: dynamic 'RSVP' text.
							_x(
								'Add new %s',
								'admin add new ticket panel heading',
								'event-tickets'
							),
							tribe_get_rsvp_label_singular( 'admin_add_new_ticket_panel_heading' )
						)
					);
					?>
				</h4>
				<h4
					id="rsvp_title_edit"
					class="ticket_form_title tribe-dependent"
					data-depends="#ticket_id"
					data-condition-is-not-empty
				>
					<?php
					echo esc_html(
						sprintf(
							// Translators: %s: dynamic 'RSVP' text.
							_x(
								'Edit %s',
								'admin edit ticket panel heading',
								'event-tickets'
							),
							tribe_get_rsvp_label_singular( 'admin_edit_ticket_panel_heading' )
						)
					);
					?>
				</h4>
			</div>
			<section id="ticket_form_main" class="main" data-datepicker_format="<?php echo esc_attr( Tribe__Date_Utils::get_datepicker_format_index() ); ?>">
				<div class="input_block">
					<label class="ticket_form_label ticket_form_left" for="ticket_name"><?php esc_html_e( 'Type:', 'event-tickets' ); ?></label>
					<input
						type='text'
						id='ticket_name'
						name='ticket_name'
						class="ticket_field ticket_form_right"
						size='25'
						value="<?php echo esc_attr( $ticket_name ); ?>"
						data-validation-is-required
						data-validation-error="<?php echo esc_attr( $msg_rsvp_type_required ); ?>"
					/>
					<span
						class="tribe_soft_note ticket_form_right"
						data-depends="#Tribe__Tickets__RSVP_radio"
						data-condition-not-checked
					><?php
						echo esc_html(
							sprintf(
								// Translators: %1$s: dynamic 'ticket' text.
								_x(
									'This is the name of your %1$s. It is displayed on the frontend of your website and within %1$s emails.',
									'admin edit ticket panel note',
									'event-tickets'
								),
								tribe_get_ticket_label_singular_lowercase( 'admin_edit_ticket_panel_note' )
							)
						);
						?>
					</span>
					<span
						class="tribe_soft_note ticket_form_right"
						data-depends="#Tribe__Tickets__RSVP_radio"
						data-condition-is-checked
					><?php
						echo esc_html(
							sprintf(
								// Translators: %1$s: dynamic 'RSVP' text.
								_x(
									'This is the name of your %1$s. It is displayed on the frontend of your website and within %1$s emails.',
									'admin edit RSVP panel note',
									'event-tickets'
								),
								tribe_get_rsvp_label_singular( 'admin_edit_rsvp_panel_note' )
							)
						);
						?>
					</span>
				</div>
				<div class="input_block">
					<label class="ticket_form_label ticket_form_left" for="ticket_description"><?php esc_html_e( 'Description:', 'event-tickets' ); ?></label>
					<textarea
						rows="5"
						cols="40"
						name="ticket_description"
						class="ticket_field ticket_form_right"
						id="ticket_description"
					><?php echo esc_textarea( $ticket_description ); ?></textarea>
					<div class="input_block">
						<label class="tribe_soft_note">
							<input
								type="checkbox"
								id="tribe_tickets_show_description"
								name="ticket_show_description"
								value="1"
								class="ticket_field ticket_form_left"
								<?php checked( true, $ticket ? $ticket->show_description : true ); ?>
							>
							<?php
							echo esc_html(
								sprintf(
								// Translators: %s: dynamic 'tickets' text.
									_x(
										'Show description on frontend %s form.',
										'default ticket provider',
										'event-tickets'
									),
									tribe_get_ticket_label_singular_lowercase( 'default_ticket_provider' )
								)
							);
							?>
						</label>
					</div>
				</div>
				<div class="input_block">
					<label class="ticket_form_label ticket_form_left" for="ticket_start_date"><?php esc_html_e( 'Start sale:', 'event-tickets' ); ?></label>
					<div class="ticket_form_right">
						<input
							autocomplete="off"
							type="text"
							class="tribe-datepicker tribe-field-start_date ticket_field"
							name="ticket_start_date"
							id="ticket_start_date"
							value="<?php echo esc_attr( $ticket ? $start_date : null ); ?>"
							data-validation-type="datepicker"
							data-validation-is-less-or-equal-to="#ticket_end_date"
							data-validation-error="<?php echo esc_attr( wp_json_encode( $start_date_errors ) ); ?>"
						/>
						<span class="helper-text hide-if-js"><?php esc_html_e( 'YYYY-MM-DD', 'event-tickets' ); ?></span>
						<span class="datetime_seperator"> <?php esc_html_e( 'at', 'event-tickets' ); ?> </span>
						<input
							autocomplete="off"
							type="text"
							class="tribe-timepicker tribe-field-start_time ticket_field"
							name="ticket_start_time"
							id="ticket_start_time"
							<?php echo Tribe__View_Helpers::is_24hr_format() ? 'data-format="H:i"' : ''; ?>
							data-step="<?php echo esc_attr( $timepicker_step ); ?>"
							data-round="<?php echo esc_attr( $timepicker_round ); ?>"
							value="<?php echo esc_attr( $ticket_start_time ); ?>"
							aria-label="<?php echo esc_attr( $msg_ticket_start_time_aria ); ?>"
						/>
						<span class="helper-text hide-if-js"><?php esc_html_e( 'HH:MM', 'event-tickets' ); ?></span>
						<span class="dashicons dashicons-editor-help" title="<?php echo esc_attr( $msg_ticket_start_date ); ?>">
			</span>
					</div>
				</div>
				<div class="input_block">
					<label class="ticket_form_label ticket_form_left" for="ticket_end_date"><?php esc_html_e( 'End sale:', 'event-tickets' ); ?></label>
					<div class="ticket_form_right">
						<input
							autocomplete="off"
							type="text"
							class="tribe-datepicker tribe-field-end_date ticket_field"
							name="ticket_end_date"
							id="ticket_end_date"
							value="<?php echo esc_attr( $ticket ? $end_date : null ); ?>"
						/>
						<span class="helper-text hide-if-js"><?php esc_html_e( 'YYYY-MM-DD', 'event-tickets' ); ?></span>
						<span class="datetime_seperator"> <?php esc_html_e( 'at', 'event-tickets' ); ?> </span>
						<input
							autocomplete="off"
							type="text"
							class="tribe-timepicker tribe-field-end_time ticket_field"
							name="ticket_end_time"
							id="ticket_end_time"
							<?php echo Tribe__View_Helpers::is_24hr_format() ? 'data-format="H:i"' : ''; ?>
							data-step="<?php echo esc_attr( $timepicker_step ); ?>"
							data-round="<?php echo esc_attr( $timepicker_round ); ?>"
							value="<?php echo esc_attr( $ticket_end_time ); ?>"
							aria-label="<?php echo esc_attr( $msg_ticket_end_date_aria ); ?>"
						/>
						<span class="helper-text hide-if-js"><?php esc_html_e( 'HH:MM', 'event-tickets' ); ?></span>
						<span class="dashicons dashicons-editor-help"
						<?php
						if (
							class_exists( 'Tribe__Events__Main' )
							&& Tribe__Events__Main::POSTTYPE === get_post_type( $post_id )
						) :
							?>
							title="<?php echo esc_attr( $msg_ticket_end_date_title_if_tec ); ?>"
						<?php else : ?>
							title="<?php echo esc_attr( $msg_ticket_end_date_title_if_not_tec ); ?>"
						<?php endif; ?>
					></span>
					</div>
				</div>
				<fieldset id="tribe_ticket_provider_wrapper" class="input_block" aria-hidden="true">
					<legend class="ticket_form_label"><?php esc_html_e( 'Sell using:', 'event-tickets' ); ?></legend>
					<?php foreach ( $modules as $class => $module ) : ?>
						<input
							type="radio"
							name="ticket_provider"
							id="<?php echo esc_attr( $class . '_radio' ); ?>"
							value="<?php echo esc_attr( $class ); ?>"
							class="ticket_field ticket_provider"
							tabindex="-1"
							<?php checked( true, $provider_class === $class ); ?>
						>
						<span>
							<?php
							/**
							 * Allows for the editing of the module name before output
							 *
							 * @since 4.6
							 *
							 * @param string $module the module name
							 */
							echo esc_html( apply_filters( 'tribe_events_tickets_module_name', $module ) );
							?>
						</span>
					<?php endforeach; ?>
				</fieldset>
				<?php
				/**
				 * Allows for the insertion of additional content into the ticket edit form - main section
				 *
				 * @since 4.6
				 *
				 * @param int Post ID
				 * @param int Ticket ID
				 */
				do_action( 'tribe_events_tickets_metabox_edit_main', $post_id, $ticket_id ); ?>
			</section>
			<div class="accordion">
				<?php
				/** @var Tribe__Tickets__Admin__Views $admin_views */
				$admin_views = tribe( 'tickets.admin.views' );

				$admin_context = [
					'post_id'   => $post_id,
					'ticket_id' => $ticket_id,
					'provider'  => $provider,
				];

				$admin_views->template( 'editor/fieldset/advanced', $admin_context );
				$admin_views->template( 'editor/fieldset/history', [ 'post_id' => $post_id, 'ticket_id' => $ticket_id ] );

				/**
				 * Allows for the insertion of additional content sections into the ticket edit form accordion
				 *
				 * @since 4.6
				 *
				 * @param int Post ID
				 * @param int Ticket ID
				 */
				do_action( 'tribe_events_tickets_metabox_edit_accordion_content', $post_id, $ticket_id );
				?>
			</div>

			<?php
			/**
			 * Allows for the insertion of additional elements into the main ticket edit panel below the accordion section
			 *
			 * @since 4.6
			 *
			 * @param int Post ID
			 * @param int Ticket ID
			 */
			do_action( 'tribe_events_tickets_post_accordion', $post_id, $ticket_id );
			?>
			<div class="ticket_bottom">
				<input
					type="hidden"
					name="ticket_id"
					id="ticket_id"
					class="ticket_field"
					value="<?php echo esc_attr( $ticket_id ); ?>"
				/>
				<input
					type="button"
					id="ticket_form_save"
					class="button-primary tribe-dependent tribe-validation-submit"
					name="ticket_form_save"
					value="<?php echo esc_attr( $msg_ticket_form_save ); ?>"
					data-depends="#Tribe__Tickets__RSVP_radio"
					data-condition-is-not-checked
				/>
				<input
					type="button"
					id="rsvp_form_save"
					class="button-primary tribe-dependent tribe-validation-submit"
					name="ticket_form_save"
					value="<?php echo esc_attr( $msg_rsvp_form_save ); ?>"
					data-depends="#Tribe__Tickets__RSVP_radio"
					data-condition-is-checked
				/>
				<input
					type="button"
					id="ticket_form_cancel"
					class="button-secondary"
					name="ticket_form_cancel"
					value="<?php esc_attr_e( 'Cancel', 'event-tickets' ); ?>"
				/>

				<?php
				/**
				 * Allows for the insertion of additional content into the ticket edit form bottom (buttons) section
				 *
				 * @since 4.6
				 *
				 * @param int Post ID
				 * @param int Ticket ID
				 */
				do_action( 'tribe_events_tickets_bottom', $post_id, $ticket_id );
				?>

				<div id="ticket_bottom_right">
					<?php
					/**
					 * Allows for the insertion of additional content into the ticket edit form bottom (links on right) section
					 *
					 * @since 4.6
					 *
					 * @param int Post ID
					 * @param int Ticket ID
					 */
					do_action( 'tribe_events_tickets_bottom_right', $post_id, $ticket_id );
					?>
				</div>
			</div>
		</div>
	</div>
</div>
