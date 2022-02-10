<?php

abstract class SwpmProtectionBase {

    protected $bitmap;
    protected $posts;
    protected $pages;
    protected $comments;
    protected $categories;
    protected $attachments;
    protected $custom_posts;
    protected $details;
    protected $options;

    private function __construct() {

    }

    protected function init($level_id) {
        global $wpdb;
        $this->owning_level_id = $level_id;
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}swpm_membership_tbl WHERE "
                . (is_numeric($level_id) ? 'id = %d' : 'md5(id) = %s' ), $level_id);
        $result = $wpdb->get_row($query);

        $this->bitmap = isset($result->permissions) ? $result->permissions : 0;
        $this->posts = isset($result->post_list) ? (array) unserialize($result->post_list) : array();
        $this->pages = isset($result->page_list) ? (array) unserialize($result->page_list) : array();
        $this->comments = isset($result->comment_list) ? (array) unserialize($result->comment_list) : array();
        $this->categories = isset($result->category_list) ? (array) unserialize($result->category_list) : array();
        $this->attachments = isset($result->attachment_list) ? (array) unserialize($result->attachment_list) : array();
        $this->custom_posts = isset($result->custom_post_list) ? (array) unserialize($result->custom_post_list) : array();
        $this->options = isset($result->options) ? (array) unserialize($result->options) : array();
        $this->disable_bookmark = isset($result->disable_bookmark_list) ? (array) unserialize($result->disable_bookmark_list) : array();
        $this->details = (array) $result;
    }

    public function apply($ids, $type) {
        $post_types = get_post_types(array('public' => true, '_builtin' => false));
        if (in_array($type, $post_types)) {
            $type = 'custom_post';
        }
        return $this->update_perms($ids, true, $type);
    }

    public function remove($ids, $type) {
        $post_types = get_post_types(array('public' => true, '_builtin' => false));
        if (in_array($type, $post_types)) {
            $type = 'custom_post';
        }
        return $this->update_perms($ids, false, $type);
    }

    public function get_options() {
        return $this->options;
    }

    public function get_posts() {
        return $this->posts;
    }

    public function get_pages() {
        return $this->pages;
    }

    public function get_comments() {
        return $this->comments;
    }

    public function get_categories() {
        return $this->categories;
    }

    public function get_attachments() {
        return $this->attachments;
    }

    public function get_custom_posts() {
        return $this->custom_posts;
    }

    public function is_bookmark_disabled($id) {
        $posts = isset($this->disable_bookmark['posts']) ?
                (array) $this->disable_bookmark['posts'] : array();
        $pages = isset($this->disable_bookmark['pages']) ?
                (array) $this->disable_bookmark['pages'] : array();
        return in_array($id, $pages) || in_array($id, $posts);
    }

    public function in_posts($id) {
        return (/* ($this->bitmap&4)===4) && */in_array($id, (array) $this->posts));
    }

    public function in_pages($id) {
        return (/* ($this->bitmap&8)===8) && */ in_array($id, (array) $this->pages));
    }

    public function in_attachments($id) {
        return (/* ($this->bitmap&16)===16) && */in_array($id, (array) $this->attachments));
    }

    public function in_custom_posts($id) {
        return (/* ($this->bitmap&32)===32) && */ in_array($id, (array) $this->custom_posts));
    }

    public function in_comments($id) {
        return (/* ($this->bitmap&2)===2) && */ in_array($id, (array) $this->comments));
    }

    public function in_categories($id) {
        if (empty($this->categories))
            return false;
        return (/* ($this->bitmap&1)===1) && */ in_array($id, (array) $this->categories));
    }

    public function post_in_categories($post_id) {
        if (empty($this->categories)){
            return false;
        }
        $taxonomies = get_taxonomies(array('public' => true,'_builtin'=>false));
        if (!is_array($taxonomies) || empty($taxonomies)) {
        	$taxonomies = 'category';
        } else {
        	$taxonomies['category'] = 'category';
    	}
        $terms = wp_get_post_terms( $post_id, $taxonomies, array('fields'=>'ids'));
        if(!is_array($terms)){
            return false;
        }
        
        foreach ($terms as $key=>$value){
            if (in_array($value, $this->categories)) {return true;}
        }
        return false;               
    }

    public function in_parent_categories($id) {
    if (empty($this->categories)){
            return false;
        }
        $taxonomies = get_taxonomies(array('public' => true,'_builtin'=>false));
        if (!is_array($taxonomies) || empty($taxonomies)) {
        	$taxonomies = 'category';
        } else {
        	$taxonomies['category'] = 'category';
    	}
        $terms = get_term($id, $taxonomies);
        if(!is_array($terms)){
            return false;
        }
        
        foreach ($terms as $term){
            if ($term->parent == 0) {continue;}
            
            if (in_array($term->parent, $this->categories)) {return true;}
        }
        return false;
    }

    public function post_in_parent_categories($post_id) {
        if (empty($this->categories)){
            return false;
        }
        $taxonomies = get_taxonomies(array('public' => true,'_builtin'=>false));
        if (!is_array($taxonomies) || empty($taxonomies)) {
        	$taxonomies = 'category';
        } else {
        	$taxonomies['category'] = 'category';
    	}
        $terms = wp_get_post_terms( $post_id, $taxonomies, array('fields'=>'all'));
        if(!is_array($terms)){
            return false;
        }
        
        foreach ($terms as $term){
            if ($term->parent != 0 &&in_array($term->parent, $this->categories)) {
                return true;                
            }            
        }

        return false;
    }

    public function add_posts($ids) {
        return $this->update_perms($ids, true, 'post');
    }

    public function add_pages($ids) {
        return $this->update_perms($ids, true, 'page');
    }

    public function add_attachments($ids) {
        return $this->update_perms($ids, true, 'attachment');
    }

    public function add_comments($ids) {
        return $this->update_perms($ids, true, 'comment');
    }

    public function add_categories($ids) {
        return $this->update_perms($ids, true, 'category');
    }

    public function add_custom_posts($ids) {
        return $this->update_perms($ids, true, 'custom_post');
    }

    public function remove_posts($ids) {
        return $this->update_perms($ids, false, 'post');
    }

    public function remove_pages($ids) {
        return $this->update_perms($ids, false, 'page');
    }

    public function remove_attachments($ids) {
        return $this->update_perms($ids, false, 'attachment');
    }

    public function remove_comments($ids) {
        return $this->update_perms($ids, false, 'comment');
    }

    public function remove_categories($ids) {
        return $this->update_perms($ids, false, 'category');
    }

    public function remove_custom_posts($ids) {
        return $this->update_perms($ids, false, 'custom_post');
    }

    private function update_perms($ids, $set, $type) {
        $list = null;
        $index = '';
        if (empty($ids)) {
            return $this;
        }
        $ids = (array) $ids;
        switch ($type) {
            case 'page':
                $list = $this->pages;
                $index = 'page_list';
                break;
            case 'post':
                $list = $this->posts;
                $index = 'post_list';
                break;
            case 'attachment':
                $list = $this->attachments;
                $index = 'attachment_list';
                break;
            case 'comment':
                $list = $this->comments;
                $index = 'comment_list';
                break;
            case 'category':
                $list = $this->categories;
                $index = 'category_list';
                break;
            case 'custom_post':
                $list = $this->custom_posts;
                $index = 'custom_post_list';
                break;
            default:
                break;
        }

        if (!empty($index)) {
            if ($set) {
                $list = array_merge($list, $ids);
                $list = array_unique($list);
            } else {
                $list = array_diff($list, $ids);
            }
            switch ($type) {
                case 'page':
                    $this->pages = $list;
                    break;
                case 'post':
                    $this->posts = $list;
                    break;
                case 'attachment':
                    $this->attachments = $list;
                    break;
                case 'comment':
                    $this->comments = $list;
                    break;
                case 'category':
                    $this->categories = $list;
                    break;
                case 'custom_post':
                    $this->custom_posts = $list;
                    break;
                default:
                    break;
            }
            $this->details[$index] = $list;
        }
        return $this;
    }

    public function save() {
        global $wpdb;
        $data = array();

        $list_type = array('page_list', 'post_list', 'attachment_list',
            'custom_post_list', 'comment_list', 'category_list');
        foreach ($this->details as $key => $value) {
            if ($key == 'id')
                continue;
            if (is_serialized($value) || !in_array($key, $list_type))
                $data[$key] = $value;
            else
                $data[$key] = serialize($value);
        }
        $wpdb->update($wpdb->prefix . "swpm_membership_tbl", $data, array('id' => $this->owning_level_id));
    }

    public function get($key, $default = '') {
        if (isset($this->details[$key])) {
            return $this->details[$key];
        }
        return $default;
    }

}
