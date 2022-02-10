<?php
$actions = new Secure_Copy_Content_Protection_Subscribe_Actions($this->plugin_name);

if (isset($_REQUEST['ays_submit'])) {
    $actions->store_data($_REQUEST);
}

$data = $actions->get_data();
$data_lastIds = $actions->sccp_get_bs_last_id();
$data_lastId = (array) $data_lastIds;
$data_check = $actions->sccp_get_last_id_check();
$data_check = !empty( $data_check[0]) ? implode(' ', $data_check[0]) : '';
$data_check_id = isset($data_check) && $data_check == '' ? "false" : "true";

$bs_last_id = $data_lastId['AUTO_INCREMENT'];
$block_subscribe = array_reverse($data);
?>
<div class="wrap" style="position:relative;">
    <div class="container-fluid">
        <form method="post">
            <h1 class="wp-heading-inline">
                <?php
                echo __('Subscribe to view', $this->plugin_name);
                 $other_attributes = array();
                submit_button(__('Save changes', $this->plugin_name), 'primary ays-button ays-sccp-save-comp', 'ays_submit', false, $other_attributes);
                ?>
            </h1>
            <?php
            if (isset($_REQUEST['status'])) {
                $actions->sccp_subscribe_notices($_REQUEST['status']);
            }
            ?>
            <hr/>
            <div class="ays-settings-wrapper">
                                    
                <button type="button" class="button add_new_block_subscribe"
                        style="margin-bottom: 20px"><?= __('Add new', $this->plugin_name); ?></button>
                <div class="all_block_subscribes" data-last-id="<?php echo $bs_last_id; ?>">
                    <?php
                     foreach ( $block_subscribe as $key => $blocsubscribe ) { 
                        $block_id = isset($blocsubscribe['id']) ? absint( intval($blocsubscribe['id'])) : $bs_last_id;
                        $block_options = isset($blocsubscribe['options']) ? json_decode($blocsubscribe['options'], true) : array();
                        $block_sub_require_verification = isset($block_options['require_verification']) && $block_options['require_verification'] == 'on' ? 'checked' : '';
                        $enable_block_sub_name_field = isset($block_options['enable_name_field']) && $block_options['enable_name_field'] == 'on' ? 'checked' : '';
                    ?>
                        <div class="blockcont_one" id="blocksub<?php echo $block_id; ?>">
                            <div class="copy_protection_container row ays_bc_row ">
                                <div class="col sccp_block_sub">
                                    <div class="sccp_block_sub_label_inp">
                                        <div class="sccp_block_sub_label">
                                            <label for="sccp_block_subscribe_shortcode_<?php echo $block_id; ?>" class="sccp_bc_label"><?= __('Shortcode', $this->plugin_name); ?></label>
                                        </div>                                    
                                        <div class="sccp_block_sub_inp">
                                            <input type="text" name="sccp_block_subscribe_shortcode[]" id="sccp_block_subscribe_shortcode_<?php echo $block_id; ?>"
                                                   class="ays-text-input sccp_blockcont_shortcode select2_style"
                                                   value="[ays_block_subscribe id='<?php echo $block_id; ?>'] Content [/ays_block_subscribe]"
                                                   readonly>
                                            <input type="hidden" name="sccp_blocksub_id[]" value="<?php echo $block_id; ?>">
                                            <input type="hidden" class="ays_data_checker" value="<?php echo $data_check_id; ?>">
                                        </div>
                                        <hr>
                                        <div class="copy_protection_container row">
                                            <div class="col-sm-4">
                                                <label for="sccp_enable_block_sub_name_field_<?php echo $block_id; ?>"><?= __("Name field", $this->plugin_name); ?></label>
                                                <a class="ays_help" data-toggle="tooltip"
                                                   title="<?= __('Tick the checkbox to show the Name field', $this->plugin_name) ?>">
                                                    <i class="ays_fa ays_fa_info_circle"></i>
                                                </a>
                                            </div>
                                            <div class="col-sm-8">
                                                <input type="checkbox" class="modern-checkbox" id="sccp_enable_block_sub_name_field_<?php echo $block_id; ?>"
                                                       name="sccp_enable_block_sub_name_field[<?php echo $block_id?>][]" <?php echo $enable_block_sub_name_field ?>
                                                       value="true">
                                               
                                            </div>
                                        </div>                          
                                    </div>
                                    <div class="sccp_block_sub_inp_row">
                                        <div class="sccp_pro " title="<?= __('This feature will available in PRO version', $this->plugin_name); ?>">
                                            <div class="pro_features sccp_general_pro">
                                                <div>
                                                    <p style="font-size: 16px !important;">
                                                        <?= __("This feature is available only in ", $this->plugin_name); ?>
                                                        <a href="https://ays-pro.com/index.php/wordpress/secure-copy-content-protection"
                                                           target="_blank"
                                                           title="PRO feature"><?= __("PRO version!!!", $this->plugin_name); ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="sccp_block_sub_label">
                                                <label for="sccp_require_verification_<?php echo $block_id; ?>" class="sccp_bc_label"><?= __('Require verification', $this->plugin_name); ?></label>
                                            </div>
                                            <div class="sccp_block_sub_inp">
                                                <input type="checkbox" name="sccp_subscribe_require_verification[]" id="sccp_require_verification_<?php echo $block_id; ?>"
                                                       class="ays-text-input sccp_blocksub select2_style" value="on"
                                                       <?php echo  $block_sub_require_verification; ?>
                                                       >
                                                <input type="hidden" name="sub_require_verification[]" class="sccp_blocksub_hid" value="<?php echo isset($block_options['require_verification']) && $block_options['require_verification'] == 'on' ? 'on' : 'off'; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <br>
                                    <p class="blocksub_delete_icon"><i class="ays_fa fa-trash-o" aria-hidden="true"></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <input type="hidden" class="deleted_ids" value="" name="deleted_ids">
                </div>
                <button type="button" class="button add_new_block_subscribe"
                        style="margin-top: 20px"><?= __('Add new', $this->plugin_name); ?></button> 
                <hr/>                        
            </div>
            <?php
            wp_nonce_field('subscribe_action', 'subscribe_action');
            $other_attributes = array();
            submit_button(__('Save changes', $this->plugin_name), 'primary ays-button', 'ays_submit', true, $other_attributes);
            ?>
        </form>
    </div>
</div>