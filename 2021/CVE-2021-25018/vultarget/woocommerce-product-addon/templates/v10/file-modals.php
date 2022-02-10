<?php
/**
 * Images modal html
 * */
ppom_direct_access_not_allowed();

$modal_id = 'modalFile'.$file_id;
?>

<div class="modal ppom-modals fade" id="<?php echo esc_attr($modal_id)?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo $image_title?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
         <img src="<?php echo $image_full?>">   
      </div>
      
    </div>
  </div>
</div>