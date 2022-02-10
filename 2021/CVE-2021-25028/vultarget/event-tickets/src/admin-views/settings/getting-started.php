<?php
/**
 * Getting started banner section.
 *
 * @since 5.1.2
 *
 * @var bool $etp_enabled         Event Tickets Plus enabled or not.
 * @var array $et_resource_links  Knowledgebase links for Event Tickets.
 * @var array $etp_resource_links Knowledgebase links for Event Tickets.
 */

$help_text = $etp_enabled
	? __( 'Thank you for using Event Tickets and Event Tickets Plus! We recommend looking through the settings below so that you can fine tune your specific ticketing needs. Here are some resources that can help.', 'event-tickets' )
	: __( 'Thank you for using Event Tickets! We recommend looking through the settings below so that you can fine tune your specific ticketing needs. Here are some resources that can help.', 'event-tickets' );

?>
<div class="event-tickets__admin-banner">
	<h3><?php echo esc_html__( 'Getting Started With Tickets', 'event-tickets' ); ?></h3>
	<p class="event-tickets__admin-banner-help-text"><?php echo esc_html__( $help_text ); ?></p>

	<div class="event-tickets__admin-banner-help-links-wrapper">
		<div class="event-tickets__admin-banner-links">
			<h3><?php esc_html_e( 'Beginner Resources', 'event-tickets' ); ?> </h3>

			<ul class="event-tickets__admin-banner-kb-list">
				<?php
				foreach ( $et_resource_links as $link ) {
					$new_label = isset( $link['new'] ) ? '<span class="event-tickets__admin-banner-links-link-label--new">' . esc_html( 'New!', 'event-tickets' ) . '</span>' : '';
					printf( '<li><a href="%s" target="_blank" rel="noopener noreferrer">%s%s</a></li>', esc_url( $link['href'] ), esc_html( $link['label'] ), $new_label );
				}
				?>
			</ul>
		</div>
		<div class="event-tickets__admin-banner-links">
			<h3>
				<?php esc_html_e( 'Advanced Plus Features', 'event-tickets' ); ?>
				<?php
					if ( ! $etp_enabled ) {
						printf( ' - <a class="upgrade-link" href="%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_url( 'https://theeventscalendar.com/products/wordpress-event-tickets/' ), esc_html__( 'Need To Upgrade?', 'event-tickets' ) );
					}
				?>
			</h3>

			<ul class="event-tickets__admin-banner-kb-list">
				<?php
				foreach ( $etp_resource_links as $link ) {
					printf( '<li><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></li>', esc_url( $link['href'] ), esc_html( $link['label'] ) );
				}
				?>
			</ul>
		</div>
	</div>
</div>
