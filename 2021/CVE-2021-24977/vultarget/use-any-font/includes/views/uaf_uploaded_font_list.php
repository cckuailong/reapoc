<?php 
if ( ! defined( 'ABSPATH' ) ) exit; 
$fontsData      = uaf_get_uploaded_font_data(); ?>

<?php 
if (!empty($fontsData)):
    $fontsDataWithVariations = uaf_group_fontdata_by_fontname($fontsData);
    $sn = 0;
    foreach ($fontsDataWithVariations as $fontName=>$fontsData):
    $sn++
?>
    <div class="font_holder">
        <div class="font_meta">
            <div class="font_name"><?php echo ucfirst($fontName); ?></div>
            <div class="fontclassname">Class to use this font : <em><strong><?php echo $fontName; ?></strong></em></div>              
        </div>

        <?php 
            if (!empty($fontsData)):
                uasort($fontsData, 'uaf_order_font_by_weight');
                foreach ($fontsData as $key => $fontData):
            ?>
                <div class="font_demo">
                    
                    <?php                       
                        if (isset($fontData['font_weight']) && !empty(trim($fontData['font_weight']))):
                    ?>
                        <div class="font-weight-style">
                            <?php echo $GLOBALS['uaf_fix_settings']['font_weight_variations'][$fontData['font_weight']]; ?> <?php echo $fontData['font_style']; ?>
                        </div>
                    <?php endif; ?>

                    <span class="<?php echo $fontData['font_name'] ?>" style="font-weight:<?php echo $fontData['font_weight']; ?>; font-style: <?php echo $fontData['font_style']; ?>;">The quick brown fox jumps over the lazy dog</span>

                    <div class="delete_link"><a onclick="if (!confirm('Are you sure ?')){return false;}" href="<?php echo wp_nonce_url( 'admin.php?page=use-any-font&tab=font_upload&delete_font_key='.$key, 'uaf_delete_font', 'uaf_nonce' ); ?>">Delete</a></div>
                </div>
        <?php 
                endforeach;
            endif; ?>
        <br style="clear:both;" />
    </div>
<?php endforeach; ?>

<?php else: ?>
    <div class="dcinfo"><p>No font found. Please click on Add Fonts to upload font</p></div>
<?php endif; ?>