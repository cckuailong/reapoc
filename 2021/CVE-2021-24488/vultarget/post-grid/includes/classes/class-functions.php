<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

class class_post_grid_functions{
	
	public function __construct(){
		
		
		}


    function get_query_orderby(){

        $args['ID'] = __('ID','post-grid');
        $args['author'] = __('Author','post-grid');
        $args['title'] = __('Title','post-grid');
        $args['name'] = __('Name','post-grid');
        $args['type'] = __('Type','post-grid');
        $args['date'] = __('Date','post-grid');
        $args['post_date'] = __('post_date','post-grid');
        $args['modified'] = __('modified','post-grid');
        $args['parent'] = __('Parent','post-grid');
        $args['rand'] = __('Random','post-grid');
        $args['comment_count'] = __('Comment count','post-grid');
        $args['menu_order'] = __('Menu order','post-grid');
        $args['meta_value'] = __('Meta value','post-grid');
        $args['meta_value_num'] = __('Meta Value(number)','post-grid');
        $args['post__in'] = __('post__in','post-grid');
        $args['post_name__in'] = __('post_name__in','post-grid');

        return apply_filters('post_grid_orderby', $args);
    }

    function get_post_status(){

        $args['publish'] = __('Publish','post-grid');
        $args['pending'] = __('Pending','post-grid');
        $args['draft'] = __('Draft','post-grid');
        $args['auto-draft'] = __('Auto draft','post-grid');
        $args['future'] = __('Future','post-grid');
        $args['private'] = __('Private','post-grid');
        $args['inherit'] = __('Inherit','post-grid');
        $args['trash'] = __('Trash','post-grid');
        $args['any'] = __('Any','post-grid');


        return apply_filters('post_grid_post_status', $args);
    }

    function addons_list(){

        $args['pro'] = array('title' => __('Post Grid Pro','post-grid'), 'thumb'=> post_grid_plugin_url.'assets/admin/images/pro.png', 'item_link' => 'https://www.pickplugins.com/item/post-grid-create-awesome-grid-from-any-post-type-for-wordpress/?ref=dashboard');

        $args['search'] = array('title' => __('Search & Filter','post-grid'), 'thumb'=> post_grid_plugin_url.'assets/admin/images/search.png', 'item_link' => 'https://github.com/pickplugins/post-grid-search');
        $args['post-templates'] = array('title' => __('Post/Page Templates','post-grid'), 'thumb'=>post_grid_plugin_url.'assets/admin/images/post-templates.png',  'item_link' => 'https://github.com/pickplugins/post-grid-post-templates');
        $args['loop-ads'] = array('title' => __('Loop ads','post-grid'), 'thumb'=> post_grid_plugin_url.'assets/admin/images/loop-ads.png', 'item_link' => 'https://github.com/pickplugins/post-grid-loop-ads');



        return apply_filters('post_grid_extensions', $args);
    }
	
