<?php

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Uploads_Scanner
{
    public $upload_url;
    public $upload_base_url;

    public $file_found_cache;

    public function __construct()
    {
        $upload_dir=wp_upload_dir();
        $this->upload_url=$upload_dir['baseurl'];
        $this->upload_base_url = substr($upload_dir['baseurl'],1+strlen(get_site_url()));

        $this->file_found_cache=array();
    }

    public function init_scan_task()
    {
        $this->check_table();

        $task['start_time']=time();
        $task['running_time']=time();
        $task['status']='running';
        $task['progress']=0;
        $task['offset']=0;

        update_option('scan_unused_files_task',$task);
    }

    public function check_table_exist()
    {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . "wpvivid_scan_result";
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                path text NOT NULL,
                from_post INT NOT NULL,
                PRIMARY KEY (id)
                ) ". $charset_collate . ";";
            //reference to upgrade.php file
            dbDelta( $sql );
        }
    }

    public function check_unused_uploads_files_table_exist()
    {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . "wpvivid_unused_uploads_files";
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                path text NOT NULL,
                folder text NOT NULL,
                PRIMARY KEY (id)
                )". $charset_collate . ";";
            //reference to upgrade.php file
            dbDelta( $sql );
        }
    }

    public function check_table()
    {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . "wpvivid_scan_result";
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                path text NOT NULL,
                from_post INT NOT NULL,
                PRIMARY KEY (id)
                ) ". $charset_collate . ";";
            //reference to upgrade.php file
            dbDelta( $sql );
        }

        $wpdb->query("TRUNCATE TABLE $table_name");
    }

    public function init_unused_uploads_task($folders)
    {
        $this->check_unused_uploads_files_table();
        update_option('unused_uploads_task',array());
        $task['start_time']=time();
        $task['running_time']=time();
        $task['status']='running';
        $task['progress']=0;
        $task['size']=0;

        $upload_folder = wp_upload_dir();

        $root_path =$upload_folder['basedir'];

        foreach ($folders as $folder)
        {
            $task['folder'][$folder]['finished']=0;
            $task['folder'][$folder]['offset']=0;
            if($folder=='.')
            {
                $task['folder'][$folder]['total']=0;
            }
            else
            {
                $path=$root_path.DIRECTORY_SEPARATOR.$folder;
                if(file_exists($path))
                {
                    $fi = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
                    $task['folder'][$folder]['total']=iterator_count($fi);
                }
                else {
                    $task['folder'][$folder]['total']=0;
                }
            }
        }

        update_option('unused_uploads_task',$task);
    }

    public function check_unused_uploads_files_table()
    {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . "wpvivid_unused_uploads_files";
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                path text NOT NULL,
                folder text NOT NULL,
                PRIMARY KEY (id)
                )". $charset_collate . ";";
            //reference to upgrade.php file
            dbDelta( $sql );
        }

        $wpdb->query("TRUNCATE TABLE $table_name");
    }

    public function scan_sidebars_widgets()
    {
        global $wp_registered_widgets;
        $syswidgets = $wp_registered_widgets;
        $active_widgets = get_option( 'sidebars_widgets' );

        $files=array();

        foreach ( $active_widgets as $sidebar_name => $widgets )
        {
            if ( $sidebar_name != 'wp_inactive_widgets' && !empty( $widgets ) && is_array( $widgets ) )
            {
                foreach ( $widgets as $key => $widget )
                {
                    $files=array_merge($files,$this->get_images_from_widget($syswidgets[$widget]));

                    //do_action( 'wpmc_scan_widget', $syswidgets[$widget] );
                    //$acfwidget = $syswidgets[$widget]['callback'][0]->id;
                    //if ( strlen($acfwidget)>11 && substr($acfwidget,0,11)=='acf_widget_' )
                    //{
                        //$this->get_images_from_acfwidgets ( $acfwidget );
                    //}
                }
            }
        }

        return $files;
    }

    public function scan_divi_options()
    {
        $files=array();
        $options=get_option('et_divi',false);

        if($options!==false)
        {
            if(isset($options['divi_logo']))
            {
                $files[]=$this->get_src($options['divi_logo']);
            }
        }

        $options=get_option('widget_text',false);

        if($options!==false)
        {
            foreach ($options as $option)
            {
                if(isset($option['title']))
                {
                    $this->get_img_from_divi($option['title'],$files);
                }

                if(isset($option['text']))
                {
                    $this->get_img_from_divi($option['text'],$files);
                }
            }
        }


        $options=get_option('theme_mods_Divi',false);

        if($options!==false)
        {
            if(isset($options['background_image']))
            {
                $files[]=$this->get_src($options['background_image']);
            }
        }

        return $files;
    }

    public function get_images_from_widget($widget)
    {
        $widget_class = $widget['callback'][0]->option_name;
        $instance_id = $widget['params'][0]['number'];
        $widget_data = get_option( $widget_class );

        $files=array();

        $ids=array();

        if ( !empty( $widget_data[$instance_id]['text'] ) )
        {
            $html = $widget_data[$instance_id]['text']; // mm change
            $media=$this->get_media_from_html($html);

            if(!empty($media))
            {
                $files=$media;
            }
        }
        if ( !empty( $widget_data[$instance_id]['attachment_id'] ) )
        {
            $id = $widget_data[$instance_id]['attachment_id'];
            array_push( $ids, $id );
        }


        if ( !empty( $widget_data[$instance_id]['url'] ) )
        {
            $url = $widget_data[$instance_id]['url'];
            if ( $this->is_url( $url ) )
            {
                $src=$this->get_src($url);
                array_push( $files, $src );
            }
        }
        if ( !empty( $widget_data[$instance_id]['ids'] ) )
        {
            $newIds = $widget_data[$instance_id]['ids'];
            $ids = array_merge( $ids, $newIds );
        }
        // Recent Blog Posts
        if ( !empty( $widget_data[$instance_id]['thumbnail'] ) )
        {
            $id = $widget_data[$instance_id]['thumbnail'];
            array_push( $ids, $id );
        }

        foreach ($ids as $id)
        {
            $files=array_merge($files,$this->get_img_from_id($id));
        }

        return $files;
    }

    public function scan_termmeta_thumbnail()
    {
        global $wpdb;
        $query = "SELECT meta_value FROM $wpdb->termmeta WHERE meta_key LIKE '%thumbnail_id%'";
        $metas = $wpdb->get_col( $query );

        $files=array();
        if(count($metas)>0)
        {
            $ids=array();
            foreach ( $metas as $id )
            {
                if ( is_numeric( $id ) && $id > 0 )
                    $ids[]=$id;
            }

            foreach ($ids as $id)
            {
                $files=array_merge($files,$this->get_img_from_id($id));
            }
        }

        $placeholder_id = get_option( 'woocommerce_placeholder_image', null, true );
        if ( !empty( $placeholder_id ) )
            $files=array_merge($files,$this->get_img_from_id($placeholder_id));
        return $files;
    }

    public function array_to_file($exploded)
    {
        $file='';
        foreach ($exploded as $key=>$value)
        {
            $file=$value;
        }
        return $file;
    }

    public function scan_image_from_nextend()
    {
        global $wpdb;
        $query = "SELECT image FROM ".$wpdb->prefix."nextend2_image_storage";
        $metas = $wpdb->get_col( $query );
        $file_array=array();

        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];

        foreach ($metas as $meta)
        {
            $exploded = explode( ',', $meta );
            if ( is_array( $exploded ) )
            {
                $file_tmp = $this->array_to_file($exploded);
                $file = str_replace('$upload$', $upload_path, $file_tmp);
                if(file_exists($file))
                {
                    $file_array[] = str_replace('$upload$'.'/', '', $file_tmp);
                }
                continue;
            }
        }
        return $file_array;
    }

    /*
    public function get_images_from_acfwidgets( $widget)
    {
        global $wpdb;
        $result=array();
        // $widget starts with: acf_widget_ and looks like this: acf_widget_15011-2
        $LikeKey = 'widget_' . $widget . '_%'; // Example: option_name starts with widget_acf_widget_15216-3_
        $q = "SELECT option_name, option_value FROM {$wpdb->options} where option_name like %s;";
        $OptionRows = $wpdb->get_results( $wpdb->prepare( $q, $LikeKey ) , ARRAY_N );
        if ( $wpdb->last_error )
        {
            $result['result']='failed';
            $result['error']=$wpdb->last_error;
            return $result;
        }
        if ( count( $OptionRows ) > 0 )
        {
            $ACFWidget_ids = array();
            $ACFWidget_urls = array();
            foreach( $OptionRows as $row )
            {
                //$row[0] = option_name from wp_options
                //$row[1] = option_value from wp_options
                // Three if statements in priority order (image ids, link fields, text fields)
                // *** An image field containing a post id for the image or is it???
                if ( strpos($row[0], 'image') || strpos($row[0], 'icon') !== false )
                {
                    if ( is_numeric( $row[1] ) ) {
                        array_push( $ACFWidget_ids, $row[1] );
                    }
                }

                // No else here because sometimes image or icon is present in the option_name and link is also present
                // Example: widget_acf_widget_15011-2_link_1_link_icon
                // Example: widget_acf_widget_15216-3_widget_image_link

                // *** A link field may contain a link or be empty
                if ( strpos( $row[0], 'link' ) || strpos( $row[0], 'url' ) !== false )
                {
                    if ( $this->is_url($row[1]) ) {
                        $url = $this->clean_url($row[1]);
                        if (!empty($url)) {
                            array_push($ACFWidget_urls, $url);
                        }
                    }
                }

                // *** A text field may contain HTML
                if (strpos($row[0], 'text') || strpos($row[0], 'html') !== false)
                {
                    if (!empty($row[1])) {
                        $ACFWidget_urls = array_merge($ACFWidget_urls, $this->get_urls_from_html($row[1]));  // mm change
                    }
                }
            }
        }
    }
    */

    public function get_post_count()
    {
        global $wpdb;

        $post_types=apply_filters('wpvivid_scan_post_types', array());

        $post_types="post_type NOT IN ('".implode("','",$post_types)."')";

        $post_status="post_status NOT IN ('inherit', 'trash', 'auto-draft')";

        $query="SELECT COUNT(*) FROM $wpdb->posts WHERE $post_types AND $post_status";
        $result=$wpdb->get_results($query,ARRAY_N);

        if($result && sizeof($result)>0)
        {
            $count = $result[0][0];
        }
        else
        {
            $count=0;
        }

        return $count;
    }

    public function get_posts($start,$limit)
    {
        global $wpdb;

        $post_types=apply_filters('wpvivid_scan_post_types', array());

        $post_types="post_type NOT IN ('".implode("','",$post_types)."')";

        $post_status="post_status NOT IN ('inherit', 'trash', 'auto-draft')";

        $query=$wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE $post_types AND $post_status LIMIT %d, %d",$start,$limit);

        $posts = $wpdb->get_col( $query );

        return $posts;
    }

    public function get_media_from_html($html)
    {
        $html = mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' );
        $html = do_shortcode( $html );
        $html = wp_filter_content_tags( $html );

        if ( !class_exists("DOMDocument") )
        {
            echo 'The DOM extension for PHP is not installed.';
            return array();
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML( $html );
        libxml_clear_errors();
        $results = array();

        $this->get_img_from_tag_img($dom,$results);

        $this->get_img_from_meta($dom,$results);

        $this->get_img_from_tag_a($dom,$results);

        $this->get_img_from_tag_a($dom,$results,'link');

        $this->get_img_from_bk($html,$results);

        $this->get_img_from_wp_image($html,$results);

        return $results;
    }

    public function get_media_from_post_content($post)
    {
        $html = get_post_field( 'post_content', $post );

        $html = mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' );
        ob_start();
        $html = do_shortcode( $html );
        ob_clean();
        ob_end_flush();
        $html = wp_filter_content_tags( $html );

        if ( !class_exists("DOMDocument") )
        {
            echo 'The DOM extension for PHP is not installed.';
            return array();
        }

        if(empty($html))
        {
            return array();
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML( $html );
        libxml_clear_errors();
        $results = array();

        $this->get_img_from_tag_img($dom,$results);

        $this->get_img_from_meta($dom,$results);

        $this->get_img_from_tag_a($dom,$results);

        $this->get_img_from_tag_a($dom,$results,'link');

        $this->get_img_from_bk($html,$results);

        $this->get_img_from_wp_image($html,$results);

        $this->get_img_from_divi($html,$results);

        $galleries = get_post_galleries_images( $post );
        foreach ( $galleries as $gallery )
        {
            foreach ( $gallery as $image )
            {
                $src=$this->get_src($image);
                if($src!==false)
                {
                    array_push( $results, $src );
                }
            }
        }

        return $results;
    }

    public function get_img_from_tag_img($dom,&$results)
    {
        $imgs = $dom->getElementsByTagName( 'img' );
        foreach ( $imgs as $img )
        {
            $url = $img->getAttribute('src');
            $src=$this->get_src($url);
            if($src!==false)
            {
                array_push( $results, $src );
            }

            $srcset = $img->getAttribute('srcset');
            if ( !empty( $srcset ) )
            {
                $setImgs = explode( ',', trim( $srcset ) );
                foreach ( $setImgs as $setImg )
                {
                    $urls = explode( ' ', trim( $setImg ) );
                    if ( is_array( $urls ) )
                    {
                        $src=$this->get_src($urls[0]);
                        if($src!==false)
                        {
                            array_push( $results, $src );
                        }
                    }
                }
            }
        }
    }

    public function get_img_from_meta($dom,&$results)
    {
        $metas = $dom->getElementsByTagName( 'meta' );
        foreach ( $metas as $meta )
        {
            $property = $meta->getAttribute( 'property' );
            if ( $property == 'og:image' || $property == 'og:image:secure_url' || $property == 'twitter:image' )
            {
                $url = $meta->getAttribute( 'content' );
                $src=$this->get_src($url);
                if($src!==false)
                {
                    array_push( $results, $src );
                }
            }
        }
    }

    public function get_img_from_tag_a($dom,&$results,$tag='a')
    {
        $urls = $dom->getElementsByTagName($tag);
        foreach ( $urls as $url )
        {
            $url_href = $url->getAttribute('href'); // mm change

            $src=$this->get_src($url_href);
            if($src!==false)
            {
                if ( !empty( $src ) )
                {
                    array_push( $results, $src );
                }
            }
        }
    }

    public function get_img_from_bk($html,&$results)
    {
        preg_match_all( "/url\(\'?\"?((https?:\/\/)?[^\\&\#\[\] \"\?]+\.(jpe?g|gif|png))\'?\"?/", $html, $res );
        if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 )
        {
            foreach ( $res[1] as $url )
            {
                $src=$this->get_src($url);
                if($src!==false)
                {
                    array_push( $results, $src );
                }
            }
        }
    }

    public function get_img_from_wp_image($html,&$results)
    {
        $posts_images_ids=array();
        preg_match_all( "/wp-image-([0-9]+)/", $html, $res );
        if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 )
        {
            $posts_images_ids = array_merge( $posts_images_ids, $res[1] );
        }

        preg_match_all('/\[gallery.*ids=.(.*).\]/', $html, $res );
        if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 )
        {
            foreach ( $res[1] as $id )
            {
                $ids = explode( ',', $id );
                $posts_images_ids = array_merge( $posts_images_ids, $ids );
            }
        }

        if(!empty($posts_images_ids))
        {
            foreach ($posts_images_ids as $id)
            {
                $files=$this->get_attachment_size($id);
                if(!empty($files))
                {
                    $results=array_merge( $results, $files );
                }
            }
        }
    }

    public function get_img_from_divi( $html, &$results )
    {
        $galleries_images_et = array();

        // Single Image
        preg_match_all( "/src=\"((https?:\/\/)?[^\\&\#\[\] \"\?]+\.(jpe?g|gif|png|ico|tif?f|bmp))\"/", $html, $res );
        if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 )
        {
            foreach ( $res[1] as $url )
            {
                $src=$this->get_src($url);
                if($src!==false)
                {
                    array_push( $results, $src );
                }
            }
        }

        preg_match_all( "/image=\"((https?:\/\/)?[^\\&\#\[\] \"\?]+\.(jpe?g|gif|png|ico|tif?f|bmp))\"/", $html, $res );
        if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 )
        {
            foreach ( $res[1] as $url )
            {
                $src=$this->get_src($url);
                if($src!==false)
                {
                    array_push( $results, $src );
                }
            }
        }
        // Background Image
        preg_match_all( "/background_image=\"((https?:\/\/)?[^\\&\#\[\] \"\?]+\.(jpe?g|gif|png|ico|tif?f|bmp))\"/", $html, $res );
        if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 )
        {
            foreach ( $res[1] as $url )
            {
                $src=$this->get_src($url);
                if($src!==false)
                {
                    array_push( $results, $src );
                }
            }
        }

        // Modules with URL (like the Person module)
        preg_match_all( "/url=\"((https?:\/\/)?[^\\&\#\[\] \"\?]+\.(jpe?g|gif|png|ico|tif?f|bmp))\"/", $html, $res );
        if ( !empty( $res ) && isset( $res[1] ) )
        {
            foreach ( $res[1] as $url )
            {
                $src=$this->get_src($url);
                if($src!==false)
                {
                    array_push( $results, $src );
                }
            }
        }

        // Galleries
        preg_match_all( "/gallery_ids=\"([0-9,]+)/", $html, $res );
        if ( !empty( $res ) && isset( $res[1] ) )
        {
            foreach ( $res[1] as $r )
            {
                $ids = explode( ',', $r );
                $galleries_images_et = array_merge( $galleries_images_et, $ids );
            }
        }

        foreach ($galleries_images_et as $id)
        {
            $results=array_merge($results,$this->get_img_from_id($id));
        }

    }

    public function get_attachment_size($attachment_id)
    {
        $files=array();
        global $wpdb;
        $meta_key="(meta_key = '_wp_attached_file')";
        $postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND $meta_key", $attachment_id ) );

        foreach ( $postmeta as $meta )
        {
            if($meta->meta_key=='_wp_attached_file')
            {
                $files[]=$meta->meta_value;

                $attach_meta      = wp_get_attachment_metadata( $attachment_id );
                if($attach_meta!=false)
                {
                    if(isset($attach_meta['sizes']))
                    {
                        foreach ($attach_meta['sizes'] as $key=>$value)
                        {
                            $data=image_get_intermediate_size($attachment_id,$key);
                            $files[]=$data['path'];
                        }
                    }
                }
            }
        }

        return $files;
    }

    public function get_media_from_post_meta($post)
    {
        global $wpdb;
        $meta_key="(meta_key = '_thumbnail_id')";
        $query=$wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND $meta_key",$post);

        $metas = $wpdb->get_col($query);

        $postmeta_images_ids = array();
        $postmeta_images_urls = array();

        foreach ($metas as $meta)
        {
            if ( is_numeric( $meta ) )
            {
                if ( $meta > 0 )
                    array_push( $postmeta_images_ids, $meta );
                continue;
            }
            else if ( is_serialized( $meta ) )
            {
                $decoded = @unserialize( $meta );
                if ( is_array( $decoded ) )
                {
                    $this->array_to_ids_or_urls( $decoded, $postmeta_images_ids, $postmeta_images_urls );
                    continue;
                }
            }
            else {
                $exploded = explode( ',', $meta );
                if ( is_array( $exploded ) )
                {
                    $this->array_to_ids_or_urls( $exploded, $postmeta_images_ids, $postmeta_images_urls );
                    continue;
                }
            }
        }

        //
        $meta_key="(meta_key = '_product_image_gallery')";
        $query=$wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND $meta_key",$post);
        $metas = $wpdb->get_col($query);
        foreach ($metas as $meta)
        {
            if ( is_numeric( $meta ) )
            {
                if ( $meta > 0 )
                    array_push( $postmeta_images_ids, $meta );
                continue;
            }
            else if ( is_serialized( $meta ) )
            {
                $decoded = @unserialize( $meta );
                if ( is_array( $decoded ) )
                {
                    $this->array_to_ids_or_urls( $decoded, $postmeta_images_ids, $postmeta_images_urls );
                    continue;
                }
            }
            else {
                $exploded = explode( ',', $meta );
                if ( is_array( $exploded ) )
                {
                    $this->array_to_ids_or_urls( $exploded, $postmeta_images_ids, $postmeta_images_urls );
                    continue;
                }
            }
        }
        //

        $files=array();

        foreach ($postmeta_images_ids as $id)
        {
            $files=array_merge($files,$this->get_img_from_id($id));
        }

        return $files;
    }

    public function get_media_from_post_meta_elementor( $post )
    {
        $postmeta_images_ids = array();
        $files=array();

        $_elementor_meta = get_post_meta( $post, '_elementor_data',true);
        if($_elementor_meta!=false)
        {
            if ( is_string( $_elementor_meta ) && ! empty( $_elementor_meta ) )
            {
                $_elementor_meta = json_decode( $_elementor_meta, true );
            }
            if ( empty( $_elementor_meta ) )
            {
                $_elementor_meta = array();
            }
            $elements_data=$_elementor_meta;
            foreach ( $elements_data as $element_data )
            {
                $element_image=$this->get_element_image($element_data,$postmeta_images_ids);
                $postmeta_images_ids=array_merge($postmeta_images_ids,$element_image);
            }


            foreach ($postmeta_images_ids as $id)
            {
                $files=array_merge($files,$this->get_img_from_id($id));
            }
        }
        return $files;
    }

    public function get_media_from_post_custom_meta( $post )
    {
        $custom_fields=get_post_custom($post);
        $files=array();


        if($custom_fields!=false)
        {
            if(isset($custom_fields['essb_cached_image']))
            {
                if ( is_string( $custom_fields['essb_cached_image'] ) && ! empty( $custom_fields['essb_cached_image'] ) )
                {
                    $files[]=$this->get_src($custom_fields['essb_cached_image']);
                }
                else if(is_array( $custom_fields['essb_cached_image'] )&& ! empty( $custom_fields['essb_cached_image'] ))
                {
                    foreach ($custom_fields['essb_cached_image'] as $essb_cached_image)
                    {
                        $files[]=$this->get_src($essb_cached_image);
                    }
                }

            }
        }

        return $files;
    }

    public function get_element_image($element_data,&$attachment_added_ids)
    {
        $element_image=array();

        if(!empty($element_data['settings']))
        {
            $settings=$element_data['settings'];
            if(isset($settings['image']))
            {
                if(!in_array($settings['image']['id'],$attachment_added_ids))
                {
                    $element_image[]=$settings['image']['id'];
                    $attachment_added_ids[]=$settings['image']['id'];
                }

            }

            if(isset($settings['logo_items']))
            {
                foreach ($settings['logo_items'] as $item)
                {
                    if(isset($item['logo_image']))
                    {
                        if(!in_array($item['logo_image']['id'],$attachment_added_ids))
                        {
                            $element_image[]=$item['logo_image']['id'];
                            $attachment_added_ids[]=$item['logo_image']['id'];
                        }
                    }
                }
            }

            if(isset($settings['gallery']))
            {
                foreach ($settings['gallery'] as $item)
                {
                    if(isset($item['id']))
                    {
                        if(!in_array($item['id'],$attachment_added_ids))
                        {
                            $element_image[]=$item['id'];
                            $attachment_added_ids[]=$item['id'];
                        }
                    }
                }
            }

            if(isset($settings['background_image']))
            {
                if(isset($settings['background_image']['id']))
                {
                    if(!in_array($settings['background_image']['id'],$attachment_added_ids))
                    {
                        $element_image[]=$settings['background_image']['id'];
                        $attachment_added_ids[]=$settings['background_image']['id'];
                    }
                }
            }
        }

        if(!empty($element_data['elements']))
        {
            foreach ($element_data['elements'] as $element)
            {
                $temp=$this->get_element_image($element,$attachment_added_ids);
                $element_image=array_merge($element_image,$temp);
            }
        }

        return $element_image;
    }

    public function get_from_meta( $meta, $lookFor, &$ids, &$urls )
    {
        foreach ( $meta as $key => $value ) {
            if ( is_object( $value ) || is_array( $value ) )
                $this->get_from_meta( $value, $lookFor, $ids, $urls );
            else if ( in_array( $key, $lookFor ) ) {
                if ( empty( $value ) )
                    continue;
                else if ( is_numeric( $value ) ) {
                    // It this an ID?
                    array_push( $ids, $value );
                }
                else {
                    if ( $this->is_url( $value ) ) {
                        // Is this an URL?
                        array_push( $urls, $this->clean_url( $value ) );
                    }
                    else {
                        // Is this an array of IDs, encoded as a string? (like "20,13")
                        $pieces = explode( ',', $value );
                        foreach ( $pieces as $pval ) {
                            if ( is_numeric( $pval ) ) {
                                array_push( $ids, $pval );
                            }
                        }
                    }
                }
            }
        }
    }

    public function get_img_from_id($attachment_id)
    {
        $files=array();
        $attach_meta      = wp_get_attachment_metadata( $attachment_id );
        if($attach_meta!=false)
        {
            if(isset($attach_meta['sizes']))
            {
                foreach ($attach_meta['sizes'] as $key=>$value)
                {
                    $data=image_get_intermediate_size($attachment_id,$key);
                    $data['path']=ltrim($data['path'], './');
                    $name=$data['path'];
                    if(!in_array($name,$files))
                    {
                        $files[]=$name;
                    }
                }
            }

            if(isset($attach_meta['file'])&&is_string($attach_meta['file']))
            {
                if(!in_array($attach_meta['file'],$files))
                {
                    $files[]=$attach_meta['file'];
                }
            }
        }
        return $files;
    }

    public function get_src($url)
    {
        if(empty($url)||!is_string( $url ))
        {
            return false;
        }

        if(strlen($url)>4&&strtolower( substr( $url, 0, 4) ) == 'http')
        {
            $ipos = strpos( $url, $this->upload_url );
            if ($ipos === false)
            {
                return false;
            }

            $str=substr( $url, 1 + strlen( $this->upload_url ) + $ipos );

            return $str;
        }
        else if($url[0] == '/')
        {
            $ipos = strpos( $url, $this->upload_base_url );
            if ($ipos === false)
                return false;
            return substr( $url, 1 + strlen( $this->upload_base_url ) + $ipos );
        }
        else
        {
            return false;
        }
    }

    function is_url( $url ) {
        return ( (
            !empty( $url ) ) &&
            is_string( $url ) &&
            strlen( $url ) > 4 && (
                strtolower( substr( $url, 0, 4) ) == 'http' || $url[0] == '/'
            )
        );
    }

    function array_to_ids_or_urls( &$meta, &$ids, &$urls )
    {
        $regex_file = '/[A-Za-z0-9-_,.\(\)\s]+[.]{1}(jpg|jpeg|jpe|gif|png|tiff|bmp|csv|pdf|xls|xlsx|doc|docx|odt|wpd|rtf|tiff|mp3|mp4|wav|lua)/';
        foreach ( $meta as $k => $m )
        {
            if ( is_numeric( $m ) ) {
                // Probably a Media ID
                if ( $m > 0 )
                    array_push( $ids, $m );
            }
            else if ( is_array( $m ) )
            {
                // If it's an array with a width, probably that the index is the Media ID
                if ( isset( $m['width'] ) && is_numeric( $k ) ) {
                    if ( $k > 0 )
                        array_push( $ids, $k );
                }
            }
            else if ( !empty( $m ) )
            {
                // If it's a string, maybe it's a file (with an extension)
                if ( preg_match( $regex_file, $m ) )
                    array_push( $urls, $m );
            }
        }
    }

    public function get_folders()
    {
        $upload_folder = wp_upload_dir();

        $root_path =$upload_folder['basedir'];

        $regex=apply_filters('wpvivid_uc_scan_include_files_regex',array());

        $exclude_regex=apply_filters('wpvivid_uc_scan_exclude_files_regex',array());

        $result=$this->get_folder_list($root_path,$regex,$exclude_regex);

        return $result;
    }

    public function get_files($folder)
    {
        $upload_folder = wp_upload_dir();

        $root_path =$upload_folder['basedir'];

        $files =array();

        $regex=apply_filters('wpvivid_uc_scan_include_files_regex',array());

        $exclude_regex=apply_filters('wpvivid_uc_scan_exclude_files_regex',array());

        if($folder === '.')
        {
            $this->scan_root_uploaded_files($files, $root_path.DIRECTORY_SEPARATOR.$folder,$root_path,$regex,$exclude_regex);
        }
        else
        {
            $this->scan_list_uploaded_files($files, $root_path.DIRECTORY_SEPARATOR.$folder,$root_path,$regex,$exclude_regex);
        }

        return $files;
    }

    private function regex_match($regex_array,$string,$mode)
    {
        if(empty($regex_array))
        {
            return true;
        }

        if($mode==0)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return false;
                }
            }

            return true;
        }

        if($mode==1)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    private function get_folder_list($root_path,$regex=array(),$exclude_regex=array())
    {
        $result['folders']=array();
        $result['files']=array();
        $result['size']=0;
        $handler = opendir($root_path);
        if($handler!==false)
        {
            while (($filename = readdir($handler)) !== false)
            {
                if ($filename != "." && $filename != "..")
                {
                    if (is_dir($root_path . DIRECTORY_SEPARATOR . $filename))
                    {
                        if(preg_match('#^\d{4}$#',$filename))
                        {
                            $result['folders']=array_merge( $result['folders'],$this->get_sub_folder($root_path . DIRECTORY_SEPARATOR . $filename,$filename));
                        }
                        else
                        {
                            $result['folders'][]=$filename;
                        }

                    }
                    else
                    {
                        if ($this->regex_match($exclude_regex, $filename, 0))
                        {
                            if($this->regex_match($regex, $filename, 1))
                            {
                                $result['files'][] = $filename;
                                $result['size']+=filesize($root_path . DIRECTORY_SEPARATOR . $filename);
                            }
                        }
                    }
                }
            }
            if($handler)
                @closedir($handler);
        }

        $result['folders'][]='.';
        return $result;
    }

    function get_sub_folder($path,$root)
    {
        $folders=array();
        $handler = opendir($path);
        if($handler!==false)
        {
            while (($filename = readdir($handler)) !== false)
            {
                if ($filename != "." && $filename != "..")
                {
                    if (is_dir($path . DIRECTORY_SEPARATOR . $filename))
                    {
                        $folders[]=$root.DIRECTORY_SEPARATOR.$filename;

                    }
                }
            }
            if($handler)
                @closedir($handler);
        }
        return $folders;
    }

    function scan_root_uploaded_files( &$files,$path,$root,$regex=array(),$exclude_regex=array())
    {
        $count = 0;
        if(is_dir($path))
        {
            $handler = opendir($path);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..")
                    {
                        $count++;
                        if ($this->regex_match($exclude_regex, $path . DIRECTORY_SEPARATOR . $filename, 0))
                        {
                            if($this->regex_match($regex, $filename, 1))
                            {
                                $result['files'][] = $filename;
                                $files[] = str_replace($path . DIRECTORY_SEPARATOR,'',$path . DIRECTORY_SEPARATOR . $filename);
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }
        }
        return $files;
    }

    function scan_list_uploaded_files( &$files,$path,$root,$regex=array(),$exclude_regex=array())
    {
        $count = 0;
        if(is_dir($path))
        {
            $handler = opendir($path);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..")
                    {
                        $count++;

                        if (is_dir($path . DIRECTORY_SEPARATOR . $filename))
                        {
                            $this->scan_list_uploaded_files($files, $path . DIRECTORY_SEPARATOR . $filename,$root,$regex);
                        }
                        else
                        {
                            if ($this->regex_match($exclude_regex, $path . DIRECTORY_SEPARATOR . $filename, 0))
                            {
                                if($this->regex_match($regex, $filename, 1))
                                {
                                    $result['files'][] = $filename;
                                    $files[] = str_replace($root . DIRECTORY_SEPARATOR,'',$path . DIRECTORY_SEPARATOR . $filename);
                                }
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }
        }

        return $files;
    }

    public function update_scan_task($uploads_files,$offset,$status='running',$progress=0)
    {
        $task=get_option('scan_unused_files_task',array());

        $task['running_time']=time();
        $task['status']=$status;
        $task['progress']=$progress;
        $task['offset']=$offset;

        $this->insert_scan_result($uploads_files);
        update_option('scan_unused_files_task',$task);
    }

    public function update_unused_uploads_task($uploads_files,$folder,$finished,$offset,$status='running',$progress=0,$size=0)
    {
        $task=get_option('unused_uploads_task',array());

        $task['running_time']=time();
        $task['status']=$status;
        $task['progress']=$progress;
        $task['size']+=$size;
        $task['folder'][$folder]['finished']=$finished;
        $task['folder'][$folder]['offset']=$offset;
        if(!empty($uploads_files))
            $this->insert_unused_uploads_files($folder,$uploads_files);
        update_option('unused_uploads_task',$task);
    }

    public function get_unused_uploads_progress()
    {
        $task=get_option('unused_uploads_task',array());

        if(isset($task['folder']))
        {
            $i=0;
            foreach ($task['folder'] as $folder=>$item)
            {
                if($item['finished'])
                    $i++;
            }

            $progress=intval(($i/sizeof($task['folder']))*100);

            $ret['percent']=$progress;
            $ret['total_folders']=sizeof($task['folder']);
            $ret['scanned_folders']=$i;
            return $ret;
        }
        else
        {
            $ret['percent']=0;
            $ret['total_folders']=0;
            $ret['scanned_folders']=0;
            return $ret;
        }
    }

    public function get_unfinished_folder()
    {
        $task=get_option('unused_uploads_task',array());

        foreach ($task['folder'] as $folder=>$data)
        {
            if(!$data['finished'])
            {
                $result['folder']=$folder;
                $result['offset']=$data['offset'];
                $result['total']=$data['total'];
                return $result;
            }
        }

        return false;
    }

    public function insert_scan_result($uploads_files)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpvivid_scan_result";

        $query = "INSERT INTO $table_name (id,path,from_post) VALUES ";
        $values = array();
        $place_holders=array();
        foreach ( $uploads_files as $id=>$files )
        {
            if(empty($files))
                continue;
            foreach ($files as $path)
            {
                array_push( $values, $path );
                array_push( $values, $id );
                $place_holders[] = "(NULL,'%s',%d)";
            }
        }
        if ( !empty( $values ) )
        {
            $query .= implode( ', ', $place_holders );
            $prepared = $wpdb->prepare( "$query ", $values );
            $wpdb->query( $prepared );
        }
    }

    public function insert_unused_uploads_files($folder,$uploads_files)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpvivid_unused_uploads_files";

        $query = "INSERT INTO $table_name (id,path,folder) VALUES ";
        $values = array();
        $place_holders=array();
        foreach ( $uploads_files as $path )
        {
            array_push( $values, $path );
            array_push( $values, $folder );
            $place_holders[] = "(NULL,'%s','%s')";
        }

        if ( !empty( $values ) )
        {
            $query .= implode( ', ', $place_holders );
            $prepared = $wpdb->prepare( "$query ", $values );
            $wpdb->query( $prepared );
        }
    }

    public function is_uploads_files_exist($file)
    {
        global $wpdb;

        $file=str_replace('\\','/',$file);

        $table = $wpdb->prefix . "wpvivid_scan_result";
        $row = $wpdb->get_row( "SELECT * FROM $table WHERE path = '$file'" );
        if (empty($row))
        {
            $quick_scan=get_option('wpvivid_uc_quick_scan',false);

            if(!$quick_scan)
            {
                $attachment_id=$this->find_media_id_from_file($file);
                if($attachment_id)
                {
                    if(isset($this->file_found_cache[$attachment_id]))
                    {
                        if($this->file_found_cache[$attachment_id])
                        {
                            return true;
                        }
                        else
                        {
                            return false;
                        }
                    }

                    $files=$this->get_img_from_id($attachment_id);

                    if(!empty($files))
                    {
                        $files = implode("','",$files);
                        $sql= "SELECT * FROM $table WHERE path IN ('$files')";
                        $row = $wpdb->get_row($sql);

                        if (!empty($row))
                        {
                            $this->file_found_cache[$attachment_id]=1;
                            return true;
                        }
                        else
                        {
                            $this->file_found_cache[$attachment_id]=0;
                        }
                    }
                }
            }

            return false;
        }
        return true;
    }

    public function find_media_id_from_file( $file )
    {
        global $wpdb;

        $file=basename($file);

        $sql = "SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_wp_attachment_metadata'
			AND meta_value LIKE '%$file%'";

        $ret = $wpdb->get_var( $sql );

        if(!$ret)
        {
            $sql = $wpdb->prepare( "SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_wp_attached_file'
			AND meta_value = %s", $file
            );
            $ret = $wpdb->get_var( $sql );
        }
        return $ret;
    }

    public function get_scan_result($search,$folder)
    {
        global $wpdb;

        $where='';
        if(!empty($search)||!empty($folder))
        {
            $where='WHERE ';
            if(!empty($search))
            {
                $where.="`path` LIKE '%$search%'";
            }

            if(!empty($search)&&!empty($folder))
            {
                $where.=' AND ';
            }

            if(!empty($folder))
            {
                $where.="`folder` = '$folder'";
            }
        }

        $table = $wpdb->prefix . "wpvivid_unused_uploads_files";

        $sql="SELECT * FROM `$table` ".$where;

        return $wpdb->get_results($sql,ARRAY_A);
    }

    public function get_scan_result_count()
    {
        global $wpdb;

        $table = $wpdb->prefix . "wpvivid_unused_uploads_files";
        $sql="SELECT COUNT(*) FROM $table";

        $result=$wpdb->get_results($sql,ARRAY_N);
        if($result)
        {
            return $count=$result[0][0];
        }
        else
        {
            return false;
        }
    }

    public function get_scan_result_size()
    {
        $task=get_option('unused_uploads_task',array());

        if(empty($task))
        {
            return false;
        }
        else if(isset($task['size']))
        {
            return size_format($task['size'],2);
        }
        else
        {
            return false;
        }
    }

    public function get_all_folder()
    {
        global $wpdb;

        $table = $wpdb->prefix . "wpvivid_unused_uploads_files";
        $sql="SELECT * FROM $table GROUP BY `folder`";

        $result=$wpdb->get_results($sql,ARRAY_A);

        if($result)
        {
            $folders=array();
            foreach ($result as $item)
            {
                if($item['folder']=='.')
                {
                    $folders[]='root';
                }
                else
                {
                    $folders[]=$item['folder'];
                }

            }
           return $folders;
        }
        else
        {
            return false;
        }
    }

    public function get_selected_files_list($selected_list)
    {
        global $wpdb;

        $ids=implode(",",$selected_list);

        $table = $wpdb->prefix . "wpvivid_unused_uploads_files";
        $sql="SELECT * FROM $table WHERE `id` IN ($ids)";
        $result=$wpdb->get_results($sql,ARRAY_A);
        if($result)
        {
            $files=array();
            foreach ($result as $item)
            {
                $files[]=$item['path'];
            }
            return $files;
        }
        else
        {
            return false;
        }
    }

    public function delete_selected_files_list($selected_list)
    {
        global $wpdb;

        $table = $wpdb->prefix . "wpvivid_unused_uploads_files";

        $ids=implode(",",$selected_list);

        $sql="DELETE FROM $table WHERE `id` IN ($ids)";

        $result=$wpdb->query($sql);
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_all_files_list($search,$folder,$offset,$count)
    {
        global $wpdb;

        $where='';
        if(!empty($search)||!empty($folder))
        {
            $where='WHERE ';
            if(!empty($search))
            {
                $where.="`path` LIKE '%$search%'";
            }

            if(!empty($search)&&!empty($folder))
            {
                $where.=' AND ';
            }

            if(!empty($folder))
            {
                $where.="`folder` = '$folder'";
            }
        }
        $where.=" LIMIT $offset,$count";
        //LIMIT

        $table = $wpdb->prefix . "wpvivid_unused_uploads_files";
        $sql="SELECT * FROM $table ".$where;
        $result=$wpdb->get_results($sql,ARRAY_A);
        if($result)
        {
            $files=array();
            foreach ($result as $item)
            {
                $files[]=$item['path'];
            }
            return $files;
        }
        else
        {
            return false;
        }
    }

    public function delete_all_files_list($search,$folder,$count)
    {
        global $wpdb;

        $where='';
        if(!empty($search)||!empty($folder))
        {
            $where='WHERE ';
            if(!empty($search))
            {
                $where.="`path` LIKE '%$search%'";
            }

            if(!empty($search)&&!empty($folder))
            {
                $where.=' AND ';
            }

            if(!empty($folder))
            {
                $where.="`folder` = '$folder'";
            }
        }
        $where.=" LIMIT $count";
        //LIMIT

        $table = $wpdb->prefix . "wpvivid_unused_uploads_files";
        $sql="DELETE FROM $table ".$where;

        $result=$wpdb->query($sql);
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}