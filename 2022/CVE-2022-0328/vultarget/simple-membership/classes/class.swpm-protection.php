<?php

include_once('class.swpm-protection-base.php');

class SwpmProtection extends SwpmProtectionBase {

    private static $_this;

    private function __construct() {
        $this->msg = "";
        $this->init(1);
    }

    public static function get_instance() {
        self::$_this = empty(self::$_this) ? (new SwpmProtection()) : self::$_this;
        return self::$_this;
    }

    public function is_protected($id) {
        if ($this->post_in_parent_categories($id) || $this->post_in_categories($id)) {
            $this->msg = '<p style="background: #FFF6D5; border: 1px solid #D1B655; color: #3F2502; margin: 10px 0px 10px 0px; padding: 5px 5px 5px 10px;">
                    ' . SwpmUtils::_('The category or parent category of this post is protected. You can change the category protection settings from the ') . 
                    '<a href="admin.php?page=simple_wp_membership_levels&level_action=category_list" target="_blank">' . SwpmUtils::_('category protection menu') . '</a>.
                    </p>';
            return true;
        }
        return $this->in_posts($id) || $this->in_pages($id) || $this->in_attachments($id) || $this->in_custom_posts($id);
    }

    public function get_last_message() {
        return $this->msg;
    }

    public function is_protected_post($id) {
        return /* (($this->bitmap&4) != 4) && */ $this->in_posts($id);
    }

    public function is_protected_page($id) {
        return /* (($this->bitmap&4) != 4) && */ $this->in_pages($id);
    }

    public function is_protected_attachment($id) {
        return /* (($this->bitmap&16)!=16) && */ $this->in_attachments($id);
    }

    public function is_protected_custom_post($id) {
        return /* (($this->bitmap&32)!=32) && */ $this->in_custom_posts($id);
    }

    public function is_protected_comment($id) {
        return /* (($this->bitmap&2)!=2) && */ $this->in_comments($id);
    }

    public function is_post_in_protected_category($post_id) {
        return /* (($this->bitmap&1)!=1) && */ $this->post_in_categories($post_id);
    }

    public function is_post_in_protected_parent_category($post_id) {
        return /* (($this->bitmap&1)!=1) && */ $this->post_in_parent_categories($post_id);
    }

    public function is_protected_category($id) {
        return /* (($this->bitmap&1)!=1) && */ $this->in_categories($id);
    }

    public function is_protected_parent_category($id) {
        return /* (($this->bitmap&1)!=1) && */ $this->in_parent_categories($id);
    }

}
