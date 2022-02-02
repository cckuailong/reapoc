<?php if (!defined('ABSPATH')) { exit; } 
$backupDirs = array('uploads.zip','plugins.zip','themes.zip','others.zip','db.sql.gz');
$upload_dir = wp_upload_dir();
$backup_dirname = $upload_dir['basedir'].'/wp-file-manager-pro/fm_backup/';
$backup_baseurl = $upload_dir['baseurl'].'/wp-file-manager-pro/fm_backup/';
global $wpdb;
$fmdb = $wpdb->prefix.'wpfm_backup';
$backups = $wpdb->get_results("select * from ".$fmdb." order by id desc");
?>
<style>
.wrap.restore-sec {
    background: #fff;
    padding: 25px;
    border: 1px #dddddd solid;
	margin-top:20px;
}
.wrap.restore-sec .title {
    border-bottom: 1px #dddddd solid;
    padding-bottom: 15px;
}
.wrap.restore-sec .title h3 {
    padding: 0px;
    margin: 0px;
    color: #000;
    font-size: 22px;
    font-weight: 700;
}
.schedule-back{
	padding:35px 0px;
	    border-bottom: 1px #ddd solid;
}
.schedule-back::after{
    content:"";
    display:table;
    clear:both;
}
.schedule-back .files{
	width:50%;
	float:left;
	margin-bottom: 20px;
	 margin-top: 15px
}
.schedule-back .files .finner::after{
    content:"";
    display:table;
    clear:both;
}
.schedule-back .files h4 {
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 10px;
	font-family: sans-serif;
}
.schedule-back .files p {
    font-size: 14px;
}
.schedule-back .files .backup_btn{
    background: #267ddd;
    color: #fff;
    padding: 12px 20px;
    text-decoration: none;
    border-radius: 3px;
    font-size: 16px;
    float: left;
    margin-top: 20px;
	font-weight:500;
}

.schedule-back .well {
    width: 50%;
    background: #f1f1f1;
    clear: both;
    padding: 15px;
    border-radius: 5px;
    border: 1px #ddd solid;
    font-size: 14px;
}
.log-message{
	padding:40px 0px;
	border-bottom: 1px #ddd solid;
    clear:both;
}
.log-message p{
    background: #f4f4f4;
    padding: 12px 20px;
    border-radius: 3px;
    margin-top: 25px;
    margin-bottom: 0px;
}
.existing-back{
	padding-top:40px;
	padding-bottom:20px;
}
.existing-back h3{
	margin:0px;
	padding:0px;
	font-size: 22px;
    font-weight: 700;
    margin-bottom: 20px
}
.existing-back h3 span{
	background: #0e6bb7;
    font-size: 14px;
    font-weight: 500;
    color: #fff;
    width: 30px !important;
    display: inline-block;
    text-align: center;
    margin-left: 10px;
    padding: 2px;
    border-radius: 10px;
}
strong {
    font-weight: 700;
}
p{
	font-size:14px;
}
.existing-back p a{
    text-decoration: none;
}
.existing-back p{
	margin:10px 0px;
}

