<?php
namespace PowerpackElementsLite\Modules\Twitter\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Twitter Grid Widget
 */
class Twitter_Grid extends Powerpack_Widget {

	public function get_name() {
		return parent::get_widget_name( 'Twitter_Grid' );
	}

	public function get_title() {
		return parent::get_widget_title( 'Twitter_Grid' );
	}

	public function get_icon() {
		return parent::get_widget_icon( 'Twitter_Grid' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Twitter_Grid' );
	}

	/**
	 * Retrieve the list of scripts the twitter embedded grid widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'pp-jquery-plugin',
			'jquery-cookie',
			'twitter-widgets',
			'pp-scripts',
		);
	}

	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->start_controls_section(
			'section_grid',
			array(
				'label' => __( 'Grid', 'powerpack' ),
			)
		);

		$this->add_control(
			'url',
			array(
				'label'   => __( 'Collection URL', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'footer',
			array(
				'label'        => __( 'Show Footer?', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'width',
			array(
				'label'      => __( 'Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'unit' => 'px',
					'size' => '',
				),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
				),
			)
		);

		$this->add_control(
			'tweet_limit',
			array(
				'label'   => __( 'Tweet Limit', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->end_controls_section();

		$help_docs = PP_Config::get_widget_help_links( 'Twitter_Widget' );
		if ( ! empty( $help_docs ) ) {
			/**
			 * Content Tab: Docs Links
			 *
			 * @since 2.4.1
			 * @access protected
			 */
			$this->start_controls_section(
				'section_help_docs',
				[
					'label' => __( 'Help Docs', 'powerpack' ),
				]
			);

			$hd_counter = 1;
			foreach ( $help_docs as $hd_title => $hd_link ) {
				$this->add_control(
					'help_doc_' . $hd_counter,
					[
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => sprintf( '%1$s ' . $hd_title . ' %2$s', '<a href="' . $hd_link . '" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'pp-editor-doc-links',
					]
				);

				$hd_counter++;
			}

			$this->end_controls_section();
		}

	}

	protected function render() {
		$settings = $this->get_settings();

		$attrs = array();
		$attr  = ' ';

		$url = esc_url( $settings['url'] );

		$attrs['data-limit']  = ( ! empty( $settings['tweet_limit'] ) ) ? $settings['tweet_limit'] : '';
		$attrs['data-chrome'] = ( 'yes' !== $settings['footer'] ) ? 'nofooter' : '';
		$attrs['data-width']  = $settings['width']['size'];

		foreach ( $attrs as $key => $value ) {
			$attr .= $key;
			if ( ! empty( $value ) ) {
				$attr .= '="' . $value . '"';
			}

			$attr .= ' ';
		}

		?>
		<div class="pp-twitter-grid" <?php echo esc_attr( $attr ); ?>>
			<a class="twitter-grid" href="<?php echo esc_url( $url ); ?>?ref_src=twsrc%5Etfw" <?php echo esc_attr( $attr ); ?>></a>
		</div>
		<?php
	}

	/**
	 * Render Twitter Grid widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'atts', {
				'data-limit': ( settings.tweet_limit ) ? settings.tweet_limit : '',
				'data-chrome': ( 'yes' != settings.footer ) ? 'nofooter' : '',
				'data-width': settings.width.size,
			});
		#>
		<div class="pp-twitter-grid" {{{ view.getRenderAttributeString( 'atts' ) }}}>
			<a class="twitter-grid" href="{{ settings.url }}?ref_src=twsrc%5Etfw" {{{ view.getRenderAttributeString( 'atts' ) }}}></a>
		</div>
		<?php
	}
}
