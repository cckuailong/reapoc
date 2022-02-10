<?php
namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Taxonomies{

	public function __construct() {

		add_action( 'course-category_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'course-category_edit_form_fields', array( $this, 'edit_category_fields' ) );

		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

		add_filter( 'manage_edit-course-category_columns', array( $this, 'course_category_columns' ) );
		add_filter( 'manage_course-category_custom_column', array( $this, 'course_category_column' ), 10, 3 );

	}

	public function add_category_fields(){
		?>
		<div class="form-field term-thumbnail-wrap">
			<label><?php esc_html_e( 'Thumbnail', 'tutor' ); ?></label>
			<div id="course-category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( tutor_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
			<div style="line-height: 60px;">
				<input type="hidden" id="course-category_thumbnail_id" name="course_category_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'tutor' ); ?></button>
				<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'tutor' ); ?></button>
			</div>
			<script type="text/javascript">

                // Only show the "remove image" button when needed
                if ( ! jQuery( '#course-category_thumbnail_id' ).val() ) {
                    jQuery( '.remove_image_button' ).hide();
                }

                // Uploading files
                var file_frame;

                jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( file_frame ) {
                        file_frame.open();
                        return;
                    }

                    // Create the media frame.
                    file_frame = wp.media.frames.downloadable_file = wp.media({
                        title: '<?php esc_html_e( 'Choose an image', 'tutor' ); ?>',
                        button: {
                            text: '<?php esc_html_e( 'Use image', 'tutor' ); ?>'
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    file_frame.on( 'select', function() {
                        var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
                        var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

                        jQuery( '#course-category_thumbnail_id' ).val( attachment.id );
                        jQuery( '#course-category_thumbnail' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
                        jQuery( '.remove_image_button' ).show();
                    });

                    // Finally, open the modal.
                    file_frame.open();
                });

                jQuery( document ).on( 'click', '.remove_image_button', function() {
                    jQuery( '#course-category_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( tutor_placeholder_img_src() ); ?>' );
                    jQuery( '#course-category_thumbnail_id' ).val( '' );
                    jQuery( '.remove_image_button' ).hide();
                    return false;
                });

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}


	public function edit_category_fields($term){

		$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		} else {
			$image = tutor_placeholder_img_src();
		}
		?>

		<tr class="form-field term-thumbnail-wrap">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Thumbnail', 'woocommerce' ); ?></label></th>
			<td>

				<div class="form-field term-thumbnail-wrap">
					<div id="course-category_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
					<div style="line-height: 60px;">
						<input type="hidden" id="course-category_thumbnail_id" name="course_category_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>" />
						<button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'tutor' ); ?></button>
						<button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'tutor' ); ?></button>
					</div>
					<script type="text/javascript">

                        // Only show the "remove image" button when needed
                        if ( ! jQuery( '#course-category_thumbnail_id' ).val() ) {
                            jQuery( '.remove_image_button' ).hide();
                        }

                        // Uploading files
                        var file_frame;

                        jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

                            event.preventDefault();

                            // If the media frame already exists, reopen it.
                            if ( file_frame ) {
                                file_frame.open();
                                return;
                            }

                            // Create the media frame.
                            file_frame = wp.media.frames.downloadable_file = wp.media({
                                title: '<?php esc_html_e( 'Choose an image', 'tutor' ); ?>',
                                button: {
                                    text: '<?php esc_html_e( 'Use image', 'tutor' ); ?>'
                                },
                                multiple: false
                            });

                            // When an image is selected, run a callback.
                            file_frame.on( 'select', function() {
                                var attachment           = file_frame.state().get( 'selection' ).first().toJSON();
                                var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

                                jQuery( '#course-category_thumbnail_id' ).val( attachment.id );
                                jQuery( '#course-category_thumbnail' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
                                jQuery( '.remove_image_button' ).show();
                            });

                            // Finally, open the modal.
                            file_frame.open();
                        });

                        jQuery( document ).on( 'click', '.remove_image_button', function() {
                            jQuery( '#course-category_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( tutor_placeholder_img_src() ); ?>' );
                            jQuery( '#course-category_thumbnail_id' ).val( '' );
                            jQuery( '.remove_image_button' ).hide();
                            return false;
                        });

					</script>
					<div class="clear"></div>
				</div>

			</td>
		</tr>
		<?php
	}

	/**
	 * @param $term_id
	 * @param string $tt_id
	 * @param string $taxonomy
	 *
	 * Save Course Category Thumbnail
	 */

	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['course_category_thumbnail_id'] ) && 'course-category' === $taxonomy ) {
			update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['course_category_thumbnail_id'] ) );
		}
	}


	public function course_category_columns($columns){
		$new_columns = array();

		if ( isset( $columns['cb'] ) ) {
			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}

		$new_columns['thumb'] = __( 'Image', 'tutor' );

		$columns           = array_merge( $new_columns, $columns );
		$columns['handle'] = '';

		return $columns;
	}

	public function course_category_column( $columns, $column, $id ) {
		if ( 'thumb' === $column ) {
			$thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = tutor_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds. Ref: https://core.trac.wordpress.org/ticket/23605 .
			$image    = str_replace( ' ', '%20', $image );
			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'tutor' ) . '" class="wp-post-image" height="48" width="48" />';
		}
		if ( 'handle' === $column ) {
			$columns .= '<input type="hidden" name="term_id" value="' . esc_attr( $id ) . '" />';
		}
		return $columns;
	}


}