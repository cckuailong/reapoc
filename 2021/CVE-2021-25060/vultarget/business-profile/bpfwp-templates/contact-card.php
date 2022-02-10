<?php
/**
 * Template for a contact card
 *
 * Copy this template into your theme to modify the output of the contact card.
 * The template should sit in your theme here:
 *
 * /wp-content/themes/your-theme/business-profile-templates/contact-card.php
 *
 * You can also add custom templates for each location by creating a template
 * file with the location's ID, eg:
 *
 * /wp-content/themes/your-theme/business-profile-templates/contact-card-123.php
 *
 * By default, the template uses a filtered callback system defined in
 * bpwfwp_print_contact_card(), which you'll find in the plugins'
 * includes/template-functions.php. This structure allows it a certain kind of
 * "hands-off" flexibility for third-party integrations. Plugins can hook into
 * `bpwfwp_component_callbacks` to add or remove content without overriding or
 * effecting existing content.
 *
 * Individual settings can be accessed using the bpfwp_setting(). Example:
 *
 * <?php bpfwp_setting( 'address' ); ?>
 *
 * You can also pass a location ID to bpfwp_setting(). Example:
 *
 * <?php bpfwp_setting( 'address', 123 ); ?>
 *
 * However, ensuring you have complete Schema.org markup can be especially
 * difficult for some things, like Opening Hours. There are a number of template
 * functions at your disposal to help you print details with schema markup.
 * You'll find these at /includes/template-functions.php. Example:
 *
 * <?php bpwfwp_print_address(); ?>
 *
 * These also support a location. Example:
 *
 * <?php bpwfwp_print_address( 123 ); ?>
 *
 * This template can be loaded automatically in a post loop, from a shortcode
 * or via a widget (or you can use bpwfwp_print_contact_card() to print it
 * anywhere you want). For that reason, the location requested may not be the
 * same as the global post (eg - get_the_ID()). To ensure compatibility with the
 * plugin's [contact-card] shortcode and Contact Card widget, you should use
 * bpfwp_setting() to retrieve any possible location post ID. Example:
 *
 * <?php
 *   $location = bpfwp_get_display( 'location' );
 *   bpfwp_setting( 'address', $location );
 * ?>
 *
 * The $bpfwp_controller->display_settings array also contains information on
 * any content that has been hidden with shortcode attributes or widget options.
 * This allows a value to be printed for proper schema markup without being
 * displayed. You should check the bool values with the bpfwp_get_display()
 * helper function before printing. Example:
 *
 * <?php
 *   $location = bpfwp_get_display( 'location' );
 *   if ( bpfwp_get_display( 'show_address' ) ) {
 *     ?>
 *     <div itemprop="address">
 *       <?php bpfwp_setting( 'address', $location ); ?>
 *     </div>
 *     <?php
 *   }
 * ?>
 *
 * If you use the template functions to have the schema markup printed for you,
 * they will take account of these display settings and use hidden meta where
 * appropriate.
 *
 * If you want to explicitly set the visibility of a type of information before
 * calling it's template function, you can do that. For instance, to show only
 * the brief opening hours, you can do the following:
 *
 * <?php
 *   $location = bpfwp_get_display( 'location' );
 *   bpfwp_set_display( 'show_opening_hours_brief', true );
 *   bpwfwp_print_opening_hours( $location );
 * ?>
 *
 * Google provides a Structured Data Testing Tool which is useful for validating
 * your schema markup once you've changed it.
 *
 * https://search.google.com/structured-data/testing-tool/u/0/
 *
 * Happy theming!
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     1.1.0
 */
?>
<?php $json_ld_data = array( 'type' => bpfwp_setting( 'schema-type', bpfwp_get_display( 'location' ) ) ); ?>
<address class="bp-contact-card">
	<?php if ( bpfwp_setting( 'image', bpfwp_get_display( 'location' ) ) ) : ?>
		<?php $json_ld_data['image'] = wp_get_attachment_url( bpfwp_setting( 'image', bpfwp_get_display( 'location' ) ) ); ?>
	<?php endif; ?>
	<?php 
	foreach ( $data as $data => $callback ) { 
		
		$return_array = call_user_func( $callback, bpfwp_get_display( 'location' ) );
		if ( is_array( $return_array ) ) { $json_ld_data = array_merge( $json_ld_data, $return_array ); }
	} 
	?>
	<script type="application/ld+json">
		{
			"@context": "https://schema.org/",
			<?php echo trim( bpfwp_json_ld_contact_print( false, $json_ld_data ), ',' ); ?>
		}
	</script>
</address>
