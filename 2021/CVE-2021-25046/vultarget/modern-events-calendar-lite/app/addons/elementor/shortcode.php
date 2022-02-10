<?php
namespace Elementor;

/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC elementor addon shortcode class
 * @author Webnus <info@webnus.biz>
 */
class MEC_addon_elementor_shortcode extends \Elementor\Widget_Base
{
	/**
	 * Retrieve MEC widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name()
    {
		return 'MEC';
	}

	/**
	 * Retrieve MEC widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title()
    {
		return __('Modern Events Calendar (MEC)', 'modern-events-calendar-lite');
    }

	/**
	 * Retrieve MEC widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon()
    {
		return 'eicon-archive-posts';
	}

	/**
	 * Set widget category.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget category.
	 */
	public function get_categories()
    {
		return array('general');
	}

	/**
	 * Register MEC widget controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls()
    {
        $calendar_posts = get_posts(array('post_type'=>'mec_calendars', 'posts_per_page'=>'-1'));
        
        $calendars = array();
        foreach($calendar_posts as $calendar_post) $calendars[$calendar_post->ID] = $calendar_post->post_title;

        // Content Tab
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __('General', 'modern-events-calendar-lite'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
		);

		// Select Type Section
		$this->add_control(
			'type',
			array(
				'label' => __('Select Type', 'modern-events-calendar-lite'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $calendars,
            )
		);
		
		$this->end_controls_section();
	}

	/**
	 * Render MEC widget output on the frontend.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render()
    {
        $settings = $this->get_settings_for_display();
        if(!empty($settings['type']))
        {
		    echo do_shortcode('[MEC id="'.$settings['type'].'"]');
        }
	}
}