<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_post_grid_support{
	
	public function __construct(){

	}

    public function our_plugins(){

        $our_plugins = array(
            array(
                'title'=>'Post Grid',
                'link'=>'https://www.pickplugins.com/item/post-grid-create-awesome-grid-from-any-post-type-for-wordpress/',
                'thumb'=>'https://www.pickplugins.com/wp-content/uploads/2015/12/3814-post-grid-thumb-500x262.jpg',
            ),

            array(
                'title'=>'Woocommerce Products Slider',
                'link'=>'https://www.pickplugins.com/item/woocommerce-products-slider-for-wordpress/',
                'thumb'=>'https://www.pickplugins.com/wp-content/uploads/2016/03/4357-woocommerce-products-slider-thumb-500x250.jpg',
            ),

            array(
                'title'=>'Team Showcase',
                'link'=>'https://www.pickplugins.com/item/team-responsive-meet-the-team-grid-for-wordpress/',
                'thumb'=>'https://www.pickplugins.com/wp-content/uploads/2016/06/5145-team-thumb-500x250.jpg',
            ),

            array(
                'title'=>'Job Board Manager',
                'link'=>'https://wordpress.org/plugins/job-board-manager/',
                'thumb'=>'https://www.pickplugins.com/wp-content/uploads/2015/08/3466-job-board-manager-thumb-500x250.png',
            ),

            array(
                'title'=>'Wishlist for WooCommerce',
                'link'=>'https://www.pickplugins.com/item/woocommerce-wishlist/',
                'thumb'=>'https://www.pickplugins.com/wp-content/uploads/2017/10/12047-woocommerce-wishlist.png',
            ),

            array(
                'title'=>'Breadcrumb',
                'link'=>'https://www.pickplugins.com/item/breadcrumb-awesome-breadcrumbs-style-navigation-for-wordpress/',
                'thumb'=>'https://www.pickplugins.com/wp-content/uploads/2016/03/4242-breadcrumb-500x252.png',
            ),

            array(
                'title'=>'Pricing Table',
                'link'=>'https://www.pickplugins.com/item/pricing-table/',
                'thumb'=>'https://www.pickplugins.com/wp-content/uploads/2016/10/7042-pricing-table-thumbnail-500x250.png',
            ),

        );

        return apply_filters('post_grid_our_plugins', $our_plugins);


    }


    public function video_tutorials(){


        $tutorials = array(
            array(
                'title'=>__('Latest Version 2.0.46 Overview', 'post-grid'),
                'url'=>'https://youtu.be/YVtsIbEb9zs',
                'keywords'=>'overview latest version',
            ),

            array(
                'title'=>__('How to create post grid', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=g5kxtJIopXs',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Custom read more text', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=LY7IjS7SFNk',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Remove read more text', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=ZcS2vRcTe4A',
            ),
            array(
                'title'=>__('Excerpt word count', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=gZ6E3UiKQqk',
                'keywords'=>'',
            ),

            array(
                'title'=>__('Custom media height', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=TupF2TpHHFA',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Item custom padding margin', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=HRZpoib1VvI',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Grid item height', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=ydqlgzfsboQ',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Column Width or column number', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=ZV8hd1ij5Wo',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Post title linked', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=oUVZB9F5d4U',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Featured image linked to post', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=stGOJLwUF-k',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Query post by categories or terms', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=xYzqtWRg8W4',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Query post by tags or terms', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=RKb-B_Q72Ak',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Display search input', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=psJR65Fmc_s',
                'keywords'=>'',
            ),
            array(
                'title'=>__('Work with layout editor', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=9bQc7q40jMc',
                'keywords'=>'',
            ),
            array(
                'title'=>__('[ Pro ] Create filterable grid', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=Zg2r7idmEm0',
                'keywords'=>'',
            ),
            array(
                'title'=>__('[ Pro ] Filterable custom filter type data logic', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=5Dueav6Yoyc',
                'keywords'=>'',
            ),

            array(
                'title'=>__('[ Pro ] Filterable custom all text', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=JvVkAyoXC3g',
                'keywords'=>'',
            ),
            array(
                'title'=>__('[ Pro ] Filterable default active filter', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=h2rbyZNhMhU',
                'keywords'=>'',
            ),

            array(
                'title'=>__('[ Pro ] Filterable custom filter', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=e8phxNKIRsU',
                'keywords'=>'',
            ),

            array(
                'title'=>__('[ Pro ] Filterable dropdown single filter', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=ZHY8qf-z3H0',
                'keywords'=>'',
            ),

            array(
                'title'=>__('[ Pro ] Filterable display sort filter', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=21TYNsp2OPI',
                'keywords'=>'',
            ),

            array(
                'title'=>__('[ Pro ] Filterable multi filter', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=uRcfd_R9YCM',
                'keywords'=>'',
            ),



            array(
                'title'=>__('[ Pro ] Post grid on archive tags', 'post-grid'),
                'url'=>'https://youtu.be/lNyAjva_UXo',
                'keywords'=>'',
            ),




            array(
                'title'=>__('[ Pro ] Query post by meta field', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=0AIDNJvZGR0',
                'keywords'=>'',
            ),


            array(
                'title'=>__('[ Pro ] Multi skin', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=YzUs_P3cFCo',
                'keywords'=>'',
            ),
            array(
                'title'=>__('[ Pro ] Sticky post query', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=nVIOUbVjML4',
                'keywords'=>'',
            ),
            array(
                'title'=>__('[ Pro ] Masonry layout', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=qYjbv2euNpE',
                'keywords'=>'',
            ),
            array(
                'title'=>__('[ Pro ] Post query by author', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=KtoGa8NB3ig',
                'keywords'=>'',
            ),
            array(
                'title'=>__('[ Pro ] Create glossary grid', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=MKL4EZ-WYTs',
                'keywords'=>'',
            ),
            array(
                'title'=>__('[ Pro ] Post carousel slider', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=A0bZ_luBtQQ',
                'keywords'=>'',
            ),

            array(
                'title'=>__('[ Pro ] Grid layout type', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=58piQVkDZN4',
                'keywords'=>'',
            ),
            array(
                'title'=>__('[ Pro ] Thumbnail youtube', 'post-grid'),
                'url'=>'https://www.youtube.com/watch?v=Zm5vD15yvNM',
                'keywords'=>'',
            ),
            array(
                'title'=>__('How to Create a Post Grid?', 'post-grid'),
                'url'=>'https://www.pickplugins.com/documentation/post-grid/faq/how-to-create-a-post-grid/',
            ),
            array(
                'title'=>__('How to upgrade to premium?', 'post-grid'),
                'url'=>'https://www.pickplugins.com/documentation/post-grid/upgrade-to-premium/',
            ),

            array(
                'title'=>__('Post grid on archive page?', 'post-grid'),
                'url'=>'https://www.pickplugins.com/documentation/post-grid/faq/post-grid-for-archive-page/',
            ),


            array(
                'title'=>__('How to display HTML/Shortcode via layout editor ?', 'post-grid'),
                'url'=>'https://www.pickplugins.com/documentation/post-grid/faq/layout-editor-how-at-add-htmlshortcode/',
            ),

        );


        return apply_filters('post_grid_video_tutorials', $tutorials);


    }



    public function faq(){
        $faq = array(

        );


        return apply_filters('post_grid_faq', $faq);



    }





	
	
}

