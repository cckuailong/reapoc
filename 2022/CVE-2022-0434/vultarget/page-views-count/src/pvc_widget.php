<?php
namespace A3Rev\PageViewsCount\Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use A3Rev\PageViewsCount;

class PVC extends \WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_pvc_stats',
			'description' => __( 'Show total views and views today of Page/Post.', 'page-views-count' )
		); 
		parent::__construct( 'widget_pvc_stats', __( 'a3 Page Views Count', 'page-views-count' ), $widget_ops );

		add_filter( 'pvc_stats_widget', array( $this, 'pvc_stats_widget' ), 10, 4 );
	}

	function widget( $args, $instance ) {
		$title            = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$postid           = sanitize_text_field( $instance['postid'] );
		$increase         = intval( $instance['increase'] );
		$show_views_today = intval( $instance['show_views_today'] );

		$pvc_stats_output = apply_filters( 'pvc_stats_widget', '', $postid, $increase, $show_views_today );

		if ( empty( $pvc_stats_output ) ) {
			return '';
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		if ( $title ) { 
			echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		echo $pvc_stats_output;

		echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}

	public function pvc_stats_widget( $output = '', $postid = '', $increase = 1, $show_views_today = 1 ) {
		if ( empty( $postid ) ) {
			global $post;
			if ( $post ) {
				$postid = $post->ID;
			}
		}

		if ( empty( $postid ) ) {
			return '';
		}

		$attributes = array( 'views_type' => ( 0 == $show_views_today ? 'total_only' : 'all' ) );

		if ( 1 == $increase ) {
			$output = PageViewsCount\A3_PVC::custom_stats_update_echo( $postid, 0, $attributes );
		} else {
			$output = PageViewsCount\A3_PVC::custom_stats_echo( $postid, 0, $attributes );
		}

		return $output;
	}

	function update( $new_instance, $old_instance ) {
		$instance           = $old_instance;
		$instance['title']  = sanitize_text_field( $new_instance['title'] );
		$instance['postid'] = sanitize_text_field( $new_instance['postid'] );

		$instance['increase'] = 1;
		if ( ! isset( $new_instance['increase'] ) ) {
			$instance['increase'] = 0;
		}

		$instance['show_views_today'] = 1;
		if ( ! isset( $new_instance['show_views_today'] ) ) {
			$instance['show_views_today'] = 0;
		}

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title'            => '',
			'postid'           => '',
			'increase'         => 1,
			'show_views_today' => 1,
			) );

		$title            = esc_attr( $instance['title'] );
		$postid           = esc_attr( $instance['postid'] );
		$increase         = intval( $instance['increase'] );
		$show_views_today = intval( $instance['show_views_today'] );
?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php _e('Title', 'page-views-count' ); ?>:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
        <p>
			<label for="<?php echo esc_attr( $this->get_field_id('postid') ); ?>"><?php _e('Post/Page ID', 'page-views-count' ); ?>:</label>
			<input style="width:50px;" id="<?php echo esc_attr( $this->get_field_id('postid') ); ?>" name="<?php echo esc_attr( $this->get_field_name('postid') ); ?>" type="text" value="<?php echo esc_attr( $postid ); ?>" /> <br />
			<span class="description"><?php _e( 'Post/Page ID want to show stats, leave empty for use ID of current post.', 'page-views-count' ); ?></span>
		</p>
        <p>
        	<input type="checkbox" <?php checked( $increase, 1 ); ?> id="<?php echo esc_attr( $this->get_field_id('increase') ); ?>" name="<?php echo esc_attr( $this->get_field_name('increase') ); ?>" value="1" />
        	<label for="<?php echo esc_attr( $this->get_field_id('increase') ); ?>"><?php _e( 'Increase count', 'page-views-count' ); ?></label>
        </p>
        <p>
        	<input type="checkbox" <?php checked( $show_views_today, 1 ); ?> id="<?php echo esc_attr( $this->get_field_id('show_views_today') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_views_today') ); ?>" value="1" />
        	<label for="<?php echo esc_attr( $this->get_field_id('show_views_today') ); ?>"><?php _e( 'Show Views Today', 'page-views-count' ); ?></label>
        </p>
<?php
	}
}
