<?php
/**
 * Custom Feeds for Facebook Feed Locator Summary Template
 * Creates the HTML for the feed locator summary
 *
 * @version 2.19 Custom Feeds for Facebook Pro by Smash Balloon
 *
 */

use CustomFacebookFeed\CFF_FB_Settings;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$database_settings = get_option('cff_style_settings');
$connected_accounts = (array)json_decode(stripcslashes(get_option( 'cff_connected_accounts' )));

?>
<div class="cff-feed-locator-summary-wrap">
	<h3><?php esc_html_e( 'Feed Finder Summary', 'custom-facebook-feed' ); ?></h3>
    <p><?php esc_html_e( 'The table below shows a record of all feeds found on your site. A feed may not show up here immediately after being created.', 'custom-facebook-feed' ); ?></p>
<?php
if ( ! empty( $locator_summary ) ) : ?>

    <?php foreach ( $locator_summary as $locator_section ) :
        if ( ! empty( $locator_section['results'] ) ) : ?>
	<div class="cff-single-location">
		<h4><?php echo esc_html( $locator_section['label'] ); ?></h4>
		<table class="widefat striped">
			<thead>
			<tr>
                <th><?php esc_html_e( 'Type', 'custom-facebook-feed' ); ?></th>
                <th><?php esc_html_e( 'Sources', 'custom-facebook-feed' ); ?></th>
                <th><?php esc_html_e( 'Shortcode', 'custom-facebook-feed' ); ?></th>
				<th><?php esc_html_e( 'Location', 'custom-facebook-feed' ); ?></th>
			</tr>
			</thead>
			<tbody>

			<?php
				$atts_for_page = array();
				foreach ( $locator_section['results'] as $result ) :
					$should_add = true;
					if ( ! empty( $atts_for_page[ $result['post_id'] ] ) ) {
						foreach ( $atts_for_page[ $result['post_id'] ] as $existing_atts ) {
							if ( $existing_atts === $result['shortcode_atts'] ) {
								$should_add = false;
							}
						}
					}
				if ( $should_add ) {
					$atts_for_page[ $result['post_id'] ][] = $result['shortcode_atts'];
					$shortcode_atts = $result['shortcode_atts'] != '[""]' ? json_decode( $result['shortcode_atts'], true ) : [];
					$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : array();
	                $display_terms = CFF_FB_Settings::feed_type_and_terms_display( $connected_accounts, $result, $database_settings );
	                $comma_separated =  implode(',',$display_terms['name']);

					$display = $comma_separated;
	                if ( strlen( $comma_separated ) > 31 ) {
		                $display = '<span class="cff-condensed-wrap">' . esc_html( substr( $comma_separated, 0, 30 ) ) . '<a class="cff-locator-more" href="JavaScript:void(0);">...</a></span>';
	                    $comma_separated = '<span class="cff-full-wrap">' . esc_html( $comma_separated ) . '</span>';
	                } else {
		                $comma_separated = '';
	                }
					$type = implode(',',$display_terms['type']);

					$full_shortcode_string = '[custom-facebook-feed';
					foreach ( $shortcode_atts as $key => $value ) {
						$full_shortcode_string .= ' ' . esc_html( $key ) . '="' . esc_html( $value ) . '"';
					}
					$full_shortcode_string .= ']';
					?>
				<tr>
	                <td><?php echo esc_html( $type ); ?></td>
	                <td><?php echo $display . $comma_separated; ?></td>
	                <td>
	                    <span class="cff-condensed-wrap"><a class="cff-locator-more" href="JavaScript:void(0);"><?php esc_html_e( 'Show', 'custom-facebook-feed' ); ?></a></span>
	                    <span class="cff-full-wrap"><?php echo $full_shortcode_string; ?></span>
	                </td>
					<td><a href="<?php echo esc_url( get_the_permalink( $result['post_id'] ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( get_the_title( $result['post_id'] ) ); ?></a></td>
				</tr>
				<?php
				}
			endforeach; ?>


			</tbody>
		</table>
	</div>

        <?php endif;
    endforeach;
else: ?>
    <p><?php esc_html_e( 'Locations of your feeds are currently being detected. You\'ll see more information posted here soon!', 'custom-facebook-feed' ); ?></p>
<?php endif; ?>
</div>