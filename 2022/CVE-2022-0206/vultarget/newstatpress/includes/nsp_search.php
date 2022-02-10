<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

function nsp_DatabaseSearch($what='') {
  global $wpdb;
  $table_name = nsp_TABLENAME;

  $f['urlrequested']=__('URL Requested','newstatpress');
  $f['agent']=__('Agent','newstatpress');
  $f['referrer']=__('Referrer','newstatpress');
  $f['search']=__('Search terms','newstatpress');
  $f['searchengine']=__('Search engine','newstatpress');
  $f['os']=__('Operative system','newstatpress');
  $f['browser']=__('Browser','newstatpress');
  $f['spider']=__('Spider','newstatpress');
  $f['ip']=__('IP','newstatpress');
?>
  <div class='wrap'><h2><?php _e('Search','newstatpress'); ?></h2>
  <form method=get><table>
  <?php
    for($i=1;$i<=3;$i++) {
      print "<tr>";
      print "<td>".__('Field','newstatpress')." <select name=where$i><option value=''></option>";
      foreach ( array_keys($f) as $k ) {
        print "<option value='$k'";
        if($_GET["where$i"] == $k) { print " SELECTED "; }
        print ">".$f[$k]."</option>";
      }
      print "</select></td>";
      if (isset($_GET["groupby$i"])) {
        // must only be a "checked" value if this is set
        print "<td><input type=checkbox name=groupby$i value='checked' "."checked"."> ".__('Group by','newstatpress')."</td>";
      } else print "<td><input type=checkbox name=groupby$i value='checked' "."> ".__('Group by','newstatpress')."</td>";

      if (isset($_GET["sortby$i"])) {
         // must only be a "checked" value if this is set
         print "<td><input type=checkbox name=sortby$i value='checked' "."checked"."> ".__('Sort by','newstatpress')."</td>";
      } else print "<td><input type=checkbox name=sortby$i value='checked' "."> ".__('Sort by','newstatpress')."</td>";

      $what='';
      if (isset($_GET["what$i"])) $what=$_GET["what$i"];
      print "<td>, ".__('if contains','newstatpress')." <input type=text name=what$i value='".esc_js(esc_html($what))."'></td>";
      print "</tr>";
    }
    
    $orderby='';
    if (isset($_GET['oderbycount'])) $orderby=$_GET['oderbycount'];
    
    $spider='';
    if (isset($_GET['spider'])) $spider=$_GET['spider'];
    
    $feed='';
    if (isset($_GET['feed'])) $feed=$_GET['feed'];
?>
  </table>
  <br>
  <table>
   <tr>
     <td>
       <table>
         <tr><td><input type=checkbox name=oderbycount value=checked <?php print esc_html($orderby) ?>> <?php _e('sort by count if grouped','newstatpress'); ?></td></tr>
         <tr><td><input type=checkbox name=spider value=checked <?php print esc_html($spider) ?>> <?php _e('include spiders/crawlers/bot','newstatpress'); ?></td></tr>
         <tr><td><input type=checkbox name=feed value=checked <?php print esc_html($feed) ?>> <?php _e('include feed','newstatpress'); ?></td></tr>
       </table>
     </td>
     <td width=15> </td>
     <td>
       <table>
         <tr>
           <td><?php _e('Limit results to','newstatpress'); ?>
             <select name=limitquery><?php if($_GET['limitquery'] >0) { print "<option>".esc_html($_GET['limitquery'])."</option>";} ?><option>1</option><option>5</option><option>10</option><option>20</option><option>50</option></select>
           </td>
         </tr>
         <tr><td>&nbsp;</td></tr>
         <tr>
          <?php wp_nonce_field('nsp_search', 'nsp_search_post'); ?>
          <td align=right><input class='button button-primary' type=submit value=<?php _e('Search','newstatpress'); ?> name=searchsubmit></td>
         </tr>
       </table>
     </td>
    </tr>
   </table>
   <input type=hidden name=page value='nsp_search'>
   <input type=hidden name=newstatpress_action value=search>
  </form>

  <br>
<?php

 if(isset($_GET['searchsubmit'])) {
   check_admin_referer('nsp_search', 'nsp_search_post'); 
   if (!current_user_can('administrator')) die("NO permission");
 
   $retrieved_nonce = $_REQUEST['nsp_search_post'];
   if (!wp_verify_nonce($retrieved_nonce, 'nsp_search' ) ) die( 'Failed security check' );
 
   # query builder
   $qry="";
   $array = array();
   
   # FIELDS
   $fields="";
   for($i=1;$i<=3;$i++) {
     if($_GET["where$i"] != '') {       
       $where_i=$_GET["where$i"];
       if (!array_key_exists($where_i, $f)) $where_i=''; // prevent to use not valid values
       $fields.=$where_i.',';
     }
   }
   $fields=rtrim($fields,",");
      
   
   # WHERE
   $where="WHERE 1=1";

   if (!isset($_GET['spider'])) { $where.=" AND spider=''"; }
   else if($_GET['spider'] != 'checked') { $where.=" AND spider=''"; }

   if (!isset($_GET['feed'])) { $where.=" AND feed=''"; }
   else if($_GET['feed'] != 'checked') { $where.=" AND feed=''"; }

   for($i=1;$i<=3;$i++) {   
     if(($_GET["what$i"] != '') && ($_GET["where$i"] != '')) {
       $where_i=$_GET["where$i"];
       if (array_key_exists($where_i, $f)) {
         ///$what_i=esc_sql($_GET["what$i"]);
         $what_i=$_GET["what$i"];              // sanitize with prepare  
         $where.=" AND ".$where_i." LIKE %s ";
         $array[]="%".$what_i."%";             // sanitize with prepare                               
       }  
     }
   }
   # ORDER BY
   $orderby="";
   for($i=1;$i<=3;$i++) {
     if (isset($_GET["sortby$i"]) && ($_GET["sortby$i"] == 'checked') && ($_GET["where$i"] != '')) {
       $where_i=$_GET["where$i"];
       if (array_key_exists($where_i, $f)) {
         $orderby.=$where_i.',';
       }  
     }
   }

   # GROUP BY
   $groupby="";
   for($i=1;$i<=3;$i++) {
     if(isset($_GET["groupby$i"]) && ($_GET["groupby$i"] == 'checked') && ($_GET["where$i"] != '')) {
       $where_i=$_GET["where$i"];
       if (array_key_exists($where_i, $f)) {
         $groupby.=$where_i.',';
       }
     }
   }
   if($groupby != '') {
     $groupby="GROUP BY ".rtrim($groupby,',');
     $fields.=",count(*) as totale";
     if(isset($_GET["oderbycount"]) && $_GET['oderbycount'] == 'checked') { $orderby="totale DESC,".$orderby; }
   }

   if($orderby != '') { $orderby="ORDER BY ".rtrim($orderby,','); }

   $limit_num=intval($_GET['limitquery']); // force to use integer
   $limit="LIMIT %d";     // for prepare
   $array[]=$limit_num;   // for prepare

   # Results
   print "<h2>".__('Results','newstatpress')."</h2>";
   
   // use prepare
   $sql=$wpdb->prepare("SELECT $fields FROM $table_name $where $groupby $orderby $limit;", $array);
   
   //print "$sql<br>";
   print "<table class='widefat'><thead><tr>";
   for($i=1;$i<=3;$i++) {
     $where_i=strip_tags($_GET["where$i"]);
     if($where_i != '') { print "<th scope='col'>".ucfirst(htmlspecialchars($where_i, ENT_COMPAT, 'UTF-8'))."</th>"; }
   }
   if($groupby != '') { print "<th scope='col'>".__('Count','newstatpress')."</th>"; }
     print "</tr></thead><tbody id='the-list'>";
     $qry=$wpdb->get_results($sql,ARRAY_N);
     foreach ($qry as $rk) {
       print "<tr>";
       for($i=1;$i<=3;$i++) {
         print "<td>";
         if($_GET["where$i"] == 'urlrequested') { print nsp_DecodeURL($rk[$i-1]); }
         else { if(isset($rk[$i-1])) print $rk[$i-1]; }
         print "</td>";
       }
         print "</tr>";
     }
     print "</table>";
     print "<br /><br /><font size=1 color=gray>sql: ".esc_html($sql)."</font></div>";
  }
}
?>
