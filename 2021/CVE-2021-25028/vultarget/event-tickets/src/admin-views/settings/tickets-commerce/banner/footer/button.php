<?php
/**
 * Footer button for Tickets Commerce styled, generic banner.
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

if ( empty( $button_url ) || empty( $button_text ) ) {
    return;
}

?>
<a class="event-tickets__admin-tc-banner-button" href="<?php echo esc_url( $button_url ); ?>">
    <?php echo esc_html( $button_text ); ?>
</a>
