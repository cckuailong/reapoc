<?php
    /**
     * CFF Header Notices
     * 
     * @since 4.0
     */
    do_action('cff_header_notices'); 
?>
<div id="cff-oembeds" class="cff-oembeds">
    <?php
        CustomFacebookFeed\CFF_View::render( 'sections.header' );
        CustomFacebookFeed\CFF_View::render( 'oembeds.content' );
        CustomFacebookFeed\CFF_View::render( 'sections.sticky_widget' );
    ?>
</div>