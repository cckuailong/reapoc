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

</style><?php
if(!defined("ABSPATH")) die("Shit happens!");
global $wpdb;
$my  = $wpdb->get_var("select min(year) as my from {$wpdb->prefix}ahm_download_stats");

$d = isset($_GET['d'])?(int)$_GET['d']:date('d');
$m = isset($_GET['m'])?(int)$_GET['m']:date('n');
$y = isset($_GET['y'])?(int)$_GET['y']:date('Y');
$file = wpdm_query_var('pid', ['validate' => 'int']);
$fd =  $wpdb->get_row("select * from {$wpdb->prefix}posts where id='$file'",ARRAY_A);
$data = $wpdb->get_results("select * from {$wpdb->prefix}ahm_download_stats where pid='$file' and `year`='$y' and `month`='$m' and `day`='$d'",ARRAY_A);
?>
<b>Package: <?php echo $fd['post_title']; ?></b><br>
<form method="get" action="edit.php">
    <input type="hidden" name="post_type" value="wpdmpro">
    <input type="hidden" name="page" value="wpdm-stats">
<input type="hidden" name="type" value="pvdaud">
Package ID: <input style="width: 80px" type="text" name="pid" value="<?php echo $file;?>">
    Year:
    <select name="y">
        <?php for($i=$my;$i<=date('Y');$i++) { $sel = $y==$i?'selected=selected':''; echo "<option $sel value='{$i}'>{$i}</option>";} ?>
    </select>
Month: <select name="m">
<?php for($i=1;$i<=12;$i++) { $sel = $m==$i?'selected=selected':''; echo "<option $sel value='{$i}'>{$i}</option>";} ?>
</select>
Day: <select name="d">
<?php for($i=1;$i<=31;$i++) { $sel = $d==$i?'selected=selected':''; echo "<option $sel value='{$i}'>{$i}</option>";} ?>
</select>
<input type="submit" class="button-secondary" value="Submit">

</form>
 <br>
<table  class='table table-bordered' width="100%" cellspacing="0">
<tr>
    <th align="left">User</th>
    <th align="left">Date/Time</th>
    <th align="left">IP</th>
</tr>
<?php
    foreach($data as $d){
?>
<tr>
    <td><?php echo $wpdb->get_var("select user_login from {$wpdb->prefix}users where ID='$d[uid]'"); ?></td>
    <td><?php echo date("M d,Y H:i:s",$d['timestamp']); ?></td>
    <td><?php echo $d['ip']; ?></td>
</tr>
<?php
    }
?>
</table>
