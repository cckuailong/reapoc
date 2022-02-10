<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
/**
 * Elementor icon list widget.
 *
 * Elementor widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.2.0
 */
class Futurio_Extra_Posts extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve icon list widget name.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'futurio-extra-posts';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve icon list widget title.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Posts', 'futurio-extra' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve icon list widget icon.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the icon list widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
	}

	/**
	 * Register icon list widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.2.0
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_blog',
			[
				'label' => __( 'Blog', 'futurio-extra' ),
			]
		);


		$this->add_control(
			'number',
			[
				'label' => __( 'Number of posts', 'futurio-extra' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
			]
		);
    
    $this->add_control(
			'per_row',
			[
				'label' => __( 'Posts per row', 'futurio-extra' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => [
					'12'  => __( '1', 'futurio-extra' ),
					'6' => __( '2', 'futurio-extra' ),
					'4' => __( '3', 'futurio-extra' ),
					'3' => __( '4', 'futurio-extra' ),
					'2' => __( '6', 'futurio-extra' ),
				],
			]
		);

		$this->add_control(
			'category',
			[
				'label' 	=> __( 'Categories', 'futurio-extra' ),
				'type' 		=> Controls_Manager::SELECT,
                'options' 	=> $this->get_cats(),
                'multiple' 	=> true,				
				'default' 	=> 4,
			]
		);
    
    $this->add_control(
			'limit',
			[
				'label' => __( 'Excerpt', 'futurio-extra' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'ms' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
			]
		);


		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'futurio-extra' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();


		//Post titles styles
		$this->start_controls_section(
			'section_post_title_style',
			[
				'label' => __( 'Post title', 'futurio-extra' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'name_color',
			[
				'label' 	=> __( 'Color', 'futurio-extra' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .latest-news-wrapper h4 a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'post_title_typography',
				'selector' 	=> '{{WRAPPER}} .latest-news-wrapper h4',
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
		//End post titles styles	

		

		//Content styles
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Post content', 'futurio-extra' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'content_color',
			[
				'label' 	=> __( 'Color', 'futurio-extra' ),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .latest-news-wrapper .news-item .post-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' 		=> 'content_typography',
				'selector' 	=> '{{WRAPPER}} .latest-news-wrapper .news-item .post-excerpt',
				'scheme' 	=> Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
		//End content styles
    	
    //Post spacing
		$this->start_controls_section(
			'section_post_spacing',
			[
				'label' => __( 'Post spacing', 'futurio-extra' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'post_spacing',
			[
				'label' => __( 'Spacing', 'futurio-extra' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [	
            'top' => 0,
						'right' => 15,
						'bottom' => 0,
						'left' => 15,
						'unit' => 'px',
						'isLinked' => false,
					],				
				'selectors' => [
					'.page-builders {{WRAPPER}} .f-posts-shortcode article, {{WRAPPER}} .f-posts-shortcode article' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		//End post spacing


	}

	protected function get_cats() {
		$items = [ '' => '' ];
		$terms = get_terms('category');
		foreach ( $terms as $term ) {
			$items[ $term->term_id ] = $term->name;
		}
		return $items;
	}	

	/**
	 * Render icon list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.2.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
    $limit = $settings['limit']['size'];
    $per_row = $settings['per_row'];
		$r = new \WP_Query( array(
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'cat'		  		  => $settings['category'],
			'posts_per_page'	  => $settings['number']		
		) );

		if ( $r->have_posts() ) :
		?>

		<div class="f-posts-shortcode">
			<div class="latest-news-wrapper">
			<?php while ( $r->have_posts() ) : $r->the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'news-item text-center col-md-' . absint( $per_row ) ); ?>>
					<div class="entry-thumb">
						<?php futurio_thumb_img( 'futurio-med' ); ?>		
					</div>						
  				<?php the_title( sprintf( '<h4 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' ); ?>
          <div class="f-line"></div>
					<div class="post-excerpt">
					<?php echo wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), $limit ); ?>
          </div>
				</article>
			<?php endwhile; ?>
			</div>
		</div>

		<?php 
		wp_reset_postdata();
		endif; //end have_posts() check
		?>

		<?php
	}

	/**
	 * Render icon list widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.2.0
	 * @access protected
	 */
	protected function _content_template() {
	}
}