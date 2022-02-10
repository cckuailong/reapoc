<?php
if ( !class_exists( 'WooCommerce' ) ) {
	return;
}
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Futurio_Extra_Woo_Header_Cart extends Widget_Base {

	public function get_name() {
		return 'woo-header-cart';
	}

	public function get_title() {
		return __( 'WooCommerce Header Cart', 'futurio-extra' );
	}

	public function get_icon() {
		return 'eicon-cart-light';
	}

	public function get_categories() {
		return [ 'woocommerce' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => __( 'Colors', 'futurio-extra' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'futurio-extra' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .header-cart a.cart-contents i.fa' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'counter_bg_color',
			[
				'label' => __( 'Counter background', 'futurio-extra' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cart-contents span.count' => 'background-color: {{VALUE}}',
				],

			]
		);
    
    $this->add_control(
			'counter_color',
			[
				'label' => __( 'Counter number color', 'futurio-extra' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cart-contents span.count' => 'color: {{VALUE}}',
				],

			]
		);
    
    $this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'futurio-extra' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'futurio-extra' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'futurio-extra' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'futurio-extra' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .elementor-menu-cart' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

  if ( function_exists( 'futurio_header_cart' ) && class_exists( 'WooCommerce' ) ) { ?>
		<div class="elementor-menu-cart" >
			<?php futurio_header_cart(); ?>
		</div>	
	<?php } 
	}

	protected function _content_template() {
	
	}
}
