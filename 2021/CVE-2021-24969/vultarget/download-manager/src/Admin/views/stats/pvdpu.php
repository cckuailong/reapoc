<style type="text/css">
.stable {
    border-top:1px solid #888;
    border-left:1px solid #888;
}
.stable td,.stable th{
    border-bottom:1px solid #888;
    border-right:1px solid #888;
    padding:3px;
}
</style>
<?php
if(!defined("ABSPATH")) die("Shit happens!");
global $wpdb;
$my  = $wpdb->get_var("select min(year) as my from {$wpdb->prefix}ahm_download_stats");

$uid = isset($_GET['uid'])&&$_GET['uid']!='all'?" uid='".intval($_GET['uid'])."' and":'';
$m = isset($_GET['m'])?intval($_GET['m']):date('n');
$y = isset($_GET['y'])?intval($_GET['y']):date('Y');
?>
<form method="get" action="edit.php">
<input type="hidden" name="post_type" value="wpdmpro">
<input type="hidden" name="page" value="wpdm-stats">
<input type="hidden" name="type" value="pvdpu">
User ID: <input style="width: 60px" type="text" name="uid" value="<?php echo isset($_GET['uid'])?esc_attr($_GET['uid']):'all';?>">
    Year:
    <select name="y">
        <?php for($i=$my;$i<=date('Y');$i++) { $sel = $y==$i?'selected=selected':''; echo "<option $sel value='{$i}'>{$i}</option>";} ?>
    </select>
Month: <select name="m">
<?php for($i=1;$i<=12;$i++) { $sel = $m==$i?'selected=selected':''; echo "<option $sel value='{$i}'>{$i}</option>";} ?>
</select>
<input type="submit" class="button-secondary" value="Submit">

</form>
 <br>

<?php

$files = $wpdb->get_results("select ID, post_title  from {$wpdb->prefix}posts where post_type='wpdmpro' and post_status='publish'",ARRAY_A);
$dates = $wpdb->get_results("select *, concat(year,month,day) as dt from {$wpdb->prefix}ahm_download_stats where $uid `year`='$y' and `month`='$m' group by dt order by timestamp asc",ARRAY_A);
echo "<table  class='table table-bordered table-hover' width=100% cellspacing=0>" ;
echo "<tr><th>PACKAGE/DATE</th>";
foreach($dates as $date){
    echo "<th><a href='edit.php?post_type=wpdmpro&page=wpdm-stats&type=pvupd&y={$date['year']}&m={$date['month']}&d={$date['day']}'>".date("d",$date['timestamp'])."</a></th>";

}
echo "</tr>";
foreach($files as $file){
    echo "<tr><th align=left>{$file['post_title']}</th>";
    foreach($dates as $date){

          $fstats[$file['ID']][$date['dt']] = $wpdb->get_var("select count(*)  from {$wpdb->prefix}ahm_download_stats where concat(year,month,day)='{$date['dt']}' and pid='{$file['ID']}'");
          if($fstats[$file['ID']][$date['dt']]>0)
          echo "<td class='info' title='".$fstats[$file['ID']][$date['dt']]." downloads' align=center><a href='edit.php?post_type=wpdmpro&page=wpdm-stats&type=pvdaud&pid={$file['ID']}&y={$date['year']}&m={$date['month']}&d={$date['day']}'>".$fstats[$file['ID']][$date['dt']]."</a></td>";
          else
          echo "<td align=center>".$fstats[$file['ID']][$date['dt']]."</td>";

    }
    echo "</tr>";
}
echo "</table>" ;



