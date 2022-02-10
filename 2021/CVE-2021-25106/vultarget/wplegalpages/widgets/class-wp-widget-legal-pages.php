<?php
/**
 * The widget-specific functionality for WPLegalPages.
 *
 * @link       https://club.wpeka.com/
 * @since      1.0.0
 *
 * @package    WP_Legal_Pages
 * @subpackage WP_Legal_Pages/widgets
 */

/**
 * The widget-specific functionality for WPLegalPages.
 *
 * @link       https://club.wpeka.com/
 * @since      1.0.0
 *
 * @package    WP_Legal_Pages
 * @subpackage WP_Legal_Pages/widgets
 */
class WP_Widget_Legal_Pages extends WP_Widget {

	/**
	 * Sets up a new WPLegalPages widget instance.
	 *
	 * @since 2.4.8
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_legal_pages',
			'description'                 => __( 'A list of your site&#8217;s Legal Pages.' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'legal_pages', __( 'WPLegalPages' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current WPLegalPages widget instance.
	 *
	 * @since 2.4.8
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current WPLegalPages widget instance.
	 */
	public function widget( $args, $instance ) {
		$default_title = __( 'WPLegalPages' );
		$title         = ! empty( $instance['title'] ) ? $instance['title'] : $default_title;

		/**
		 * Filters the widget title.
		 *
		 * @since 2.4.8
		 *
		 * @param string $title    The widget title. Default 'WPLegalPages'.
		 * @param array  $instance Array of settings for the current widget.
		 * @param mixed  $id_base  The widget ID.
		 */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$sortby  = empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
		$exclude = empty( $instance['exclude'] ) ? '' : $instance['exclude'];

		if ( 'menu_order' === $sortby ) {
			$sortby = 'menu_order, post_title';
		}

		$out = wp_list_pages(
		/**
		 * Filters the arguments for the WPLegalPages widget.
		 *
		 * @since 2.4.8
		 *
		 * @see wp_list_pages()
		 *
		 * @param array $args     An array of arguments to retrieve the pages list.
		 * @param array $instance Array of settings for the current widget.
		 */
			apply_filters(
				'widget_pages_args',
				array(
					'title_li'    => '',
					'echo'        => 0,
					'sort_column' => $sortby,
					'exclude'     => $exclude,
					'meta_key'    => 'is_legal', // phpcs:ignore slow query
					'meta_value'  => 'yes', // phpcs:ignore slow query
				),
				$instance
			)
		);

		if ( ! empty( $out ) ) {
			echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			$format = current_theme_supports( 'html5', 'navigation-widgets' ) ? 'html5' : 'xhtml';

			/** This filter is documented in wp-includes/widgets/class-wp-nav-menu-widget.php */
			$format = apply_filters( 'navigation_widgets_format', $format );

			if ( 'html5' === $format ) {
				// The title may be filtered: Strip out HTML and make sure the aria-label is never empty.
				$title      = trim( wp_strip_all_tags( $title ) );
				$aria_label = $title ? $title : $default_title;
				echo '<nav role="navigation" aria-label="' . esc_attr( $aria_label ) . '">';
			}
			?>

			<ul>
				<?php echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</ul>

			<?php
			if ( 'html5' === $format ) {
				echo '</nav>';
			}

			echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Handles updating settings for the current WPLegalPages widget instance.
	 *
	 * @since 2.4.8
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( in_array( $new_instance['sortby'], array( 'post_title', 'menu_order', 'ID' ), true ) ) {
			$instance['sortby'] = $new_instance['sortby'];
		} else {
			$instance['sortby'] = 'menu_order';
		}

		$instance['exclude'] = sanitize_text_field( $new_instance['exclude'] );

		return $instance;
	}

	/**
	 * Outputs the settings form for the WPLegalPages widget.
	 *
	 * @since 2.4.8
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		// Defaults.
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'sortby'  => 'post_title',
				'title'   => '',
				'exclude' => '',
			)
		);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'wplegalpages' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sortby' ) ); ?>"><?php esc_attr_e( 'Sort by:', 'wplegalpages' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'sortby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'sortby' ) ); ?>" class="widefat">
				<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php esc_attr_e( 'Page title', 'wplegalpages' ); ?></option>
				<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php esc_attr_e( 'Page order', 'wplegalpages' ); ?></option>
				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php esc_attr_e( 'Page ID', 'wplegalpages' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>"><?php esc_attr_e( 'Exclude:', 'wplegalpages' ); ?></label>
			<input type="text" value="<?php echo esc_attr( $instance['exclude'] ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>" class="widefat" />
			<br />
			<small><?php esc_attr_e( 'Page IDs, separated by commas.', 'wplegalpages' ); ?></small>
		</p>
		<?php
	}

}

/**
 * Register and load the widget.
 */
function wplegalpages_load_widget() {
	register_widget( 'WP_Widget_Legal_Pages' );
}

add_action( 'widgets_init', 'wplegalpages_load_widget' );
