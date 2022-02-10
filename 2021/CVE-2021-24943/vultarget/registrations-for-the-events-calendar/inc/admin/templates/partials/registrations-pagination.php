<?php
$settings = $this->settings;
?>

<div class="rtec-overview-nav">

	<?php if ( $this->out_of_posts() ) : ?>
		<p class="rtec-alert"><?php _e( 'No More Events', 'registrations-for-the-events-calendar' ); ?></p>
	<?php endif; ?>

	<?php if ( $settings['off'] > 0 ) : ?>
		<a href="<?php $this->the_pagination_href( 'back' ); ?>" class="rtec-back"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> <?php _e( 'Back', 'registrations-for-the-events-calendar' ); ?></a>
	<?php endif; ?>

	<?php if ( ! $this->out_of_posts() ) : ?>
		<a href="<?php $this->the_pagination_href( 'more' ); ?>" class="rtec-next"><?php _e( 'Next', 'registrations-for-the-events-calendar' ); ?> <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
	<?php endif; ?>

</div>
