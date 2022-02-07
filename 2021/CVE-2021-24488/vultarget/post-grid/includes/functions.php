<?php
if ( ! defined('ABSPATH')) exit;  // if direct access


//add_image_size( 'custom-size', 435, 435, true );
//add_image_size( 'center-435', 220, 220, array( 'center', 'center' ) );








function post_grid_get_first_post($post_type = 'post'){

    $args = array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => 1,
    );

    $post_id ='';

    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) :
        while ($wp_query->have_posts()) : $wp_query->the_post();
            $product_id = get_the_id();
            return $product_id;
        endwhile;

    endif;
}








function post_grid_add_shortcode_column( $columns ) {
    return array_merge( $columns, 
        array( 'shortcode' => __( 'Shortcode', 'post-grid' ) ) );
}
add_filter( 'manage_post_grid_posts_columns' , 'post_grid_add_shortcode_column' );


function post_grid_posts_shortcode_display( $column, $post_id ) {
    if ($column == 'shortcode'){
		?>
        <input style="background:#bfefff" type="text" onClick="this.select();" value="[post_grid <?php echo 'id=&quot;'.$post_id.'&quot;';?>]" /><br />
      <textarea cols="50" rows="1" style="background:#bfefff" onClick="this.select();" ><?php echo '<?php echo do_shortcode("[post_grid id='; echo "'".$post_id."']"; echo '"); ?>'; ?></textarea>
        <?php		
		
    }
}
add_action( 'manage_post_grid_posts_custom_column' , 'post_grid_posts_shortcode_display', 10, 2 );







