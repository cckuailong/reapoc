<?php
class WDIControllerThemes_wdi {

  private $data_format;
  private $view,$model;

  public function __construct() {
    $this->setDataFormat();
    require_once (WDI_DIR . "/admin/models/WDIModelThemes_wdi.php");
    $this->model = new WDIModelThemes_wdi();

    require_once (WDI_DIR . "/admin/views/WDIViewThemes_wdi.php");
    $this->view = new WDIViewThemes_wdi($this->model);
  }

  public function execute() {
    $task = WDILibrary::get('task');
    $id = WDILibrary::get('current_id', 0);
    $message = WDILibrary::get('message');

    if(!empty($message)){
      $message = explode(',', $message);
      foreach($message as $msg_id){
        echo WDILibrary::message_id($msg_id);
      }
    }

    $get_method_tasks = array(
      "add",
      "edit",
      "display"
    );
    $get_task = "";
    if ( WDILibrary::get('task') != '' ) {
      $get_task = WDILibrary::get('task');
    }
    if (method_exists($this, $task)) {
      if(!in_array($get_task , $get_method_tasks)){
        check_admin_referer('nonce_wd', 'nonce_wd');
      }
      $this->$task($id);
    }
    else {
      $search_value = WDILibrary::get('search_value');
      if( !empty($search_value) ){
        WDILibrary::wdi_spider_redirect(add_query_arg(array(
          'page' => WDILibrary::get('page'),
          'task' => 'display',
          'search' => $search_value,
        ), admin_url('admin.php')));
      }else{
        $this->display();
      }
    }
  }

  public function display() {
    require_once (WDI_DIR . "/admin/models/WDIModelThemes_wdi.php");
    $model = new WDIModelThemes_wdi();

    require_once (WDI_DIR . "/admin/views/WDIViewThemes_wdi.php");
    $view = new WDIViewThemes_wdi($model);
    $view->display();
  }


