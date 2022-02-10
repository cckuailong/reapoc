<?php
/**
 * User: shahnuralam
 * Date: 6/25/18
 * Time: 12:26 AM
 */
if (!defined('ABSPATH')) die();
?>

<div class="w3eden" id="wpdm-authors<?php echo isset($params['sid'])?"-{$params['sid']}":""; ?>">
    <?php $this->listAuthors($params); ?>
</div>

<style>
    .card-author{
        margin-bottom: 30px;
    }
    img.img-circle{
        border-radius: 500px !important;
    }
    .author-name{
        margin: 5px 0;
        padding: 0;
        line-height: 30px;
    }
    .author-name a{
        font-size: 12pt;
    }
</style>
<script>

</script>
