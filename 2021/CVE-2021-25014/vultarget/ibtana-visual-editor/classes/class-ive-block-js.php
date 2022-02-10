<?php
/**
 * IVE Block Helper.
 *
 * @package IVE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'IVE_Block_JS' ) ) {

	/**
	 * Class IVE_Block_JS.
	 */
	class IVE_Block_JS {


		public static function get_social_share_js( $attr, $id ) {


			$base_selector = '.ive-svg-icons';
			$selector      = $base_selector . $id;
			global $post;
			// Get the featured image.
			if ( has_post_thumbnail() ) {
				$thumbnail_id = get_post_thumbnail_id( $post->ID );
				$thumbnail    = $thumbnail_id ? current( wp_get_attachment_image_src( $thumbnail_id, 'large', true ) ) : '';
			} else {
				$thumbnail = null;
			}

			ob_start();
			?>
			var ssLinks = document.querySelectorAll( '<?php echo esc_attr( $selector ); ?>' );
			for ( var j = 0; j < ssLinks.length; j++ ) {
				var ssLink = ssLinks[j].querySelectorAll( "a[data-href]" );
				for ( var i = 0; i < ssLink.length; i++ ) {
					ssLink[i].addEventListener( "click", function() {
						var social_url = this.dataset.href;
						var target = this.getAttribute('target');
						if( social_url == "mailto:?body=" ) {
							target = "_self";
						}
						var  request_url ="";
						if( social_url.indexOf("/pin/create/link/?url=") !== -1) {
							request_url = social_url + window.location.href + "&media=" + '<?php echo esc_url( $thumbnail ); ?>';
						}else{
							request_url = social_url + window.location.href;
						}
						window.open( request_url, target );
					});
				}
			}
			<?php
			return ob_get_clean();
		}


		public static function get_button_gfonts( $attr ) {

			$load_google_font = isset( $attr['googleFont'] ) ? $attr['googleFont'] : '';
			$font_family      = isset( $attr['typography'] ) ? $attr['typography'] : '';
			$font_weight      = isset( $attr['fontWeight'] ) ? $attr['fontWeight'] : '';
			$font_subset      = isset( $attr['fontSubset'] ) ? $attr['fontSubset'] : '';

			IVE_Helper::blocks_google_font( $load_google_font, $font_family, $font_weight, $font_subset );
		}

		public static function get_progress_bar_gfonts( $attr ) {

			$load_google_font = isset( $attr['googleFont'] ) ? $attr['googleFont'] : '';
			$font_family      = isset( $attr['typography'] ) ? $attr['typography'] : '';
			$font_weight      = isset( $attr['fontWeight'] ) ? $attr['fontWeight'] : '';
			$font_subset      = isset( $attr['fontSubset'] ) ? $attr['fontSubset'] : '';

			$load_google_font_cont = isset( $attr['googleFont_cont'] ) ? $attr['googleFont_cont'] : '';
			$font_family_cont      = isset( $attr['typography_cont'] ) ? $attr['typography_cont'] : '';
			$font_weight_cont      = isset( $attr['fontWeight_cont'] ) ? $attr['fontWeight_cont'] : '';
			$font_subset_cont      = isset( $attr['fontSubset_cont'] ) ? $attr['fontSubset_cont'] : '';

			IVE_Helper::blocks_google_font( $load_google_font, $font_family, $font_weight, $font_subset );

			IVE_Helper::blocks_google_font( $load_google_font_cont, $font_family_cont, $font_weight_cont, $font_subset_cont );
		}

		public static function get_tabs_gfonts( $attr ) {

      $load_google_font = isset( $attr['googleFont'] ) ? $attr['googleFont'] : '';
			$font_family      = isset( $attr['typography'] ) ? $attr['typography'] : '';
			$font_weight      = isset( $attr['fontWeight'] ) ? $attr['fontWeight'] : '';
			$font_subset      = isset( $attr['fontSubset'] ) ? $attr['fontSubset'] : '';

      $load_google_font_subtitle = isset( $attr['subtitleFont'][0]['google'] ) ? $attr['subtitleFont'][0]['google'] : '';
			$font_family_subtitle      = isset( $attr['subtitleFont'][0]['family'] ) ? $attr['subtitleFont'][0]['family'] : '';
			$font_weight_subtitle      = isset( $attr['subtitleFont'][0]['weight'] ) ? $attr['subtitleFont'][0]['weight'] : '';
			$font_subset_subtitle      = isset( $attr['subtitleFont'][0]['subset'] ) ? $attr['subtitleFont'][0]['subset'] : '';

			IVE_Helper::blocks_google_font( $load_google_font, $font_family, $font_weight, $font_subset );

			IVE_Helper::blocks_google_font( $load_google_font_subtitle, $font_family_subtitle, $font_weight_subtitle, $font_subset_subtitle );
    }

		public static function get_advanced_text_gfonts( $attr ) {
			$load_google_font = isset( $attr['googleFont'] ) ? $attr['googleFont'] : '';
			$font_family      = isset( $attr['typography'] ) ? $attr['typography'] : '';
			$font_weight      = isset( $attr['fontWeight'] ) ? $attr['fontWeight'] : '';
			$font_subset      = isset( $attr['fontSubset'] ) ? $attr['fontSubset'] : '';

      IVE_Helper::blocks_google_font( $load_google_font, $font_family, $font_weight, $font_subset );
		}

		public static function get_accordion_title_gfonts( $attr ) {
			$load_google_font = isset( $attr['titleStyles'][ 0 ]['google'] ) ? $attr['titleStyles'][ 0 ]['google'] : '';
			$font_family      = isset( $attr['titleStyles'][ 0 ]['family'] ) ? $attr['titleStyles'][ 0 ]['family'] : '';
			$font_weight      = isset( $attr['titleStyles'][ 0 ]['fontWeight'] ) ? $attr['titleStyles'][ 0 ]['weight'] : '';
			$font_subset      = isset( $attr['titleStyles'][ 0 ]['fontSubset'] ) ? $attr['titleStyles'][ 0 ]['fontSubset'] : '';

      IVE_Helper::blocks_google_font( $load_google_font, $font_family, $font_weight, $font_subset );
		}


	}
}
