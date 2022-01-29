<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'widgets/html/account.php'); else {
?>
<div class="dbfl" id="rm-user-greeting">
    <strong id="rm-greeting-text"></strong>
        <?php if(!empty($data->user->first_name)): ?>
                <div class="dbfl"><?php echo $data->user->first_name; ?></div>
        <?php else: ?>
                <div class="dbfl"> </span> <?php echo $data->user->display_name; ?></div>
        <?php endif; ?>
</div>
<div class="rm-user-panel-user-image dbfl">
    <?php echo get_avatar($data->user->ID); ?>
</div>
<div class="rm-user-panel-user-details rm-rounded-corners dbfl">
    <div class="rm-panel-row dbfl">
        <div class="rm-panel-field difl"><?php echo RM_UI_Strings::get('FIELD_TYPE_FNAME'); ?></div>
        <div class="rm-panel-value difl"><?php echo $data->user->first_name; ?></div>
    </div>
    <div class="rm-panel-row dbfl">
        <div class="rm-panel-field difl"><?php echo RM_UI_Strings::get('FIELD_TYPE_LNAME'); ?></div>
        <div class="rm-panel-value difl"><?php echo $data->user->last_name; ?></div>
    </div>
    <div class="rm-panel-row dbfl">
        <div class="rm-panel-field difl"><?php echo RM_UI_Strings::get('LABEL_BIO'); ?></div>
        <div class="rm-panel-value difl"><?php echo $data->user->description; ?></div>
    </div>
    <?php
        if ($data->user->user_email)
        {
            ?>

            <div class="rm-panel-row dbfl">
                <div class="rm-panel-field difl"><?php echo RM_UI_Strings::get('LABEL_EMAIL'); ?>:</div>
                <div class="rm-panel-value difl"><?php echo $data->user->user_email; ?></div>
            </div>
            <?php
        }
        if ($data->user->sec_email)
        {
            ?>

            <div class="rm-panel-row dbfl">
                <div class="rm-panel-field difl"><?php echo RM_UI_Strings::get('LABEL_SECEMAIL'); ?>:</div>
                <div class="rm-panel-value difl"><?php echo $data->user->sec_email; ?></div>
            </div>
            <?php
        }
        if ($data->user->nickname)
        {
            ?>

            <div class="rm-panel-row dbfl">
                <div class="rm-panel-field difl"><?php echo RM_UI_Strings::get('FIELD_TYPE_NICKNAME'); ?>:</div>
                <div class="rm-panel-value difl"><?php echo $data->user->nickname; ?></div>
            </div>
            <?php
        }
        if ($data->user->user_url)
        {
            ?>

            <div class="rm-panel-row dbfl">
                <div class="rm-panel-field difl"><?php echo RM_UI_Strings::get('FIELD_TYPE_WEBSITE'); ?>:</div>
                <div class="rm-panel-value difl"><?php echo $data->user->user_url; ?></div>
            </div>
            <?php
        }

    $editable_forms = array();
    if (is_array($data->custom_fields) || is_object($data->custom_fields)) {
        foreach ($data->custom_fields as $field_id => $sub) {
            $key = $sub->label;
            $meta = $sub->value;
            $sub_original = $sub;
            if(!isset($sub->type)){
                                $sub->type = '';
                            }
            $meta = RM_Utilities::strip_slash_array(maybe_unserialize($meta));
            ?>
            <div class="rm-panel-row dbfl">

                <div class="rm-panel-field difl"><?php echo $key; ?></div>
                <div class="rm-panel-value difl">
                    <?php
                    if (is_array($meta) || is_object($meta)) {
                                        if (isset($meta['rm_field_type']) && $meta['rm_field_type'] == 'File') {
                                            unset($meta['rm_field_type']);

                                            foreach ($meta as $sub) {

                                                $att_path = get_attached_file($sub);
                                                $att_url = wp_get_attachment_url($sub);
                                                ?>
                                                <div class="rm-submission-attachment">
                                                    <?php echo wp_get_attachment_link($sub, 'thumbnail', false, true, false); ?>
                                                    <div class="rm-submission-attachment-field"><?php echo basename($att_path); ?></div>
                                                    <div class="rm-submission-attachment-field"><a href="<?php echo $att_url; ?>"><?php echo RM_UI_Strings::get('LABEL_DOWNLOAD'); ?></a></div>
                                                </div>

                                                <?php
                                            }
                                        } elseif (isset($meta['rm_field_type']) && $meta['rm_field_type'] == 'Address') {
                                            $sub = $meta['original'] . '<br/>';
                                            if (count($meta) === 8) {
                                                $sub .= '<b>'.__('Street Address', 'custom-registration-form-builder-with-submission-manager').'</b> : ' . $meta['st_number'] . ', ' . $meta['st_route'] . '<br/>';
                                                $sub .= '<b>'.__('City', 'custom-registration-form-builder-with-submission-manager').'</b> : ' . $meta['city'] . '<br/>';
                                                $sub .= '<b>'.__('State', 'custom-registration-form-builder-with-submission-manager').'</b> : ' . $meta['state'] . '<br/>';
                                                $sub .= '<b>'.__('Zip Code', 'custom-registration-form-builder-with-submission-manager').'</b> : ' . $meta['zip'] . '<br/>';
                                                $sub .= '<b>'.__('Country', 'custom-registration-form-builder-with-submission-manager').'</b> : ' . $meta['country'];
                                            }
                                                echo $sub;
                                        } elseif ($sub->type == 'Time') {                                  
                                    echo $meta['time'].", Timezone: ".$meta['timezone'];
                                } elseif ($sub->type == 'Checkbox') {   
                                    echo implode(', ',RM_Utilities::get_lable_for_option($field_id, $meta));
                                } else {
                                            $sub = implode(', ', $meta);
                                            echo $sub;
                                        }
                                    } else {
                                        if($sub->type=='Rating')
                                        {
                                            if(defined('REGMAGIC_ADDON'))
                                                echo RM_Utilities::enqueue_external_scripts('script_rm_rating', RM_ADDON_BASE_URL . 'public/js/rating3/jquery.rateit.js');
                                           echo '<div class="rateit" id="rateit5" data-rateit-min="0" data-rateit-max="5" data-rateit-value="'.$meta.'" data-rateit-ispreset="true" data-rateit-readonly="true"></div>';
                                 
                                        }
                                        elseif ($sub->type == 'Radio' || $sub->type == 'Select')
                                        {   
                                            echo RM_Utilities::get_lable_for_option($field_id, $meta);
                                        }
                                        else
                                        echo $meta;
                                    }
                    ?>
                </div>
            </div>
            <?php
            //check if any field is editable
            if($sub_original->is_editable == 1 && !in_array($sub_original->form_id, $editable_forms)){
                $editable_forms[] = $sub_original->form_id;
            }
        }
    }
    ?>

</div>
<?php if(!empty($editable_forms)){ ?>
 <div id="rm_edit_sub_link">
                <form method="post" name="rm_form" action="<?php echo get_permalink(get_option('rm_option_front_sub_page_id')); ?>" id="rmeditsubmissions">
                    <input type="hidden" name="rm_edit_user_details" value="true">
                    <input type="hidden" name="form_ids" value='<?php echo json_encode($editable_forms); ?>'>
                </form>
                <a href="javascript:void(0)" onclick="document.getElementById('rmeditsubmissions').submit();"><?php echo RM_UI_Strings::get('MSG_EDIT_YOUR_SUBMISSIONS'); ?></a>
            </div> 
<?php } } ?>