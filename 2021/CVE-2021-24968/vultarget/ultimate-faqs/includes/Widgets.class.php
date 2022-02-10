<?php

class ewdufaqWidgetManager {

	public function __construct() {

		add_action( 'widgets_init', array( $this, 'register_faq_post_list_widget' ) );
		add_action( 'widgets_init', array( $this, 'register_recent_faqs_widget' ) );
		add_action( 'widgets_init', array( $this, 'register_popular_faqs_widget' ) );
		add_action( 'widgets_init', array( $this, 'register_random_faqs_widget' ) );
		add_action( 'widgets_init', array( $this, 'register_faq_categories_widget' ) );
	}

	public function register_faq_post_list_widget() {

		return register_widget( 'ewdufaqFAQPostListWidget' );
	}

	public function register_recent_faqs_widget() {

		return register_widget( 'ewdufaqRecentFAQsWidget' );
	}

	public function register_popular_faqs_widget() {

		return register_widget( 'ewdufaqPopularFAQsWidget' );
	}

	public function register_random_faqs_widget() {

		return register_widget( 'ewdufaqRandomFAQsWidget' );
	}

	public function register_faq_categories_widget() {

		return register_widget( 'ewdufaqFAQCategoriesWidget' );
	}

}

class ewdufaqFAQPostListWidget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		parent::__construct(
			'ewd_ufaq_display_faq_post_list', // Base ID
			__( 'UFAQ FAQ ID List', 'ultimate-faqs' ), // Name
			array( 'description' => __( 'Insert FAQ posts using a comma-separated list of post IDs', 'ultimate-faqs' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		if ( $instance['faq_title'] != '' ) { echo ( $args['before_title'] != '' ? $args['before_title'] : '<h3>' ) . $instance['faq_title'] . ( $args['after_title'] != '' ? $args['after_title'] : '</h3>' ); }
		echo do_shortcode( "[select-faq faq_id='". $instance['faq_id'] . "' no_comments='Yes']" );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$faq_id = ! empty( $instance['faq_id'] ) ? $instance['faq_id'] : __( 'FAQ ID List', 'ultimate-faqs' );
		$faq_title = ! empty( $instance['faq_title'] ) ? $instance['faq_title'] : __( '', 'ultimate-faqs' );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'faq_id' ); ?>"><?php _e( 'FAQ ID List:', 'ultimate-faqs' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'faq_id' ); ?>" name="<?php echo $this->get_field_name( 'faq_id' ); ?>" type="text" value="<?php echo esc_attr( $faq_id ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'faq_title' ); ?>"><?php _e( 'Widget Title:', 'ultimate-faqs' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'faq_title' ); ?>" name="<?php echo $this->get_field_name( 'faq_title' ); ?>" type="text" value="<?php echo esc_attr( $faq_title ); ?>">
		</p>

		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['faq_id'] = ( ! empty( $new_instance['faq_id'] ) ) ? strip_tags( $new_instance['faq_id'] ) : '';
		$instance['faq_title'] = ( ! empty( $new_instance['faq_title'] ) ) ? strip_tags( $new_instance['faq_title'] ) : '';

		return $instance;
	}
}

class ewdufaqRecentFAQsWidget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		parent::__construct(
			'ewd_ufaq_display_recent_faqs', // Base ID
			__( 'Recent FAQs', 'ultimate-faqs' ), // Name
			array( 'description' => __( 'Insert a number of the most recent FAQs', 'ultimate-faqs' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		if ( $instance['faq_title'] != '' ) { echo ( $args['before_title'] != '' ? $args['before_title'] : '<h3>' ) . $instance['faq_title'] . ( $args['after_title'] != '' ? $args['after_title'] : '</h3>' ); }
		echo do_shortcode( "[recent-faqs post_count='". $instance['post_count'] . "' no_comments='Yes']" );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$post_count = ! empty( $instance['post_count'] ) ? $instance['post_count'] : __( 'Number of FAQs', 'ultimate-faqs' );
		$faq_title = ! empty( $instance['faq_title'] ) ? $instance['faq_title'] : __( 'Widget Title', 'ultimate-faqs' );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'post_count' ); ?>"><?php _e( 'Number of FAQs:', 'ultimate-faqs' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'post_count' ); ?>" name="<?php echo $this->get_field_name( 'post_count' ); ?>" type="text" value="<?php echo esc_attr( $post_count ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'faq_title' ); ?>"><?php _e( 'Widget Title:', 'ultimate-faqs' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'faq_title' ); ?>" name="<?php echo $this->get_field_name( 'faq_title' ); ?>" type="text" value="<?php echo esc_attr( $faq_title ); ?>">
		</p>

		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['post_count'] = ( ! empty( $new_instance['post_count'] ) ) ? strip_tags( $new_instance['post_count'] ) : '';
		$instance['faq_title'] = ( ! empty( $new_instance['faq_title'] ) ) ? strip_tags( $new_instance['faq_title'] ) : '';

		return $instance;
	}
}