.backup-main{
	border:1px #ddd solid;
	padding:10px;
	font-weight:bold;
}
.backup-main .backup-date{
    width: 230px;
    display:inline-block;
    /*float: left;*/
    position: relative;
}
.backup-main input[type=checkbox]{  
	/*position: absolute;
    top: 15%;*/
}
.backup-main .backup-date span{
   /* display: block;
    padding-left: 25px;*/
}
.database-sec{
	    border: 1px #ddd solid;
    padding: 15px 10px;
    font-weight: bold;
	border-top:0px !important;
	background:#f4f4f4;
}
.database-sec::after{
    content:"";
    dispaly:table;
    clear:both;
}
.database-sec .backup-date {
    width: 230px;
    display: inline-block;
    /*float: left;*/
    position: relative;
    vertical-align: middle;
}
.database-sec input[type=checkbox]{
    /*position: absolute;
    top: 15%;*/
}
.database-sec .backup-date span{
    /*display: block;
    padding-left: 25%;*/
}
.database-sec a {
    color: #404040;
    text-decoration: none;
    background: #fff;
    padding: 7px 15px;
    border-radius: 5px;
    border: 1px #ddd solid;
    font-size: 12px;
    display: inline-block;
    margin-bottom: 3px;
}
.database-sec a:hover{
	color: #404040;
}
.action-sec a{
	color: #404040;
    text-decoration: none;
    background: #fff;
    padding: 7px 15px;
    margin-left: 10px;
    border-radius: 5px;
    border: 1px #ddd solid;
	font-weight: bold;
}
.action-sec {
    margin-top: 30px;
	    margin-bottom: 20px;
}
.action-sec strong {
    margin-right: 15px;
}
.action-sec i {
    font-size: 14px;
    color: #999;
    margin-left: 15px;
}
.light-back{
	background:#f4f4f4 !important;
	color:#898989 !important;
}
.fm_open_files_options{
    border:1px solid #ddd;
    clear: both;
    padding: 20px;
    margin-top: 20px;
    position:relative;
    /* display:none; */
}
.double-col li{
    list-style:none;
    margin:0px;
}
.double-col::after{
    content:"";
    display:table;
    clear:both;
}
.double-col .inner-col-wrap{
    margin-bottom:20px;
}
.double-col h4{
    margin: 0px 0px 17px;
    font-size: 16px;
    font-weight: bold;
}
.double-col .inner-col-wrap::after{
    content:"";
    display:table;
    clear:both;
}
.double-col .inner-col-half .colmn-div3{
    float: left;
    width: calc(33.3333% - 20px);
    margin-right: 20px;
}
.double-col .inner-col-half .backup_btn {
    background: #0e6bb7;
    color: #fff;
    border: none;
    padding: 10px 12px;
    border-radius: 3px;
    cursor: pointer;
}
.fm_open_files_options::before{
    content:"";
    position: absolute;
    top: -9px;
    left: 20px;
    right: 0;
    width: 15px;
    height: 15px;
    transform: rotate(-135deg);
    -webkit-transform: rotate(-135deg);
    -moz-transform: rotate(-135deg);
    -o-transform: rotate(-135deg);
    -ms-transform: rotate(-135deg);
    border-right: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    background: #fff;
}
/* All pop-ups css*/
.fmbkp_console_popup, .restore_backup_popup, .dlt_backup_popup, .dlt_success_popup{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: none;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
}
.fmbkp_console_popup_tbl, .restore_backup_popup_tbl, .dlt_backup_popup_tbl, .dlt_success_popup_tbl{
    display:table;
    width:100%;
    height:100%;
}
.fmbkp_console_popup_cel, .restore_backup_popup_cel, .dlt_backup_popup_cel, .dlt_success_popup_cel{
    display:table-cell;
    vertical-align:middle;
}
.fmbkp_console_popup_inner, .restore_backup_popup_inner, .dlt_backup_popup_inner, .dlt_success_popup_inner{
    max-width: 450px;
    margin: 0 auto;
    background: #fff;
    position: relative;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,.5);
}
.fmbkp_console_popup_inner, .restore_backup_popup_inner{
    border-bottom: 10px solid #0e6bb7;
}
.dlt_backup_popup_inner{
    border-bottom: 10px solid #de524b;
}
.dlt_success_popup_inner{
    border-bottom: 10px solid green;
}
.fmbkp_console_popup_inner .close_fm_console , .close_restore_backup, .close_dlt_backup, .close_dlt_success {
    position: absolute;
    color: #fff;
    text-decoration: none;
    right: 20px;
    font-size: 30px;
    top: 20px;
}
.schedule-back h3{
    margin: 0px;
    padding: 25px 20px;
    font-size: 22px;
    font-weight: 700;
    border-bottom: 1px solid #e5e5e5;
    text-align: center;
    background: #0e6bb7;
    color: #fff;
}
.schedule-back .dlt_backup_popup h3{
    background: #de524b;
}
.schedule-back .dlt_success_popup h3{
    background: green;
}
.log-message h3{
	margin:0px;
	padding:0px;
	font-size: 22px;
    font-weight: 700;
}
.restore_btn_wrap, .dlt_btn_wrap, .dlt_success_wrap{
    padding: 20px 20px 30px;
    text-align: center;
}
.backup_btn_common{
    border: none;
    width: 76px;
    line-height: 30px;
    padding: 0px;
    color: #fff;
    border-radius: 3px;
    cursor:pointer;
}
.restore_cancel,  .dlt_cancel{
    background: #de524b;
}
.restore_confirmed, .dlt_btn_wrap .dlt_confirmed{
    background: #156bb7;
}
.dlt_confirmed_success{
    background: green;
}
/**/          
#fmbkp_console {
    clear: both;
    color: #fff;
    padding-bottom: 15px;
}
#fmbkp_console .fm_console_success{
    color: green;
}
#fmbkp_console .fm_console_log_pop{
    margin: 0px;
    margin-bottom: 15px;
    padding: 25px 20px;
    font-size: 22px;
    color: #fff;
    font-weight: 700;
    border-bottom: 1px solid #e5e5e5;
    text-align: left;
    background: #0e6bb7;
}

