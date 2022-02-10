    

<div class="swpm-yellow-box">    
    <p>
        <?php echo SwpmUtils::_('First of all, globally protect posts and pages on your site by selecting "General Protection" from the drop-down box below and then select posts and pages that should be protected from non-logged in users.'); ?>
    </p>
    <p>
        <?php echo SwpmUtils::_('Next, select an existing membership level from the drop-down box below and then select posts and pages you want to grant access to (for that particular membership level).'); ?>
    </p>
    <p>    
        <?php echo SwpmUtils::_('Read the '); ?><a href="https://simple-membership-plugin.com/apply-protection-posts-pages-bulk/" target="_blank"><?php echo SwpmUtils::_('bulk protect posts and pages documentation'); ?></a><?php echo SwpmUtils::_(' to learn how to use it.'); ?>
    </p>
</div>
<style>
    #swpm-list-type-nav .nav-tab {
        padding: 1px 15px;
        font-size: 12px;
    }
</style>
<div id="swpm-list-type-nav" class="nav-tab-wrapper">
    <a class="nav-tab<?php echo $post_list->type == 'post' ? ' nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=post_list&list_type=post"><?php SwpmUtils::e('Posts'); ?></a>
    <a class="nav-tab<?php echo $post_list->type == 'page' ? ' nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=post_list&list_type=page"><?php SwpmUtils::e('Pages'); ?></a>
    <a class="nav-tab<?php echo $post_list->type == 'custom_post' ? ' nav-tab-active' : ''; ?>" href="admin.php?page=simple_wp_membership_levels&level_action=post_list&list_type=custom_post"><?php SwpmUtils::e('Custom Posts'); ?></a>
</div>

<br />
<div class="swpm_post_protection_list_form">
<form id="post_list_form" method="post">
    <input type="hidden" name="swpm_post_prot_update_nonce" value="<?php echo wp_create_nonce('swpm_post_prot_update_nonce_action'); ?>" />
    
    <p class="swpm-select-box-left">
        <label for="membership_level_id"><?php SwpmUtils::e('Membership Level:'); ?></label>
        <select id="membership_level_id" name="membership_level_id">
            <option <?php echo $post_list->selected_level_id == 1 ? "selected" : "" ?> value="1"><?php echo SwpmUtils::_('General Protection'); ?></option>
            <?php echo SwpmUtils::membership_level_dropdown($post_list->selected_level_id); ?>
        </select>                
    </p>
    <p class="swpm-select-box-left">
        <input type="submit" class="button-primary" name="update_post_list" value="<?php SwpmUtils::e('Update'); ?>">
    </p>
        <?php $post_list->prepare_items(); ?>   
        <?php $post_list->display(); ?>
    <input type="hidden" name="list_type" value="<?php echo $post_list->type; ?>">
</form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#membership_level_id').change(function () {
            $('#post_list_form').submit();
        });
    });
</script>
