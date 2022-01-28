<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC feed class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feed extends MEC_base
{
    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return string
     */
    public function title($post_id)
    {
        $title = get_the_title($post_id);
        return apply_filters('the_title_rss', $title);
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return string
     */
    public function permalink($post_id)
    {
        $permalink = get_post_permalink($post_id);
        return esc_url(apply_filters('the_permalink_rss', $permalink));
    }

    /**
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return string
     */
    public function attachment($post_id)
    {
        $main = $this->getMain();

        $featured_link = $main->get_post_thumbnail_url($post_id,'full');
        return esc_url(apply_filters('the_attachment_rss', $featured_link));
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return string
     */
    public function comments_link_feed($post_id)
    {
        return esc_url(apply_filters('comments_link_feed', get_comments_link($post_id)));
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param int $user_id
     * @return string
     */
    public function author($user_id)
    {
        $authordata = get_userdata($user_id);
        return apply_filters('the_author', is_object($authordata) ? $authordata->display_name : null);
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return string
     */
    public function excerpt($post_id)
    {
        $post = get_post($post_id);

        if(empty($post)) return '';
        if(post_password_required($post)) return __('There is no excerpt because this is a protected post.');

        return apply_filters('get_the_excerpt', $post->post_excerpt, $post);
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @param string $feed_type
     * @return string
     */
    public function content($post_id, $feed_type = NULL)
    {
        if(!$feed_type) $feed_type = get_default_feed();
        
        $post = get_post($post_id);
        $content = str_replace(']]>', ']]&gt;', apply_filters('the_content', $post->post_content));
        
        return apply_filters('the_content_feed', $content, $feed_type);
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return string
     */
    public function enclosure_rss($post_id)
    {
        $enclosure = '';
        if(post_password_required($post_id)) return $enclosure;
        
        foreach((array) get_post_custom($post_id) as $key=>$val)
        {
            if($key != 'enclosure') continue;
            
            foreach((array) $val as $enc)
            {
                $enc = explode("\n", $enc);

                $t = preg_split('/[ \t]/', trim($enc[2]));
                $type = $t[0];

                $enclosure .= apply_filters('rss_enclosure', '<enclosure url="'.trim(htmlspecialchars($enc[0])).'" length="'.trim($enc[1]).'" type="'.$type.'" />'."\n");
            }
        }
        
        return $enclosure;
    }
    
    /**
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return string
     */
    public function enclosure_atom($post_id)
    {
        $enclosure = '';
        if(post_password_required($post_id)) return $enclosure;

        foreach((array) get_post_custom() as $key=>$val)
        {
            if($key != 'enclosure') continue;
            foreach((array) $val as $enc)
            {
                $enc = explode("\n", $enc);
                $enclosure .= apply_filters('atom_enclosure', '<link href="'.trim(htmlspecialchars($enc[0])).'" rel="enclosure" length="'.trim($enc[1]).'" type="'.trim($enc[2]).'" />'."\n");
            }
        }
        
        return $enclosure;
    }
}