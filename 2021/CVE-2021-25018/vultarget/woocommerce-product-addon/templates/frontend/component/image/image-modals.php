<?php
/**
* Image Modal Template
* 
* This template can be overridden by copying it to yourtheme/ppom/frontend/component/image/image-modals.php
* 
* @version 1.0
**/

/* 
**========== Block direct access =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

$modal_id = 'modalImage'.$image_id;
?>

<div id="<?php echo esc_attr($modal_id)?>" class="ppom-popup-wrapper ppom-popup-handle">
    <div class="ppom-popup-inner-section">
        <header class="ppom-popup-header"> 
            <!-- <a href="#" class="js-modal-close close">×</a> -->
            <h3><?php echo $image_title?></h3>
        </header>
        <div class="ppom-popup-body">
            <img src="<?php echo esc_url($image_full) ?>">
        </div>
        <footer class="ppom-popup-footer"> 
            <a href="#" class="ppom-popup-button ppom-popup-close-js">Close</a> 
        </footer>
    </div>
</div>