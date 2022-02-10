<?php
/**
 * Tickets Commerce styled, generic banner.
 *
 * @since 5.2.0
 *
 * @var Tribe__Tickets__Admin__Views $this           Template object.
 * @var string                       $banner_title   Banner title.
 * @var string                       $banner_content Banner content text or HTML.
 * @var string                       $button_text    Button text.
 * @var string                       $button_url     Button URL.
 * @var string                       $link_text      Link text.
 * @var string                       $link_url       Link URL.
 * @var bool                         $show_new       Show "New!" badge.
 */

?>
<div class="event-tickets__admin-banner event-tickets__admin-tc-banner">
    <div class="event-tickets__admin-tc-banner-header">
        <h4 class="event-tickets__admin-tc-banner-header-title"><?php echo esc_html( $banner_title ); ?></h4>
        <?php $this->template( 'settings/tickets-commerce/banner/new-badge' ); ?>
    </div>
	<p class="event-tickets__admin-tc-banner-content">
        <?php echo wp_kses( $banner_content, 'post' ); ?>
    </p>
    <?php $this->template( 'settings/tickets-commerce/banner/footer' ); ?>
</div>
