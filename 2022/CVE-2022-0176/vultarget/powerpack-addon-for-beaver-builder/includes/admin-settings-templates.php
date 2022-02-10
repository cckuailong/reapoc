<?php
/**
 * PowerPack admin settings page-templates tab.
 *
 * @since 1.0.0
 * @package bb-powerpack
 */

?>

<?php
    $navigate = ( isset( $_REQUEST['navigate'] ) && ! empty( $_REQUEST['navigate'] ) ) ? $_REQUEST['navigate'] : 'page-templates';
    $template_type = ( $navigate == 'page-templates' ) ? 'page' : 'row';
    $template_categories = pp_templates_categories( $template_type );
    $activated_templates = array();
?>

<div class="pp-page-templates">

    <?php if ( !is_network_admin() && is_multisite() ) : ?>

    <div class="notice notice-info">
        <p><?php esc_html_e( 'You can manage the templates for your site from this page. By activating / deactivating any template will override the network settings.', 'bb-powerpack-lite' ); ?></p>
    </div>

<?php endif; ?>

    <div class="wp-filter pp-template-filter hide-if-no-js">
        <div class="filter-count">
            <span class="count theme-count">
                <?php
                    if ( $template_type == 'page' ) {
                        echo count( $template_categories );
                    }
                    if ( $template_type == 'row' ) {
                        $count = 0;
                        foreach ( $template_categories as $template_cat => $template_info ) {
                            if ( isset( $template_info['count'] ) ) {
                                $count = $count + $template_info['count'];
                            }
                        }
                        echo $count;
                    }
                ?>
            </span>
        </div>
        <ul class="filter-links">
            <li><a href="<?php echo self::get_form_action( '&tab=templates&navigate=page-templates' ); ?>" class="<?php echo ( 'page-templates' == $navigate ) ? 'current' : ''; ?>" data-type="page-templates"><?php esc_html_e( 'Page Templates', 'bb-powerpack-lite' ); ?></a></li>
            <li><a href="<?php echo self::get_form_action( '&tab=templates&navigate=row-templates' ); ?>" class="<?php echo ( 'row-templates' == $navigate ) ? 'current' : ''; ?>" data-type="row-templates"><?php esc_html_e( 'Row Templates', 'bb-powerpack-lite' ); ?></a></li>
        </ul>
        <div class="search-form">
            <label class="screen-reader-text" for="wp-filter-search-input"><?php esc_html_e( 'Search Templates', 'bb-powerpack-lite' ); ?></label>
            <input placeholder="Search templates..." type="search" aria-describedby="live-search-desc" id="wp-filter-search-input" class="wp-filter-search">
        </div>
        <ul class="filter-sublinks filter-page-templates" <?php echo ( 'page-templates' !== $navigate ) ? 'style="display:none;"' : ''; ?>>
            <li class="filter-label"><strong><?php esc_html_e( 'Filter:', 'bb-powerpack-lite' ); ?></strong></li>
            <?php foreach ( pp_template_filters() as $filter_key => $filter_name ) : ?>
                <li><a href="<?php echo self::get_form_action( '&tab=templates&navigate=page-templates&filter='.$filter_key ); ?>" class="" data-filter="<?php echo $filter_key; ?>"><?php echo $filter_name; ?></a><span>|</span></li>
            <?php endforeach; ?>
        </ul>
        <div class="pp-refresh-panel">
            <a href="<?php echo self::get_form_action( '&tab=templates&navigate='.$navigate.'&refresh=1' ); ?>" class="button button-primary"><?php esc_html_e( 'Reload', 'bb-powerpack-lite' ); ?></a>
        </div>
    </div>

    <div class="pp-page-templates-grid wp-clearfix">

        <?php if ( count( $template_categories ) ) : ?>

            <?php foreach ( $template_categories as $cat => $info ) : $preview = pp_templates_preview_src( $template_type, $cat ); ?>

                <div class="pp-template pp-<?php echo $template_type; ?>-template<?php echo !empty( $preview ) ? ' pp-preview-enabled' : ''; ?>" data-filter="<?php echo $info['type']; ?>">
                    <div class="pp-template-screenshot"><img src="<?php echo pp_get_template_screenshot_url( $template_type, $cat ); ?>" /></div>
                    <?php if ( !empty( $preview ) ) { ?>
                    <span class="pp-template-preview" data-preview-src="<?php echo $preview; ?>" data-template-cat="<?php echo $cat; ?>"><?php esc_html_e( 'Preview', 'bb-powerpack-lite' ); ?></span>
                    <?php } ?>
                    <h2 class="pp-template-category"><span></span> <?php esc_html_e( $info['title'], 'bb-powerpack-lite' ); ?> <?php echo isset( $info['count'] ) ? '- ' . $info['count'] : ''; ?></h2>
                    <div class="pp-template-actions">
                        <span class="ajax-spinner"><img src="<?php echo admin_url( 'images/loading.gif' ); ?>" class="loader-image" /></span>
                        <a class="button button-primary" href="<?php echo BB_POWERPACK_PRO; ?>" target="_blank"><?php esc_html_e('Upgrade Now', 'bb-powerpack-lite'); ?></a>
                    </div>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>