function post_grid_get_media($item_post_id, $media_source, $featured_img_size, $thumb_linked){

    $item_post_permalink = apply_filters('post_grid_item_post_permalink', get_permalink($item_post_id));

    $post_grid_post_settings = get_post_meta($item_post_id, 'post_grid_post_settings');
    $item_thumb_placeholder = apply_filters('post_grid_item_thumb_placeholder', post_grid_plugin_url.'assets/frontend/images/placeholder.png');

    $custom_thumb_source = isset($post_grid_post_settings[0]['custom_thumb_source']) ? $post_grid_post_settings[0]['custom_thumb_source'] : $item_thumb_placeholder;
    $thumb_custom_url = isset($post_grid_post_settings[0]['thumb_custom_url']) ? $post_grid_post_settings[0]['thumb_custom_url'] : '';
    $font_awesome_icon = isset($post_grid_post_settings[0]['font_awesome_icon']) ? $post_grid_post_settings[0]['font_awesome_icon'] : '';
    $font_awesome_icon_color = isset($post_grid_post_settings[0]['font_awesome_icon_color']) ? $post_grid_post_settings[0]['font_awesome_icon_color'] : '#737272';
    $font_awesome_icon_size = isset($post_grid_post_settings[0]['font_awesome_icon_size']) ? $post_grid_post_settings[0]['font_awesome_icon_size'] : '50px';
    $custom_youtube_id = isset($post_grid_post_settings[0]['custom_youtube_id']) ? $post_grid_post_settings[0]['custom_youtube_id'] : '';
    $custom_vimeo_id = isset($post_grid_post_settings[0]['custom_vimeo_id']) ? $post_grid_post_settings[0]['custom_vimeo_id'] : '';
    $custom_dailymotion_id = isset($post_grid_post_settings[0]['custom_dailymotion_id']) ? $post_grid_post_settings[0]['custom_dailymotion_id'] : '';
    $custom_mp3_url = isset($post_grid_post_settings[0]['custom_mp3_url']) ? $post_grid_post_settings[0]['custom_mp3_url'] : '';
    $custom_soundcloud_id = isset($post_grid_post_settings[0]['custom_soundcloud_id']) ? $post_grid_post_settings[0]['custom_soundcloud_id'] : '';

		//echo '<pre>'.var_export($post_grid_post_settings).'</pre>';
		
    $html_thumb = '';
		

    if($media_source == 'featured_image'){
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($item_post_id), $featured_img_size );
        $alt_text = get_post_meta(get_post_thumbnail_id($item_post_id), '_wp_attachment_image_alt', true);
        $thumb_url = isset($thumb['0']) ? $thumb['0'] : '';

        if(!empty($thumb_url)){
            if($thumb_linked=='yes'){
                if(!empty($thumb_custom_url)){
                    $html_thumb.= '<a href="'.$thumb_custom_url.'"><img alt="'.$alt_text.'" src="'.$thumb_url.'" /></a>';
                    }
                else{
                    $html_thumb.= '<a href="'.$item_post_permalink.'"><img alt="'.$alt_text.'" src="'.$thumb_url.'" /></a>';
                    }
            }
            else{
                $html_thumb.= '<img alt="'.$alt_text.'" src="'.$thumb_url.'" />';
            }
        }
        else{
            $html_thumb.= '';
        }
    }
    elseif($media_source == 'empty_thumb'){

        if($thumb_linked=='yes'){
            $html_thumb.= '<a class="custom" href="'.$item_post_permalink.'"><img src="'.post_grid_plugin_url.'assets/frontend/images/placeholder.png" /></a>';
        }
        else{
            $html_thumb.= '<img class="custom" src="'.post_grid_plugin_url.'assets/frontend/images/placeholder.png" />';
        }
    }
    elseif($media_source == 'custom_thumb'){
        if(!empty($custom_thumb_source)){
            if($thumb_linked=='yes'){
                $html_thumb.= '<a href="'.$item_post_permalink.'"><img src="'.$custom_thumb_source.'" /></a>';
            }
            else{
                $html_thumb.= '<img src="'.$custom_thumb_source.'" />';
            }
        }
    }
    elseif($media_source == 'font_awesome'){
        if(!empty($custom_thumb_source)){
            if($thumb_linked=='yes'){
                $html_thumb.= '<a href="'.$item_post_permalink.'"><i style="color:'.$font_awesome_icon_color.';font-size:'.$font_awesome_icon_size.'" class="fa '.$font_awesome_icon.'"></i></a>';
            }
            else{
                $html_thumb.= '<i style="color:'.$font_awesome_icon_color.';font-size:'.$font_awesome_icon_size.'" class="fa '.$font_awesome_icon.'"></i>';
            }
        }
    }
    elseif($media_source == 'first_image'){
        //global $post, $posts;
        $post = get_post($item_post_id);
        $post_content = $post->post_content;
        $first_img = '';
        ob_start();
        ob_end_clean();
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);

        if(!empty($matches[1][0]))
        $first_img = $matches[1][0];

        if(empty($first_img)) {
            $html_thumb.= '';
            }
        else{

            if($thumb_linked=='yes'){
                $html_thumb.= '<a href="'.$item_post_permalink.'"><img src="'.$first_img.'" /></a>';
            }
            else{
                $html_thumb.= '<img src="'.$first_img.'" />';
            }
        }
    }
    elseif($media_source == 'first_gallery'){

        $gallery = get_post_gallery( $item_post_id, false );

        if(!empty($gallery)){
        $html_thumb.= '<div class="gallery owl-carousel">';

        if(!empty($gallery['ids'])){
            $ids = $gallery['ids'];
            $ids = explode(',',$ids);


        }
        else{
            $ids = array();
        }


        foreach($ids as $id ){

            $src = wp_get_attachment_url( $id);
            $alt_text = get_post_meta($id, '_wp_attachment_image_alt', true);
            $html_thumb .= '<img src="'.$src.'" class="gallery-item" alt="'.$alt_text.'" />';
        }

        $html_thumb.= '</div>';
        }
    }
    elseif($media_source == 'first_youtube'){
        $post = get_post($item_post_id);
        $post_type = $post->post_type;

        if($post_type=='page'){
            $content = '';
            $html_thumb.= '';
        }
        else{
            $content = do_shortcode( $post->post_content );
        }

        $content = apply_filters('the_content', $content);
        $embeds = get_media_embedded_in_content( $content );


        foreach($embeds as $key=>$embed){

            if(strchr($embed,'youtube')){
                $embed_youtube = $embed;
                }
        }

        if(!empty($embed_youtube) ){
            $html_thumb.= $embed_youtube;
            }
        else{
            $html_thumb.= '';
            }

        }

    elseif($media_source == 'first_vimeo'){

        $post = get_post($item_post_id);
        $post_type = $post->post_type;
        //var_dump($post_type);

        if($post_type=='page'){
            $content = '';
            $html_thumb.= '';
            }
        else{

            $content = do_shortcode( $post->post_content );
            }
        $embeds = get_media_embedded_in_content( $content );

        foreach($embeds as $key=>$embed){

            if(strchr($embed,'vimeo')){

                $embed_youtube = $embed;
                }

            }

        if(!empty($embed_youtube) ){
            $html_thumb.= $embed_youtube;
            }
        else{
            $html_thumb.= '';
            }


    }
    elseif($media_source == 'first_dailymotion'){

        $post = get_post($item_post_id);
        $post_type = $post->post_type;
        //var_dump($post_type);

        if($post_type=='page'){
            $content = '';
            $html_thumb.= '';
            }
        else{

            $content = do_shortcode( $post->post_content );
            }

        $content = apply_filters('the_content', $content);
        $embeds = get_media_embedded_in_content( $content );

        foreach($embeds as $key=>$embed){

            if(strchr($embed,'dailymotion')){

                $embed_youtube = $embed;
                }

            }

        if(!empty($embed_youtube) ){
            $html_thumb.= $embed_youtube;
            }
        else{
            $html_thumb.= '';
            }

        }




    elseif($media_source == 'first_mp3'){

        $post = get_post($item_post_id);
        $post_type = $post->post_type;
        //var_dump($post_type);

        if($post_type=='page'){
            $content = '';
            $html_thumb.= '';
            }
        else{

            $content = do_shortcode( $post->post_content );
            }

        $content = apply_filters('the_content', $content);
        $embeds = get_media_embedded_in_content( $content );

        foreach($embeds as $key=>$embed){

            if(strchr($embed,'mp3')){

                $embed_youtube = $embed;
                }

            }

        if(!empty($embed_youtube) ){
            $html_thumb.= $embed_youtube;
            }
        else{
            $html_thumb.= '';
            }

        }

    elseif($media_source == 'first_soundcloud'){

        $post = get_post($item_post_id);
        $post_type = $post->post_type;
        //var_dump($post_type);

        if($post_type=='page'){
            $content = '';
            $html_thumb.= '';
            }
        else{

            $content = do_shortcode( $post->post_content );
            }

        $content = apply_filters('the_content', $content);
        $embeds = get_media_embedded_in_content( $content );

        foreach($embeds as $key=>$embed){

            if(strchr($embed,'soundcloud')){

                $embed_youtube = $embed;
                }

            }

        if(!empty($embed_youtube) ){
            $html_thumb.= $embed_youtube;
            }
        else{
            $html_thumb.= '';
            }

        }


    elseif($media_source == 'custom_youtube'){

            if(!empty($custom_youtube_id)){
                $html_thumb.= '<iframe frameborder="0" allowfullscreen="" src="http://www.youtube.com/embed/'.$custom_youtube_id.'?feature=oembed"></iframe>';

                }


        }



    elseif($media_source == 'custom_vimeo'){

            if(!empty($custom_vimeo_id)){
                $html_thumb.= '<iframe frameborder="0" allowfullscreen="" mozallowfullscreen="" webkitallowfullscreen="" src="https://player.vimeo.com/video/'.$custom_vimeo_id.'"></iframe>';

                }


        }


    elseif($media_source == 'custom_dailymotion'){

            if(!empty($custom_dailymotion_id)){
                $html_thumb.= '<iframe frameborder="0" allowfullscreen="" mozallowfullscreen="" webkitallowfullscreen="" src="//www.dailymotion.com/embed/video/'.$custom_dailymotion_id.'"></iframe>';

                }


        }



    elseif($media_source == 'custom_mp3'){

            if(!empty($custom_mp3_url)){
                $html_thumb.= do_shortcode('[audio src="'.$custom_mp3_url.'"]');

                }

        }



    elseif($media_source == 'custom_video'){

//var_dump($post_grid_post_settings);

        $video_html = '';


        if(!empty($post_grid_post_settings[0]['custom_video_MP4'])):

            $video_html .= 'mp4="'.$post_grid_post_settings[0]['custom_video_MP4'].'"';

        elseif (!empty($post_grid_post_settings[0]['custom_video_WEBM'])):

            $video_html .= 'webm="'.$post_grid_post_settings[0]['custom_video_WEBM'].'"';

        elseif (!empty($post_grid_post_settings[0]['custom_video_OGV'])):

            $video_html .= 'ogv="'.$post_grid_post_settings[0]['custom_video_OGV'].'"';

        endif;

            $html_thumb.= do_shortcode('[video '.$video_html.'][/video]');



    }


    elseif($media_source == 'custom_soundcloud'){

            if(!empty($custom_soundcloud_id)){
                $html_thumb.= '<iframe width="100%" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.$custom_soundcloud_id.'&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false&amp;visual=true"></iframe>';

                }

        }




    return $html_thumb;


	
	
	}






