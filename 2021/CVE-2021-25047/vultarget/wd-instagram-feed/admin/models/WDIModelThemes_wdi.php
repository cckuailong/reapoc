<?php

class WDIModelThemes_wdi {

  private $page_number = null;
  private $search_text = "";
  public function __construct() {
    if ( WDILibrary::get('paged', 0, 'intval') != 0 ) {
        $this->page_number = WDILibrary::get('paged', 0, 'intval');
    } elseif ( WDILibrary::get('page_number', 0, 'intval') !=  0 ) {
        $this->page_number = WDILibrary::get('page_number', 0, 'intval');
    }
    if ( WDILibrary::get('search_value') != '' ) {
      $this->search_text = WDILibrary::get('search_value');
    } elseif ( WDILibrary::get('search') != '' ) {
      $this->search_text = WDILibrary::get('search');
    }
  }

  public function get_rows_data() {
    global $wpdb;
    $where = ((!empty($this->search_text)) ? 'WHERE theme_name LIKE "%' . esc_html(stripslashes($this->search_text)) . '%"' : '');
    $asc_or_desc = WDILibrary::get('order') == 'asc' ? 'asc' : 'desc';
    $order_by_arr = array('id', 'theme_name', 'default_theme');
    $order_by = WDILibrary::get('order_by');
    $order_by = (in_array($order_by, $order_by_arr)) ? $order_by : 'id';
    $order_by = ' ORDER BY `' . $order_by . '` ' . $asc_or_desc;
    if (isset($this->page_number) && $this->page_number) {
      $limit = ((int) $this->page_number - 1) * 20;
    }
    else {
      $limit = 0;
    }
    $query_limit = " LIMIT " . $limit . ",20";
    $query = "SELECT * FROM " . $wpdb->prefix . WDI_THEME_TABLE .' '. $where . $order_by . $query_limit;
 
    $rows = $wpdb->get_results($query);
    return $rows;
  }


