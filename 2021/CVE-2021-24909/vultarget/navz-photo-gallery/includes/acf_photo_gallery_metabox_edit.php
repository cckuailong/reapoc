<?php
    // exit if accessed directly
    if( ! defined( 'ABSPATH' ) ) exit;
?>
<div id="acf-photo-gallery-metabox-edit-<?php echo esc_attr($attachment); ?>" class="acf-edit-photo-gallery">
    <h3>Edit Image</h3>
    <input class="acf-photo-gallery-edit-field" type="hidden" name="acf-pg-hidden-field" value="<?php echo esc_attr($field); ?>"/>
    <input class="acf-photo-gallery-edit-field" type="hidden" name="acf-pg-hidden-post" value="<?php echo esc_attr($_GET['post']); ?>"/>
    <input class="acf-photo-gallery-edit-field" type="hidden" name="acf-pg-hidden-attachment" value="<?php echo esc_attr($attachment); ?>"/>
    <input class="acf-photo-gallery-edit-field" type="hidden" name="acf-pg-hidden-action" value="acf_photo_gallery_edit_save"/>
    <input class="acf-photo-gallery-edit-field" type="hidden" name="acf-pg-hidden-nonce" value="<?php echo $nonce; ?>"/>
    <?php
        foreach( $fields as $key => $item ){
            $type = esc_attr($item['type'])?$item['type']:null;
            $label = esc_attr($item['label'])?$item['label']:null;
            $name = esc_attr($item['name'])?$item['name']:null;
            $value = esc_attr($item['value'])?$item['value']:null;
    ?>
        <?php if( in_array($type, array('text', 'date', 'color', 'datetime-local', 'email', 'number', 'tel', 'time', 'url', 'week', 'range')) ){ ?>
            <label><?php echo esc_attr($label); ?></label>
            <input class="acf-photo-gallery-edit-field" type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>"/>
        <?php } ?>
        <?php if( $type == 'checkbox' ){ ?>
            <label>
                <input class="acf-photo-gallery-edit-field" type="checkbox" name="<?php echo esc_attr($name); ?>" value="true" <?php echo ($value=='true')?'checked':''; ?>/>
                <?php echo esc_attr($label); ?>
            </label>
        <?php } ?>
        <?php if( $type == 'radio' ){ ?>
            <label>
                <input class="acf-photo-gallery-edit-field" type="radio" name="<?php echo esc_attr($name); ?>" value="true" <?php echo ($value=='true')?'checked':''; ?>/>
                <?php echo esc_attr($label); ?>
            </label>
        <?php } ?>
        <?php if( $type == 'textarea' ){ ?>
            <label><?php echo esc_attr($label); ?></label>
            <textarea class="acf-photo-gallery-edit-field" name="<?php echo esc_attr($name); ?>"><?php echo esc_textarea($value); ?></textarea>
        <?php } ?>
        <?php if( $type == 'select' ){ ?>
            <label><?php echo esc_attr($label); ?></label>
            <select class="acf-photo-gallery-edit-field" name="<?php echo esc_attr($name); ?>">
                <?php foreach($value[0] as $key => $item){ ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php echo esc_attr($key==$value[1]?'selected':''); ?>><?php echo esc_attr($item); ?></option>
                <?php } ?>
            </select>
        <?php } ?>
    <?php } ?>
    <div class="save-changes-wrap">
        <button class="button button-primary button-large" type="submit" data-fieldname="<?php echo esc_attr($acf_fieldkey); ?>" data-id="<?php echo esc_attr($attachment); ?>" data-ajaxurl="<?php echo admin_url('admin-ajax.php'); ?>">Save Changes</button>
        <button class="button button-large button-close" type="button" data-close="<?php echo esc_attr($attachment); ?>">Close</button>
    </div>
</div>