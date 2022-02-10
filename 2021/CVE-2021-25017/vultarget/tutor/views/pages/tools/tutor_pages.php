<?php
$tutor_pages = tutils()->tutor_pages();
?>

<div id="tools-tutor-pages" class="tools-tutor-pages">

    <table class="tutor-table tutor-pages-table">

        <thead>
        <tr>
            <th><?php _e('ID', 'tutor'); ?></th>
            <th><?php _e('Page Name', 'tutor'); ?></th>
            <th><?php _e('Status', 'tutor'); ?></th>

        </tr>
        </thead>

        <tbody>

		<?php
		foreach ($tutor_pages as $page){
		    $page_id = $page['page_id'];
			?>
            <tr>
                <td><?php echo $page_id; ?></td>
                <td>
					<?php
					echo "<p>";

					if ($page['page_exists']){
						$edit_url = admin_url("post.php?post={$page_id}&action=edit");
						echo "<a href='{$edit_url}' target='_blank'>";
					}
					echo $page['page_name'];

					if ($page['page_exists']){
						echo '</a>';
					}
					echo "</p>";
					?>
                </td>
                
                <td>
                    <?php
                    if ( ! $page_id ){
	                    echo '<p style="color: red;">';
	                    echo "<i class='dashicons dashicons-warning'></i> "; _e(' Page not set', 'tutor');
	                    echo '</p>';
                    }

                    if ( ! $page['page_exists'] ){
	                    echo '<p style="color: red;">';
	                    echo "<i class='dashicons dashicons-warning'></i> "; _e(' Page deleted, please set new one', 'tutor');
	                    echo '</p>';
                    }

                    if ($page['page_exists'] && ! $page['page_visible']){
	                    echo '<p style="color: red;">';
	                    echo "<i class='dashicons dashicons-warning'></i> "; _e('Page visibility is not public', 'tutor');
	                    echo '</p>';
                    }

                    if ($page['page_exists'] && $page['page_visible']){
	                    $page = get_post($page_id);

	                    echo "<a href='".get_permalink($page)."' target='_blank' style='color: green;'> <i class='dashicons dashicons-yes-alt'></i> /{$page->post_name} </a>";
                    }
                    ?>
                </td>
            </tr>
			<?php
		}
		?>
        </tbody>
    </table>

    <form action="" method="post">
        <?php
        tutor_action_field('regenerate_tutor_pages');
        tutor_nonce_field(); ?>

        <p>
            <button class="tutor-button tutor-button-primary" type="submit"><?php _e('Re-Generate Tutor Pages','tutor'); ?></button>
        </p>
    </form>

	<?php
	tutor_alert(__('Note: This tool will install all the missing Tutor pages. Pages already defined and set up will not be replaced.', 'tutor'), 'info');
	?>
</div>