  public function page_nav() {
    global $wpdb;
    $where = ((isset($this->search_text) && !empty($this->search_text) && (esc_html(stripslashes($this->search_text)) != '')) ? 'WHERE theme_name LIKE "%' . esc_html(stripslashes($this->search_text)) . '%"'  : '');
    $total = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . WDI_THEME_TABLE. ' ' . $where);
    $page_nav['total'] = $total;
    if (isset($this->page_number) && $this->page_number) {
      $limit = ((int) $this->page_number - 1) * 20;
    }
    else {
      $limit = 0;
    }
    $page_nav['limit'] = (int) ($limit / 20 + 1);
    return $page_nav;
  }

  public static function get_theme_defaults(){
  global $wdi_options; 
  $settings = array(
    'theme_name' => 'Instagram Design',
    'default_theme'=> '0',
    'feed_container_bg_color' => '#FFFFFF',
    'feed_wrapper_width' => '100%',
    'feed_container_width' => '100%',
    'feed_wrapper_bg_color' => '#FFFFFF',
    'active_filter_bg_color' => '#429fff',
    'header_margin' => '0px',
    'header_padding' => '5px',
    'header_border_size' => '0px',
    'header_border_color' => '#DDDDDD',
    'header_position' => 'left',
    'header_img_width' => '40',
    'header_border_radius'  => 0,
    'header_text_padding' => '5px',
    'header_text_color' => '#0f4973',
    'header_font_weight' => '400',
    'header_text_font_size' => '18px',
    'header_text_font_style' => 'normal',
    'follow_btn_border_radius'=>'3',
    'follow_btn_padding'=>'25',
    'follow_btn_margin'=>'10',
    'follow_btn_bg_color'=>'#ffffff',
    'follow_btn_border_color'=>'#0f4973',
    'follow_btn_text_color'=>'#0f4973',
    'follow_btn_font_size'=>'18',
    'follow_btn_border_hover_color'=>'#0f4973',
    'follow_btn_text_hover_color'=>'#0f4973',
    'follow_btn_background_hover_color'=>'#ffffff',

    'user_padding' => '5px',
    /////////////////////////disabled////////////////////////
    'user_horizontal_margin' => '',//*
    'user_border_size' => '0px',//*
    'user_border_color' => '',//*
    'user_img_width' => '40px',//enabled
    'user_border_radius' => '0px',//enabled
    'user_background_color' => '',//*
    'users_border_size' => '0px',//*
    'users_border_color' => '',//*
    'users_background_color' => '',//*
    //////////////////////////////////////////////////////////
    //////////////////////lightbox////////////////////////////
    'users_text_color' => '#0f4973',
    'users_font_weight' => '400',
    'users_text_font_size' => '18px',
    'users_text_font_style' => 'normal',
    'user_description_font_size' => '18px',
    
    'lightbox_overlay_bg_color'=>'#25292c',
    'lightbox_overlay_bg_transparent'=>'90',
    'lightbox_bg_color'=>'#ffffff',
    'lightbox_ctrl_btn_height'=>'20',
    'lightbox_ctrl_btn_margin_top'=>'10',
    'lightbox_ctrl_btn_margin_left'=>'7',
    'lightbox_ctrl_btn_pos'=>'bottom',
    'lightbox_ctrl_cont_bg_color'=>'#2a5b83',
    'lightbox_ctrl_cont_border_radius'=>'4',
    'lightbox_ctrl_cont_transparent'=>'80',
    'lightbox_ctrl_btn_align'=>'center',
    'lightbox_ctrl_btn_color'=>'#FFFFFF',
    'lightbox_ctrl_btn_transparent'=>'100',
    'lightbox_toggle_btn_height'=>'14',
    'lightbox_toggle_btn_width'=>'100',
    'lightbox_close_btn_border_radius'=>'16',
    'lightbox_close_btn_border_width'=>'2',
    'lightbox_close_btn_border_style'=>'none',
    'lightbox_close_btn_border_color'=>'#FFFFFF',
    'lightbox_close_btn_box_shadow'=>'none',
    'lightbox_close_btn_bg_color'=>'#2a5b83',
    'lightbox_close_btn_transparent'=>'100',
    'lightbox_close_btn_width'=>'20',
    'lightbox_close_btn_height'=>'20',
    'lightbox_close_btn_top'=>'-10',
    'lightbox_close_btn_right'=>'-10',
    'lightbox_close_btn_size'=>'15',
    'lightbox_close_btn_color'=>'#FFFFFF',
    'lightbox_close_btn_full_color'=>'#000000',
    'lightbox_close_btn_hover_color'=>'#000000',
    'lightbox_comment_share_button_color'=>'#ffffff',
    'lightbox_rl_btn_style'=>'tenweb-i-chevron',
    'lightbox_rl_btn_bg_color'=>'#2a5b83',
    'lightbox_rl_btn_transparent'=>'80',
    'lightbox_rl_btn_box_shadow'=>'none',
    'lightbox_rl_btn_height'=>'40',
    'lightbox_rl_btn_width'=>'40',
    'lightbox_rl_btn_size'=>'20',
    'lightbox_close_rl_btn_hover_color'=>'#25292c',
    'lightbox_rl_btn_color'=>'#FFFFFF',
    'lightbox_rl_btn_border_radius'=>'20',
    'lightbox_rl_btn_border_width'=>'0',
    'lightbox_rl_btn_border_style'=>'none',
    'lightbox_rl_btn_border_color'=>'#FFFFFF',
    'lightbox_filmstrip_pos'=>'top',
    'lightbox_filmstrip_thumb_margin'=>'0 1px',
    'lightbox_filmstrip_thumb_border_width'=>'1',
    'lightbox_filmstrip_thumb_border_style'=>'solid',
    'lightbox_filmstrip_thumb_border_color'=>'#25292c',
    'lightbox_filmstrip_thumb_border_radius'=>'0',
    'lightbox_filmstrip_thumb_active_border_width'=>'0',
    'lightbox_filmstrip_thumb_active_border_color'=>'#FFFFFF',
    'lightbox_filmstrip_thumb_deactive_transparent'=>'70',
    'lightbox_filmstrip_rl_btn_size'=>'20',
    'lightbox_filmstrip_rl_btn_color'=>'#FFFFFF',
    'lightbox_filmstrip_rl_bg_color'=>'#3B3B3B',
    'lightbox_info_pos'=>'top',
    'lightbox_info_align'=>'right',
    'lightbox_info_bg_color'=>'#3b3b3b',
    'lightbox_info_bg_transparent'=>'80',
    'lightbox_info_border_width'=>'1',
    'lightbox_info_border_style'=>'none',
    'lightbox_info_border_color'=>'#3b3b3b',
    'lightbox_info_border_radius'=>'5',
    'lightbox_info_padding'=>'5px',
    'lightbox_info_margin'=>'15px',
    'lightbox_title_color'=>'#FFFFFF',
    'lightbox_title_font_style'=>'segoe ui',
    'lightbox_title_font_weight'=>'bold',
    'lightbox_title_font_size'=>'13',
    'lightbox_description_color'=>'#FFFFFF',
    'lightbox_description_font_style'=>'segoe ui',
    'lightbox_description_font_weight'=>'normal',
    'lightbox_description_font_size'=>'14',
    'lightbox_info_height'=>'30',
    'lightbox_comment_width'=>'250',
    'lightbox_comment_pos'=>'right',
    'lightbox_comment_bg_color'=>'#ffffff',
    'lightbox_comment_font_size'=>'12',
    'lightbox_comment_font_color'=>'#000000',
    'lightbox_comment_font_style'=>'segoe ui',
    'lightbox_comment_author_font_size'=>'14',
    'lightbox_comment_author_font_color'=>'#125688',
    'lightbox_comment_author_font_color_hover'=>'#002160',
    'lightbox_comment_date_font_size'=>'10',
    'lightbox_comment_body_font_size'=>'12',
    'lightbox_comment_input_border_width'=>'1',
    'lightbox_comment_input_border_style'=>'none',
    'lightbox_comment_input_border_color'=>'#666666',
    'lightbox_comment_input_border_radius'=>'0',
    'lightbox_comment_input_padding'=>'2px',
    'lightbox_comment_input_bg_color'=>'#333333',
    'lightbox_comment_button_bg_color'=>'#616161',
    'lightbox_comment_button_padding'=>'3px 10px',
    'lightbox_comment_button_border_width'=>'1',
    'lightbox_comment_button_border_style'=>'none',
    'lightbox_comment_button_border_color'=>'#666666',
    'lightbox_comment_button_border_radius'=>'3',
    'lightbox_comment_separator_width'=>'1',
    'lightbox_comment_separator_style'=>'solid',
    'lightbox_comment_separator_color'=>'#125688',
    'lightbox_comment_load_more_color' =>"#125688",
    'lightbox_comment_load_more_color_hover' =>"#000000",
    //////////////////////////////////////////////////////////
    'th_photo_wrap_padding' => '10px',
    'th_photo_wrap_border_size' => '10px',
    'th_photo_wrap_border_color' => '#ffffff',
    'th_photo_img_border_radius' => '0px',
    'th_photo_wrap_bg_color' => '#FFFFFF',
    'th_photo_meta_bg_color' => '#FFFFFF',
    'th_photo_meta_one_line' => '1',
    'th_like_text_color' => '#8a8d8e',
    'th_comment_text_color' => '#8a8d8e',
    'th_photo_caption_font_size' => '14px',
    'th_photo_caption_color' => '#125688',
    'th_feed_item_margin' => '0',
    'th_photo_caption_hover_color' =>'#8e8e8e',
    'th_like_comm_font_size' => '13px',
    'th_overlay_hover_color'=>'#125688',
    'th_overlay_hover_transparent'=>'50',
    'th_overlay_hover_icon_color'=>'#FFFFFF',
    'th_overlay_hover_icon_font_size'=>'25px',    
    'th_photo_img_hover_effect' => 'none',
    //////////////////////////////////////////////////////////
    'mas_photo_wrap_padding' => '10px',
    'mas_photo_wrap_border_size' => '0px',
    'mas_photo_wrap_border_color' => 'gray',
    'mas_photo_img_border_radius' => '0px',
    'mas_photo_wrap_bg_color' => '#FFFFFF',
    'mas_photo_meta_bg_color' => '#FFFFFF',
    'mas_photo_meta_one_line' => '1',
    'mas_like_text_color' => '#8a8d8e',
    'mas_comment_text_color' => '#8a8d8e',
    'mas_photo_caption_font_size' => '14px',
    'mas_photo_caption_color' => '#125688',
    'mas_feed_item_margin' => '0',
    'mas_photo_caption_hover_color' =>'#8e8e8e',
    'mas_like_comm_font_size' => '13px',
    'mas_overlay_hover_color'=>'#125688',
    'mas_overlay_hover_transparent'=>'50',
    'mas_overlay_hover_icon_color'=>'#FFFFFF',
    'mas_overlay_hover_icon_font_size'=>'25px',    
    'mas_photo_img_hover_effect'=>'none',
   
    'blog_style_photo_wrap_padding' => '10px',
    'blog_style_photo_wrap_border_size' => '0px',
    'blog_style_photo_wrap_border_color' => 'gray',
    'blog_style_photo_img_border_radius' => '0px',
    'blog_style_photo_wrap_bg_color' => '#FFFFFF',
    'blog_style_photo_meta_bg_color' => '#FFFFFF',
    'blog_style_photo_meta_one_line' => '1',
    'blog_style_like_text_color' => '#8a8d8e',
    'blog_style_comment_text_color' => '#8a8d8e',
    'blog_style_photo_caption_font_size' => '16px',
    'blog_style_photo_caption_color' => '#125688',
    'blog_style_feed_item_margin' => '0',
    'blog_style_photo_caption_hover_color' =>'#8e8e8e',
    'blog_style_like_comm_font_size' => '20px',

    'image_browser_photo_wrap_padding' => '10px',
    'image_browser_photo_wrap_border_size' => '0px',
    'image_browser_photo_wrap_border_color' => 'gray',
    'image_browser_photo_img_border_radius' => '0px',
    'image_browser_photo_wrap_bg_color' => '#FFFFFF',
    'image_browser_photo_meta_bg_color' => '#FFFFFF',
    'image_browser_photo_meta_one_line' => '1',
    'image_browser_like_text_color' => '#8a8d8e',
    'image_browser_comment_text_color' => '#8a8d8e',
    'image_browser_photo_caption_font_size' => '16px',
    'image_browser_photo_caption_color' => '#125688',
    'image_browser_feed_item_margin' => '0',
    'image_browser_photo_caption_hover_color' =>'#8e8e8e',
    'image_browser_like_comm_font_size' => '20px',
    
    'load_more_position' => 'center',
    'load_more_padding' => '4px',
    'load_more_bg_color' => '#ffffff',
    'load_more_border_radius' => '500px',
    'load_more_height' => '90px',
    'load_more_width' => '90px',
    'load_more_border_size' => '1px',
    'load_more_border_color' => '#0f4973',
    'load_more_text_color' => '#1e73be',
    /*load more icon*/
    'load_more_text_font_size' => '14px',
    'load_more_wrap_hover_color' => 'transparent',
    'pagination_ctrl_color' => '#0f4973',
    'pagination_size' => '18px',
    'pagination_ctrl_margin' => '15px',
    'pagination_ctrl_hover_color' => '#25292c',
    'pagination_position' => 'center',
    'pagination_position_vert' => 'top',

    /* since v1.0.6*/
    /* keep order */
    'th_thumb_user_bg_color'=>'#429FFF',
    'th_thumb_user_color'=>'#FFFFFF',
    'mas_thumb_user_bg_color'=>'#429FFF',
    'mas_thumb_user_color'=>'#FFFFFF',
    );
  return $settings;
}

 public function get_sanitize_types(){
  $sanitize_types = array(
    'theme_name' => 'string',
    'default_theme'=> 'number',
    'feed_container_bg_color' => 'color',//*
    'feed_wrapper_width' => 'length',//
    'feed_container_width' => 'length',
    'feed_wrapper_bg_color' => 'color',//*
    'active_filter_bg_color' => 'color',
    'header_margin' => 'length_multi',//*
    'header_padding' => 'length_multi',//*
    'header_border_size' => 'length',//*
    'header_border_color' => 'color',//*
    'header_position' => 'position',//*
    'header_img_width' => 'number',//*
    'header_border_radius'  => 'number',////////////////////////////*
    'header_text_padding' => 'length',//*
    'header_text_color' => 'color',//*
    'header_font_weight' => 'number',//*
    'header_text_font_size' => 'length',///////////* 
    'header_text_font_style' => 'string',///////////////////
    'follow_btn_border_radius'=>'number',
    'follow_btn_padding'=>'number',
    'follow_btn_margin'=>'number',
    'follow_btn_bg_color'=>'color',
    'follow_btn_border_color'=>'color',
    'follow_btn_text_color'=>'color',
    'follow_btn_font_size'=>'number',
    'follow_btn_border_hover_color'=>'color',
    'follow_btn_text_hover_color'=>'color',
    'follow_btn_background_hover_color'=>'color',

    'user_padding' => 'length_multi',//*
    'user_horizontal_margin' => 'length',//*
    'user_border_size' => 'length',//*
    'user_border_color' => 'color',//*
    'user_img_width' => 'number',
    'user_border_radius' => 'number',
    'user_background_color' => 'color',//*
    'users_border_size' => 'length',//*
    'users_border_color' => 'color',//*
    'users_background_color' => 'color',//*
    
    /////////////////////////LightBox////////////////////////
    'users_text_color' => 'color',
    'users_font_weight' => 'number',
    'users_text_font_size' => 'length',
    'users_text_font_style' => 'string',
    'user_description_font_size' => 'length',
    'lightbox_overlay_bg_color'=>'color',
    'lightbox_overlay_bg_transparent'=>'number_max_100',
    'lightbox_bg_color'=>'color',
    'lightbox_ctrl_btn_height'=>'number',
    'lightbox_ctrl_btn_margin_top'=>'number',
    'lightbox_ctrl_btn_margin_left'=>'number',
    'lightbox_ctrl_btn_pos'=>'string',
    'lightbox_ctrl_cont_bg_color'=>'color',
    'lightbox_ctrl_cont_border_radius'=>'number',
    'lightbox_ctrl_cont_transparent'=>'number_max_100',
    'lightbox_ctrl_btn_align'=>'position',
    'lightbox_ctrl_btn_color'=>'color',
    'lightbox_ctrl_btn_transparent'=>'number_max_100',
    'lightbox_toggle_btn_height'=>'number',
    'lightbox_toggle_btn_width'=>'number',
    'lightbox_close_btn_border_radius'=>'number',
    'lightbox_close_btn_border_width'=>'number',
    'lightbox_close_btn_border_style'=>'string',
    'lightbox_close_btn_border_color'=>'color',
    'lightbox_close_btn_box_shadow'=>'css_box_shadow',
    'lightbox_close_btn_bg_color'=>'color',
    'lightbox_close_btn_transparent'=>'number_max_100',
    'lightbox_close_btn_width'=>'number',
    'lightbox_close_btn_height'=>'number',
    'lightbox_close_btn_top'=>'number_neg',
    'lightbox_close_btn_right'=>'number_neg',
    'lightbox_close_btn_size'=>'number',
    'lightbox_close_btn_color'=>'color',
    'lightbox_close_btn_full_color'=>'color',
    'lightbox_close_btn_hover_color'=>'color',
    'lightbox_comment_share_button_color'=>'color',
    'lightbox_rl_btn_style'=>'string',
    'lightbox_rl_btn_bg_color'=>'color',
    'lightbox_rl_btn_transparent'=>'number_max_100',
    'lightbox_rl_btn_box_shadow'=>'css_box_shadow',
    'lightbox_rl_btn_height'=>'number',
    'lightbox_rl_btn_width'=>'number',
    'lightbox_rl_btn_size'=>'number',
    'lightbox_close_rl_btn_hover_color'=>'color',
    'lightbox_rl_btn_color'=>'color',
    'lightbox_rl_btn_border_radius'=>'number',
    'lightbox_rl_btn_border_width'=>'number',
    'lightbox_rl_btn_border_style'=>'string',
    'lightbox_rl_btn_border_color'=>'color',
    'lightbox_filmstrip_pos'=>'position',
    'lightbox_filmstrip_thumb_margin'=>'length_multi',
    'lightbox_filmstrip_thumb_border_width'=>'number',
    'lightbox_filmstrip_thumb_border_style'=>'string',
    'lightbox_filmstrip_thumb_border_color'=>'color',
    'lightbox_filmstrip_thumb_border_radius'=>'number',
    'lightbox_filmstrip_thumb_active_border_width'=>'number',
    'lightbox_filmstrip_thumb_active_border_color'=>'color',
    'lightbox_filmstrip_thumb_deactive_transparent'=>'number_max_100',
    'lightbox_filmstrip_rl_btn_size'=>'number',
    'lightbox_filmstrip_rl_btn_color'=>'color',
    'lightbox_filmstrip_rl_bg_color'=>'color',
    'lightbox_info_pos'=>'position',
    'lightbox_info_align'=>'string',
    'lightbox_info_bg_color'=>'color',
    'lightbox_info_bg_transparent'=>'number_max_100',
    'lightbox_info_border_width'=>'number',
    'lightbox_info_border_style'=>'string',
    'lightbox_info_border_color'=>'color',
    'lightbox_info_border_radius'=>'number',
    'lightbox_info_padding'=>'length_multi',
    'lightbox_info_margin'=>'length_multi',
    'lightbox_title_color'=>'color',
    'lightbox_title_font_style'=>'string',
    'lightbox_title_font_weight'=>'string',
    'lightbox_title_font_size'=>'number',
    'lightbox_description_color'=>'color',
    'lightbox_description_font_style'=>'string',
    'lightbox_description_font_weight'=>'string',
    'lightbox_description_font_size'=>'number',
    'lightbox_info_height'=>'number_max_100',
    'lightbox_comment_width'=>'number',
    'lightbox_comment_pos'=>'string',
    'lightbox_comment_bg_color'=>'color',
    'lightbox_comment_font_size'=>'number',
    'lightbox_comment_font_color'=>'color',
    'lightbox_comment_font_style'=>'string',
    'lightbox_comment_author_font_size'=>'number',
    'lightbox_comment_author_font_color'=>'color',
    'lightbox_comment_author_font_color_hover'=>'color',
    'lightbox_comment_date_font_size'=>'number',
    'lightbox_comment_body_font_size'=>'number',
    'lightbox_comment_input_border_width'=>'number',
    'lightbox_comment_input_border_style'=>'string',
    'lightbox_comment_input_border_color'=>'color',
    'lightbox_comment_input_border_radius'=>'number',
    'lightbox_comment_input_padding'=>'length_multi',
    'lightbox_comment_input_bg_color'=>'color',
    'lightbox_comment_button_bg_color'=>'color',
    'lightbox_comment_button_padding'=>'length_multi',
    'lightbox_comment_button_border_width'=>'number',
    'lightbox_comment_button_border_style'=>'string',
    'lightbox_comment_button_border_color'=>'color',
    'lightbox_comment_button_border_radius'=>'number',
    'lightbox_comment_separator_width'=>'number',
    'lightbox_comment_separator_style'=>'string',
    'lightbox_comment_separator_color'=>'color',
    'lightbox_comment_load_more_color' =>'color',
    'lightbox_comment_load_more_color_hover' =>'color',
    /////////////////////////////////////////////////////////
    'th_photo_wrap_padding' => 'length',
    'th_photo_wrap_border_size' => 'length',
    'th_photo_wrap_border_color' => 'color',
    'th_photo_img_border_radius' => 'length',
    'th_photo_wrap_bg_color' => 'color',
    'th_photo_meta_bg_color' => 'color',
    'th_photo_meta_one_line' => 'bool',
    'th_like_text_color' => 'color',
    'th_comment_text_color' => 'color',
    'th_photo_caption_font_size' => 'length',
    'th_photo_caption_color' => 'color',
    'th_feed_item_margin' => 'length',
    'th_photo_caption_hover_color' =>'color',
    'th_like_comm_font_size' => 'length',
    'th_overlay_hover_color'=>'color',
    'th_overlay_hover_transparent'=>'number',
    'th_overlay_hover_icon_color'=>'color',
    'th_overlay_hover_icon_font_size'=>'length',
    'th_thumb_user_bg_color'=>'color',
    'th_thumb_user_color'=>'color',
    'th_photo_img_hover_effect' =>'string',
    
    /////////////////////////////////////////////////////////
    'mas_photo_wrap_padding' => 'length',
    'mas_photo_wrap_border_size' => 'length',
    'mas_photo_wrap_border_color' => 'color',
    'mas_photo_img_border_radius' => 'length',
    'mas_photo_wrap_bg_color' => 'color',
    'mas_photo_meta_bg_color' => 'color',
    'mas_photo_meta_one_line' => 'bool',
    'mas_like_text_color' => 'color',
    'mas_comment_text_color' => 'color',
    'mas_photo_caption_font_size' => 'length',
    'mas_photo_caption_color' => 'color',
    'mas_feed_item_margin' => 'length',
    'mas_photo_caption_hover_color' =>'color',
    'mas_like_comm_font_size' => 'length',
    'mas_overlay_hover_color'=>'color',
    'mas_overlay_hover_transparent'=>'number',
    'mas_overlay_hover_icon_color'=>'color',
    'mas_overlay_hover_icon_font_size'=>'length',
    'mas_thumb_user_bg_color'=>'color',
    'mas_thumb_user_color'=>'color',
    'mas_photo_img_hover_effect' => 'string',
    
    /////////////////////////////////////////////////    
    'blog_style_photo_wrap_padding' => 'length',
    'blog_style_photo_wrap_border_size' => 'length',
    'blog_style_photo_wrap_border_color' => 'color',
    'blog_style_photo_img_border_radius' => 'length',
    'blog_style_photo_wrap_bg_color' => 'color',
    'blog_style_photo_meta_bg_color' => 'color',
    'blog_style_photo_meta_one_line' => 'bool',
    'blog_style_like_text_color' => 'color',
    'blog_style_comment_text_color' => 'color',
    'blog_style_photo_caption_font_size' => 'length',
    'blog_style_photo_caption_color' => 'color',
    'blog_style_feed_item_margin' => 'length',
    'blog_style_photo_caption_hover_color' =>'color',
    'blog_style_like_comm_font_size' => 'length',

     /////////////////////////////////////////////////
    'image_browser_photo_wrap_padding' => 'length',
    'image_browser_photo_wrap_border_size' => 'length',
    'image_browser_photo_wrap_border_color' => 'color',
    'image_browser_photo_img_border_radius' => 'length',
    'image_browser_photo_wrap_bg_color' => 'color',
    'image_browser_photo_meta_bg_color' => 'color',
    'image_browser_photo_meta_one_line' => 'bool',
    'image_browser_like_text_color' => 'color',
    'image_browser_comment_text_color' => 'color',
    'image_browser_photo_caption_font_size' => 'length',
    'image_browser_photo_caption_color' => 'color',
    'image_browser_feed_item_margin' => 'length',
    'image_browser_photo_caption_hover_color' =>'color',
    'image_browser_like_comm_font_size' => 'length',
    
    //////////////////////////////////////////
    'load_more_position' => 'position',//*
    'load_more_padding' => 'length',//*
    'load_more_bg_color' => 'color',//*
    'load_more_border_radius' => 'length',//*
    'load_more_height' => 'length',//*
    'load_more_width' => 'length',//*
    'load_more_border_size' => 'length',//*
    'load_more_border_color' => 'color',//*
    'load_more_text_color' => 'color',//*
    /*load more icon*/
    'load_more_text_font_size' => 'length',//*
    'load_more_wrap_hover_color' => 'color',//*
    'pagination_ctrl_color' => 'color',
    'pagination_size' => 'length',
    'pagination_ctrl_margin' => 'length_multi',
    'pagination_ctrl_hover_color' => 'color',
    'pagination_position' => 'position',
    'pagination_position_vert'=>'position'
    );
  return $sanitize_types;
}

public static function get_theme_row($current_id){
  global $wpdb;
  $theme_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ". $wpdb->prefix.WDI_THEME_TABLE. " WHERE id ='%d' ",$current_id));
  return $theme_row;
}
public static function get_themes(){
  global $wpdb;
  $themes = WDILibrary::objectToArray($wpdb->get_results("SELECT `id`, `theme_name` FROM " . $wpdb->prefix.WDI_THEME_TABLE));
  foreach ($themes as $theme) {
    $output[$theme['id']] = $theme['theme_name']; 
  }
  return $output;
}
public function check_default($current_id){
  global $wpdb;
  $query = $wpdb->prepare("SELECT `default_theme`FROM " . $wpdb->prefix.WDI_THEME_TABLE . " WHERE id='%d'",$current_id);
  $row = WDILibrary::objectToArray($wpdb->get_row($query));
  return ($row['default_theme']);
}
}
  ?>