  private function setDataFormat(){
  $this->data_format = array(
          '%s',/*theme_name*/
          '%s',/*default_theme*/
          '%s',/*feed_container_bg_color*/
          '%s',/*feed_wrapper_width*/
          '%s',/*feed_container_width*/
          '%s',/*feed_wrapper_bg_color*/
          '%s',/*active_filter_bg_color*/
          '%s',/*header_margin*/
          '%s',/*header_padding*/
          '%s',/*header_border_size*/
          '%s',/*header_border_color*/
          '%s',/*header_position*/
          '%s',/*header_img_width*/
          '%s',/*header_border_radius*/
          '%s',/*header_text_padding*/
          '%s',/*header_text_color*/
          '%d',/*header_font_weight*/
          '%s',/*header_text_font_size*/
          '%s',/*header_text_font_style*/
          '%s',/*'follow_btn_border_radius'=>'number'*/
          '%s',/*'follow_btn_padding'=>'number'*/
          '%d',/*'follow_btn_margin'=>'number'*/
          '%s',//'follow_btn_bg_color'=>'color',
          '%s',//'follow_btn_border_color'=>'color',
          '%s',//'follow_btn_text_color'=>'color',
          '%d',//'follow_btn_font_size'=>'number',
          '%s',//'follow_btn_border_hover_color'=>'color',
          '%s',//'follow_btn_text_hover_color'=>'color',
          '%s',//'follow_btn_background_hover_color'=>'color',

          '%s',/*user_horizontal_margin*/
          '%s',/*user_padding*/
          '%s',/*user_border_size*/
          '%s',/*user_border_color*/
          '%d',//'user_img_width'
          '%d',/*user_border_radius*/
          '%s',/*user_background_color*/
          '%s',/*users_border_size*/
          '%s',/*users_border_color*/
          '%s',/*users_background_color*/
          '%s',//users_text_color
          '%d',//users_font_weight
          '%s',//users_text_font_size
          '%s',//users_text_font_style
          '%s',//user_description_font_size
          '%s',//'lightbox_overlay_bg_color'=>'color',
          '%d',//'lightbox_overlay_bg_transparent'=>'number_max_100',
          '%s',//'lightbox_bg_color'=>'color',
          '%d',//'lightbox_ctrl_btn_height'=>'number',
          '%d',//'lightbox_ctrl_btn_margin_top'=>'number',
          '%d',//'lightbox_ctrl_btn_margin_left'=>'number',
          '%s',//'lightbox_ctrl_btn_pos'=>'string',
          '%s',//'lightbox_ctrl_cont_bg_color'=>'color',
          '%d',//'lightbox_ctrl_cont_border_radius'=>'number',
          '%d',//'lightbox_ctrl_cont_transparent'=>'number_max_100',
          '%s',//'lightbox_ctrl_btn_align'=>'position',
          '%s',//'lightbox_ctrl_btn_color'=>'color',
          '%d',//'lightbox_ctrl_btn_transparent'=>'number_max_100',
          '%d',//'lightbox_toggle_btn_height'=>'number',
          '%d',//'lightbox_toggle_btn_width'=>'number',
          '%d',//'lightbox_close_btn_border_radius'=>'number',
          '%d',//'lightbox_close_btn_border_width'=>'number',
          '%s',//'lightbox_close_btn_border_style'=>'string',
          '%s',//'lightbox_close_btn_border_color'=>'color',
          '%s',//'lightbox_close_btn_box_shadow'=>'css_box_shadow',
          '%s',//'lightbox_close_btn_bg_color'=>'color',
          '%d',//'lightbox_close_btn_transparent'=>'number_max_100',
          '%d',//'lightbox_close_btn_width'=>'number',
          '%d',//'lightbox_close_btn_height'=>'number',
          '%d',//'lightbox_close_btn_top'=>'number_neg',
          '%d',//'lightbox_close_btn_right'=>'number_neg',
          '%d',//'lightbox_close_btn_size'=>'number',
          '%s',//'lightbox_close_btn_color'=>'color',
          '%s',//'lightbox_close_btn_full_color'=>'color',
          '%s',//'lightbox_close_btn_hover_color'=>'color'
          '%s',//'lightbox_comment_share_button_color'=>'color',
          '%s',//'lightbox_rl_btn_style'=>'string',
          '%s',//'lightbox_rl_btn_bg_color'=>'color',
          '%d',//'lightbox_rl_btn_transparent'=>'number_max_100',
          '%s',//'lightbox_rl_btn_box_shadow'=>'css_box_shadow',
          '%d',//'lightbox_rl_btn_height'=>'number',
          '%d',//'lightbox_rl_btn_width'=>'number',
          '%d',//'lightbox_rl_btn_size'=>'number',
          '%s',//'lightbox_close_rl_btn_hover_color'=>'color',
          '%s',//'lightbox_rl_btn_color'=>'color',
          '%d',//'lightbox_rl_btn_border_radius'=>'number',
          '%d',//'lightbox_rl_btn_border_width'=>'number',
          '%s',//'lightbox_rl_btn_border_style'=>'string',
          '%s',//'lightbox_rl_btn_border_color'=>'color',
          '%s',//'lightbox_filmstrip_pos'=>'position',
          '%s',//'lightbox_filmstrip_thumb_margin'=>'length_multi',
          '%d',//'lightbox_filmstrip_thumb_border_width'=>'number',
          '%s',//'lightbox_filmstrip_thumb_border_style'=>'string',
          '%s',//'lightbox_filmstrip_thumb_border_color'=>'color',
          '%d',//'lightbox_filmstrip_thumb_border_radius'=>'number',
          '%d',//'lightbox_filmstrip_thumb_active_border_width'=>'number',
          '%s',//'lightbox_filmstrip_thumb_active_border_color'=>'color',
          '%s',//'lightbox_filmstrip_thumb_deactive_transparent'=>'number_max_100',
          '%d',//'lightbox_filmstrip_rl_btn_size'=>'number',
          '%s',//'lightbox_filmstrip_rl_btn_color'=>'color',
          '%s',//'lightbox_filmstrip_rl_bg_color'=>'color',
          '%s',//'lightbox_info_pos'=>'position',
          '%s',//'lightbox_info_align'=>'string',
          '%s',//'lightbox_info_bg_color'=>'color',
          '%d',//'lightbox_info_bg_transparent'=>'number_max_100',
          '%d',//'lightbox_info_border_width'=>'number',
          '%s',//'lightbox_info_border_style'=>'string',
          '%s',//'lightbox_info_border_color'=>'color',
          '%d',//'lightbox_info_border_radius'=>'number',
          '%s',//'lightbox_info_padding'=>'length_multi',
          '%s',//'lightbox_info_margin'=>'length_multi',
          '%s',//'lightbox_title_color'=>'color',
          '%s',//'lightbox_title_font_style'=>'string',
          '%s',//'lightbox_title_font_weight'=>'string',
          '%d',//'lightbox_title_font_size'=>'number',
          '%s',//'lightbox_description_color'=>'color',
          '%s',//'lightbox_description_font_style'=>'string',
          '%s',//'lightbox_description_font_weight'=>'string',
          '%d',//'lightbox_description_font_size'=>'number',
          '%d',//'lightbox_info_height'=>'number_max_100'
          '%d',//'lightbox_comment_width'=>'number',
          '%s',//'lightbox_comment_pos'=>'string',
          '%s',//'lightbox_comment_bg_color'=>'color',
          '%d',//'lightbox_comment_font_size'=>'number',
          '%s',//'lightbox_comment_font_color'=>'color',
          '%s',//'lightbox_comment_font_style'=>'string',
          '%d',//'lightbox_comment_author_font_size'=>'number',
          '%s',//'lightbox_comment_author_font_color'=>'color',
          '%s',//'lightbox_comment_author_font_color_hover'=>'color'
          '%d',//'lightbox_comment_date_font_size'=>'number',
          '%d',//'lightbox_comment_body_font_size'=>'number',
          '%d',//'lightbox_comment_input_border_width'=>'number',
          '%s',//'lightbox_comment_input_border_style'=>'string',
          '%s',//'lightbox_comment_input_border_color'=>'color',
          '%d',//'lightbox_comment_input_border_radius'=>'number',
          '%s',//'lightbox_comment_input_padding'=>'length_multi',
          '%s',//'lightbox_comment_input_bg_color'=>'color',
          '%s',//'lightbox_comment_button_bg_color'=>'color',
          '%s',//'lightbox_comment_button_padding'=>'length_multi',
          '%d',//'lightbox_comment_button_border_width'=>'number',
          '%s',//'lightbox_comment_button_border_style'=>'string',
          '%s',//'lightbox_comment_button_border_color'=>'color',
          '%d',//'lightbox_comment_button_border_radius'=>'number',
          '%d',//'lightbox_comment_separator_width'=>'number',
          '%s',//'lightbox_comment_separator_style'=>'string',
          '%s',//'lightbox_comment_separator_color'=>'color',
          '%s',//'lightbox_comment_load_more_color' =>'color',
          '%s',//'lightbox_comment_load_more_color_hover' =>'color',

          '%s',/*th_photo_wrap_padding*/
          '%s',/*th_photo_wrap_border_size*/
          '%s',/*th_photo_wrap_border_color*/
          '%s',/*th_photo_img_border_radius*/
          '%s',/*th_photo_wrap_bg_color*/
          '%s',/*th_photo_meta_bg_color*/
          '%s',/*th_photo_meta_one_line*/
          '%s',/*th_like_text_color*/
          '%s',/*th_comment_text_color*/
          '%s',/*th_photo_caption_font_size*/
          '%s',/*th_photo_caption_color*/
          '%s',/*th_feed_item_margin*/
          '%s',/*th_photo_caption_hover_color*/
          '%s',/*th_like_comm_font_size*/
          '%s',//'th_overlay_hover_color'=>'color',
          '%d',//'th_overlay_hover_transparent'=>'number',
          '%s',//'th_overlay_hover_icon_color'=>'color',
          '%s',//'th_overlay_hover_icon_font_size'=>'length',

          '%s',//th_photo_img_hover_effect

          '%s',/*mas_photo_wrap_padding*/
          '%s',/*mas_photo_wrap_border_size*/
          '%s',/*mas_photo_wrap_border_color*/
          '%s',/*mas_photo_img_border_radius*/
          '%s',/*mas_photo_wrap_bg_color*/
          '%s',/*mas_photo_meta_bg_color*/
          '%s',/*mas_photo_meta_one_line*/
          '%s',/*mas_like_text_color*/
          '%s',/*mas_comment_text_color*/
          '%s',/*mas_photo_caption_font_size*/
          '%s',/*mas_photo_caption_color*/
          '%s',/*mas_feed_item_margin*/
          '%s',/*mas_photo_caption_hover_color*/
          '%s',/*mas_like_comm_font_size*/
          '%s',//'mas_overlay_hover_color'=>'color',
          '%d',//'mas_overlay_hover_transparent'=>'number',
          '%s',//'mas_overlay_hover_icon_color'=>'color',
          '%s',//'mas_overlay_hover_icon_font_size'=>'length',
          '%s',//mas_photo_img_hover_effect
          
          '%s',/*blog_style_photo_wrap_padding*/
          '%s',/*blog_style_photo_wrap_border_size*/
          '%s',/*blog_style_photo_wrap_border_color*/
          '%s',/*blog_style_photo_img_border_radius*/
          '%s',/*blog_style_photo_wrap_bg_color*/
          '%s',/*blog_style_photo_meta_bg_color*/
          '%s',/*blog_style_photo_meta_one_line*/
          '%s',/*blog_style_like_text_color*/
          '%s',/*blog_style_comment_text_color*/
          '%s',/*blog_style_photo_caption_font_size*/
          '%s',/*blog_style_photo_caption_color*/
          '%s',/*blog_style_feed_item_margin*/
          '%s',/*blog_style_photo_caption_hover_color*/
          '%s',/*blog_style_like_comm_font_size*/

          '%s',/*image_browser_photo_wrap_padding*/
          '%s',/*image_browser_photo_wrap_border_size*/
          '%s',/*image_browser_photo_wrap_border_color*/
          '%s',/*image_browser_photo_img_border_radius*/
          '%s',/*image_browser_photo_wrap_bg_color*/
          '%s',/*image_browser_photo_meta_bg_color*/
          '%s',/*image_browser_photo_meta_one_line*/
          '%s',/*image_browser_like_text_color*/
          '%s',/*image_browser_comment_text_color*/
          '%s',/*image_browser_photo_caption_font_size*/
          '%s',/*image_browser_photo_caption_color*/
          '%s',/*image_browser_feed_item_margin*/
          '%s',/*image_browser_photo_caption_hover_color*/
          '%s',/*image_browser_like_comm_font_size*/

          '%s',/*load_more_position*/
          '%s',/*load_more_padding*/
          '%s',/*load_more_bg_color*/
          '%s',/*load_more_border_radius*/
          '%s',/*load_more_height*/
          '%s',/*load_more_width*/
          '%s',/*load_more_border_size*/
          '%s',/*load_more_border_color*/
          '%s',/*load_more_text_color*/
          '%s',/*load_more_text_font_size*/
          '%s',/*load_more_wrap_hover_color*/
          '%s',// 'pagination_ctrl_color' => 'color',
          '%s',// 'pagination_size' => 'length',
          '%s',// 'pagination_ctrl_margin' => 'length_multi',
          '%s',// 'pagination_ctrl_hover_color' => 'color'
          '%s',//'pagination_position'=>'position'
          '%s',//'pagination_position_vert'=>'position'

          /* since v1.0.6. keep order, defaults*/
          
          '%s',//'th_thumb_user_bg_color'=>'color',
          '%s',//'th_thumb_user_color'=>'color'
          '%s',//'mas_thumb_user_bg_color'=>'color',
          '%s',//'mas_thumb_user_color'=>'color'
            );
}

  public static function remove_theme_file($theme_id){
    return null;
  }

  public static function remove_all_themes_files(){
      return;
  }

}