<div class="pp-template-overlay">
    <div class="pp-template-backdrop"></div>
    <div class="pp-template-wrap wp-clearfix">
        <div class="pp-template-header">
            <button class="left dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e('Show previous template', 'bb-powerpack-lite'); ?></span></button>
			<button class="right dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e('Show next template', 'bb-powerpack-lite'); ?></span></button>
			<button class="close dashicons dashicons-no"><span class="screen-reader-text"><?php esc_html_e('Close details dialog', 'bb-powerpack-lite'); ?></span></button>
        </div>
        <div class="pp-template-info wp-clearfix">
            <span class="ajax-spinner"><img src="<?php echo admin_url( 'images/spinner.gif' ); ?>" class="loader-image" /></span>
            <iframe class="pp-template-preview-frame" src="" frameborder="0" height="100%" width="100%" seamless></iframe>
        </div>
        <div class="pp-template-actions">
            <span class="ajax-spinner"><img src="<?php echo admin_url( 'images/loading.gif' ); ?>" class="loader-image" /></span>
            <a class="button button-primary" href="<?php echo BB_POWERPACK_PRO; ?>" target="_blank"><?php esc_html_e('Upgrade Now', 'bb-powerpack-lite'); ?></a>
        </div>
    </div>
</div>

<?php wp_nonce_field('pp-templates', 'pp-templates-nonce'); ?>

<script>
    jQuery(document).ready(function($) {

        if ( history.pushState ) {
            if ( document.location.search.search( '&refresh' ) > -1 ) {
                var url = document.location.href.split('&refresh')[0];
                window.history.pushState( { path:url }, '', url );
            }
        }

        $('.pp-template-filter .filter-sublinks a').on('click', function(e) {

            e.preventDefault();

            var filter = $(this).data('filter');

            $(this).parents('.filter-sublinks').find('li').removeClass('current');
            $(this).parent().addClass('current');

            if ( 'all' === filter ) {
                $('.pp-template').fadeIn();
            } else {
                $('.pp-template[data-filter="'+filter+'"]').fadeIn();
                $('.pp-template:not([data-filter="'+filter+'"])').fadeOut();
            }

        });

        /* Search */
        $('#wp-filter-search-input').on('keyup', function() {
            if( $(this).val().length >= 3 ){
                var search_term = $(this).val().toLowerCase().trim();
                $('.pp-template').hide();
                $('.pp-template').each(function() {
                    if( $(this).find('.pp-template-category').text().toLowerCase().trim().search(search_term) !== -1 ) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                $('.pp-template').show();
            }
        });

    });

    jQuery(document).on('click', '.pp-template.pp-preview-enabled', function(e) {

        e.preventDefault();

        var preview = jQuery(this).find('.pp-template-preview');
        var previewSrc = preview.data('preview-src');
        var templateCat = preview.data('template-cat');
        var activateLink = jQuery(this).find('.pp-activate-template').attr('href');
        var deactivateLink = jQuery(this).find('.pp-deactivate-template').attr('href');
        var scrollPos = jQuery(window).scrollTop();

        jQuery('.pp-template-overlay').show().find('.pp-template-preview-frame').attr('src', previewSrc);
        jQuery('.pp-template-overlay').find('.pp-activate-template').attr('data-template-cat', templateCat).attr('href', activateLink);
        jQuery('.pp-template-overlay').find('.pp-deactivate-template').attr('data-template-cat', templateCat).attr('href', deactivateLink);

        if(jQuery(this).hasClass('active')) {
            jQuery('.pp-template-overlay').addClass('active');
        }

        jQuery('.pp-template-overlay').find('button.close').on('click', function() {
            jQuery(window).scrollTop(scrollPos);
        });
    });

    jQuery('.pp-template-overlay .pp-template-header .close').on('click', function(e) {

        e.preventDefault();

        var overlay = jQuery(this).parents('.pp-template-overlay');
        overlay.fadeOut(100).find('.pp-template-preview-frame').attr('src', '');

        setTimeout(function() {
            overlay.removeClass('active');
        }, 100);

    });
</script>