	public function media_source(){
		
						$media_source = array(

						    'featured_image' =>array('id'=>'featured_image','title'=>__('Featured Image', 'post-grid'),'checked'=>'yes'),
                            'first_image'=>array('id'=>'first_image','title'=>__('First images from content', 'post-grid'),'checked'=>'yes'),
                            'empty_thumb'=>array('id'=>'empty_thumb','title'=>__('Empty thumbnail', 'post-grid'),'checked'=>'yes'),


						);
											
						$media_source = apply_filters('post_grid_filter_media_source', $media_source);				
											
						return $media_source;
											
		
		}
	
	
	public function layout_items(){



        $layout_items['general'] = array(

            'name'=>'General',
            'description'=>'Default WordPress items for post.',
            'items'=>array(

                'title'=>array(
                    'name'=>'Title',
                    'dummy_html'=>'Lorem Ipsum is simply.',
                    'css'=>'display: block;font-size: 21px;line-height: normal;padding: 5px 10px;text-align: left;',
                    ),

                'title_link'=>array(
                                'name'=>'Title with Link',
                                'dummy_html'=>'<a href="#">Lorem Ipsum is simply</a>',
                                'css'=>'display: block;font-size: 21px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'content'=>array(
                                'name'=>'Content',
                                'dummy_html'=>'Lorem',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'read_more'=>array(
                                'name'=>'Read more',
                                'dummy_html'=>'<a href="#">Read more</a>',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'thumb'=>array(
                                'name'=>'Thumbnail',
                                'dummy_html'=>'<img style="width:100%;" src="'.post_grid_plugin_url.'assets/admin/images/thumb.png" />',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'thumb_link'=>array(
                                'name'=>'Thumbnail with Link',
                                'dummy_html'=>'<a href="#"><img style="width:100%;" src="'.post_grid_plugin_url.'assets/admin/images/thumb.png" /></a>',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'excerpt'=>array(
                                'name'=>'Excerpt',
                                'dummy_html'=>'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'excerpt_read_more'=>array(
                                'name'=>'Excerpt with Read more',
                                'dummy_html'=>'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text <a href="#">Read more</a>',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'post_date'=>array(
                                'name'=>'Post date',
                                'dummy_html'=>'18/06/2015',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'author'=>array(
                                'name'=>'Author',
                                'dummy_html'=>'PickPlugins',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'author_link'=>array(
                                'name'=>'Author with Link',
                                'dummy_html'=>'Lorem',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'categories'=>array(
                                'name'=>'Categories',
                                'dummy_html'=>'<a hidden="#">Category 1</a> <a hidden="#">Category 2</a>',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'tags'=>array(
                                'name'=>'Tags',
                                'dummy_html'=>'<a hidden="#">Tags 1</a> <a hidden="#">Tags 2</a>',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'comments_count'=>array(
                                'name'=>'Comments Count',
                                'dummy_html'=>'3 Comments',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'comments'=>array(
                                'name'=>'Comments',
                                'dummy_html'=>'Lorem',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'rating_widget'=>array(
                                'name'=>'Rating-Widget: Star Review System',
                                'dummy_html'=>'Lorem',
                                'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                                ),
                'share_button'=>array(
                    'name'=>'Share button',
                    'dummy_html'=>'<i class="fa fa-facebook-square"></i> <i class="fa fa-twitter-square"></i> <i class="fa fa-google-plus-square"></i>',
                    'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                ),

                'hr'=>array(
                    'name'=>'Horizontal line',
                    'dummy_html'=>'<hr />',
                    'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                ),

                'five_star'=>array(
                    'name'=>'Five star',
                    'dummy_html'=>'Star',
                    'css'=>'display: block;font-size: 13px;line-height: normal;padding: 5px 10px;text-align: left;',
                ),


            ),

        );


		$layout_items = apply_filters('post_grid_filter_layout_items', $layout_items);
		
		return $layout_items;
		}
	
	
	public function layout_content_list(){
		
		$layout_content_list = array(
		
						'flat'=>array(
								'0'=>array('key'=>'title_link', 'char_limit'=>'20', 'name'=>'Title with linked', 'css'=>'display: block;font-size: 21px;line-height: normal;padding: 5px 10px;text-align: left; text-decoration: none;', 'css_hover'=>'', ),
								'1'=>array('key'=>'excerpt', 'char_limit'=>'20', 'name'=>'Excerpt', 'css'=>'display: block;font-size: 14px;padding: 5px 10px;text-align: left;', 'css_hover'=>''),
								'2'=>array('key'=>'read_more', 'name'=>'Read more', 'css'=>'display: block;font-size: 12px;font-weight: bold;padding: 0 10px;text-align: left;text-decoration: none;', 'css_hover'=>''),

                            ),
									
						'flat-center'=>array(												
								'0'=>array('key'=>'title_link', 'char_limit'=>'20', 'name'=>'Title with linked', 'css'=>'display: block;font-size: 21px;line-height: normal;padding: 5px 10px;text-align: center;text-decoration: none;', 'css_hover'=>''),
								'1'=>array('key'=>'excerpt', 'char_limit'=>'20', 'name'=>'Excerpt', 'css'=>'display: block;font-size: 14px;padding: 5px 10px;text-align: center;', 'css_hover'=>''),
								'2'=>array('key'=>'read_more', 'name'=>'Read more', 'css'=>'display: block;font-size: 12px;font-weight: bold;padding: 0 10px;text-align: center;', 'css_hover'=>''),

									),
									
						'flat-right'=>array(												
								'0'=>array('key'=>'title_link', 'char_limit'=>'20', 'name'=>'Title with linked', 'css'=>'display: block;font-size: 21px;line-height: normal;padding: 5px 10px;text-align: right;text-decoration: none;', 'css_hover'=>''),
								'1'=>array('key'=>'excerpt', 'char_limit'=>'20', 'name'=>'Excerpt', 'css'=>'display: block;font-size: 14px;padding: 5px 10px;text-align: right;', 'css_hover'=>''),
								'2'=>array('key'=>'read_more', 'name'=>'Read more', 'css'=>'display: block;font-size: 12px;font-weight: bold;padding: 0 10px;text-align: right;', 'css_hover'=>''),					
									),
									
						'flat-left'=>array(												
								'0'=>array('key'=>'title_link', 'char_limit'=>'20', 'name'=>'Title with linked', 'css'=>'display: block;font-size: 21px;line-height: normal;padding: 5px 10px;text-align: left;text-decoration: none;', 'css_hover'=>''),
								
								'1'=>array('key'=>'excerpt', 'char_limit'=>'20', 'name'=>'Excerpt', 'css'=>'display: block;font-size: 14px;padding: 5px 10px;text-align: left;', 'css_hover'=>''),
								'2'=>array('key'=>'read_more', 'name'=>'Read more', 'css'=>'display: block;font-size: 12px;font-weight: bold;padding: 0 10px;text-align: left;', 'css_hover'=>'')
									),
									
						'wc-center-price'=>array(													
								'0'=>array('key'=>'title_link', 'char_limit'=>'20', 'name'=>'Title with linked', 'css'=>'display: block;font-size: 21px;line-height: normal;padding: 5px 10px;text-align: center;text-decoration: none;', 'css_hover'=>''),
								'1'=>array('key'=>'wc_full_price', 'name'=>'Price', 'css'=>'background:#f9b013;color:#fff;display: inline-block;font-size: 20px;line-height:normal;padding: 0 17px;text-align: center;', 'css_hover'=>''),
								'2'=>array('key'=>'excerpt', 'char_limit'=>'20', 'name'=>'Excerpt', 'css'=>'display: block;font-size: 14px;padding: 5px 10px;text-align: center;', 'css_hover'=>''),
									),								
									
						'wc-center-cart'=>array(													
								'0'=>array('key'=>'title_link', 'char_limit'=>'20', 'name'=>'Title with linked', 'css'=>'display: block;font-size: 21px;line-height: normal;padding: 5px 10px;text-align: center;text-decoration: none;', 'css_hover'=>''),
								'1'=>array('key'=>'wc_gallery', 'name'=>'Add to Cart', 'css'=>'color:#555;display: inline-block;font-size: 13px;line-height:normal;padding: 0 17px;text-align: center;', 'css_hover'=>''),
								
								'2'=>array('key'=>'excerpt', 'char_limit'=>'20', 'name'=>'Excerpt', 'css'=>'display: block;font-size: 14px;padding: 5px 10px;text-align: center;', 'css_hover'=>''),
									),										

						);
		
		$layout_content_list = apply_filters('post_grid_filter_layout_content_list', $layout_content_list);
		
		
		return $layout_content_list;
		}	
	

	
	public function layout_content($layout){
		
		$layout_content = $this->layout_content_list();
		
		return $layout_content[$layout];
		}	
		

	public function skins(){
		
		$skins = array(


		
            'flat'=> array(
                'slug'=>'flat',
                'name'=>'Flat',
                'thumb_url'=>'',
                ),
            'flip-x'=> array(
                'slug'=>'flip-x',
                'name'=>'Flip-x',
                'thumb_url'=>'',
                ),
            'spinright'=>array(
                'slug'=>'spinright',
                'name'=>'SpinRight',
                'thumb_url'=>'',
            ),
            'thumbgoleft'=>array(
                'slug'=>'thumbgoleft',
                'name'=>'ThumbGoLeft',
                'thumb_url'=>'',
            ),
            'thumbrounded'=>array(
                'slug'=>'thumbrounded',
                'name'=>'ThumbRounded',
                'thumb_url'=>'',
            ),
            'contentbottom'=>array(
                'slug'=>'contentbottom',
                'name'=>'ContentBottom',
                'thumb_url'=>'',
            ),

										



            );
		
		$skins = apply_filters('post_grid_filter_skins', $skins);	
		
		return $skins;
		
		}





}
	
