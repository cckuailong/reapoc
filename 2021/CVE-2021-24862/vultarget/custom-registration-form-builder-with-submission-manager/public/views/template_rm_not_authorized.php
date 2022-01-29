<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'views/template_rm_not_authorized.php'); else {
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<div class="rmagic">
    <div class="rmnotice-container">
        <div class="rmnotice">
            <?php echo $data; ?>
        </div>
    </div>   
</div>

<?php if($is_sub) echo do_shortcode('[RM_Login]'); ?>
<?php } ?>