<?php
/**
 * Most Liked Content tempplate
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
?>

<?php if (count($post_loop) > 0): ?>
    <div class="um-profile-note" style="display: block;"><span><?php echo __('Liked Content', 'likebtn-like-button'); ?></span></div>

    <?php foreach ($post_loop as $post): ?>
        <div class="um-item">
            <div class="um-item-link">
                <?php if ($post['type'] == LIKEBTN_ENTITY_COMMENT) :?>
                    <i class="um-icon-chatboxes"></i>
                <?php elseif (in_array($post['type'], array(LIKEBTN_ENTITY_USER, LIKEBTN_ENTITY_UM_USER, LIKEBTN_ENTITY_BP_MEMBER, LIKEBTN_ENTITY_BBP_USER))): ?>
                    <i class="um-faicon-user"></i>
                <?php else: ?>
                    <i class="um-icon-ios-paper"></i>
                <?php endif ?>
                <a href="<?php echo $post['link'] ?>"><?php echo $post['title'] ?></a>
            </div>
            <div class="um-item-meta">
                <span><?php echo date_i18n(get_option('date_format'), strtotime($post['vote_date'])) ?></span>
                <span class="badge">
                    <?php if ($post['vote_type'] == 1): ?>
                        <i class="um-faicon-thumbs-o-up"></i>
                    <?php else: ?>
                        <i class="um-faicon-thumbs-o-down"></i>
                    <?php endif ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: // No items ?>
    <div class="um-profile-note" style="display: block;"><span><?php echo __('No Content Liked Yet', 'likebtn-like-button'); ?></span></div>
<?php
endif;
?>