<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>

<div class="tab-pane hide" id="file_info_{{fileindex}}">
    <div class="form-group">
        <div class="media">
            <div class="pull-left">
                <img class="file-ico" src="<?php echo \WPDM\__\FileSystem::fileTypeIcon('_blank'); ?>"/>
            </div>
            <div class="media-body">
                <input placeholder="<?php _e("File Title", "download-manager"); ?>"
                       title="<?php _e("File Title", "download-manager"); ?>"
                       class="form-control" type="text"
                       name='file[fileinfo][{{fileindex}}][title]'
                       value="{{filetitle}}"/><br/>
                <div class="row">
                    <div class="col-md-5">
                        <div class="input-group">
                                                        <span class="input-group-addon">
                                                            ID:
                                                        </span>
                            <input readonly="readonly" class="form-control"
                                   type="text" value="{{fileindex}}">
                            <span class="input-group-btn">
                                                            <button type="button" class="btn btn-secondary"
                                                                    onclick="__showDownloadLink(<?php the_ID(); ?>, '{{fileindex}}')"><i
                                                                        class="fa fa-link"></i></button>
                                                        </span>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="input-group">
                            <input placeholder="<?php _e("File Password", "download-manager"); ?>"
                                   title="<?php _e("File Password", "download-manager"); ?>"
                                   class="form-control" type="text"
                                   id="indpass_{{fileindex}}"
                                   name='file[fileinfo][{{fileindex}}][password]'
                                   value="">
                            <span class="input-group-btn">
                                                    <button type="button" class="btn btn-secondary" class="genpass"
                                                            title='Generate Password'
                                                            onclick="return generatepass('indpass_{{fileindex}}')"><i
                                                                class="fa fa-ellipsis-h"></i></button>
                                                </span>
                        </div>
                    </div>

                </div>
                <?php do_action("wpdm_attached_file", $post->ID, 0); ?>
            </div>
        </div>
    </div>
    <?php if (class_exists('\WPDMPP\WPDMPremiumPackage')) { ?>
        <div class="form-group">
            <div class="file-access-settings"
                 id="file-access-settings-{{fileindex}}">

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
                                       name="file[fileinfo][{{fileindex}}][license_price][<?php echo $licid; ?>]"
                                       class="form-control lic-file-price-<?php echo $licid; ?>"
                                       id="lic-file-price-<?php echo $licid; ?>"
                                       placeholder="Price"
                                       value=""
                                       type="text" /></td>
                            <?php $zl++;
                        } ?>
                    </tr>
                    <tr>
                        <td colspan="<?php echo  count($pre_licenses); ?>"><?php _e('File availability with license pack:', 'download-manager'); ?></td>
                    </tr>
                    <tr>
                        <?php
                        foreach ($pre_licenses as $licid => $pre_license) {
                            $_license_pack = wpdm_valueof($license_pack, $licid);
                            $_license_pack = is_array($_license_pack) ? $_license_pack : [];
                            ?>
                            <td>
                                <label>
                                    <label class="switch">
                                        <input name="file[license_pack][<?php echo $licid; ?>][]" value="{{fileindex}}" type="checkbox"  />
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
                    <span class="input-group-addon"><?php _e('Price:', 'wpdmpo'); ?></span><input
                            class="form-control" type="text"
                            name="file[fileinfo][{{fileindex}}][price]"
                            value=""/>
                </div>

            </div>
        </div>
    <?php } ?>
</div>
