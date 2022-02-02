<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
//$settings = get_option('wp_file_manager_pro_settings');	
$this->fm_custom_assets(); 
?>

<div class="wrap fmShorcodePage">
<div class="fmInnerWrap">
<h3 class="mainHeading">
<span class="headingIcon"><img src="<?php echo plugins_url( 'images/fm-shortcode-icon.png', __FILE__ );?>"></span>
<span class="headingText"><?php _e('File Manager - Shortcode','wp-file-manager-pro'); ?></span>
</h3>

<div class="fm_codeParaTxt">
<div class="para"><div class="lftText"><strong>USE:</strong></div>  <div class="rtTxt"><code>[wp_file_manager_admin]</code> -> It will show file manager on front end. You can control all settings from file manager settings. It will work same as backend WP File Manager.</div></div>

<div class="para"><div class="lftText"><strong>USE:</strong></div>  <div class="rtTxt"> <code>[wp_file_manager]</code> -> It will show file manager on front end. But only Administrator can access it and will control from file manager settings.</div></div>

<div class="para"><div class="lftText"><strong>USE:</strong></div>  <div class="rtTxt"> <code>[wp_file_manager view="list" lang="en" theme="light" dateformat="d M, Y h:i A" allowed_roles="editor,author" access_folder="wp-content/plugins" write = "true" read = "false" hide_files = "kumar,abc.php" lock_extensions=".php,.css" allowed_operations="upload,download" ban_user_ids="2,3"]</code></div></div>

</div>

<label class="labelHeading">Parameters:</label> 

<ul class="shortcodeDocList">
<li><div class="lftTxt"><span class="num">1</span></div>  <div class="rtTxt"><span class="strongText">allowed_roles = "*"</span> <span class="lineText">-> It will allow all roles to access file manager on front end or You can simple use for particular user roles as like allowed_roles="editor,author" (seprated by comma(,))</span></div> </li>

<li><div class="lftTxt"><span class="num">2</span></div>  <div class="rtTxt"> <span class="strongText">access_folder="test"</span> <span class="lineText">-> Here "test" is the name of folder which is located on root directory, or you can give path for sub folders as like "wp-content/plugins". If leave blank or empty it will access all folders on root directory. Default: Root directory</span></div> </li>

<li><div class="lftTxt"><span class="num">3</span></div>  <div class="rtTxt"> <span class="strongText">write = "true"</span> <span class="lineText">-> for access to write files permissions, note: true/false, default: false</span></div> </li>

<li><div class="lftTxt"><span class="num">4</span></div>  <div class="rtTxt"> <span class="strongText">read = "true"</span> <span class="lineText">-> for access to read files permission, note: true/false, default: true</span></div> </li>

<li><div class="lftTxt"><span class="num">5</span></div>  <div class="rtTxt"> <span class="strongText">hide_files = "wp-content/plugins,wp-config.php"</span> <span class="lineText">-> it will hide mentioned here. Note: seprated by comma(,). Default: Null</span></div> </li>

<li><div class="lftTxt"><span class="num">6</span></div>  <div class="rtTxt"> <span class="strongText">lock_extensions=".php,.css"</span> <span class="lineText">-> It will lock mentioned in commas. you can lock more as like ".php,.css,.js" etc. Default: Null</span></div> </li>

<li><div class="lftTxt"><span class="num">7</span></div>  <div class="rtTxt"> <span class="strongText">allowed_operations="*"</span> <span class="lineText">-> * for all operations and to allow some operation you can mention operation name as like, allowed_operations="upload,download". Note: seprated by comma(,). Default: *</span> </div></li>

</ul>

<div class="subHeading"><span class="num">7.1</span> File Operations List: </div>

<div class="twoColListWrap">
<ul class="numList numListCol">
<li><span class="num">1.</span> <span class="strongText">mkdir -></span> <span class="lineText">Make directory or folder</span> </li>
<li><span class="num">2.</span> <span class="strongText">mkfile -></span> <span class="lineText">Make file</span> </li>
<li><span class="num">3.</span> <span class="strongText">rename -></span> <span class="lineText">Rename a file or folder</span> </li>
<li><span class="num">4.</span> <span class="strongText">duplicate -></span> <span class="lineText">Duplicate or clone a folder or file</span> </li>
<li><span class="num">5.</span> <span class="strongText">paste -></span> <span class="lineText"> Paste a file or folder</span> </li>
<li><span class="num">6.</span> <span class="strongText">ban -></span> <span class="lineText">Ban </span> </li>
<li><span class="num">7.</span> <span class="strongText">archive -></span> <span class="lineText">To make a archive or zip</span> </li>
<li><span class="num">8.</span> <span class="strongText">extract -></span> <span class="lineText">Extract archive or zipped file</span> </li>
<li><span class="num">9.</span> <span class="strongText">copy -></span> <span class="lineText">Copy files or folders</span> </li>
</ul>

<ul class="numList numListCol">
<li><span class="num">10.</span> <span class="strongText">cut -></span> <span class="lineText">Simple cut a file or folder</span> </li>
<li><span class="num">11.</span> <span class="strongText">edit -></span> <span class="lineText">Edit a file</span> </li>
<li><span class="num">12.</span> <span class="strongText">rm -></span> <span class="lineText">Remove or delete files and folders</span> </li>
<li><span class="num">13.</span> <span class="strongText">download -></span> <span class="lineText">Download files</span> </li>
<li><span class="num">14.</span> <span class="strongText">upload -></span> <span class="lineText">Upload files</span> </li>
<li><span class="num">15.</span> <span class="strongText">search -> </span> <span class="lineText">Search things</span> </li>
<li><span class="num">16.</span> <span class="strongText">info -></span> <span class="lineText">Info of file</span> </li>
<li><span class="num">17.</span> <span class="strongText">help -></span> <span class="lineText">Help</span> </li>
</ul>

</div>

<ul class="shortcodeDocList">
<li><div class="lftTxt"><span class="num">8</span></div>  <div class="rtTxt"> <span class="strongText">ban_user_ids="2,3"</span> <span class="lineText">->  It will ban particular users by just putting their ids seprated by commas(,). If user is Ban then they will not able to access wp file manager on front end.</span></div> </li>
<li><div class="lftTxt"><span class="num">9</span></div>  <div class="rtTxt"> <span class="strongText">view="list"</span> <span class="lineText">-> Filemanager UI View. Default: grid</span> </div></li>
<li><div class="lftTxt"><span class="num">10</span></div>  <div class="rtTxt"> <span class="strongText">dateformat="d M, Y h:i A"</span> <span class="lineText">-> File Modified or Create date format. Default: d M, Y h:i A </span> </div></li>
<li><div class="lftTxt"><span class="num">11</span></div>  <div class="rtTxt"> <span class="strongText">lang="en"</span> <span class="lineText">-> File manager Language. Default: English(en) </span> </div></li>
<li><div class="lftTxt"><span class="num">12</span></div>  <div class="rtTxt"> <span class="strongText">theme="light"</span> <span class="lineText">-> File Manager Theme. Default: Light </span> </div></li>
</ul>

</div>
</div>