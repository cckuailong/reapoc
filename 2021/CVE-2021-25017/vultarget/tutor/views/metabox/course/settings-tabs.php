<?php
$args = $this->args;
$current_tab = tutils()->array_get('settings_tab', $_GET);

?>

<div id="tutor-metabox-course-settings-tabs" class="tutor-course-settings-tabs">

	<?php
	if ( ! $this->is_gutenberg_enable){
		?>
        <div class="settings-tabs-heading">
            <h3><?php _e('Course Settings', 'tutor'); ?></h3>
        </div>
		<?php
	}
	?>

    <div class="course-settings-tabs-container">
        <div class="settings-tabs-navs-wrap">
            <ul class="settings-tabs-navs">
				<?php
				$i = 0;
				foreach ($args as $key => $arg){
					$i++;

					if ($current_tab){
						$active = $current_tab === $key ? 'active' : '' ;
					}else{
						$active = $i ===1 ? 'active' : '';
					}

					$label      = tutils()->array_get('label', $arg);
					$icon_class = tutils()->array_get('icon_class', $arg);
					$url        = add_query_arg(array('settings_tab' => $key));

					$icon = '';
					if ($icon_class){
						$icon = "<i class='{$icon_class}'></i>";
					}

					echo "<li class='{$active}'><a href='{$url}' data-target='#settings-tab-{$key}'>{$icon} {$label}</a> </li>";
				} ?>
            </ul>
        </div>

        <div class="settings-tabs-container">
			<?php
			$i = 0;
			foreach ($args as $key => $tab){
				$i++;

				$label = tutils()->array_get('label', $tab);
				$callback = tutils()->array_get('callback', $tab);
				$fields = tutils()->array_get('fields', $tab);

				if ($current_tab){
					$active = $current_tab === $key ? 'active' : '' ;
					$display = $current_tab === $key ? 'block' : 'none' ;
				}else{
					$active = $i ===1 ? 'active' : '';
					$display = $i ===1 ? 'block' : 'none' ;
				}

				echo "<div id='settings-tab-{$key}' class='settings-tab-wrap {$active}' style='display: {$display};'>";

				do_action("tutor_course/settings_tab_content/before", $key, $tab);
				do_action("tutor_course/settings_tab_content/before/{$key}", $tab);

				if (tutils()->count($fields)){
					$this->generate_field($fields);
				}

				/**
				 * Handling Callback
				 */
				if ( $callback && is_callable( $callback ) ) {
					call_user_func( $callback, $key, $tab );
				}

				do_action("tutor_course/settings_tab_content/after", $key, $tab);
				do_action("tutor_course/settings_tab_content/after/{$key}", $tab);

				echo "</div>";
			}
			?>
        </div>

    </div>


</div>
