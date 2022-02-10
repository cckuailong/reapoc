<?php
/**
 * Footer link for Tickets Commerce styled, generic banner.
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

if ( empty( $link_text ) || empty( $link_url ) ) {
    return;
}

?>
<a
    class="event-tickets__admin-tc-banner-link"
    href="<?php echo esc_url( $link_url ); ?>"
    rel="noopener noreferrer"
    target="_blank"
>
    <?php echo esc_html( $link_text ); ?>
</a>
