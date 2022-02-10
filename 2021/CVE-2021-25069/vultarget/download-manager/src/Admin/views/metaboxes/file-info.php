<?php

use WPDM\__\__;
use WPDM\__\FileSystem;

if(!defined("ABSPATH")) die("Shit happens!");
?>

<div class="tab-pane hide" id="file_info_<?php echo  $id ?>">
    <div class="form-group">
        <div class="media">
            <div class="pull-left">
                <img class="file-ico"
                     onerror="this.src='<?php echo FileSystem::fileTypeIcon($ext); ?>';"
                     src="<?php echo $thumb && __::is_url($thumb) ? $thumb : FileSystem::fileTypeIcon($ext); ?>"/>
            </div>
            <div class="media-body">
                <input placeholder="<?php _e("File Title", "download-manager"); ?>"
                       title="<?php _e("File Title", "download-manager"); ?>"
                       class="form-control" type="text"
                       name='file[fileinfo][<?php echo $id; ?>][title]'
                       value="<?php echo !isset($fileinfo[$id], $fileinfo[$id]['title']) ? __::valueof($fileinfo, "{$value}/title", ["validate" => "esc_html"]) : __::valueof($fileinfo, "{$id}/title", ["validate" => "esc_html"]); ?>"/><br/>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-group">
                                                        <span class="input-group-addon">
                                                            ID:
                                                        </span>
                            <input readonly="readonly" class="form-control"
                                   type="text" value="<?php echo $id; ?>">
                            <span class="input-group-btn">
                                                            <button type="button" class="btn btn-secondary"
                                                                    onclick="__showDownloadLink(<?php the_ID(); ?>, '<?php echo $id; ?>')"><i
                                                                        class="fa fa-link"></i></button>
                                                        </span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-group">
                            <input placeholder="<?php _e("File Password", "download-manager"); ?>"
                                   title="<?php _e("File Password", "download-manager"); ?>"
                                   class="form-control" type="text"
                                   id="indpass_<?php echo $file_index; ?>"
                                   name='file[fileinfo][<?php echo $id; ?>][password]'
                                   value="<?php echo __::valueof($fileinfo, "{$id}/password"); ?>">
                            <span class="input-group-btn">
                                                    <button type="button" class="btn btn-secondary genpass"
                                                            title='Generate Password'
                                                            onclick="return generatepass('indpass_<?php echo $file_index; ?>')"><i
                                                                class="fa fa-ellipsis-h"></i></button>
                                                </span>
                        </div>
                    </div>

                </div>
                <div class="row" style="margin-top: 10px">
                    <div class="col-lg-6">
                        <label><?php echo __( 'File Version', 'download-manager' ); ?>:</label>
                        <input type="text" class="form-control" placeholder="1.0.0" name="file[fileinfo][<?php echo $id; ?>][version]" value="<?php echo __::valueof($fileinfo, "{$id}/version"); ?>" />
                    </div>
                    <div class="col-lg-6">
                        <label><?php echo __( 'Update Date', 'download-manager' ); ?>:</label>
                        <input type="date" style="padding: 0 15px" class="form-control" name="file[fileinfo][<?php echo $id; ?>][update_date]" value="<?php echo __::valueof($fileinfo, "{$id}/update_date"); ?>" />
                    </div>

                </div>
                <?php do_action("wpdm_attached_file", $post->ID, $id); ?>
            </div>
        </div>
    </div>
    <?php if (class_exists('\WPDMPP\WPDMPremiumPackage')) { ?>
        <div class="form-group">
            <div class="file-access-settings"
                 id="file-access-settings-<?php echo $id; ?>">

                <?php
                $license_req = get_post_meta($post->ID, "__wpdm_enable_license", true);
                $license_pack = get_post_meta($post->ID, "__wpdm_license_pack", true);

                $pre_licenses = wpdmpp_get_licenses();
                $license_infs = get_post_meta($post->ID, "__wpdm_license", true);
                $license_infs = maybe_unserialize($license_infs);
                $zl = 0;
                ?>
                <table class="table table-v table-bordered file-price-data file-price-table" <?php if ($license_req != 1) echo "style='display:none;'"; ?>>
                    <tr>
                        <?php foreach ($pre_licenses as $licid => $lic) {
                            echo "<th>{$lic['name']}</th>";
                        } ?>
                    </tr>
                    <tr>
                        <td colspan="<?php echo  count($pre_licenses); ?>"><?php _e('License specific file price:', 'download-manager'); ?></td>
                    </tr>
                    <tr>
                        <?php foreach ($pre_licenses as $licid => $pre_license) { ?>
                            <td><input min="0"
                                       name="file[fileinfo][<?php echo $id; ?>][license_price][<?php echo $licid; ?>]"
                                       class="form-control lic-file-price-<?php echo $licid; ?>"
                                       id="lic-file-price-<?php echo $licid; ?>"
                                       placeholder="Price"
                                       value="<?php echo !isset($fileinfo[$id]['license_price']) || !isset($fileinfo[$id]['license_price'][$licid]) || $fileinfo[$id]['license_price'][$licid] == '' ? (isset($fileinfo[$id]['price']) && $zl == 0 ? (double)$fileinfo[$id]['price'] : '') : (double)$fileinfo[$id]['license_price'][$licid]; ?>"
                                       type="text"></td>
                            <?php $zl++;
                        } ?>
                    </tr>
                    <tr>
                        <td colspan="<?php echo  count($pre_licenses); ?>"><?php _e('File availability with license pack:', 'download-manager'); ?></td>
                    </tr>
                    <tr>
                        <?php
                        foreach ($pre_licenses as $licid => $pre_license) {
                            $_license_pack = __::valueof($license_pack, $licid);
                            $_license_pack = is_array($_license_pack) ? $_license_pack : [];
                            ?>
                            <td>
                                <label>
                                    <label class="switch">
                                        <input name="file[license_pack][<?php echo $licid; ?>][]" value="<?php echo $id; ?>" type="checkbox" <?php checked(in_array($id, $_license_pack)) ?> />
                                        <span class="slider"></span>
                                    </label>
                                    <?php _e('Available', 'download-manager'); ?>
                                </label>
                            </td>
                            <?php $zl++;
                        } ?>
                    </tr>

                    </tbody></table>

                <div class="input-group file-price-data file-price-field" <?php if ($license_req == 1) echo "style='display:none;'"; ?>>
                    <span class="input-group-addon"><?php _e('Price:', 'wpdmpo'); ?></span>
                    <input class="form-control" type="text" name="file[fileinfo][<?php echo $id; ?>][price]" value="<?php echo __::valueof($fileinfo, "{$id}/price", ["validate" => "double", "default" => 0]); ?>" />
                </div>

            </div>
        </div>
    <?php } ?>
</div>