class ewdufaqPopularFAQsWidget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		parent::__construct(
			'ewd_ufaq_popular_reviews_widget', // Base ID
			__( 'Popular FAQs', 'ultimate-faqs' ), // Name
			array( 'description' => __( 'Insert a number of popular FAQs.', 'ultimate-faqs' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		if ( $instance['faq_title'] != '' ) { echo ( $args['before_title'] != '' ? $args['before_title'] : '<h3>' ) . $instance['faq_title'] . ( $args['after_title'] != '' ? $args['after_title'] : '</h3>' ); }
		echo do_shortcode( "[popular-faqs post_count='". $instance['post_count'] . "' no_comments='Yes']" );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$post_count = ! empty( $instance['post_count'] ) ? $instance['post_count'] : __( 'Number of FAQs', 'ultimate-faqs' );
		$faq_title = ! empty( $instance['faq_title'] ) ? $instance['faq_title'] : __( 'Widget Title', 'ultimate-faqs' );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'post_count' ); ?>"><?php _e( 'Number of FAQs:', 'ultimate-faqs' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'post_count' ); ?>" name="<?php echo $this->get_field_name( 'post_count' ); ?>" type="text" value="<?php echo esc_attr( $post_count ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'faq_title' ); ?>"><?php _e( 'Widget Title:', 'ultimate-faqs' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'faq_title' ); ?>" name="<?php echo $this->get_field_name( 'faq_title' ); ?>" type="text" value="<?php echo esc_attr( $faq_title ); ?>">
		</p>

		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['post_count'] = ( ! empty( $new_instance['post_count'] ) ) ? strip_tags( $new_instance['post_count'] ) : '';
		$instance['faq_title'] = ( ! empty( $new_instance['faq_title'] ) ) ? strip_tags( $new_instance['faq_title'] ) : '';

		return $instance;
	}
}

class ewdufaqRandomFAQsWidget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		parent::__construct(
			'ewd_ufaq_display_random_faq', // Base ID
			__( 'Random FAQ', 'ultimate-faqs' ), // Name
			array( 'description' => __( 'Display a random FAQ', 'ultimate-faqs' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		$args = array(
			'orderby' 			=> 'rand', 
			'posts_per_page' 	=> '1', 
			'post_type' 		=> EWD_UFAQ_FAQ_POST_TYPE
		);

		$faqs = get_posts( $args );
		$faq = reset( $faqs );

		echo $args['before_widget'];
		if ($instance['faq_title'] != "") {echo ($args['before_title'] != '' ? $args['before_title'] : "<h3>") . $instance['faq_title'] . ($args['after_title'] != '' ? $args['after_title'] : "</h3>");}
		echo do_shortcode( "[select-faq faq_id='". $faq->ID . "' no_comments='Yes']" );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$faq_title = ! empty( $instance['faq_title'] ) ? $instance['faq_title'] : __( 'Widget Title', 'ultimate-faqs' );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'faq_title' ); ?>"><?php _e( 'Widget Title:', 'ultimate-faqs' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'faq_title' ); ?>" name="<?php echo $this->get_field_name( 'faq_title' ); ?>" type="text" value="<?php echo esc_attr( $faq_title ); ?>">
		</p>

		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['faq_title'] = ( ! empty( $new_instance['faq_title'] ) ) ? strip_tags( $new_instance['faq_title'] ) : '';

		return $instance;
	}
}

class ewdufaqFAQCategoriesWidget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		parent::__construct(
			'ewd_ufaq_display_faq_categories', // Base ID
			__( 'UFAQ FAQ Category List', 'ultimate-faqs' ), // Name
			array( 'description' => __( 'Insert FAQ posts using a comma-separated list of categories', 'ultimate-faqs' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		if ($instance['faq_title'] != "") {echo ($args['before_title'] != '' ? $args['before_title'] : "<h3>") . $instance['faq_title'] . ($args['after_title'] != '' ? $args['after_title'] : "</h3>");}
		echo do_shortcode( "[ultimate-faqs include_category='". $instance['include_category'] . "' no_comments='Yes']" );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$include_category = ! empty( $instance['include_category'] ) ? $instance['include_category'] : __( 'FAQ Category List', 'ultimate-faqs' );
		$faq_title = ! empty( $instance['faq_title'] ) ? $instance['faq_title'] : __( 'Widget Title', 'ultimate-faqs' );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'include_category' ); ?>"><?php _e( 'FAQ Category List:', 'ultimate-faqs' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'include_category' ); ?>" name="<?php echo $this->get_field_name( 'include_category' ); ?>" type="text" value="<?php echo esc_attr( $include_category ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'faq_title' ); ?>"><?php _e( 'Widget Title:', 'ultimate-faqs' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'faq_title' ); ?>" name="<?php echo $this->get_field_name( 'faq_title' ); ?>" type="text" value="<?php echo esc_attr( $faq_title ); ?>">
		</p>

		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['include_category'] = ( ! empty( $new_instance['include_category'] ) ) ? strip_tags( $new_instance['include_category'] ) : '';
		$instance['faq_title'] = ( ! empty( $new_instance['faq_title'] ) ) ? strip_tags( $new_instance['faq_title'] ) : '';

		return $instance;
	}
}