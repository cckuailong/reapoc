<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 10/19/18
 * Time: 4:00 PM
 */

class WDIElementorWidget extends \Elementor\Widget_Base {
  private $feed_options = array();
  private $default_feed = '0';
  /**
   * Get widget name.
   *
   * @return string Widget name.
   */
  public function get_name(){
    return 'wdi-elementor';
  }

  /**
   * Get widget title.
   *
   * @return string Widget title.
   */
  public function get_title(){
    return __('Instagram Feed', 'wd-instagram-feed');
  }

  /**
   * Get widget icon.
   *
   * @return string Widget icon.
   */
  public function get_icon(){
    return 'twbb-wd-instagram-feed twbb-widget-icon';
  }

  /**
   * Get widget categories.
   *
   * @return array Widget categories.
   */
  public function get_categories(){
    return ['tenweb-plugins-widgets'];
  }

  /**
   * Register widget controls.
   */
  protected function _register_controls(){
    $this->set_options();
    $this->start_controls_section(
      'wdi_general',
      [
        'label' => __('General', 'wd-instagram-feed'),
      ]
    );

    if($this->get_id() !== null){
      $settings = $this->get_init_settings();
    }

    $wdi_feed_id = $this->default_feed;
    if(isset($settings) && isset($settings["wdi_feeds"]) && intval($settings["wdi_feeds"])>0){
      $wdi_feed_id = intval($settings["wdi_feeds"]);
    }
    if(intval($this->default_feed) !==0){
      $wdi_edit_link  = add_query_arg(array( 'page' => 'wdi_feeds&task=edit&current_id='.$wdi_feed_id), admin_url('admin.php'));
    }else{
      $wdi_edit_link  = add_query_arg(array( 'page' => 'wdi_feeds'), admin_url('admin.php'));
    }

    $this->add_control(
      'wdi_feeds',
      [
        'label' => __('Select Feed', 'wd-instagram-feed'),
        'label_block' => true,
        'description' => __('Select the feed to display.', 'wd-instagram-feed') .' <a target="_blank" " href="' . $wdi_edit_link . '">' . __('Edit feed', 'wd-instagram-feed') . '</a>',
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => $this->default_feed,
        'options' => $this->feed_options
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Render widget output on the frontend.
   */
  protected function render(){
    $settings = $this->get_settings_for_display();
    if(!empty($settings['wdi_feeds'])) {
      echo wdi_feed(array('id' => $settings['wdi_feeds']));
    } else {
      echo __('No feed. Create and publish a feed to display it.', "wd-instagram-feed");
    }
  }

  public function set_options(){
    require_once WDI_DIR . "/admin/models/WDIModelEditorShortcode.php";
    $model = new WDIModelEditorShortcode();
    $rows = $model->get_row_data();
    if(!empty($rows)) {
      foreach($rows as $row) {
        $this->feed_options[$row->id] = $row->feed_name;
      }
    } else {
      $this->feed_options = array('0' => 'No Feed');
    }

    reset($this->feed_options);
    $this->default_feed = key($this->feed_options);
  }
}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new WDIElementorWidget());