#fmbkp_console p{
    padding: 10px 20px;
    margin: 0px;
    color: #444;
}
#fmbkp_console p.backup_wait{
    margin: 0px;
    margin-bottom: 15px;
    padding: 25px 20px;
    font-size: 22px;
    line-height: 18px;
    color: #fff;
    font-weight: 700;
    border-bottom: 1px solid #e5e5e5;
    text-align: left;
    background: #0e6bb7;
}
#fmbkp_console .fm_console_error {
	color: red;
}
.no_backup {
	text-align: center;
	color: #fe0505;
	padding: 15px;
	margin: 0;
	font-size: 18px;
	margin-top: 20px;
}
.fmbkp_console_loader img {
	width: 70px;
	height: 20px;
}
.backup-main::after{
    content:"";
    display:table;
    clear:both;
}
.bck_action {
	/* float: left; */
	width: calc(100% - 495px);
	display: inline-block;
	vertical-align: middle;
}
.action_ele {
	/* float: left; */
	width: 252px;
	display: inline-block;
    vertical-align: middle;
}
.database-sec::after{
    content:"";
    display:table;
    clear:both;
}
.exitBackBtn{
	border: none;
	padding: 7px 15px;
	cursor: pointer;
    border-radius:5px;
    color: #fff;
    margin-bottom: 3px;
}
.restore_btn{
    background: #0e6bb7;
}
.del_btn{
    background: #de524c;
	
}
.log_btn{
    background: #fff;
	color: #404040;
    border:1px solid #ddd;

}
.log_msg_align_center {
    text-align: center;
}
.disabled_btn {
	cursor: default;
	pointer-events: none;
	background: #ddd;
	color: #fff;
}
</style>
<div class="wrap restore-sec">
	<div class="title">
		<h3> <?php _e('WP File Manager - Backup/Restore', 'wp-file-manager'); ?></h3>
	</div>
	
	<div class="schedule-back">
		<!-- <h3><?php //_e('Backup Options:', 'wp-file-manager'); ?></h3> -->
		
        <div class="double-col">
            <h4><?php _e('Backup Options:', 'wp-file-manager'); ?></h4>
            <div class="inner-col-wrap">
                <div class="inner-col-half">
                    <div class="colmn-div3">
                        <input type="checkbox" name="fm_bkp_database" id="fm_bkp_database" value="5" checked="checked"> <?php _e('Database Backup', 'wp-file-manager'); ?> 
                    </div>
                    <div class="colmn-div3">
                        <input type="checkbox" name="fm_bkp_files" id="fm_bkp_files" value="files" checked="checked"> <a href="javascript:void(0)" id="fm_open_files_option"><?php _e('Files Backup', 'wp-file-manager'); ?></a>
                        <div id="fm_open_files_options" class="fm_open_files_options">
                            <li><input type="checkbox" id="fm_bkp_plugins" name="fm_bkp_plugins" value="1" checked="checked"><?php _e('Plugins', 'wp-file-manager'); ?></li>
                            <li><input type="checkbox" id="fm_bkp_themes" name="fm_bkp_themes" value="2" checked="checked"><?php _e('Themes', 'wp-file-manager'); ?></li>
                            <li><input type="checkbox" id="fm_bkp_uploads" name="fm_bkp_uploads" value="3" checked="checked"><?php _e('Uploads', 'wp-file-manager'); ?></li>
                            <li><input type="checkbox" id="fm_bkp_other" name="fm_bkp_other" value="4" checked="checked"><?php _e('Any other directories found inside wp-content', 'wp-file-manager'); ?></li>
                        </div>
                    </div>
                    <div class="colmn-div3 inner-col-half">
                        <button id="wpfm-backupnow-button" type="button" class="backup_btn"><?php _e('Backup Now', 'wp-file-manager'); ?></button>
                    </div>
                </div>
            </div>                    
        </div>
        
        <div class="double-col">
            <div class="well">
                <b><?php _e('Time now', 'wp-file-manager'); ?></b>:  <?php echo date('D, F d, Y H:i');?>
            </div>
        </div>

		<div class="dlt_success_popup">
            <div class="dlt_success_popup_tbl">
                <div class="dlt_success_popup_cel">
                    <div class="dlt_success_popup_inner">
                        <a href="javascript:void(0)" class="close_dlt_success">&times;</a>
		                <div id="dlt_success_success"> 
                            <h3><?php _e('Success', 'wp-file-manager'); ?></h3>
                            <div class="dlt_success_wrap">
                                <p><?php _e('Backup successfully deleted', 'wp-file-manager'); ?></p>
                                <button class="dlt_confirmed_success backup_btn_common"><?php _e('Ok', 'wp-file-manager'); ?></button>
                            </div>
                        </div>
                    </div><!--dlt_success_popup_inner-->
                </div>
            </div>
        </div>
        <!--dlt_success_popup-->

		<div class="dlt_backup_popup">
            <div class="dlt_backup_popup_tbl">
                <div class="dlt_backup_popup_cel">
                    <div class="dlt_backup_popup_inner">
                        <a href="javascript:void(0)" class="close_dlt_backup">&times;</a>
		                <div id="dlt_backup">
                            <h3><?php _e('DELETE FILES', 'wp-file-manager'); ?></h3>
                            <div class="dlt_btn_wrap">
                                <p><?php _e('Are you sure you want to delete this backup?', 'wp-file-manager'); ?></p>
                                <button class="dlt_cancel backup_btn_common"><?php _e('Cancel', 'wp-file-manager'); ?></button>
                                <button class="dlt_confirmed backup_btn_common"><?php _e('Confirm', 'wp-file-manager'); ?></button>
                            </div>
                        </div>
                    </div><!--dlt_backup_popup_inner-->
                </div>
            </div>
        </div>
        <!--dlt_backup_popup-->

		<div class="restore_backup_popup">
            <div class="restore_backup_popup_tbl">
                <div class="restore_backup_popup_cel">
                    <div class="restore_backup_popup_inner">
                        <a href="javascript:void(0)" class="close_restore_backup">&times;</a>
		                <div id="restore_backup"> 
                            <h3><?php _e('RESTORE FILES', 'wp-file-manager'); ?></h3>
                            <div class="restore_btn_wrap">
                                <p><?php _e('Are you sure you want to restore this backup?', 'wp-file-manager'); ?></p>
                                <button class="restore_cancel backup_btn_common"><?php _e('Cancel', 'wp-file-manager'); ?></button>
                                <button class="restore_confirmed backup_btn_common"><?php _e('Confirm', 'wp-file-manager'); ?></button>
                            </div>
                        </div>
                    </div><!--restore_backup_popup_inner-->
                </div>
            </div>
        </div>
        <!--restore_backup_popup-->

		<div class="fmbkp_console_popup">
            <div class="fmbkp_console_popup_tbl">
                <div class="fmbkp_console_popup_cel">
                    <div class="fmbkp_console_popup_inner">
                        <a href="javascript:void(0)" class="close_fm_console">&times;</a>
		                <div id="fmbkp_console"> </div>
                        <div class="fmbkp_console_loader">
                            <img src="<?php echo plugins_url('images/loader-fm-console.gif', dirname(__FILE__)); ?>"/>
                        </div>
                    </div><!--fmbkp_console_popup_inner-->
                </div>
            </div>
        </div>
        <!--fmbkp_console_popup-->
	</div>
	
	<div class="log-message">
		<h3><?php _e('Last Log Message', 'wp-file-manager'); ?></h3>
        <p>
        <?php if(isset($backups) && !empty($backups)) { ?>
            <?php _e('The backup apparently succeeded and is now complete', 'wp-file-manager'); ?> (<?php echo $backups[0]->backup_date;?>)
             <?php } else { ?>
                <?php _e('No log message', 'wp-file-manager'); ?> 
             <?php } ?>
		</p>
	</div>
	
	<div class="existing-back">
		<h3><?php _e('Existing Backups', 'wp-file-manager'); ?> <span><?php echo count($backups);?></span> </h3>
		<!--p><strong> More tasks: </strong> <a href="#">upload backup files</a> | <a href="#">Rescan local folder for new backup sets </a> | <a href="#">Rescan remote storage</a></p-->
		
	</div>
	
	<div class="backup-main">
		<div class="backup-date">
			<input type="checkbox" class="bkpchkCheckAll"> <span> <?php _e('Backup Date', 'wp-file-manager'); ?> </span>
		</div>
		<div class="download bck_action">
			 <span> <?php _e('Backup data (click to download)', 'wp-file-manager'); ?></span>
		</div>
        <div class="action_ele">
			 <span> <?php _e('Action', 'wp-file-manager'); ?></span>
		</div>
	</div>
	

    <?php if(isset($backups) && !empty($backups)) {
        $count = 1;
        $todayDate = date('Y-m-d');
        $todayDate = strtotime($todayDate);
		foreach($backups as $backup) { 
            $backupNameExp = $backup->backup_date; 
            $compareDate = date("Y-m-d", strtotime($backupNameExp));
            $compareDate = strtotime($compareDate);
            $backupName = date("M d, Y H:i", strtotime($backupNameExp));
		?>
	<div class="database-sec <?php echo($count++%2 == 0) ? 'even' : 'odd'?>">
		<div class="backup-date">
			<input type="checkbox" value="<?php echo $backup->id;?>" name="backupids[]" class="backupids"> 
            <span><?php echo $backupName; ?> <?php echo ($todayDate == $compareDate) ? '(Today)' : '';?> </span>
		</div>
		<div class="download bck_action">
		<?php foreach($backupDirs as $backupDir) {
                 $bkpName = $backup->backup_name.'-'.$backupDir;
                 $dir = $backup_dirname.$bkpName;
               if(file_exists($dir)) {   
                if($backupDir == 'db.sql.gz') {
                    $dirName = 'Database';
                } else {
                    $dirName = str_replace('.zip','',$backupDir);
                }
                $size = filesize($dir);
               ?>
                  <a href="<?php echo $backup_baseurl.$bkpName;?>"><?php echo ucfirst($dirName); ?> (<?php echo $this->formatSizeUnits($size); ?>)</a>
              <?php } 
            } ?>
		</div>
        <div class="action_ele">
			 <button class="exitBackBtn restore_btn bkpRestoreID" id="<?php echo $backup->id; ?>"><?php _e('Restore', 'wp-file-manager'); ?></button>
             <button class="exitBackBtn del_btn bkpDeleteID" id="<?php echo $backup->id; ?>"><?php _e('Delete', 'wp-file-manager'); ?></button>
             <button class="exitBackBtn log_btn bkpViewLog" id="<?php echo $backup->id; ?>"><?php _e('View Log', 'wp-file-manager'); ?></button>
		</div>
	</div>
	<?php } ?>
	<?php } else { ?>
           <p class="no_backup"><?php _e('Currently no backups found.', 'wp-file-manager'); ?></p>
          <?php } ?>
	<div class="action-sec">
		<strong> <?php _e('Actions upon selected backups', 'wp-file-manager'); ?></strong>
        <button class="exitBackBtn bkpDelete del_btn disabled_btn"><?php _e('Delete', 'wp-file-manager'); ?></button>
		<button class="exitBackBtn bkpCheckAll restore_btn"><?php _e('Select All', 'wp-file-manager'); ?></button>
        <button class="exitBackBtn bkpUnCheckAll log_btn disabled_btn"><?php _e('Deselect', 'wp-file-manager'); ?></button>	
	</div>
