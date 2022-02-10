<?php
    /**
     * CFF Header Notices
     * 
     * @since 4.0
     */
    do_action('cff_header_notices'); 
?>
<div id="cff-settings" class="cff-settings" :data-app-loaded="appLoaded ? 'true' : 'false'">
    <?php
        CustomFacebookFeed\CFF_View::render( 'sections.header' );
        CustomFacebookFeed\CFF_View::render( 'settings.content' );
        CustomFacebookFeed\CFF_View::render( 'sections.sticky_widget' );
    ?>
    <div class="sb-control-elem-tltp-content" v-show="tooltip.hover" @mouseover.prevent.default="hoverTooltip(true)" @mouseleave.prevent.default="hoverTooltip(false)">
		<div class="sb-control-elem-tltp-txt" v-html="tooltip.text"></div>
	</div>
</div>