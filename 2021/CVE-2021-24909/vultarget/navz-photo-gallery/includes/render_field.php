<?php

    // exit if accessed directly
    if( ! defined( 'ABSPATH' ) ) exit;


    global $pagenow;
    if( $pagenow == 'edit-tags.php'){
        echo 'ACF Photo Gallery Field is not supported on taxonomy.';
    } else {

    $remove_edit_button = "";
    $remove_delete_button = "";
    $images_limit = "";
    
    global $post;    
    $nonce_acf_photo_gallery = wp_create_nonce( 'nonce_acf_photo_gallery' );
    if( ACF_VERSION >= 4 and ACF_VERSION < 5 ){
        $fieldname = $field['_name'];
        $value = $field['value'];
        $key = $field['key'];
        $edit_model = (!empty($field['edit_modal']))? esc_attr($field['edit_modal']):'Default';
    } else if( ACF_VERSION >= 5 and ACF_VERSION < 6 ){
        $fieldname = $field['_name'];
        $value = $field['value'];
        $key = $field['key'];
        $remove_edit_button = (!empty($field['fields[' . $fieldname]['remove_edit_button']))? esc_attr($field['fields[' . $fieldname]['remove_edit_button']):null;    
        $remove_delete_button = (!empty($field['fields[' . $fieldname]['remove_delete_button']))? esc_attr($field['fields[' . $fieldname]['remove_delete_button']):null;    
        $edit_model = (!empty($field['fields[' . $fieldname]['edit_modal']))? esc_attr($field['fields[' . $fieldname]['edit_modal']):'Default';    
        $images_limit = (!empty($field['fields[' . $fieldname]['images_limit']))? esc_attr($field['fields[' . $fieldname]['images_limit']):null;    
        $replace_textarea_editor = (!empty($field['fields[' . $fieldname]['replace_caption_tinymce']))? esc_attr($field['fields[' . $fieldname]['replace_caption_tinymce']):null;    
    }
?>
<div class="acf-photo-gallery-group-<?php echo esc_attr($key); ?>">
    <input type="hidden" name="acf-photo-gallery-edit-modal" value="<?php echo esc_attr($edit_model); ?>" />
    <input type="hidden" name="acf-photo-gallery-groups[]" value="<?php echo esc_attr($field['_name']); ?>"/>
    <input type="hidden" name="acf-photo-gallery-images_limit" value="<?php echo esc_attr($images_limit); ?>"/>
    <input type="hidden" name="acf-photo-gallery-field" value="<?php echo esc_attr($key); ?>"/>
    <div id="acf-photo-gallery-metabox-edit">
        <?php
            if( $value ):
                $acf_photo_gallery_editbox_caption_from_attachment = apply_filters( 'acf_photo_gallery_editbox_caption_from_attachment', $field);
                $acf_photo_gallery_attachments =  $value;
                $acf_photo_gallery_attachments = explode(',', $acf_photo_gallery_attachments);
                $args = array( 'post_type' => 'attachment', 'posts_per_page' => -1, 'post__in' => $acf_photo_gallery_attachments );
                $acf_photo_gallery_attachments = get_posts( $args );
                $nonce = wp_create_nonce('acf_photo_gallery_edit_save');
                foreach($acf_photo_gallery_attachments as $attachment):
                    $id = $attachment->ID;
                    $url = get_post_meta($id, $field['_name'] . '_url', true);
                    $target = get_post_meta($id, $field['_name'] . '_target', true);
                    $title = $attachment->post_title;
                    if( $acf_photo_gallery_editbox_caption_from_attachment == 1 ){
                        $caption = wp_get_attachment_caption( $id );
                    } else {
                        $caption = $attachment->post_content;
                    }
                    acf_photo_gallery_edit($field['_name'], $nonce, $id, $url, $title, $caption, $target, $key, $replace_textarea_editor);
                endforeach;
            endif;
        ?>
    </div>
    <ul id="acf-photo-gallery-metabox-list" class="acf-photo-gallery-metabox-list">
        <?php
            if( $value ):
                $acf_photo_gallery_attachments =  $value;
                $acf_photo_gallery_attachments = explode(',', $acf_photo_gallery_attachments);
                foreach($acf_photo_gallery_attachments as $image):
        ?>
        <li class="acf-photo-gallery-mediabox acf-photo-gallery-mediabox-<?php echo esc_attr($image); ?>">
            <?php if($remove_edit_button != "Yes") { ?>
                <a class="dashicons dashicons-edit" href="#" title="Edit" data-id="<?php echo esc_attr($image); ?>" data-field="<?php echo esc_attr($key); ?>"></a>
            <?php } ?>
            <?php if($remove_delete_button != "Yes") { ?>
                <a class="dashicons dashicons-dismiss" href="#" data-id="<?php echo esc_attr($image); ?>" data-field="<?php echo esc_attr($key); ?>" title="Remove this photo from the gallery"></a>
            <?php } ?>
            <input type="hidden" name="<?php echo esc_attr($field['_name']); ?>[]" value="<?php echo esc_attr($image); ?>"/>
            <img src="<?php echo wp_get_attachment_thumb_url( $image ); ?>"/>
        </li>
        <?php endforeach; else: ?><li class="acf-photo-gallery-media-box-placeholder"><span class="dashicons dashicons-format-image"></span></li><?php endif; ?>
    </ul>
    <button class="button button-primary button-large acf-photo-gallery-metabox-add-images" type="button" data-field="<?php echo htmlspecialchars(json_encode($field), ENT_QUOTES, 'UTF-8'); ?>">Add Images</button>
</div>
<?php } ?>