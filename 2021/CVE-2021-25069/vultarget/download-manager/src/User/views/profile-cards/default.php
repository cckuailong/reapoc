<?php
/**
 * User: shahnuralam
 * Date: 6/25/18
 * Time: 12:29 AM
 */
if (!defined('ABSPATH')) die();
?>
<div class="card card-author">
    <div class="card-body text-center">
        <?php echo get_avatar($user->user_email, 512, '', $user->display_name, array('class' => 'img-circle')); ?>
        <h3 class="author-name"><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo $user->display_name; ?></a></h3>
        <div class="text-muted"><?php echo count_user_posts( $user->ID , "wpdmpro"  ); ?> Stories</div>
    </div>
</div>