function post_grid_media($post_id, $args ){

    $source_id = $args['source_id'] ;
    $source_args = $args['source_args'] ;
    $post_settings = $args['post_settings'] ;

    $thumb_linked = '';

    $item_post_permalink = apply_filters('post_grid_item_post_permalink', get_permalink($post_id));



    $html_thumb = '';

    ob_start();


    if($source_id == 'featured_image'){


        $image_size = isset($source_args['image_size']) ? $source_args['image_size'] : 'large';
        $link_to = isset($source_args['link_to']) ? $source_args['link_to'] : 'post_link';
        $link_target = isset($source_args['link_target']) ? $source_args['link_target'] : '';

        $thumb_custom_url = isset($post_settings['thumb_custom_url']) ? $post_settings['thumb_custom_url'] : '';


        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), $image_size );
        $alt_text = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
        $thumb_url = isset($thumb['0']) ? $thumb['0'] : '';

        if(!empty($thumb_url)){
            if($link_to=='post_link'){
                if(!empty($thumb_custom_url)){
                    $html_thumb.= '<a target="'.$link_target.'" href="'.$thumb_custom_url.'"><img alt="'.$alt_text.'" src="'.$thumb_url.'" /></a>';
                }
                else{
                    $html_thumb.= '<a target="'.$link_target.'" href="'.$item_post_permalink.'"><img alt="'.$alt_text.'" src="'.$thumb_url.'" /></a>';
                }
            }
            else{
                $html_thumb.= '<img alt="'.$alt_text.'" src="'.$thumb_url.'" />';
            }
        }
        else{
            $html_thumb.= '';
        }
    }



    elseif($source_id == 'empty_thumb'){

        $link_to = isset($source_args['link_to']) ? $source_args['link_to'] : 'post_link';
        $link_target = isset($source_args['link_target']) ? $source_args['link_target'] : '';

        $default_thumb_src = isset($source_args['default_thumb_src']) ? $source_args['default_thumb_src'] : post_grid_plugin_url.'assets/frontend/images/placeholder.png';


        if($link_to=='post_link'){
            $html_thumb.= '<a target="'.$link_target.'" class="custom" href="'.$item_post_permalink.'"><img src="'.$default_thumb_src.'" /></a>';
        }
        else{
            $html_thumb.= '<img class="custom" src="'.$default_thumb_src.'" />';
        }
    }


    elseif($source_id == 'first_image'){

        $link_to = isset($source_args['link_to']) ? $source_args['link_to'] : 'post_link';
        $link_target = isset($source_args['link_target']) ? $source_args['link_target'] : '';


        //global $post, $posts;
        $post = get_post($post_id);
        $post_content = $post->post_content;
        $first_img = '';
        ob_start();
        ob_end_clean();
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);

        if(!empty($matches[1][0]))
            $first_img = $matches[1][0];

        if(empty($first_img)) {
            $html_thumb.= '';
        }
        else{

            if($link_to=='post_link'){
                $html_thumb.= '<a target="'.$link_target.'" href="'.$item_post_permalink.'"><img src="'.$first_img.'" /></a>';
            }
            else{
                $html_thumb.= '<img src="'.$first_img.'" />';
            }
        }
    }
    elseif($source_id == 'siteorigin_first_image'){

        $link_to = isset($source_args['link_to']) ? $source_args['link_to'] : 'post_link';
        $link_target = isset($source_args['link_target']) ? $source_args['link_target'] : '';

        //global $post, $posts;
        $post = get_post($post_id);
        /**$post_content = $post->post_content; */
        $post_content = htmlspecialchars_decode($post->post_content,ENT_QUOTES);
        $first_img = '';
        ob_start();
        ob_end_clean();


        if ( class_exists( 'SiteOrigin_Widgets_Bundle' ) ){
            $output = str_replace( array( '\/' ), "\\" ,$post_content); // SiteOrigin adds \/ combinations
            $output = str_replace( array( 'src=\\' ), 'src=',$output);   // SiteOrigin adds \\
            $output = str_replace( array( '"url":' ), ' <img src=',$output);  //SiteOrigin does change the src to url
            $output = str_replace( array( '&lt;img src=&quot;' ), '<img src="',$output);    //SiteOrigin does add &&lt and &quot combinations which are not removed
            $output = str_replace( array( '&quot;"' ), '"',$output); // Remove this quot combination
            $output = str_replace( array( '&quot;' ), '',$output);   // Remove this quot combination

            /** search for post containing SiteOrigin image */
            $findme='"image":';
            $start= strpos($post_content, $findme);
            $findme = ',"image_fallback"';
            $end = strpos($post_content, $findme);
            $lengte= $end-$start;
            $search=(substr($post_content,$start,$lengte));
            /** error_log('Gevonden:' .$search); */
            if ($search !=""){
                /** split the text */
                $stringParts = explode(":", $search);
                $firstPart = $stringParts[0];
                /** copy the post_id */
                $ImagePost = $stringParts[1];
                /** error_log("postNo:" .$ImagePost);           */
                $getimage=wp_get_attachment_image($ImagePost,$size='medium' );

                if ($getimage !=""){
                    $output = $getimage ;
                }
            }
        }
        else {
            /** no SiteOrigin image so get the matches */
            $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post_content, $matches);

        }

        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*?>/i', $output, $matches);
        if ($output = '0'){
            $output = preg_match_all('/?<img src=[\'"]([^\'"]+)[\'"].*?>/i', $output, $matches);
        }
        if(!empty($matches[1][0])) {
            $first_img = $matches[1][0];
            /** error_log('first_img:' .$first_img); */
            $last_char = $first_img[strlen($first_img) - 1]; // Check to see if a slash is at the end of the line
            if ($last_char == '\\') {
                $first_img = substr($first_img, 0, -1);
            }
        }


        if(empty($first_img)) {
            $html_thumb.= '';
        }
        else{

            if($link_to=='post_link'){
                $html_thumb.= '<a target="'.$link_target.'" href="'.$item_post_permalink.'"><img src="'.$first_img.'" /></a>';
            }
            else{
                $html_thumb.= '<img src="'.$first_img.'" />';
            }
        }


    }else{
        do_action('post_grid_media', $post_id, $args);
    }


    echo $html_thumb;

    $html_thumb = ob_get_clean();

    return $html_thumb;




}














