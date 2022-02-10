<?php if ( ! defined( 'ABSPATH' ) ) exit;  
add_thickbox();
?>
<p align="right">
    <input type="button" name="open_add_font" onClick="open_add_font();" class="button-primary" value="Upload Fonts" />

    <a href="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=uaf_predefined_font_interface&width=800&height=550" class="thickbox button-primary" title="Add Predefined Fonts (Personal and Commercial Use)" >Add Predefined Fonts</a>

    <br/></p>

<div id="font-upload" style="display:none;">
    <div class="dcform">        
        <?php if (!empty($GLOBALS['uaf_user_settings']['uaf_api_key'])) : ?>
            <form action="admin.php?page=use-any-font&tab=font_upload" id="open_add_font_form_php" method="post" enctype="multipart/form-data">

                <p>
                     <label>Font Name *</label>
                     <span class="field">
                         <input type="text" name="font_name" value="" maxlength="20" class="uaf_required medium"/>
                         <span class="field_error">Please give font name.</span>
                     </span>
                </p>
                <p>
                     <label>&nbsp;</label>
                     <span class="field">
                         <input type="checkbox" name="enable_font_variation" id="enable_font_variation" onclick="multi_variation_holder_toggle();"> Enable Font Weight Variation ( For Advance Users ). <a href="https://dineshkarki.com.np/add-multiple-font-variation-in-use-any-font.html" target="_blank">How to !</a>
                     </span>
                 </p>
                 <div class="multi_variation_holder" style="display:none;">
                     <p>
                         <label>Font Weight *</label>
                         <span class="field">
                             <select name="font_weight" class="medium">
                                <?php foreach ($GLOBALS['uaf_fix_settings']['font_weight_variations'] as $variation => $variation_label): ?>
                                    <option value="<?php echo $variation ?>"><?php echo $variation_label; ?></option>
                                <?php endforeach; ?>
                             </select>
                         </span>
                    </p>
                    <p>
                         <label>Font Style *</label>
                         <span class="field">
                             <select name="font_style" class="medium">
                                <option value="normal">Normal</option>
                                <option value="italic">Italic</option>                            
                             </select>
                         </span>
                    </p>
                </div>
                <p>
                     <label>Font File *</label>
                     <span class="field">
                         <input type="file" name="font_file" id="font_file" value="" class="uaf_required" accept=".woff,.ttf,.otf" />
                         <span class="field_error">Please select font file.</span>
                         <br/>
                         <em>Accepted Font Format : <?php echo join(", ",$GLOBALS['uaf_fix_settings']['allowedFontFormats']); ?> | Font Size: Upto <?php echo uaf_max_upload_size_for_php(); ?>MB</em>
                     </span>
                 </p>

                 <p>
                     <label>&nbsp;</label>
                     <span class="field">
                         <input type="hidden" name="url" value="<?php echo base64_decode($GLOBALS['uaf_user_settings']['uaf_activated_url']); ?>" />
                         <input type="hidden" name="api_key" value="<?php echo $GLOBALS['uaf_user_settings']['uaf_api_key']; ?>" />
                         <input type="hidden" name="font_count" value="<?php echo uaf_count_uploaded_fonts(); ?>" />
                         <input type="submit" name="submit-uaf-font-php" class="button-primary" value="Upload" />
                         <?php wp_nonce_field( 'uaf_font_upload_php', 'uaf_nonce' ); ?>
                         <br/>
                            <span>By clicking on Upload, you confirm that you have rights to use this font.</span>
                    </span>
                </p>
            </form>
        <?php else: ?>
             <div class="dcinfo"><p>You need to add API key in <a href="admin.php?page=use-any-font&tab=api">API key</a> section to upload the fonts.</p></div>
        <?php endif; ?>
    </div>
</div>
<?php include('uaf_uploaded_font_list.php'); ?>