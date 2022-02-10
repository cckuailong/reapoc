
<?php if($term_id){?>
<tr class="form-field">
    <th scope="row" valign="top"><label for="ecwd_taxonomy_image"><?php _e('Image', 'event-calendar-wd');?></label></th>
    <td>
        <?php if($term_meta['ecwd_taxonomy_image'] !== ''){
            ?>
            <img class="taxonomy-image" src="<?php echo $icon=$term_meta['ecwd_taxonomy_image'];?>"/>
        <?php }?>
        <br/><input type="text" name="<?php echo ECWD_PLUGIN_PREFIX;?>_event_category[ecwd_taxonomy_image]" id="ecwd_taxonomy_image" value="<?php echo $icon=$term_meta['ecwd_taxonomy_image'];?>" /><br />
        <button class="ecwd_upload_image_button button"><?php _e('Upload/Add image', 'event-calendar-wd'); ?></button>
        <?php if($icon !== ''){?>
            <button class="ecwd_remove_image_button button"><?php _e('Remove image', 'event-calendar-wd'); ?></button>
        <?php }?>
    </td>
</tr>

<tr class="form-field">
    <th scope="row" valign="top">  
        <label for="<?php echo ECWD_PLUGIN_PREFIX;?>_category_color"><?php _e('Category color', 'event-calendar-wd'); ?></label>
    </th>  
    <td>  
        <div style="width:225px;">
            <input type="text" name="<?php echo ECWD_PLUGIN_PREFIX;?>_event_category[color]" id="<?php echo ECWD_PLUGIN_PREFIX;?>_category_color" style="<?php if(isset($term_meta['color'])){ echo 'background-color: '.$term_meta['color'];}?>" value="<?php if(isset($term_meta['color'])){echo $term_meta['color'];}?>" /><br />
        </div>
        <p class="description"><?php _e('Choose the color', ''); ?></p> 
    </td>
</tr>
<?php }else{?>
    <div class="form-field">
        <label for="ecwd_taxonomy_image"><?php _e('Image', 'event-calendar-wd');?></label>
        <input type="text" name="<?php echo ECWD_PLUGIN_PREFIX;?>_event_category[ecwd_taxonomy_image]" id="ecwd_taxonomy_image" value="" />
        <br/>
        <button class="ecwd_upload_image_button button"><?php _e('Upload/Add image', 'event-calendar-wd'); ?></button>
    </div>
    <div class="form-field">
        <label for="<?php echo ECWD_PLUGIN_PREFIX;?>_category_color"><?php _e('Category color', 'event-calendar-wd'); ?></label>
        <input type="text" name="<?php echo ECWD_PLUGIN_PREFIX;?>_event_category[color]" id="<?php echo ECWD_PLUGIN_PREFIX;?>_category_color" />
    </div>
<?php }?>