function post_grid_term_slug_list($post_id){
	
	
	$term_slug_list = '';
	
	$post_taxonomies = get_post_taxonomies($post_id);
	
	foreach($post_taxonomies as $taxonomy){
		
		$term_list[] = wp_get_post_terms(get_the_ID(), $taxonomy, array("fields" => "all"));
		
		}

	if(!empty($term_list)){
		foreach($term_list as $term_key=>$term) 
			{
				foreach($term as $term_id=>$term){
					$term_slug_list .= $term->slug.' ';
					}
			}
		
		}


	return $term_slug_list;

	}










function post_grid_layout_content_ajax(){
	
	if(current_user_can('manage_options')){
		
		
		$layout_key = sanitize_text_field($_POST['layout']);
		
		$class_post_grid_functions = new class_post_grid_functions();
		$post_grid_layout_content = get_option( 'post_grid_layout_content' );
		
		if(empty($post_grid_layout_content)){
				$layout = $class_post_grid_functions->layout_content($layout_key);
			}
		else{
				$layout = $post_grid_layout_content[$layout_key];
			
			}
		

	
		?>
		<div class="<?php echo $layout_key; ?>">
		<?php
		
			foreach($layout as $item_key=>$item_info){
				$item_key = $item_info['key'];
				?>
					<div class="item <?php echo $item_key; ?>" style=" <?php echo $item_info['css']; ?> ">
					
					<?php
					
					if($item_key=='thumb'){
						
						?>
						<img src="<?php echo post_grid_plugin_url; ?>assets/admin/images/thumb.png" />
						<?php
						}
						
					elseif($item_key=='title'){
						
						?>
						Lorem Ipsum is simply
						
						<?php
						}								
						
					elseif($item_key=='excerpt'){
						
						?>
						Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text
						<?php
						}	
						
					elseif($item_key=='excerpt_read_more'){
						
						?>
						Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text <a href="#">Read more</a>
						<?php
						}					
						
					elseif($item_key=='read_more'){
						
						?>
						<a href="#">Read more</a>
						<?php
						}												
						
					elseif($item_key=='post_date'){
						
						?>
						18/06/2015
						<?php
						}	
						
					elseif($item_key=='author'){
						
						?>
						PickPlugins
						<?php
						}					
						
					elseif($item_key=='categories'){
						
						?>
						<a hidden="#">Category 1</a> <a hidden="#">Category 2</a>
						<?php
						}
						
					elseif($item_key=='tags'){
						
						?>
						<a hidden="#">Tags 1</a> <a hidden="#">Tags 2</a>
						<?php
						}	
						
					elseif($item_key=='comments_count'){
						
						?>
						3 Comments
						<?php
						}
						
						// WooCommerce
					elseif($item_key=='wc_full_price'){
						
						?>
						<del>$45</del> - <ins>$40</ins>
						<?php
						}											
					elseif($item_key=='wc_sale_price'){
						
						?>
						$45
						<?php
						}					
										
					elseif($item_key=='wc_regular_price'){
						
						?>
						$45
						<?php
						}	
						
					elseif($item_key=='wc_add_to_cart'){
						
						?>
						Add to Cart
						<?php
						}	
						
					elseif($item_key=='wc_rating_star'){
						
						?>
						*****
						<?php
						}					
											
					elseif($item_key=='wc_rating_text'){
						
						?>
						2 Reviews
						<?php
						}	
					elseif($item_key=='wc_categories'){
						
						?>
						<a hidden="#">Category 1</a> <a hidden="#">Category 2</a>
						<?php
						}					
						
					elseif($item_key=='wc_tags'){
						
						?>
						<a hidden="#">Tags 1</a> <a hidden="#">Tags 2</a>
						<?php
						}
						
					elseif($item_key=='edd_price'){
						
						?>
						$45
						<?php
						}					
																											
						
					else{
						
						echo $item_info['name'];
						
						}
					
					?>
					
					
					
					</div>
					<?php
				}
		
		?>
		</div>
		<?php
		
		}
	
	

	
	die();
	
	}
	