<p><i><?php _e('Note: Backup files will be under <code>'.$backup_dirname.'</code>', 'wp-file-manager'); ?></i></p>	
</div>
<?php $wpfmbackup = wp_create_nonce( 'wpfmbackup' ); ?>
<script>
jQuery(document).ready(function(){
    var ajax_url = "<?php echo admin_url('admin-ajax.php')?>";
    jQuery("#wpfm-backupnow-button").click(function(){
        var fm_bkp_database = jQuery('#fm_bkp_database').prop('checked');
        var fm_bkp_files = jQuery('#fm_bkp_files').prop('checked');
        var fm_bkp_plugins = jQuery('#fm_bkp_plugins').prop('checked');
        var fm_bkp_themes = jQuery('#fm_bkp_themes').prop('checked');
        var fm_bkp_uploads = jQuery('#fm_bkp_uploads').prop('checked');
        var fm_bkp_other = jQuery('#fm_bkp_other').prop('checked');
        var fm_bkp_id = ''; // empty
        jQuery('.fmbkp_console_popup').show();
        jQuery('#fmbkp_console').show().html('<p class="backup_wait">Backuping please wait...</p>');
        wp_fm_backup(ajax_url, fm_bkp_database,fm_bkp_files,fm_bkp_plugins,fm_bkp_themes,fm_bkp_uploads,fm_bkp_other,fm_bkp_id);
  });
 function wp_fm_backup(ajax_url, fm_bkp_database,fm_bkp_files,fm_bkp_plugins,fm_bkp_themes,fm_bkp_uploads,fm_bkp_other,fm_bkp_id){
    jQuery.ajax({
		url : ajax_url,
		type : 'post',
		data : {
			action : 'mk_file_manager_backup',
			database : fm_bkp_database,
            files: fm_bkp_files,
            plugins: fm_bkp_plugins,
            themes: fm_bkp_themes,
            uploads: fm_bkp_uploads,
            others: fm_bkp_other,
            bkpid: fm_bkp_id,
            nonce: '<?php echo $wpfmbackup;?>'
		},
		success : function( response ) {
			var res = JSON.parse(response);
            console.log(res);
            var next_step = res.step;
            jQuery('.fmbkp_console_popup').show();
            if(next_step == '0') {
                jQuery('.fmbkp_console_loader').hide();              
                jQuery('#fmbkp_console').show().append('<p>> '+res.msg+'</p>');
                location.reload();
            } else {
                jQuery('#fmbkp_console').show().append('<p>> '+res.msg+'</p>');
                wp_fm_backup(ajax_url,res.database,res.files,res.plugins,res.themes,res.uploads,res.others,res.bkpid);
            }            
		}
    });
 } 

// select all -> backups
jQuery(".bkpchkCheckAll").click(function () {
    jQuery(".backupids").prop('checked', jQuery(this).prop('checked'));
    if(jQuery(this).prop('checked')) {
      jQuery('.bkpDelete,.bkpUnCheckAll').removeClass('disabled_btn');
      jQuery('.bkpCheckAll').addClass('disabled_btn');
    } else {
        jQuery('.bkpDelete,.bkpUnCheckAll').addClass('disabled_btn');
        jQuery('.bkpCheckAll').removeClass('disabled_btn');
    }
});
jQuery(".bkpCheckAll").click(function () {
    jQuery(".backupids,.bkpchkCheckAll").prop('checked', true);
    jQuery('.bkpDelete,.bkpUnCheckAll').removeClass('disabled_btn');
    jQuery(this).addClass('disabled_btn');
});
jQuery(".bkpUnCheckAll").click(function () {
    jQuery(".backupids,.bkpchkCheckAll").prop('checked', false);
    jQuery('.bkpDelete,.bkpUnCheckAll').addClass('disabled_btn');
    jQuery('.bkpCheckAll').removeClass('disabled_btn');
    
});
// for toggle backup options
jQuery("#fm_open_files_option").click(function () {
    jQuery("#fm_open_files_options").slideToggle();
});
//close console popup
jQuery(".close_fm_console").click(function () {
    jQuery(".fmbkp_console_popup").hide();
});

// on delete - ajax
jQuery(".bkpDelete").click(function () {
    var delarr = new Array();

    jQuery(".backupids").each(function () {
        if(jQuery(this).is(':checked')) {
        delarr.push(jQuery(this).val());
        }
    }); //each

    if(delarr == '') {
    alert('Select backups to delete!');
    } else {
        var r = confirm("Are you sure want to remove selected backups?")
        if (r == true) {
            jQuery.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                        action : 'mk_file_manager_backup_remove',
                        delarr: delarr,
                        nonce: '<?php echo wp_create_nonce( 'wpfmbackupremove' );?>'
                    },
                cache: false,

            success: function(response) {   
                alert(response);
                location.reload();
            }
            });//ajax
        }
 }
}); //click



