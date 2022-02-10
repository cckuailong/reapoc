<?php 
if ( ! defined( 'ABSPATH' ) ) exit; 
$fontsData		         = uaf_get_uploaded_font_data();
$fontsDataWithVariations = uaf_group_fontdata_by_fontname($fontsData);
?>

<p align="right"><input type="button" name="open_assign_font" onClick="open_assign_font();" class="button-primary" id="open_assign_font_button" value="Assign Font" /><br/></p>

<div id="open_assign_font" style="display:none;">
	<form action="admin.php?page=use-any-font&tab=font_assign"  id="open_assign_font_form" method="post">
    	<table class="uaf_form">        	
            
            <tr>
            	<td width="175">Select Font</td>
                <td>
                	<select name="font_key" class="uaf_required" style="width:200px;">
                    	<option value="">- Select -</option>
                        <?php
                        if (!empty($fontsDataWithVariations)):
							foreach ($fontsDataWithVariations as $key=>$fontDataVariation)	: ?>
								<option value="<?php echo array_key_first($fontDataVariation); ?>"><?php echo $key ?></option>
							<?php endforeach;
						endif; 
						?>
                    </select>
                </td>
            </tr>
            <?php
            global $TRP_LANGUAGE;    
            $languageSelector = uaf_get_language_selector();
            if ($languageSelector['enableMultiLang'] == TRUE ): ?>    
                <tr>
                    <td width="175">Select Language</td>
                    <td><?php echo $languageSelector['selectHTML']; ?></td>
                </tr>
            <?php endif; ?>
            <tr>    
                <td valign="top">Select elements to assign</td>
                <td>
                    
                    <div class="elements_holder">
                       <p><b>Headings And Titles </b></p>
                        <input name="elements[]" value="h1" type="checkbox" /> Headline 1 (h1 tags)<br/>
                        <input name="elements[]" value="h2" type="checkbox" /> Headline 2 (h2 tags)<br/>
                        <input name="elements[]" value="h3" type="checkbox" /> Headline 3 (h3 tags)<br/>
                        <input name="elements[]" value="h4" type="checkbox" /> Headline 4 (h4 tags)<br/>
                        <input name="elements[]" value="h5" type="checkbox" /> Headline 5 (h5 tags)<br/>
                        <input name="elements[]" value="h6" type="checkbox" /> Headline 6 (h6 tags)<br/>
                        <input name="elements[]" value=".entry-title" type="checkbox" /> Post,Page&Category Title<br/>
                        <input name="elements[]" value="body.single-post .entry-title" type="checkbox" /> Post Title Only<br/>
                        <input name="elements[]" value="body.page .entry-title" type="checkbox" /> Page Title Only<br/>
                        <input name="elements[]" value="body.category .entry-title" type="checkbox" /> Category Title Only<br/>
                        <input name="elements[]" value=".widget-title" type="checkbox" /> Widget Title<br/>                        
                    </div>

                    <div class="elements_holder">
                        <p><b>Site Identity</b></p>
                        <input name="elements[]" value=".site-title" type="checkbox" /> Site Title<br/>
                        <input name="elements[]" value=".site-description" type="checkbox" /> Site Description<br/>

                        <p><br/><b>Body</b></p>
                        <input name="elements[]" value="body" type="checkbox" /> Body (body tags)<br/>
                        <input name="elements[]" value="p" type="checkbox" /> Paragraphs (p tags)<br/>
                        <input name="elements[]" value="blockquote" type="checkbox" /> Blockquotes<br/>
                        <input name="elements[]" value="li" type="checkbox" /> Lists (li tag)<br/>
                        <input name="elements[]" value="a" type="checkbox" /> Hyperlink (a tag)<br/>
                        <input name="elements[]" value="strong, b" type="checkbox" /> Bold (strong tag )<br/>
                        <input name="elements[]" value="em" type="checkbox" /> Italic (em tag )<br/>
                    </div>

                    <div class="elements_holder">
                        <p><b>Menus</b></p>
                        <?php
                        $menus = get_terms('nav_menu');
                            if (!empty($menus)){
                                foreach($menus as $menu){
                        ?>
                                    <input name="elements[]" value=".menu-<?php echo $menu->slug; ?>-container li a, .menu-<?php echo $menu->slug; ?>-container li span, #menu-<?php echo $menu->slug; ?> li a, #menu-<?php echo $menu->slug; ?> li span" type="checkbox" /> <?php echo $menu->name; ?><br/> 
                        <?php
                                }
                            } else {
                                echo 'No Menus Found<br/>';
                            }
                        ?>
                    </div>                    
                </td>
            </tr>
            <tr>        
                <td valign="top">Custom Elements</td>
                <td><textarea name="custom_elements" style="width:400px; height:150px;"></textarea><br/>
					<br/>
                    <strong>Note</strong><br/>
                    Each line indicate one css element. You don't need to use any css.<br />
					<strong>Example:</strong><br/>
                    <em>#content .wrap</em><br/>
                    <em>#content p </em>
                    <br/>
                </td>
            </tr>
            <tr>        
                <td>&nbsp;</td>
                <td>
                    <?php wp_nonce_field( 'uaf_font_assign', 'uaf_nonce' ); ?>
                    <input type="submit" name="submit-uaf-font-assign" class="button-primary" value="Assign Font" />

                </td>
            </tr>
        </table>	
    </form>
    <br/><br/>
</div>

<?php 
$fontsImplementRawData 	= get_option('uaf_font_implement');
$fontsImplementData		= json_decode($fontsImplementRawData, true);

?>
<table cellspacing="0" class="wp-list-table widefat fixed bookmarks">
	<thead>
    	<tr>
        	<th width="20">Sn</th>
            <th>Font</th>
            <th>Applied To</th>
            <th width="100">Delete</th>
        </tr>
    </thead>
    
    <tbody>
    	<?php if (!empty($fontsImplementData)): ?>
        <?php 
		$sn = 0;
		foreach ($fontsImplementData as $key=>$fontImplementData):
		$sn++
		?>
        <tr>
        	<td><?php echo $sn; ?></td>
            <td>
                <?php 
                    if (isset($fontImplementData['font_name']) && !empty(trim($fontImplementData['font_name']))){
                        echo $fontImplementData['font_name'];
                    } else {
                        echo @$fontsData[$fontImplementData['font_key']]['font_name'];
                    }
                ?>                    
            </td>
            <td><?php echo $fontImplementData['font_elements'] ?></td>
            <td><a onclick="if (!confirm('Are you sure ?')){return false;}" href="<?php echo wp_nonce_url( 'admin.php?page=use-any-font&tab=font_assign&delete_font_assign_key='.$key, 'uaf_delete_font_assign', 'uaf_nonce' ); ?>">Delete</a></td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
        	<td colspan="4">No font assign yet. Click on Assign Font to start.</td>
        </tr>
        <?php endif; ?>        
    </tbody>    
</table>