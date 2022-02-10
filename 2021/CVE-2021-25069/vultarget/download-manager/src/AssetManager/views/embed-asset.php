<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 2019-08-08 12:34
 * Date: 2019-08-17
 * Version: 1.1.0
 */
if(!defined("ABSPATH")) die();
?>
<div class="w3eden">
    <div class="card wpdm-asset-link wpdm-asset-link-<?php echo $asset->ID; ?>">
        <div class="card-body">
            <div class="media" style="display: flex">
                <div class="media-body" style="width: 100% !important;display: grid"><h3 class="package-title" style="font-size: 12pt;margin: 0;line-height: 18px;margin-bottom: 2px"><?php echo $asset->name; ?></h3>
                    <small class="text-muted"><?php echo $asset->size; ?></small>
                </div>
                <div>
                    <a class="btn btn-primary btn-lg" href="<?php echo $asset->temp_download_url; ?>"><?php echo __( "Download", "download-manager" ); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
