<?php
    /**
     * CFF Header Notices
     * 
     * @since 4.0
     */
    do_action('cff_header_notices'); 
?>
<div id="cff-about" class="cff-about">
    <?php
        CustomFacebookFeed\CFF_View::render( 'sections.header' );
        CustomFacebookFeed\CFF_View::render( 'about.content' );
        CustomFacebookFeed\CFF_View::render( 'sections.sticky_widget' );
    ?>
</div>