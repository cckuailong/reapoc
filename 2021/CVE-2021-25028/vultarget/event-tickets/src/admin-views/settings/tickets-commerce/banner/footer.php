<?php
/**
 * Footer for Tickets Commerce styled, generic banner.
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

// If not showing button or link, then bail.
$hide_button = empty( $button_text ) || empty( $button_url );
$hide_link   = empty( $link_text ) || empty( $link_url );
if ( $hide_button && $hide_link ) {
    return;
}

?>
<div class="event-tickets__admin-tc-banner-footer">
    <?php $this->template( 'settings/tickets-commerce/banner/footer/button' ); ?>
    <?php $this->template( 'settings/tickets-commerce/banner/footer/link' ); ?>
</div>
