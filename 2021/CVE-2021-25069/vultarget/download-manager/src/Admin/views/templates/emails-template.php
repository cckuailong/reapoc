<?php

    $etpl = get_option('_wpdm_etpl');  
    if(isset($_GET['loadetpl']) && file_exists(WPDM_BASE_DIR.'email-templates/'.wpdm_query_var('filename', 'txt')) && substr_count(wpdm_query_var('filename', 'txt'), '.html')){
        $etpl['body'] = file_get_contents(WPDM_BASE_DIR.'email-templates/'.wpdm_query_var('filename', 'txt'));
    }

?>


<div class="wrap w3eden">
    <div class="panel panel-primary"  id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fa fa-users"></i> &nbsp; <?php echo __( "Subscribers" , "download-manager" ); ?></b>
           <a style="margin-left: 10px" id="basic" href="edit.php?post_type=wpdmpro&page=emails&task=export" class="btn btn-sm btn-primary pull-right"><?php echo __( "Export All" , "download-manager" ); ?></a>
           <a id="basic" href="edit.php?post_type=wpdmpro&page=emails&task=export&uniq=1" class="btn btn-sm btn-primary pull-right"><?php echo __( "Export Unique Emails" , "download-manager" ); ?></a>&nbsp;

        </div>

        <ul id="tabs" class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;">
            <li><a id="basic" href="edit.php?post_type=wpdmpro&page=emails"><?php echo __( "Emails" , "download-manager" ); ?></a></li>
            <li class="active"><a id="basic" href="edit.php?post_type=wpdmpro&page=emails&task=template"><?php echo __( "Email Template" , "download-manager" ); ?></a></li>
            </ul>

 


           
<form method="post" action="" id="posts-filter" style="padding: 20px;">
<input name="task" value="save-etpl" type="hidden" />
<div style="margin-bottom: 10px;padding-bottom: 10px;border-bottom: 1px solid #eeeeee;">
<b>Load Template:</b> 
 
<select id="xtpl" class="form-control input-sm" style="display: inline !important;width: 300px">
<?php
$xtpls = scandir(WPDM_BASE_DIR.'email-templates/');

foreach($xtpls as $xtpl){
    $tmp = explode('.', $xtpl);
    if(end($tmp)=='html')
    echo "<option value='{$xtpl}'>{$xtpl}</option>";
}
 
?>
</select>
<input type="button" value="Load" class="btn btn-info btn-sm" onclick="location.href='edit.php?post_type=wpdmpro&page=emails&task=template&loadetpl='+jQuery('#xtpl').val();">
 </div>
Subject:
<input  class="form-control input-lg" type="text" value="<?php echo isset($etpl['subject'])?htmlentities(stripcslashes($etpl['subject'])):''; ?>" placeholder="Subject" name="et[subject]" /><br/><br/>
<b>Template:</b>
<div id="poststuff" class="postarea" contentEditable="true" style="border-radius: 3px;border: 1px solid #ccc;padding:10px;">
<?php echo htmlspecialchars_decode(stripslashes($etpl['body'])); //,'et[body]','body', false, false); ?>                
</div>
<input type="hidden" name="et[body]" value="" id="mbd" />
<input type="hidden" value="0" id="rst" />
<br/>
<b>Variables:</b><br/>
<code>[download_url]</code> - Download URL<Br/>
<code>[title]</code> - Package Title<Br/>
<code>Double click on image to change it</code>
<br/>From Mail:
<input class="form-control input-lg" type="text" value="<?php echo isset($etpl['frommail'])?$etpl['frommail']:''; ?>" placeholder="From Mail" name="et[frommail]" /><br/><br/>
From Name:
<input class="form-control input-lg" type="text" value="<?php echo isset($etpl['fromname'])?htmlentities(stripcslashes($etpl['fromname'])):''; ?>" placeholder="From Name" name="et[fromname]" /><br/><br/>

<input type="submit" class="btn btn-primary button-large" value="Save Template"  style="margin-top: 10px;">
</form>
<br class="clear">

</div>
</div>

 <script language="JavaScript">
 <!--
   jQuery(function(){
       jQuery('#rst').val(0);
       jQuery('#posts-filter').submit(function(){
           if(jQuery('#rst').val()==1) return true;      
           jQuery('#mbd').val(jQuery('#poststuff').html());           
           jQuery('#rst').val(1);
           jQuery('#posts-filter').submit();  
           //if(jQuery('#rst').val()==0) return false;      
       });
       
       jQuery('#poststuff img').dblclick(function() {                            
                var ob = jQuery(this);
                tb_show('', '<?php echo admin_url('media-upload.php?type=image&TB_iframe=1&width=640&height=551'); ?>');
                window.send_to_editor = function(html) {           
                  var imgurl = jQuery('img',"<p>"+html+"</p>").attr('src');                     
                  jQuery(ob).attr("src",imgurl).css("max-width","100%").css("max-height","100%");
                  tb_remove();
                  }
                return false;
            });
 
       
   });
 //-->
 </script>