add_action('wp_ajax_post_grid_layout_content_ajax', 'post_grid_layout_content_ajax');








function post_grid_layout_add_elements(){
	
	if(current_user_can('manage_options')){
		
		
		$item_key = sanitize_text_field($_POST['item_key']);
        $item_group = sanitize_text_field($_POST['item_group']);
		$layout = sanitize_text_field($_POST['layout']);	
		$unique_id = sanitize_text_field($_POST['unique_id']);	
	
		$class_post_grid_functions = new class_post_grid_functions();
        $layout_items_group = $class_post_grid_functions->layout_items();


		$item_name = $layout_items_group[$item_group]['items'][$item_key]['name'];
		$item_html = $layout_items_group[$item_group]['items'][$item_key]['dummy_html'];
		$item_css = $layout_items_group[$item_group]['items'][$item_key]['css'];
	
		$html = array();
		
		
		
		$html['item'] = '';
		$html['item'].= '<div class="item '.$item_key.'" id="item-'.$unique_id.'" >';
		$html['item'].= $item_html;
		$html['item'].= '</div>';
	
		$html['options'] = '';
		$html['options'].= '<div class="item" id="'.$unique_id.'">';
		$html['options'].= '<div class="header">
		<span class="remove " title="'.__('Remove', 'post-grid').'"><i class="fa fa-times"></i></span>
		<span class="move " title="'.__('Move', 'post-grid').'"><i class="fas fa-bars"></i></span>
		<span class="expand " title="'.__('Expand or collapse', 'post-grid').'">
			<i class="fas fa-expand"></i>
			<i class="fas fa-compress"></i>
		</span>
		<span class="name">'.$item_name.'</span>
		</div>';
		$html['options'].= '<div class="options">';


        $html['options'].= ''.__('Custom class:', 'post-grid').' <br /><input type="text" value="" name="post_grid_layout_content['.$layout.']['.$unique_id.'][custom_class]" /><br /><br />';


        if($item_key=='meta_key'){
			
			$html['options'].= ''.__('Meta Key:', 'post-grid').' <br /><input type="text" value="" name="post_grid_layout_content['.$layout.']['.$unique_id.'][field_id]" /><br /><br />';
			$html['options'].= ''.__('Wrapper:', 'post-grid').' <br />use %s where you want to repalce the meta value. Example<pre>&lt;div&gt;%s&lt;/div&gt;</pre> <br /><input type="text" value="%s" name="post_grid_layout_content['.$layout.']['.$unique_id.'][wrapper]" /><br /><br />';
			
			
			}
			
		if($item_key=='html'){
			
			$html['options'].= ''.__('Custom HTML:', 'post-grid').' <br /><input type="text" value="" name="post_grid_layout_content['.$layout.']['.$unique_id.'][html]" /><br /><br />';
	
			}		
			
			
			
		if($item_key=='read_more' || $item_key=='excerpt_read_more'){
			
			$html['options'].= ''.__('Read more text:', 'post-grid').' <br /><input type="text" value="" name="post_grid_layout_content['.$layout.']['.$unique_id.'][read_more_text]" /><br /><br />';
			}		
			
		if($item_key=='five_star'){
			
			$html['options'].= ''.__('Five star count:', 'post-grid').' <br /><input type="text" value="" name="post_grid_layout_content['.$layout.']['.$unique_id.'][five_star_count]" /><br /><br />';
			}		
			
		if($item_key=='custom_taxonomy'){
			
			$html['options'].= ''.__('Taxonomy:', 'post-grid').' <br /><input type="text" value="" name="post_grid_layout_content['.$layout.']['.$unique_id.'][taxonomy]" /><br /><br />';
			$html['options'].= ''.__('Term count:', 'post-grid').' <br /><input type="text" value="" name="post_grid_layout_content['.$layout.']['.$unique_id.'][taxonomy_term_count]" /><br /><br />';
			}		
			
			
			
		if($item_key=='up_arrow' || $item_key=='down_arrow' ){
			
			$html['options'].= ''.__('Arrow size(px):', 'post-grid').' <br /><input type="text" placeholder="10px" value="" name="post_grid_layout_content['.$layout.']['.$unique_id.'][arrow_size]" /><br /><br />';
			$html['options'].= ''.__('Background color:', 'post-grid').' <br /><input class="color" type="text" value="" name="post_grid_layout_content['.$layout.']['.$unique_id.'][arrow_bg_color]" /><br /><br />';
			}		
			
			
			
			
		if($item_key=='title'  || $item_key=='title_link'  || $item_key=='excerpt' || $item_key=='excerpt_read_more' ){
			
			$html['options'].= ''.__('Character limit:', 'post-grid').' <br /><input type="text" value="20" name="post_grid_layout_content['.$layout.']['.$unique_id.'][char_limit]" /><br /><br />';
			}
			
			
			
			
		if($item_key=='title_link' || $item_key=='read_more' || $item_key=='excerpt_read_more'  ){
			
			$html['options'].= ''.__('Link target:', 'post-grid').' <br />
			<select name="post_grid_layout_content['.$layout.']['.$unique_id.'][link_target]" >
			<option value="_blank">_blank</option>
			<option value="_parent">_parent</option>
			<option value="_self">_self</option>
			<option value="_top">_top</option>
			<option value="new">new</option>
			 </select><br /><br />';
			}		
			
			
			
			
			
			
			
			
	
		$html['options'].= '
		<input type="hidden" value="'.$item_key.'" name="post_grid_layout_content['.$layout.']['.$unique_id.'][key]" />
		<input type="hidden" value="'.$item_name.'" name="post_grid_layout_content['.$layout.']['.$unique_id.'][name]" />
		CSS: <br />
		<a target="_blank" href="https://www.pickplugins.com/demo/post-grid/sample-css-for-layout-editor/">Sample css</a><br />
		<textarea class="custom_css" item_id="'.$unique_id.'" name="post_grid_layout_content['.$layout.']['.$unique_id.'][css]"  style="width:100%" spellcheck="false" autocapitalize="off" autocorrect="off">'.$item_css.'</textarea><br /><br />
		
		CSS Hover: <br />
		<textarea class="custom_css" item_id="'.$item_key.'" name="post_grid_layout_content['.$layout.']['.$unique_id.'][css_hover]"  style="width:100%" spellcheck="false" autocapitalize="off" autocorrect="off"></textarea>';
		
		
		
		
		
		
		$html['options'].= '</div>';
		$html['options'].= '</div>';	
	
	
	
		echo json_encode($html);

		
		}
	
	die();
	
	}
	
