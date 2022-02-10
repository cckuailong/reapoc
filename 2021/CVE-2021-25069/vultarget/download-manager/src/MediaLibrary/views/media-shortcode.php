<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 29/12/19 01:54
 */

if(!defined("ABSPATH")) die();
?>
<div class="w3eden">
    <div class="card media-card">
        <div class="card-body">
            <div class="media">
                <?php if($media->icon !== ''){ ?>
                <div class="mr-3">
                    <?php echo $media->icon; ?>
                </div>
                <?php } ?>
                <div class="media-body">
                    <h3 class="m-0 mb-1 p-0" style="font-size: 12pt"><?php echo $media->post_title; ?></h3>
                    <?php echo $media->filesize; ?>
                </div>
                <div class="ml-3">
                    <a class="btn btn-lg btn-primary" style="padding-top: 10px;padding-bottom: 10px" href="<?php echo $media->guid; ?>"><?php echo __( "Download", "download-manager" ) ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .media-card .attachment-thumbnail.size-thumbnail {
        height: 44px;
    }
    @media (max-width: 600px) {
        .card.media-card .media .mr-3{
            margin: 0 0 10px !important;
        }
        .media-card .attachment-thumbnail.size-thumbnail {
            width: auto;
            max-width: 100%;
            height: auto;
            margin: 0 auto;
        }
        .card.media-card .media {
            display: block !important;
        }

        .card.media-card .media .ml-3 {
            margin: 10px 0 0 !important;
        }

        .card.media-card .media .ml-3 .btn{
            display: block;
            width: 100%;
        }
    }
</style>
