<?php
/**
 * Like box tempplate
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
?>

<?php if (count($user_loop) > 0): ?>
    <div class="likebtn-likebox">
        <?php if ($text): ?>
            <div class="likebtn-likebox-txt">
                <?php echo $text; ?>
            </div>
        <?php endif ?>
    	<div class="likebtn-likebox-list">
    	<?php foreach ($user_loop as $user): ?>
    		<div class="likebtn-likebox-user" >
                <a href="<?php echo $user['url']?>" title="<?php echo $user['name'] ?>" class="likebtn-likebox-lnk"><img width="32" height="32" alt="<?php echo $user['name'] ?>" class="avatar avatar-32 user-<?php echo $user['user_id']?>-avatar gravatar" src="<?php echo $user['avatar']?>"></a>
    		</div>
    	<?php endforeach; ?>
    	</div>
    </div>
<?php endif ?>