add_action('wp_ajax_post_grid_layout_add_elements', 'post_grid_layout_add_elements');





function post_grid_ajax_search(){



    $grid_id = isset($_POST['grid_id']) ? sanitize_text_field($_POST['grid_id']) : '';

    $post_grid_options = get_post_meta($grid_id, 'post_grid_meta_options', true);

    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';


    $post_types = isset($post_grid_options['post_types']) ? $post_grid_options['post_types'] : array('post');
    //$keyword = isset($post_grid_options['keyword']) ? $post_grid_options['keyword'] : '';
    $exclude_post_id = isset($post_grid_options['exclude_post_id']) ? $post_grid_options['exclude_post_id'] : '';

    $post_status = isset($post_grid_options['post_status']) ? $post_grid_options['post_status'] : 'publish';
    $query_order = isset($post_grid_options['query_order']) ? $post_grid_options['query_order'] : 'DESC';
    $query_orderby = isset($post_grid_options['query_orderby']) ? $post_grid_options['query_orderby'] : array('date');
    $query_orderby = implode(' ', $query_orderby);
    $offset = isset($post_grid_options['offset']) ? (int)$post_grid_options['offset'] : '';
    $posts_per_page = isset($post_grid_options['posts_per_page']) ? $post_grid_options['posts_per_page'] : 10;
    $query_orderby_meta_key = isset($post_grid_options['query_orderby_meta_key']) ? $post_grid_options['query_orderby_meta_key'] : '';


    $taxonomies = !empty($post_grid_options['taxonomies']) ? $post_grid_options['taxonomies'] : array();
    $categories_relation = isset($post_grid_options['categories_relation']) ? $post_grid_options['categories_relation'] : 'OR';

    $query_args = array();



    /* ################################ Tax query ######################################*/

    $tax_query = array();

    foreach($taxonomies as $taxonomy => $taxonomyData){

        $terms = !empty($taxonomyData['terms']) ? $taxonomyData['terms'] : array();
        $terms_relation = !empty($taxonomyData['terms_relation']) ? $taxonomyData['terms_relation'] : 'OR';
        $checked = !empty($taxonomyData['checked']) ? $taxonomyData['checked'] : '';

        if(!empty($terms) && !empty($checked)){
            $tax_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $terms,
                'operator'    => $terms_relation,
            );
        }
    }


    $tax_query_relation = array( 'relation' => $categories_relation );
    $tax_query = array_merge($tax_query_relation, $tax_query );


    /* ################################ Keyword query ######################################*/

    $keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : $keyword;


    /* ################################ Single pages ######################################*/


    if(is_singular()):
        $current_post_id = get_the_ID();
        $query_args['post__not_in'] = array($current_post_id);
    endif;




    if ( get_query_var('paged') ) {
        $paged = get_query_var('paged');
    }elseif ( get_query_var('page') ) {
        $paged = get_query_var('page');
    }else {
        $paged = 1;
    }




    if(!empty($post_types))
        $query_args['post_type'] = $post_types;

    if(!empty($post_status))
        $query_args['post_status'] = $post_status;

    if(!empty($keyword))
        $query_args['s'] = $keyword;


    if(!empty($exclude_post_id))
        $query_args['post__not_in'] = $exclude_post_id;

    if(!empty($query_order))
        $query_args['order'] = $query_order;

    if(!empty($query_orderby))
        $query_args['orderby'] = $query_orderby;

    if(!empty($query_orderby_meta_key))
        $query_args['meta_key'] = $query_orderby_meta_key;

    if(!empty($posts_per_page))
        $query_args['posts_per_page'] = (int)$posts_per_page;

    if(!empty($paged))
        $query_args['paged'] = $paged;

    if(!empty($offset))
        $query_args['offset'] = $offset + ( ($paged-1) * $posts_per_page );


    if(!empty($tax_query))
        $query_args['tax_query'] = $tax_query;



    $query_args = apply_filters('post_grid_ajax_query_args', $query_args, $grid_id);
   // $query_args = apply_filters('post_grid_query_args', $query_args, $args);


    //echo '<pre>'.var_export($query_args, true).'</pre>';

    $post_grid_wp_query = new WP_Query($query_args);

    //$wp_query = $post_grid_wp_query;

    $args['options'] = $post_grid_options;
    //echo '<pre>'.var_export($post_grid_wp_query, true).'</pre>';

    $loop_count = 0;

    ob_start();

    if ( $post_grid_wp_query->have_posts() ) :
        while ( $post_grid_wp_query->have_posts() ) : $post_grid_wp_query->the_post();
            $post_id = get_the_ID();
            $args['post_id'] = $post_id;
            $args['loop_count'] = $loop_count;

            do_action('post_grid_loop', $args);

            $loop_count++;
        endwhile;

        wp_reset_query();
        wp_reset_postdata();
    endif;

    $html = ob_get_clean();

    echo $html;

    die();
		

}

add_action('wp_ajax_post_grid_ajax_search', 'post_grid_ajax_search');
add_action('wp_ajax_nopriv_post_grid_ajax_search', 'post_grid_ajax_search');