//open DELETE popup
jQuery('.bkpDeleteID').on("click",function(){
    // alert("ewf");
    jQuery(".dlt_backup_popup").show();
    var bkpId = jQuery(this).attr('id');
    jQuery('.dlt_confirmed').attr("id", bkpId);    
});
//close DELETE popup 
jQuery(".close_dlt_backup, .dlt_cancel").click(function () {
    jQuery(".dlt_backup_popup").hide();
});
// on delete - ajax
jQuery(".dlt_confirmed").click(function () {
    var bkpId = jQuery(this).attr('id')
        jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                    action : 'mk_file_manager_single_backup_remove',
                    id: bkpId,
                    nonce: '<?php echo wp_create_nonce( 'wpfmbackupremove' );?>'
                },
            cache: false,

        success: function(response) {
            if(response == "Backup removed successfully!"){
                jQuery(".dlt_backup_popup").hide();
                jQuery(".dlt_success_popup").show();
            }
        }
        });//ajax
}); //click
jQuery(".close_dlt_success, .dlt_confirmed_success").click(function () {
    jQuery(".dlt_success_popup").hide();
    location.reload();    
});



// backup - ajax
jQuery(".bkpViewLog").click(function () {
    jQuery('.fmbkp_console_popup').show();
    jQuery('#fmbkp_console').html('');
    var bkpId = jQuery(this).attr('id')
        jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                    action : 'mk_file_manager_single_backup_logs',
                    id: bkpId,
                    nonce: '<?php echo wp_create_nonce( 'wpfmbackuplogs' );?>'
                },
            cache: false,

        success: function(response) {
            jQuery('.fmbkp_console_loader').hide();      
            jQuery('#fmbkp_console').show().html(response);
        }
        });//ajax
}); //click

//open restore popup
jQuery('.bkpRestoreID').on("click",function(){
    // alert("ewf");
    jQuery(".restore_backup_popup").show();
    var bkpId = jQuery(this).attr('id');
    jQuery('.restore_confirmed').attr("id", bkpId);    
});
//close restore popup 
jQuery(".close_restore_backup, .restore_cancel").click(function () {
    jQuery(".restore_backup_popup").hide();
});
// on delete - ajax
jQuery(".restore_confirmed").click(function () {
    var bkpId = jQuery(this).attr('id');
        jQuery.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                    action : 'mk_file_manager_single_backup_restore',
                    id: bkpId,
                    nonce: '<?php echo wp_create_nonce( 'wpfmbackuprestore' );?>'
                },
            cache: false,

        success: function(response) {
            alert(response);
            location.reload();
        }
        });//ajax

}); //click

});
</script>