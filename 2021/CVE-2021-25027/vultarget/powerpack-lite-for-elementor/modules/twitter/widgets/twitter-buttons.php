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
 * Twitter Buttons Widget
 */
class Twitter_Buttons extends Powerpack_Widget {

	public function get_name() {
		return parent::get_widget_name( 'Twitter_Buttons' );
	}

	public function get_title() {
		return parent::get_widget_title( 'Twitter_Buttons' );
	}

	public function get_icon() {
		return parent::get_widget_icon( 'Twitter_Buttons' );
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
		return parent::get_widget_keywords( 'Twitter_Buttons' );
	}

	/**
	 * Retrieve the list of scripts the logo carousel widget depended on.
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
			'section_buttons',
			array(
				'label' => __( 'Buttons', 'powerpack' ),
			)
		);

		$this->add_control(
			'button_type',
			array(
				'label'   => __( 'Type', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'share',
				'options' => array(
					'share'   => __( 'Share', 'powerpack' ),
					'follow'  => __( 'Follow', 'powerpack' ),
					'mention' => __( 'Mention', 'powerpack' ),
					'hashtag' => __( 'Hashtag', 'powerpack' ),
					'message' => __( 'Message', 'powerpack' ),
				),
			)
		);

		$this->add_control(
			'profile',
			array(
				'label'     => __( 'Profile URL or Username', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'button_type' => array( 'follow', 'mention', 'message' ),
				),
			)
		);

		$this->add_control(
			'recipient_id',
			array(
				'label'     => __( 'Recipient ID', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'button_type' => 'message',
				),
			)
		);

		$this->add_control(
			'default_text',
			array(
				'label'     => __( 'Default Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'button_type' => 'message',
				),
			)
		);

		$this->add_control(
			'hashtag_url',
			array(
				'label'     => __( 'Hashtag URL or #hashtag', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'button_type' => 'hashtag',
				),
			)
		);

		$this->add_control(
			'via',
			array(
				'label'     => __( 'Via (twitter handler)', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'button_type' => array( 'share', 'mention', 'hashtag' ),
				),
			)
		);

		$this->add_control(
			'share_text',
			array(
				'label'     => __( 'Custom Share Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'button_type' => array( 'share', 'mention', 'hashtag' ),
				),
			)
		);

		$this->add_control(
			'share_url',
			array(
				'label'     => __( 'Custom Share URL', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'button_type' => array( 'share', 'mention', 'hashtag' ),
				),
			)
		);

		$this->add_control(
			'show_count',
			array(
				'label'        => __( 'Show Count', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'button_type' => 'follow',
				),
			)
		);

		$this->add_control(
			'large_button',
			array(
				'label'        => __( 'Large Button', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default'      => 'no',
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

		$type         = $settings['button_type'];
		$profile      = $settings['profile'];
		$hashtag      = $settings['hashtag_url'];
		$recipient_id = $settings['recipient_id'];
		$default_text = ( isset( $settings['default_text'] ) && ! empty( $settings['default_text'] ) ) ? rawurlencode( $settings['default_text'] ) : '';

		$attrs['data-size'] = ( 'yes' === $settings['large_button'] ) ? 'large' : '';
		if ( 'share' === $type || 'mention' === $type || 'hashtag' === $type ) {
			$attrs['data-via']  = $settings['via'];
			$attrs['data-text'] = $settings['share_text'];
			$attrs['data-url']  = $settings['share_url'];
		}
		$attrs['data-lang'] = get_locale();

		if ( 'follow' === $type ) {
			$attrs['data-show-count'] = ( 'yes' === $settings['show_count'] ) ? 'true' : 'false';
		}

		if ( 'message' === $type ) {
			$attrs['data-screen-name'] = $profile;
		}

		foreach ( $attrs as $key => $value ) {
			$attr .= $key;
			if ( ! empty( $value ) ) {
				$attr .= '="' . $value . '"';
			}

			$attr .= ' ';
		}

		?>
		<div class="pp-twitter-buttons">
			<?php if ( 'share' === $type ) { ?>
				<a href="https://twitter.com/share" class="twitter-share-button" <?php echo esc_attr( $attr ); ?>>
					<?php esc_attr_e( 'Share', 'powerpack' ); ?>
				</a>
			<?php } elseif ( 'follow' === $type ) { ?>
				<a href="https://twitter.com/<?php echo esc_attr( $profile ); ?>" class="twitter-follow-button" <?php echo esc_attr( $attr ); ?>>
					<?php esc_attr_e( 'Follow', 'powerpack' ); ?>
				</a>
			<?php } elseif ( 'mention' === $type ) { ?>
				<a href="https://twitter.com/intent/tweet?screen_name=<?php echo esc_attr( $profile ); ?>" class="twitter-mention-button" <?php echo esc_attr( $attr ); ?>>
					<?php esc_attr_e( 'Mention', 'powerpack' ); ?>
				</a>
			<?php } elseif ( 'hashtag' === $type ) { ?>
				<a href="https://twitter.com/intent/tweet?button_hashtag=<?php echo esc_attr( $hashtag ); ?>" class="twitter-hashtag-button" <?php echo esc_attr( $attr ); ?>>
					<?php esc_attr_e( 'Hashtag', 'powerpack' ); ?>
				</a>
			<?php } else { ?>
				<a href="https://twitter.com/messages/compose?recipient_id=<?php echo esc_attr( $recipient_id ); ?><?php echo ! empty( $default_text ) ? '&text=' . esc_attr( $default_text ) : ''; ?>" class="twitter-dm-button" <?php echo esc_attr( $attr ); ?>>
					<?php esc_attr_e( 'Message', 'powerpack' ); ?>
				</a>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render Twitter Buttons widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var text = ( settings.default_text ) ? '&text='+settings.default_text : '';
			var type = settings.button_type;

			if ( 'share' == type || 'mention' == type || 'hashtag' == type ) {

				view.addRenderAttribute( 'atts', {
					'data-via': settings.via,
					'data-text': settings.share_text,
					'data-url': settings.share_url,
				});
			}
			if ( 'follow' == type ) {
				view.addRenderAttribute( 'atts', {
					'data-show-count': ( 'yes' == settings.show_count ) ? 'true' : 'false',
				});
			}

			if ( 'message' == type ) {
				view.addRenderAttribute( 'atts', {
					'data-screen-name': settings.profile,
				});
			}

			view.addRenderAttribute( 'atts', {
				'data-size': ( 'yes' == settings.large_button ) ? 'large' : '',
				'data-lang': '',
			});
		#>
		<div class="pp-twitter-buttons">
			<# if ( 'share' == settings.button_type ) { #>
				<a href="https://twitter.com/share" class="twitter-share-button" {{{ view.getRenderAttributeString( 'atts' ) }}}>Share</a>
			<# } else if ( 'follow' == settings.button_type ) { #>
				<a href="https://twitter.com/{{ settings.profile }}" class="twitter-follow-button" {{{ view.getRenderAttributeString( 'atts' ) }}}>Follow</a>
			<# } else if ( 'mention' == settings.button_type ) { #>
				<a href="https://twitter.com/intent/tweet?screen_name={{ settings.profile }}" class="twitter-mention-button" {{{ view.getRenderAttributeString( 'atts' ) }}}>Mention</a>
			<# } else if ( 'hashtag' == settings.button_type ) { #>
				<a href="https://twitter.com/intent/tweet?button_hashtag={{ settings.hashtag_url }}" class="twitter-hashtag-button" {{{ view.getRenderAttributeString( 'atts' ) }}}>Hashtag</a>
			<# } else { #>
				<a href="https://twitter.com/messages/compose?recipient_id={{ settings.recipient_id }}{{ text }}" class="twitter-dm-button" {{{ view.getRenderAttributeString( 'atts' ) }}}>Message</a>
			<# } #>
		</div>
		<?php
	}
}
