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

    $uid = isset($_GET['uid'])?intval($_GET['uid']):1;
    $m = isset($_GET['m'])?intval($_GET['m']):date('n');
    $d = isset($_GET['d'])?intval($_GET['d']):date('d');
    $y = isset($_GET['y'])?intval($_GET['y']):date('Y');
?>
<form method="get" action="edit.php">
    <input type="hidden" name="post_type" value="wpdmpro">
    <input type="hidden" name="page" value="wpdm-stats">
<input type="hidden" name="type" value="pvupd">
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

<?php

$limit = 10;
$cp = isset($_GET['paged'])?(int)$_GET['paged']:1;
$start = ($cp-1)*$limit;
$wpdb->show_errors();
$tusers = $wpdb->get_var("select count(s.id) as td from {$wpdb->prefix}users u,{$wpdb->prefix}ahm_download_stats s where `s`.`day`='$d' and `s`.`month`='$m' and `s`.`year`='$y' and u.ID=s.uid group by s.uid");
$users = $wpdb->get_results("select u.*,count(s.id) as td from {$wpdb->prefix}users u,{$wpdb->prefix}ahm_download_stats s where `s`.`day`='$d' and `s`.`month`='$m' and `s`.`year`='$y' and u.ID=s.uid group by ID limit $start, $limit",ARRAY_A);
$files = $wpdb->get_results("select f.*,count(s.id) as td from {$wpdb->prefix}posts f,{$wpdb->prefix}ahm_download_stats s where `s`.`day`='$d' and `s`.`month`='$m' and `s`.`year`='$y' and `s`.`pid`=`f`.`ID` and `f`.`post_type`='wpdmpro' group by `f`.`ID`",ARRAY_A);
$d = isset($_GET['d'])?intval($_GET['d']):date('d');
$m = isset($_GET['m'])?intval($_GET['m']):date('n');
$y = isset($_GET['y'])?intval($_GET['y']):date('Y');
echo "<table class='table table-bordered' width=100% cellspacing=0>" ;
echo "<tr><th>PACKAGE/USER</th>";
foreach($users as $user){
    if($user['td']>0){
    echo "<th>".$user['user_login']."</th>";
    $dusers[] = $user;
    }

}

echo "</tr>";
foreach($files as $file){
    echo "<tr><th align=left>{$file['post_title']}</th>";
    if($dusers){
    foreach($dusers as $user){

          $fstats[$file['ID']][$user['ID']] = $wpdb->get_var("select count(*)  from {$wpdb->prefix}ahm_download_stats where `year`='{$y}' and `month`='{$m}' and `day`='{$d}' and pid='{$file['ID']}' and uid='{$user['ID']}'");
          echo "<td align=center>".$fstats[$file['ID']][$user['ID']]."</td>";

    }}
    echo "</tr>";
}
echo "</table>" ;


$page_links = paginate_links( array(
    'base' => add_query_arg( 'paged', '%#%' ),
    'format' => '',
    'prev_text' => __('&laquo;'),
    'next_text' => __('&raquo;'),
    'total' => ceil($tusers/$limit),
    'current' => $cp
));

?>
<?php if ( $page_links ) { ?>
<div class="tablenav">
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s users' ) . '</span>%s',
    number_format_i18n( ( $cp - 1 ) * $limit + 1 ),
    number_format_i18n( min( $cp * $limit, $tusers ) ),
    number_format_i18n( $tusers ),
    $page_links
); echo $page_links_text; ?></div>
</div>
<?php } ?>



