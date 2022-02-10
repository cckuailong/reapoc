<?php

function likebtn_um_profile($args) {
    global $ultimatemember;

    ?>
        <div class="um-profile-edit um-likebtn-profile <?php if (is_user_logged_in() && (!isset($ultimatemember->user->cannot_edit) || $ultimatemember->user->cannot_edit != 1)) :?>um-likebtn-edit<?php endif ?>">
            <?php echo _likebtn_get_content_universal(LIKEBTN_ENTITY_UM_USER, um_user('ID'), '', false); ?>
        </div>
    <?php
}

add_filter('um_pre_header_editprofile', 'likebtn_um_profile');

function likebtn_um_member_directory($user_id) {
    echo _likebtn_get_content_universal(LIKEBTN_ENTITY_UM_USER_LIST, $user_id, '', false);
}

add_filter('um_members_just_after_name', 'likebtn_um_member_directory');

function likebtn_um_profile_tabs($tabs) {
    
    $tabs['likebtn-liked-content'] = array(
        'name' => __('Liked Content', 'likebtn-like-button'),
        'icon' => 'um-faicon-heart',
    );
        
    return $tabs;  
}

add_filter('um_profile_tabs', 'likebtn_um_profile_tabs', 1001);

function likebtn_um_liked_default($args) {
    $widget = new LikeBtnLikeButtonMostLiked(LikeBtnLikeButtonMostLiked::WIDGET_TYPE_BY_UM_USER, false);

    $instance = array(
        'voter' => um_profile_id(),
        'entity_name' => array_keys(_likebtn_get_entities(true, false, false)),
        'title_length' => LIKEBTN_WIDGET_TITLE_LENGTH,
        'order' => 'vote_date',
        'number' => 1000,
    );

    echo $widget->widget(array(), $instance);
}

add_action('um_profile_content_likebtn-liked-content_default', 'likebtn_um_liked_default');
