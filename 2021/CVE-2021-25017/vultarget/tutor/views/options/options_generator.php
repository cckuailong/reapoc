<div class="wrap">
    <h1><?php _e('Tutor Settings', 'tutor'); ?></h1>


    <form id="tutor-option-form" class="tutor-option-form" method="post" data-toast_success_message="<?php _e('Settings Saved', 'tutor'); ?>">
        <input type="hidden" name="action" value="tutor_option_save" >

		<?php
		$options_attr = $this->options_attr();

		if (is_array($options_attr) && count($options_attr)){
			$first_item = null;
			?>
            <ul class="tutor-option-nav-tabs">
				<?php
                $tab_page = sanitize_text_field(tutils()->array_get('tab_page', $_GET));
				foreach ($options_attr as $key => $option_group){
					if (empty($option_group)){
						continue;
					}
					if ( ! $first_item){
						$first_item = $key;
					}
					$current_page = ($first_item === $key);
					$current_class = $current_page ? 'current' : '';
					if ($tab_page){
						$current_class = $tab_page === $key? 'current' : '';
					}
					
					$nav_url = add_query_arg(array('tab_page' => $key));
					echo "<li class='option-nav-item {$current_class}'><a href='{$nav_url}' data-tab='#{$key}' class='tutor-option-nav-item'>{$option_group['label']}</a> </li>";
				}
				?>
            </ul>

			<?php
			foreach ($options_attr as $key => $option_group){
				if (empty($option_group)){
					continue;
				}
				$current_page = ($first_item === $key);
				if ($tab_page){
					$current_page = $tab_page === $key? 'current' : '';
				}

				?>

                <div id="<?php echo $key; ?>" class="tutor-option-nav-page <?php echo $current_page ? 'current-page' : ''; ?> " style="display: <?php echo $current_page ? 'block' : 'none' ?>;" >
                    <!--<h3><?php /*echo $option_group['label']; */?></h3>-->

					<?php
					do_action('tutor_options_before_'.$key);

					if (!empty($option_group['sections'])){
						foreach ($option_group['sections'] as $fgKey => $field_group){
							?>

                            <div class="tutor-option-field-row">
                                <h2><?php echo $field_group['label']; ?></h2>
                            </div>

							<?php
                            do_action("tutor_options_{$key}_{$fgKey}_before");
                            if ( ! empty($field_group['fields']) && tutor_utils()->count($field_group['fields'])) {
	                            foreach ( $field_group['fields'] as $field_key => $field ) {
		                            $field['field_key'] = $field_key;
		                            echo $this->generate_field( $field );
	                            }
                            }
							do_action("tutor_options_{$key}_{$fgKey}_after");
						}
					}

					do_action('tutor_options_after_'.$key);

					?>
                </div>
				<?php
			}
		}
		?>

        <p class="submit">
            <button type="button" id="save_tutor_option" class="button button-primary"><?php echo __('Save Settings', 'tutor') ?></button>
        </p>
    </form>


</div>


