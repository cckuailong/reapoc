<?php

class SwpmCommentFormRelated {

    public static function customize_comment_form() {
        $allow_comments = SwpmSettings::get_instance()->get_value('members-login-to-comment');
        if (empty($allow_comments)){
            return;
        }        
        if (SwpmAuth::get_instance()->is_logged_in()){
            return;            
        }
        
        //Apply a filter to the message so it can be customized using the custom message plugin
        $comment_form_msg = apply_filters('swpm_login_to_comment_msg', SwpmUtils::_("Please login to comment."));
        $comment_form_msg = '<div class="swpm-login-to-comment-msg">' . $comment_form_msg . '</div>';
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#respond').html('<?php echo $comment_form_msg; ?>');
            });
        </script>
        <?php        
    }
    
    public static function customize_comment_fields($fields){
        
        //Check if login to comment feature is enabled.
        $allow_comments = SwpmSettings::get_instance()->get_value('members-login-to-comment');
        if (empty($allow_comments)){//Feature is disabled
            return $fields;
        }        
        
        if (SwpmAuth::get_instance()->is_logged_in()){//Member is logged-in.
            return $fields;
        }
        
        //Member is not logged-in so show the protection message.
        $fields = array();
        $login_link = SwpmUtils::_('Please Login to Comment.');
        $fields['comment_field'] = $login_link;
        $fields['title_reply'] = '';
        $fields['cancel_reply_link'] = '';
        $fields['comment_notes_before'] = '';
        $fields['comment_notes_after'] = '';
        $fields['fields'] = '';
        $fields['label_submit'] = '';
        $fields['title_reply_to'] = '';
        $fields['id_submit'] = '';
        $fields['id_form'] = '';
        
        return $fields;        
    }
    
    /*
     * This function checks and restricts comment posting (via HTTP POST) to members only (if the feature is enabled)
     */
    public static function check_and_restrict_comment_posting_to_members(){    
        $allow_comments = SwpmSettings::get_instance()->get_value('members-login-to-comment');
        if (empty($allow_comments)){
            return;
        }
         
        if (is_admin()) {
            return;            
        }          
             
        if (SwpmAuth::get_instance()->is_logged_in()){
            return;            
        }
        
        $comment_id = filter_input(INPUT_POST, 'comment_post_ID');
        if (empty($comment_id)) {
            return;            
        }
        
        //Stop this request -> 1)we are on the front-side. 2) Comment posted by a not logged in member. 3) comment_post_ID missing. 
        $_POST = array();        
        wp_die(SwpmUtils::_('Comments not allowed by a non-member.'));
    }
    
}