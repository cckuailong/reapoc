<p>
    <label for="<?php echo $this->widget->get_field_id('title')?>"><?php _e('Title', CFS_LANG_CODE)?>:</label>
    <?php 
        echo htmlCfs::text($this->widget->get_field_name('title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('title'). '"', 
            'value' => (isset($this->data['title']) ? $this->data['title'] : '')));
    ?><br />
	<?php if(!empty($this->formsList)) {?>
		<label for="<?php echo $this->widget->get_field_id('id')?>"><?php _e('Form to display', CFS_LANG_CODE)?>:</label>
		<?php
			echo htmlCfs::selectbox($this->widget->get_field_name('id'), array(
				'attrs' => 'id="'. $this->widget->get_field_id('id'). '"',
				'value' => (isset($this->data['id']) ? $this->data['id'] : ''),
				'options' => $this->formsList,
			));
		?>
	<?php } else { ?>
		<span class="description">
			<?php _e('You have no Forms for now. Create your first form - and you will be able to select it for widget here.', CFS_LANG_CODE)?>
		</span>
		<br />
		<a href="<?php echo $this->createFormUrl;?>" class="button button-primary">
			<?php _e('Create Form', CFS_LANG_CODE)?>
		</a>
	<?php }?>
</p>