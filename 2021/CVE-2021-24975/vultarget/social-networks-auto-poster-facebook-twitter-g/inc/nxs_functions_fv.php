<?php 
function nxs_isMobile() {
 return preg_match("/\b(?:a(?:ndroid|vantgo)|b(?:lackberry|olt|o?ost)|cricket|docomo|hiptop|i(?:emobile|p[ao]d)|kitkat|m(?:ini|obi)|palm|(?:i|smart|windows )phone|symbian|up\.(?:browser|link)|tablet(?: browser| pc)|(?:hp-|rim |sony )tablet|w(?:ebos|indows ce|os))/i", $_SERVER["HTTP_USER_AGENT"]);
}
if (!function_exists('nxs_getImageSizes')){ function nxs_getImageSizes($size='') { global $_wp_additional_image_sizes; $sizes = array(); $get_intermediate_image_sizes = get_intermediate_image_sizes();
  foreach( $get_intermediate_image_sizes as $_size ) {
      if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) { $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' ); 
        $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' ); $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
      } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
        $sizes[ $_size ] = array( 'width' => $_wp_additional_image_sizes[ $_size ]['width'], 'height' => $_wp_additional_image_sizes[ $_size ]['height'], 'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']);
      }
  } if ($size) if( isset( $sizes[ $size ] ) ) return $sizes[ $size ]; else return false;
  return $sizes;
}}

if (!function_exists('ns_SMASV41')){function ns_SMASV41(){}} 
if (!function_exists('ns_SMASV4M1')){function ns_SMASV4M1(){}}



if (!class_exists('nxs_adminMgmt')){
    class nxs_adminMgmt {        
        
        var $page;
        var $pluginName = 'SNAP';
        
        public function __construct() {
          //add_action( 'admin_menu', array( $this, 'nxs_adminMenu' ) );        
           
        }
        public function init() {  
          add_action( 'admin_menu', array( $this, 'nxs_adminMenu' ) );                               
        }
        public function initUser() { wp_enqueue_script( 'postbox' );          
          $this->page = add_menu_page( 'Social Networks Auto Poster', 'SNAP|AutoPoster','haveown_snap_accss','nxssnap',array( $this, 'showPage_accounts' ), NXS_PLURL.'img/snap-icon.png');          
          add_submenu_page( 'nxssnap',__( 'Accounts', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Accounts', 'social-networks-auto-poster-facebook-twitter-g' ), 'haveown_snap_accss', 'nxssnap', array( $this, 'showPage_accounts' ) ,0 );                         
          
          add_submenu_page( 'nxssnap',__( 'Log/History', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Log/History', 'social-networks-auto-poster-facebook-twitter-g' ), 'haveown_snap_accss', 'nxs-log', array( $this, 'showPage_log' ) ,0 );                                  
       
        }
        //##MU
        function ntAdminMenu() {
          add_menu_page('Social Networks Auto Poster', 'SNAP|AutoPoster', 'manage_options', 'nxssnap', array($this, 'showPage_ntdashboard'), NXS_PLURL.'img/snap-icon.png');  
        }
        function showPage_ntdashboard() {
            add_meta_box( $this->page.'_dashboard', __( 'Welcome to the Social Networks Autoposter (SNAP)', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_ntdashboard' ), $this->page, 'normal' );            
            $this->showPage_side(); $this->showPage($this->pluginName.': '. __( 'Dashboard', 'social-networks-auto-poster-facebook-twitter-g' )); 
        }
        public function metabox_ntdashboard($post){ global $nxs_SNAP;      
          if ($nxs_SNAP->sMode['l']=='F') { $sites = wp_get_sites(array('public'=> 1, 'archived' => 0, 'mature' => 0, 'spam' => 0, 'deleted' => 0)); $cs = get_site_option('nxs_nts');
            if (!empty($_POST['nxs_ntsiteid']) && check_admin_referer('nxsSsPageWPN', 'nxsSsPageWPN_wpnonce') ) { $csN = (int)$_POST['nxs_ntsiteid']; 
              if (!empty($cs) && $csN!=$cs) { switch_to_blog($cs); delete_option('NS_SNAutoPoster'); restore_current_blog();} $cs = $csN; update_site_option('nxs_nts', $cs); 
            } foreach ( $sites as $i => $site ) { $blog = get_blog_details($site['blog_id']); $sites[$i]['name'] = $blog->blogname; if ( $sites[$i]['blog_id']==$cs) $cSite = $sites[$i]; }  
            // uasort( $sites, function( $site_a, $site_b ) { return strcasecmp( $site_a[ 'name' ], $site_b[ 'name' ] ); });
            ?>  <?php _e( 'You are using "Free" version of the plugin. This version could used only on a single site by single user.', 'social-networks-auto-poster-facebook-twitter-g' ); ?>&nbsp;<a target="_blank" href="http://www.nextscripts.com/social-networks-auto-poster-pro-for-wpmu"><?php _e( 'Get SNAP Pro [Multiuser Version]', 'social-networks-auto-poster-facebook-twitter-g' ); ?></a> <br/><br/>     
            <form method="post" action="" novalidate="novalidate"> <?php wp_nonce_field( 'nxsSsPageWPN', 'nxsSsPageWPN_wpnonce' ); ?>
              <span style="font-size: 15px; font-weight: bold;"><?php _e( 'Please select a site where you would like to use plugin: ', 'social-networks-auto-poster-facebook-twitter-g' ); ?></span> <select name="nxs_ntsiteid"> <?php
            foreach ( $sites as $i => $site ) echo '<option '.($site['blog_id']==$cs?'selected':'').' value="'.$site['blog_id'].'">[ID: '.$site['blog_id'].'] '.$site['name'].' - '.$site['domain'].($site['path']!='/'?$site['path']:'').'</option>'; ?></select><br/> <div style="padding-top:10px;">
            <?php if (!empty($cs) && !empty($cSite) && is_array($cSite)) { ?> <div style="padding-bottom:10px;"> 
              <?php _e( 'You already have SNAP Configured on: ', 'social-networks-auto-poster-facebook-twitter-g');?><b><?php echo 'ID:'.$cSite['blog_id'].' - '.$cSite['name'].' ('.$cSite['domain'].($cSite['path']!='/'?$cSite['path']:'').')'; ?></b><br/><br/>
              !!!! <?php _e( 'Selecting a different site will <b>delete</b> all current settings', 'social-networks-auto-poster-facebook-twitter-g' ); ?> !!!!<br/><br/>
              <input type="checkbox" onchange="jQuery('#nxsNTOSubmit').toggle();"/> <?php _e( 'I undestand that all current settings will be deleted', 'social-networks-auto-poster-facebook-twitter-g' ); ?><br/>
            </div><?php } ?>
            <input type="submit" <?php if (!empty($cs) && !empty($cSite) && is_array($cSite)) { ?>style="display: none" <?php } ?> name="submit" id="nxsNTOSubmit" class="button button-primary" value="<?php _e( 'Save Changes', 'social-networks-auto-poster-facebook-twitter-g' ); ?>"></div></form> <?php 
          }
        }
        
        public function nxs_adminMenu() { wp_enqueue_script( 'postbox' ); 
          //$this->page = add_menu_page( 'Social Networks Auto Poster', '-={<span style="font-weight:bold; color:#2ecc2e;">SNAP</span>}=-','manage_options','nxssnap',array( $this, 'showPage_dashboard' ), NXS_RELPATH.'/img/snap-icon.png');
         // $this->page = add_menu_page( 'Social Networks Auto Poster', '-={SNAP}=-','manage_options','nxssnap',array( $this, 'showPage_dashboard' ), NXS_RELPATH.'/img/snap-icon.png');
          $this->page = add_menu_page( 'Social Networks Auto Poster', 'SNAP|AutoPoster','manage_options','nxssnap',array( $this, 'showPage_accounts' ), NXS_PLURL.'img/snap-icon.png');
          
          add_submenu_page( 'nxssnap',__( 'Accounts', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Accounts', 'social-networks-auto-poster-facebook-twitter-g' ), 'manage_options', 'nxssnap', array( $this, 'showPage_accounts' ) ,0 );                         
          add_submenu_page( 'nxssnap',__( 'Quick Post', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Quick Post', 'social-networks-auto-poster-facebook-twitter-g' ), 'manage_options', 'nxssnap-post', array( $this, 'showPage_dashboard' )  );
          add_submenu_page( 'nxssnap',__( 'Query/Timeline', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Query/Timeline', 'social-networks-auto-poster-facebook-twitter-g' ), 'manage_options', 'nxssnap-query', array( $this, 'showPage_query' ) ,0 );                         
          add_submenu_page( 'nxssnap',__( 'Reposter', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Reposter', 'social-networks-auto-poster-facebook-twitter-g' ), 'manage_options', 'nxssnap-reposter', array( $this, 'showPage_reposter' ) ,0 );                                   
          add_submenu_page( 'nxssnap',__( 'Settings', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Settings', 'social-networks-auto-poster-facebook-twitter-g' ), 'manage_options', 'nxssnap-settings', array( $this, 'showPage_settings' ) ,0 );                         
          add_submenu_page( 'nxssnap',__( 'Log/History', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Log/History', 'social-networks-auto-poster-facebook-twitter-g' ), 'manage_options', 'nxs-log', array( $this, 'showPage_log' ) ,0 );                        
          add_submenu_page( 'nxssnap',__( 'Help/Support', 'social-networks-auto-poster-facebook-twitter-g' ), __( 'Help/Support', 'social-networks-auto-poster-facebook-twitter-g' ), 'manage_options', 'nxs-help', array( $this, 'showPage_about' ) ,0 );    
        }                    
                      
        public function showPage($title, $object=null) {  ?>
            <div class="wrap" id="<?php echo $this->page; ?>"><div id="nxsDivWrap"> <?php // $this->show_follow_icons(); ?>
                <h2><?php echo $title ?></h2>
                <div id="poststuff">                    
                  <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content" style="position: relative;" class=""><?php do_meta_boxes( $this->page, 'normal', $object ); ?></div>
                    <div id="postbox-container-1" class="postbox-container"><?php  do_meta_boxes( $this->page, 'side', null ); ?></div>
                    <br class="clear"/>
                  </div>
                </div>
            </div></div>
            <script type="text/javascript">
              jQuery(document).ready(function($) { $('.if-js-closed').removeClass('if-js-closed').addClass('closed'); if (typeof(postboxes)!='undefined') postboxes.add_postbox_toggles('<?php echo $this->page; ?>');});
            </script> <?php 
        }                    
        //## SIDE Boxes
        public function showPage_side() { 
            add_meta_box( $this->page.'_info', __( 'SNAP Info', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_side_info' ), $this->page, 'side' );
            if (!function_exists('ns_SMASV41')) add_meta_box( $this->page.'_purchase', __( 'Pro Version', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_side_getit' ), $this->page, 'side' );
            
            if ((current_user_can( 'manage_options' ))) add_meta_box( $this->page.'_supportbox', __( 'Support', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_side_supbox' ), $this->page, 'side' );   
            add_meta_box( $this->page.'_instrbox', __( 'Instructions', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_side_instrbox' ), $this->page, 'side' );   
        }        
        public function showPage_dashboard() { 
            global $nxs_SNAP; if (empty($nxs_SNAP)) $nxs_SNAP = new nxs_SNAP(); $t = (!current_user_can( 'manage_options' ) && current_user_can( 'haveown_snap_accss' ) ) ? $nxs_SNAP->nxs_acctsU : nxs_accts; 
            $nc = 0; if (!empty($t)) foreach ($t as $kt=>$tt) if ( (strlen($kt)==2 || strlen($kt)==3) && is_array($tt)) $nc = $nc + count($tt);
            if ($nc>0) add_meta_box( $this->page.'_newPost', __( 'New Post to Social Networks', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_newPost' ), $this->page, 'normal' );
            $this->showPage_side();      $this->showPage($this->pluginName.': '. __( 'Dashboard', 'social-networks-auto-poster-facebook-twitter-g' ));         
        }        
        public function showPage_accounts() { 
            add_meta_box( $this->page.'_accounts', __( 'Social Networks Autoposter (SNAP) Accounts', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_accounts' ), $this->page, 'normal' );                        
            $this->showPage_side();  $this->showPage($this->pluginName.': '. __( 'SNAP Accounts', 'social-networks-auto-poster-facebook-twitter-g' ));             
        }        
        public function showPage_query() { 
            add_meta_box( $this->page.'_query', __( 'Social Networks Autoposter (SNAP) Query', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_query' ), $this->page, 'normal' );                        
            $this->showPage_side();  $this->showPage($this->pluginName.': '. __( 'SNAP Query', 'social-networks-auto-poster-facebook-twitter-g' ));             
        }        
        public function showPage_reposter() { $post = null; 
            
            if (!empty($_GET['action'])&& $_GET['action']=='edit' && !empty($_GET['item']) ) { $post = get_post($_GET['item']);
              if (!empty($_POST['nxs_snap_reposter_update'])) nxs_Filters::save_filter($_GET['item']); ?> 
              <form method="post" id="nxs_form_rep"> <input name="pid" value="<?php echo $_GET['item']; ?>" type="hidden" /> <input name="action" value="nxs_snap_aj" type="hidden" />
                <input name="nxsact" value="saveRpst" type="hidden" /> <?php nxs_Filters::showEdit($this->page);
            }
            elseif (!empty($_GET['action'])&& $_GET['action']=='delete' && !empty($_GET['item']) ) {
               wp_delete_post($_GET['item']); ?> <script type="text/javascript">window.location = "<?php echo nxs_get_admin_url('admin.php?page=nxssnap-reposter'); ?>"</script>  <?php 
            } elseif (!empty($_POST['action'])&& $_POST['action']=='delete' && !empty($_POST['nxs_filter']) ) {
               foreach ($_POST['nxs_filter'] as $rr) wp_delete_post($rr);                
               ?> <script type="text/javascript">window.location = "<?php echo nxs_get_admin_url('admin.php?page=nxssnap-reposter'); ?>"</script>  <?php 
            } else {            
              add_meta_box( $this->page.'_reposter', __( 'Social Networks Autoposter (SNAP) Auto-Reposter', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_reposter' ), $this->page, 'normal' );                        
            }
            
            $this->showPage_side(); $this->showPage($this->pluginName.': '. __( 'Auto-Reposter Configuration', 'social-networks-auto-poster-facebook-twitter-g') , $post);         
            
            if (!empty($post)) echo "</form>"; 
        }        
        public function showPage_settings() { 
            add_meta_box( $this->page.'_settings', __( 'Social Networks Autoposter (SNAP) Settings', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_settings' ), $this->page, 'normal' );                        
            $this->showPage_side();  $this->showPage($this->pluginName.': '. __( 'SNAP Settings', 'social-networks-auto-poster-facebook-twitter-g' ));             
        }        
        public function showPage_log() { 
            add_meta_box( $this->page.'_log', __( 'Social Networks Autoposter (SNAP) Log/History', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_log' ), $this->page, 'normal' );                        
            $this->showPage_side();  $this->showPage($this->pluginName.': '. __( 'SNAP Log/History', 'social-networks-auto-poster-facebook-twitter-g' ));             
        }        
        public function showPage_about() { 
            add_meta_box( $this->page.'_about', __( 'Social Networks Autoposter (SNAP) Help/Support', 'social-networks-auto-poster-facebook-twitter-g' ), array( $this, 'metabox_about' ), $this->page, 'normal' );                        
            $this->showPage_side();  $this->showPage($this->pluginName.': '. __( 'SNAP Help/Support', 'social-networks-auto-poster-facebook-twitter-g' ));             
        }
        
        public function metabox_accounts($post) { global $nxs_SNAP;  if (!isset($nxs_SNAP)) return; $nxs_SNAP->showAccountsTab(); }
        public function metabox_query($post) {  global $nxs_SNAP;  if (!isset($nxs_SNAP)) return; $nxs_SNAP->showQueryTab(); }
        public function metabox_settings($post) { global $nxs_SNAP; if (!isset($nxs_SNAP)) return;  
          // if (isset($_POST['nxsMainFromElementAccts']) || isset($_POST['nxsMainFromSupportFld'])) $options = $nxs_SNAP->saveSNAPSettings($nxs_SNAP->nxs_options); //## Save Settings
          $nxs_SNAP->showSettingsTab();
        }        
        public function metabox_log($post) { global $nxs_SNAP;  if (!isset($nxs_SNAP)) return; $nxs_SNAP->showLogHistoryTab();}        
        public function metabox_about($post) { global $nxs_SNAP;  if (!isset($nxs_SNAP)) return; $nxs_SNAP->showAboutTab();}
        
        public function metabox_reposter($post) { 
             $itemsTable = new nxs_ReposterListTable(); $itemsTable->prepare_items(); ?>
           <div class="wrap">
             <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
                <p>This page demonstrates the use of the <tt><a href="http://dddddd" target="_blank" style="text-decoration:none;">WP_List_Table</a></tt> class in plugins.</p> 
             </div>
             <div id="icon-users" class="icon32"><br/></div> <h2><?php _e( 'Reposter Actions', 'social-networks-auto-poster-facebook-twitter-g' ); ?> <a id="nxsFltAddButton" href="#" class="add-new-h2"><?php _e( 'Add new Reposter Action', 'social-networks-auto-poster-facebook-twitter-g' ); ?></a> </h2>
             <form id="movies-filter" method="get"> <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /><?php $itemsTable->display() ?></form>        
            </div> <div id="nxs_spFltPopup"><span class="nxspButton bClose"><span>X</span></span><?php nxs_rpstPopupCode(); ?></div><?php 
            
        }
        
        public function metabox_dashboard($post) { ?> Quick links: <br/>
        
        
          <div align="center"><div class="nxsInfoMsg">Start here <img style="position: relative; top: 8px;" alt="Arrow" src="<?php echo NXS_PLURL; ?>img/arrow_r_green_c1.png"></div>
          <a href="admin.php?page=nxssnap-accounts" class="NXSButton" id="nxs_snapConfAccs"><?php _e('Configure Social Networks', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>&nbsp;&nbsp;&nbsp;<a href="#" class="NXSButtonB" id="nxs_snapConfRepstr"><?php _e('Configure Auto-Reposter', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>&nbsp;&nbsp;&nbsp;<a href="#" class="NXSButtonB" id="nxs_snapConfSets"><?php _e('Plugin Settings', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>&nbsp;&nbsp;&nbsp;<a href="#" class="NXSButtonB" id="nxs_snapConfLog"><?php _e('View Log', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></div>           
           <?php 
        }
        
        public function metabox_newPost($post) { $itemsTable = new nxs_QPListTable(); $itemsTable->prepare_items(); ?>
        
        <div class="nxswrap" style="overflow: auto;">
        <ul class="nsx_tabs">
          <li><a class="ab-item" id="nsx_tab1_ttl" href="#nsx_tab1"><span style="font-weight:bold; font-size: 1.2em; color:#2ecc2e; margin: 3px;">&dArr;</span><?php _e('New Quick Post', 'social-networks-auto-poster-facebook-twitter-g') ?><span style="font-weight:bold; font-size: 1.2em; color:#2ecc2e; margin: 3px;">&dArr;</span></a></li>            
          <li><a href="#nsx_tab2"><?php _e('Quick Posts History', 'social-networks-auto-poster-facebook-twitter-g') ?></a></li>
        </ul>
        <div class="nsx_tab_container">
          <div id="nsx_tab1" class="nsx_tab_content"> 
            <?php global $nxs_SNAP; if (empty($nxs_SNAP)) $nxs_SNAP = new nxs_SNAP(); $networks = (!current_user_can( 'manage_options' ) && current_user_can( 'haveown_snap_accss' ) ) ? $nxs_SNAP->nxs_acctsU : nxs_accts; nxs_showNewPostForm($networks, false); ?>        
          </div>
          <div id="nsx_tab2" class="nsx_tab_content"> 
            <div class="wrap">             
              <form id="movies-filter" method="get"> <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /><?php $itemsTable->display() ?></form>        
            </div>        
          </div>
        </div> </div> <?php         
        
        }
        
        public function metabox_side_getit($post) {?> 
          <style type="text/css">.nxs_txtIcon { margin: 0px; padding-left: 20px; background-repeat: no-repeat;} .nxs_ti_gp {background-image: url('<?php echo NXS_PLURL; ?>img/gp16.png');} 
            .nxs_ti_li {background-image: url('<?php echo NXS_PLURL; ?>img/li16.png');}  .nxs_ti_rd {background-image: url('<?php echo NXS_PLURL; ?>img/rd16.png');} 
            .nxs_ti_fp {background-image: url('<?php echo NXS_PLURL; ?>img/fp16.png');}  .nxs_ti_yt {background-image: url('<?php echo NXS_PLURL; ?>img/yt16.png');} 
            .nxs_ti_bg {background-image: url('<?php echo NXS_PLURL; ?>img/bg16.png');}  .nxs_ti_pn {background-image: url('<?php echo NXS_PLURL; ?>img/pn16.png');} 
          </style>
        
        Pro version upgrade adds the ability to configure more then one account for each social network and some addidional features.<br/><div style="padding-bottom: 10px; padding-top: 7px;" align="center">            
<b style="color: #008000">[Limited Time Only]</b> - ($49/year value) <br/> Get SNAP API(Run-time) for <b>Free</b></div><div style="padding-bottom: 5px;"><a href="http://www.nextscripts.com/snap-api/">SNAP API</a> adds autoposting to:</div> <span class="nxs_txtIcon nxs_ti_gp">Google+</span>, <span class="nxs_txtIcon nxs_ti_pn">Pinterest</span>, <span class="nxs_txtIcon nxs_ti_rd">Reddit</span>, <span class="nxs_txtIcon nxs_ti_bg">Blogger</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_yt">YouTube</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_fp">Flipboard</span>, <br/><span class="nxs_txtIcon nxs_ti_li">LinkedIn Company Pages</span><br/><br/><div align="center"><a href="#" class="NXSButton" id="nxs_snapUPG">Get SNAP Pro Plugin with SNAP API</a></div>

          <?php if(function_exists('nxsDoLic_ajax')) { ?> <br/><div align="center">
            <span style="color: #00FF00; font-weight: bold; font-size: 28px;">--&gt;</span>
            <a style="color: #008000; font-weight: normal; font-size: 13px;" class="showLic" href="#">[<?php  _e('Enter your Activation Key', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>
            <span style="color: #00FF00; font-weight: bold; font-size: 28px;">&lt;--</span>
            </div> 
            
            <div id="showLicForm"><span class="nxspButton bClose"><span>X</span></span><div style="position: absolute; right: 10px; top:10px; font-size: 34px; font-weight: lighter;"><?php  _e('Activation', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
              <br/><br/>
              <h3><?php  _e('Multiple Accounts Edition and Google+ and Pinterest Auto-Posting', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><br/><?php  _e('You can find your key on this page', 'social-networks-auto-poster-facebook-twitter-g'); ?>: <a href="http://www.nextscripts.com/mypage">http://www.nextscripts.com/mypage</a>
                <br/><br/> <?php _e('Enter your Key', 'social-networks-auto-poster-facebook-twitter-g'); ?>:  <input name="eLic" id="eLic"  style="width: 50%;"/>
                <input type="button" class="button-primary" name="eLicDo" onclick="doLic();" value="Enter" />
                <br/><br/><?php _e('Your plugin will be automatically upgraded', 'social-networks-auto-poster-facebook-twitter-g'); ?>. <?php wp_nonce_field( 'doLic', 'doLic_wpnonce' ); ?>
              </div>
            
            <?php } 
        }
        
        public function metabox_side_supbox($post) { ?>  <?php _e('Most common issues/questions are already answered in the', 'social-networks-auto-poster-facebook-twitter-g'); ?>:<br/><a style="font-weight: normal; font-size: 16px; line-height: 24px;" target="_blank" href="http://www.nextscripts.com/support-faq/"><?php _e('Troubleshooting FAQ', 'social-networks-auto-poster-facebook-twitter-g'); ?></a><br/><br/>
          <?php _e('Can\'t find the answer ins the FAQ? Have troubles/problems/found a bug?', 'social-networks-auto-poster-facebook-twitter-g'); ?>:<br/><a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="http://www.nextscripts.com/support">Open support ticket</a><br/><br/>
          <?php _e('Have questions/suggestions?', 'social-networks-auto-poster-facebook-twitter-g'); ?>:<br/><a style="font-weight:normal;font-size:18px;line-height:24px;" target="_blank" href="http://www.nextscripts.com/contact-us">Contact us</a><br/>
          <?php
        }
        
        public function metabox_side_instrbox($post) { global $nxs_snapAvNts; foreach ($nxs_snapAvNts as $avNt) { $clName = 'nxs_snapClass'.$avNt['code']; $nt = new $clName(); ?>
              <div style="padding-left: 10px; padding-top:5px;"><a style="background-image:url(<?php echo NXS_PLURL; ?>img/<?php echo $avNt['lcode']; ?>16.png) !important;" class="nxs_icon16" target="_blank" href="<?php echo $nt->ntInfo['instrURL']; ?>">  <?php echo $nt->ntInfo['name']; ?> </a></div>
            <?php }
        }
        
        public function metabox_side_info($post) { global $nxs_SNAP;  if (!isset($nxs_SNAP)) return; $options = $nxs_SNAP->nxs_options; 
            $nxsOne = NextScripts_SNAP_Version;  $sMode = $nxs_SNAP->sMode; ?>            
            <div align="center"><a target="_blank" href="http://www.nextscripts.com"><img src="<?php echo NXS_PLURL; ?>img/SNAP_Logo_2014.png"></a></div> <br/>                         
              <?php _e('Plugin Version', 'social-networks-auto-poster-facebook-twitter-g'); ?>: <span style="color:#008000;font-weight: bold;"><?php echo $nxsOne; ?></span> 
              <?php if($sMode['l']=='P') { ?> [Pro]<br/>&nbsp;&nbsp;&nbsp;(One User, Multiple Accounts)&nbsp;&nbsp;<?php } elseif ($sMode['l']=='M'){ ?> [Pro]<br/>&nbsp;&nbsp;&nbsp;(Multiple Users, Multiple Accounts)<?php } else {?> <span style="color:#800000; font-weight: bold;">[Free]<br/>&nbsp;&nbsp;&nbsp;(One User, One account per Network)</span><?php } ?><br/> 
           <?php if (defined('NXSAPIVER')) echo '<span id="nxsAPIUpd">API</span> Version: <span style="color:#008000;font-weight: bold;">'.NXSAPIVER.'</span>'; ?><?php wp_nonce_field( 'doLic', 'doLic_wpnonce' ); ?>
           
            
         <?php  
        }
    }
}



if (!class_exists("nxs_SNAP")) { class nxs_SNAP { //## SNAP General Class         
  var $dbOptionsName = "nxsSNAPOptions"; var $dbNtsName = "nxsSNAPNetworks"; var $dbNtsNameU = "nxsSNAPNetworksU"; var $nxs_snapAvNts; var $nxs_options = ""; var $nxs_ntoptions = ""; var $nxs_accts = ""; var $nxs_acctsU = "";
  var $sMode = array('s'=>'S', 'l'=>'F', 'u'=>'O', 'a'=>'S', 's'=>'S'); var $old_dbOptionsName = "NS_SNAutoPoster";
        
  function __construct($u='') { load_plugin_textdomain('social-networks-auto-poster-facebook-twitter-g', FALSE, substr(dirname( plugin_basename( __FILE__ ) ), 0, -4).'/lang/');  if (empty($u)) { $u = wp_get_current_user(); $u = $u->ID; } $this->dbNtsNameU .= $u; $this->getAPOptions();  
    $this->sMode['s'] = (defined('MULTISITE') && MULTISITE==true)?'M':'S'; $snapMgmt = new nxs_adminMgmt(); $this->sMode['l'] = function_exists("ns_SMASV41")?(function_exists("ns_SMASV4M1")?'M':'P'):'F';
    if ($this->sMode['s']=='M'){ global $blog_id; $cs=get_site_option('nxs_nts'); add_action('network_admin_menu', array($snapMgmt,'ntAdminMenu')); if ($this->sMode['l']=='M' || $cs==$blog_id) $snapMgmt->init();} else $snapMgmt->init();    
    if (function_exists('showSNAP_WPMU_OptionsPageExt') && !current_user_can( 'manage_options' ) && current_user_can( 'haveown_snap_accss' ) ) $snapMgmt->initUser();    
    //QP Post Type
  }
  function toLatestVer($options){  global $nxs_snapAvNts; if (!empty($options['v'])) $v = $options['v']; else $v = 340; $optionsOut = array();  
     switch ($v) {
     case 340: 
       //## Networks
       $nts = array(); foreach ($nxs_snapAvNts as $avNt) { //echo $avNt['lcode'];
         if (!empty($options[$avNt['lcode']])) foreach ($options[$avNt['lcode']] as $aNt) { $clName = 'nxs_snapClass'.$avNt['code']; $ntt = new $clName; if (method_exists($ntt,'toLatestVer')) $nts[$avNt['lcode']][] = $ntt->toLatestVer($aNt); else  $nts[$avNt['lcode']][] = $aNt;  } 
         unset($options[$avNt['lcode']]); 
       }  
       //## Options
       $options['fltrsOn'] = 1; $options['nxs_post_type'][] = 'post'; if (!empty($options['useForPages'])) $options['fltrs']['nxs_post_type'][] = 'page'; unset($options['useForPages']); 
       if (!empty($options['nxsCPTSeld'])) { $nxsCPTSeld = maybe_unserialize($options['nxsCPTSeld']); foreach ($nxsCPTSeld as $cpt) $options['fltrs']['nxs_post_type'][] = $cpt; unset($options['nxsCPTSeld']);}           
       if (!empty($options['exclCats'])) { $excCs = maybe_unserialize($options['exclCats']);
         foreach ($excCs as $excC) $options['fltrs']['nxs_cats_names'][] = $excC; $options['fltrs']['nxs_ie_cats_names'] = 1; unset($options['exclCats']);              
       }      
     break;          
    } $options['v'] = NXS_SETV; $this->saveNetworksOptions($nts,$options); // delete_option($this->old_dbOptionsName); 
    return $options; 
  }               
  function getAPOptions() { global $nxs_skipSSLCheck, $blog_id;  $this->nxs_accts = get_option($this->dbNtsName); $this->nxs_acctsU = get_option($this->dbNtsNameU); 
    
    $this->nxs_ntoptions = get_site_option($this->dbOptionsName); $nxs_UPPath = 'nxs-snap-pro-upgrade'; $dir = plugin_dir_path( __FILE__ ); $dir = explode('social-networks-auto-poster-facebook-twitter-g', $dir); 
    $dir = $dir[0]; $pf = $dir.$nxs_UPPath.'/'.$nxs_UPPath.'.php'; if (file_exists($pf) && !class_exists('nxs_wpAPIEngine') ) require_once $pf;      
    // if (class_exists('nxs_wpAPIEngine')) { $cl = new nxs_wpAPIEngine(); $cl->check(); }         
    //if (function_exists('nxs_getInitAdd')) nxs_getInitAdd($options); if (!empty($options['uk'])) $options['uk']='API'; 
    //if (defined('NXSAPIVER') && (empty($options['ukver']) || $options['ukver']!=NXSAPIVER)){$options['ukver']=NXSAPIVER; $this->saveNetworksOptions('',$options);}    
    if (!empty($options['ukver']) && $options['ukver'] == nsx_doDecode('q234t27414r2q2')) $options['ht'] = 104; 
    if (isset($options['skipSSLSec'])) $nxs_skipSSLCheck = $options['skipSSLSec']; $options['useSSLCert'] = '8416o4u5d4p2o22646060474k5b4t2a4u5s4'; $this->nxs_options = $options;         
    if (!empty($options)&&(empty($options['v'])||($options['v']<NXS_SETV))) { if (empty($options['v'])) add_action( 'admin_enqueue_scripts', 'nxs_snap_pointer_admin_enqueue_scripts' ); $options = $this->toLatestVer($options); } //## Check if first run after V3-V4 update.          
    $contCron = get_option('nxs_contCron'); if ((int)$contCron>0) add_action('wp_head','nxs_contCron_js');
  }
  function saveNetworksOptions($networks, $options='') { //## Set or just save (=1) Options and Networks
     if (!empty($networks)) { 
         
          if (!current_user_can( 'manage_options' ) && current_user_can( 'haveown_snap_accss' )) { if ($networks!=1) $this->nxs_acctsU = $networks; update_option($this->dbNtsNameU, $this->nxs_acctsU); } else { if ($networks!=1) $this->nxs_accts = $networks; update_option($this->dbNtsName, $this->nxs_accts); }
     
     } if (!empty($options)) {  if ($options!=1) $this->nxs_options = $options; update_option($this->dbOptionsName, $this->nxs_options); } 
  }  
        
  function showAccountsTab(){ global $nxs_snapAvNts, $nxsOne; $nxsOne = ''; $trrd=0; $nxs_snapThisPageUrl = home_url( add_query_arg( NULL, NULL ) ); $cst=strrev('enifed');  $isMobile = nxs_isMobile();
    if (function_exists('nxs_doSMAS2')) { $rf = new ReflectionFunction('nxs_doSMAS2'); $trrd++; $rff = $rf->getFileName(); if (stripos($rff, "'d code")===false) $cst(chr(100).$trrd,$trrd); }
    //## Import Settings            
    if (isset($_POST['upload_NS_SNAutoPoster_settings'])) { if (get_magic_quotes_gpc() || $_POST['nxs_mqTest']=="\'") {array_walk_recursive($_POST, 'nsx_stripSlashes');}  array_walk_recursive($_POST, 'nsx_fixSlashes');             
      $secCheck =  wp_verify_nonce($_POST['nxsChkUpl_wpnonce'], 'nxsChkUpl');
      if ($secCheck!==false && isset($_FILES['impFileSettings_button']) && is_uploaded_file($_FILES['impFileSettings_button']['tmp_name'])) { $fileData = trim(file_get_contents($_FILES['impFileSettings_button']['tmp_name']));
        while (substr($fileData, 0,1)!=='a') $fileData = substr($fileData, 1);              
        $uplOpt = maybe_unserialize($fileData); if (is_array($uplOpt) && isset($uplOpt['imgNoCheck'])) { $options = $uplOpt; 
          if (!empty($options)&&(empty($options['v'])||($options['v']<NXS_SETV))) { if (empty($options['v'])) add_action( 'admin_enqueue_scripts', 'nxs_snap_pointer_admin_enqueue_scripts' ); $options = $this->toLatestVer($options); }  //## Check if first run after V3-V4 update.
            else $this->saveNetworksOptions($options['n'], $options['o']); 
        } else { ?><div class="error" id="message"><p><strong>Incorrect Import file.</div><?php } 
      } 
    }   
    $networks = $this->nxs_acctsU; $options = $this->nxs_options; $isNoNts = true; foreach ($nxs_snapAvNts as $avNt) if (isset($networks[$avNt['lcode']]) && is_array($networks[$avNt['lcode']]) && count($networks[$avNt['lcode']])>0) {$isNoNts = false; break;} 
    ?> <form method="post" id="nsStForm" action="">          <input type="hidden" name="nxsMainFromSupportFld" id="nxsMainFromSupportFld" value="1" />
       <input name="action" value="nxs_snap_aj" type="hidden" />
       <input name="nxsact" value="setNTS" type="hidden" />
       <input name="nxs_mqTest" value="'" type="hidden" />
       <input type="hidden" id="svSetRef" name="_wp_http_referer" value="" />
       <input type="hidden" id="svSetNounce" name="_wpnonce" value="" />
     
      <a href="#" class="NXSButton" id="nxs_snapAddNew"><?php _e('Add new account', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>
      
      <?php if (!$isMobile) {?><div class="nxsInfoMsg"><img style="position: relative; top: 8px;" alt="Arrow" src="<?php echo NXS_PLURL; ?>img/arrow_l_green_c1.png"/> You can add Facebook, Twitter, Google+, Pinterest, LinkedIn, Tumblr, Blogger, ... accounts</div><?php } ?><br/><br/>
      <div id="nxs_spPopup" class="white-popupx xmfp-hide"><span class="nxspButton bClose"><span>X</span></span><div id="nxs_spPopupU" style="min-height: 300px;"><select onchange="doShowFillBlockX(this.value);" id="nxs_ntType" class="nxs_ntType"><option value =""><?php _e('Please select network...', 'social-networks-auto-poster-facebook-twitter-g'); ?></option>
      
      <?php  if (empty($options['showNTListCats'])) foreach ($nxs_snapAvNts as $avNt) { if (!isset($networks[$avNt['lcode']]) || count($networks[$avNt['lcode']])==0) $mt=0; else $mt = 1+max(array_keys($networks[$avNt['lcode']]));
              echo '<option value ="'.$avNt['code'].$mt.'" data-imagesrc="'.NXS_PLURL.'img/'.(!empty($avNt['imgcode'])?$avNt['imgcode']:$avNt['lcode']).'16.png">'.$avNt['name'].'</option>'; 
           } else { 
              $nxs_snapAvNtsDD = array(); foreach ($nxs_snapAvNts as $avNt) if (!empty($avNt['type'])) $nxs_snapAvNtsDD[$avNt['type']][] = $avNt; else $nxs_snapAvNtsDD['Other'][] = $avNt; uksort($nxs_snapAvNtsDD, 'nxs_add_array_sort');//prr($nxs_snapAvNtsDD);           
              foreach ($nxs_snapAvNtsDD as $ttp => $avNtD) { echo '<option data-title="1" data-imagesrc="'.NXS_PLURL.'img/arrow_r_green_c1.png">'.$ttp.'</option>'; 
                foreach ($avNtD as $avNt) { if (!isset($networks[$avNt['lcode']]) || count($networks[$avNt['lcode']])==0) $mt=0; else $mt = 1+max(array_keys($networks[$avNt['lcode']]));
                echo '<option value="'.$avNt['code'].$mt.'" data-imagesrc="'.NXS_PLURL.'img/'.$avNt['lcode'].'16.png" sdata-description="Add '.$avNt['name'].'">'.$avNt['name'].'</option>'; 
              }}
           } ?>
        
        </select>           
        <div id="nsx_addNT">
          <?php foreach ($nxs_snapAvNts as $avNt) { $clName = 'nxs_snapClass'.$avNt['code']; $ntClInst = new $clName(); 
          if (!isset($networks[$avNt['lcode']]) || count($networks[$avNt['lcode']])==0) { $ntClInst->showNewNTSettings(0); } else { 
             $mt = 1+max(array_keys($networks[$avNt['lcode']])); if (class_exists("nxs_wpAPIEngine") && function_exists('nxs_doSMAS1')) nxs_doSMAS1($ntClInst, $mt); else nxs_doSMAS($avNt['name'], $avNt['code'].$mt);             
          }} ?>           
        </div>
      </div> </div>
      <?php $isNTShow = maybe_unserialize(get_option('nxsSNAPntShow')); update_option('nxsSNAPntShow','');  if (!empty($isNTShow) && is_array($isNTShow)) { ?>           
      <div id="nxs_spNTPopup"><span class="nxspButton bClose"><span>X</span></span>
        <div id="nxs_info_box"> <?php $ii = $isNTShow['ii']; $nt = strtoupper($isNTShow['nt']); $ntl = strtolower($nt); 
          $pbo = $networks[$ntl][$ii];  $pbo['ntInfo']['lcode'] = $ntl; $clName = 'nxs_snapClass'.$nt; $ntObj = new $clName(); 
          $ntObj->showNTSettings($ii, $pbo, false);  
        ?> </div>
      </div>
      <script type="text/javascript"> jQuery(document).ready(function() {  jQuery('#nxs_spNTPopup').bPopup({ modalClose: false, appendTo: '#nxsDivWrap', opacity: 0.6, follow: [false, false], position: [65, 50]}); jQuery('#do<?php echo strtoupper($nt).$ii; ?>Div').show(); })</script> <?php
      } ?> 
        
       <div id="nxsAllAccntsDiv"><div class="nxs_modal"></div> 
         <?php  foreach ($nxs_snapAvNts as $avNt) { $clName = 'nxs_snapClass'.$avNt['code']; $ntClInst = new $clName(); if ( isset($networks[$avNt['lcode']]) && count($networks[$avNt['lcode']])>0) { $ntClInst->showGenNTSettings($networks[$avNt['lcode']]); }} ?>
       </div><?php
         if ($isNoNts) { ?><br/><br/><br/>You don't have any configured social networks yet. Please click "Add new account" button.<br/><br/>
           <input onclick="jQuery('#impFileSettings_button').click(); return false;" type="button" class="button" name="impSettings_repostButton" id="impSettings_button"  value="<?php _e('Import Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" />     
       <?php } else { ?>
         <input value="'" type="hidden" name="nxs_mqTest" /> 
         <div class="submitX nxclear" style="padding-bottom: 0px;">       
           <input type="button" id="svBtnSettings" onclick="nxs_saveAllNetworks();" class="button-primary" value="<?php _e('Update Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" />      
           <div id="nxsSaveLoadingImg" class="doneMsg">Saving.....</div> <div id="doneMsg" class="doneMsg">Done</div>
         </div>   
         
         <div style="margin-top: 10px;">
           <span><img style="padding: 3px;top: 5px;position: relative;" src="<?php echo NXS_PLURL; ?>img/cbch.png"/><?php _e('Checked checkbox - posts will be autposted to that network by default', 'social-networks-auto-poster-facebook-twitter-g') ?><br/></span>
           <span><img style="padding: 3px;top: 5px;position: relative;" src="<?php echo NXS_PLURL; ?>img/cbun.png"/><?php _e('Unchecked checkbox - posts will NOT be autposted to that network by default', 'social-networks-auto-poster-facebook-twitter-g') ?><br/></span>
           <span><img style="padding: 3px;top: 5px;position: relative;" src="<?php echo NXS_PLURL; ?>img/cbrb.png"/><?php _e('Radiobutton - Filters are on. Posts will be autposted or not autoposted depending on filters', 'social-networks-auto-poster-facebook-twitter-g') ?><br/></span>           
         </div>
         
                 
      <?php } //## If No Options - Save defaults
        $options = $this->nxs_options; if (empty($options['nxsURLShrtnr'])){ ?> <div style="display: none;"><?php $this->showSettingsTab(); ?></div><?php }
      ?>   
    </form>
    
    <div id="nxs_gPopup"><span class="nxspButton bClose"><span>X</span></span><div id="nxs_gPopupContent"></div></div>
      
      <div class="popShAtt" id="popOnlyCat"><?php _e('Filters are "ON". Only selected categories/tags will be autoposted to this account. Click "Show Settings->Advanced" to change', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
      <div class="popShAtt" style="width: 400px;" id="popShAttFLT" data-text="<?php _e('Filters are active. Click Show Settings->Advanced to change.<br/>', 'social-networks-auto-poster-facebook-twitter-g'); ?>"></div>
      <div class="popShAtt" id="popReActive"><?php _e('Reposter is activated for this account', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
      <div class="popShAtt" id="fbAttachType"><h3><?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3> <img src="<?php echo NXS_PLURL; ?>img/fb2wops.png" width="600" height="257" alt="<?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
      <div class="popShAtt" id="fbPostTypeDiff"><h3><?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/fbPostTypesDiff6.png" width="600" height="398" alt="<?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
      
      <div id="showCatSel" style="display: none;background-color: #fff; width: 800px; padding: 25px;"><span class="nxspButton bClose"><span>X</span></span>
        <?php _e('Setup Filter', 'social-networks-auto-poster-facebook-twitter-g'); ?>: <form method="post" id="nxs_form_rep">
        <?php //nxs_Filters::print_posts_metabox(0); ?> </form>             
       <div class="submitX"><input type="button" id="" class="button-primary" name="btnSelCats" onclick="jQuery('').val(serTxt = jQuery('#nxs_form_rep').serialize()); jQuery('#showCatSel').bPopup().close();" value="Set Filter" /></div>
      </div>
    
    
    <div style="float: right; padding: 1.5em;">           
           <input onclick="nxs_expSettings(); return false;" type="button" class="button" name="expSettings_repostButton" id="expSettings_button"  value="<?php _e('Export Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" />
           <input onclick="jQuery('#impFileSettings_button').click(); return false;" type="button" class="button" name="impSettings_repostButton" id="impSettings_button"  value="<?php _e('Import Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" />            
         </div>
    <form method="post" enctype="multipart/form-data"  id="nsStFormUpl" action="<?php echo $nxs_snapThisPageUrl?>">
      <input type="file" accept="text/plain" onchange="jQuery('#nsStFormUpl').submit();" id="impFileSettings_button" name="impFileSettings_button" style="display: block; visibility: hidden; width: 1px; height: 0;" size="chars">
      <input type="hidden" value="1" name="upload_NS_SNAutoPoster_settings" /> <input value="'" type="hidden" name="nxs_mqTest" />  <?php wp_nonce_field( 'nxsChkUpl', 'nxsChkUpl_wpnonce' ); ?> 
    </form><?php //prr($networks);
  }
        
  function showSettingsTab() { global $nxs_snapAvNts, $snap_curPageURL, $nxs_snapThisPageUrl, $nxsOne, $nxs_isWPMU, $nxs_tpWMPU; $nxsOne = ''; $options = $this->nxs_options; ?>
              <script type="text/javascript">setTimeout( function(){ document.getElementById( "nsStFormMisc" ).reset();},5);</script>
              
    <form method="post" id="nsStFormMisc" action="<?php echo $snap_curPageURL;?>"> <div id="nxsSettingsDiv">   <input type="hidden" name="nxsMainFromElementAccts" id="nxsMainFromElementAccts" value="" />
       <input type="hidden" name="nxsMainFromSupportFld" id="nxsMainFromSupportFld" value="1" />
       <input name="action" value="nxs_snap_aj" type="hidden" />
       <input name="nxsact" value="setNTset" type="hidden" />
       <input name="nxs_mqTest" value="'" type="hidden" />
       <input type="hidden" id="svSetRef" name="_wp_http_referer" value="" />
       <input type="hidden" id="svSetNounce" name="_wpnonce" value="" />
       
       
     <!-- ##################### OTHER #####################-->            

     <div class="submitX nxclear" style="padding-bottom: 10px;">       
        <input type="button" id="svBtnSettingsTop" onclick="nxs_savePluginSettings();" class="button-primary" value="<?php _e('Update Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" />      
        <div id="nxsSaveLoadingImgTop" class="doneMsg">Saving.....</div> <div id="doneMsg" class="doneMsg">Done</div>
      </div>
     
     <!-- How to make auto-posts? --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('How to make auto-posts?', 'social-networks-auto-poster-facebook-twitter-g') ?> &lt;-- (<a id="showShAttIS" onmouseover="showPopShAtt('IS', event);" onmouseout="hidePopShAtt('IS');"  onclick="return false;" class="underdash" href="#"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g') ?></a>)</h3></div>
         <div class="popShAtt" id="popShAttIS">
        <h3><?php _e('The difference between "Immediately" and "Scheduled"', 'social-networks-auto-poster-facebook-twitter-g') ?></h3>
        <?php _e('<b>"Immediately"</b> - Once you click "Publish" button plugin starts pushing your update to configured social networks. At this time you need to wait and look at the turning circle. Some APIs are pretty slow, so you have to wait and wait and wait until all updates are posted and page released back to you.', 'social-networks-auto-poster-facebook-twitter-g') ?><br/><br/>
        <?php _e('<b>"Scheduled"</b> - Releases the page immediately back to you, so you can proceed with something else and it schedules all auto-posting jobs to your WP-Cron. This is much faster and much more efficient, but it could not work if your WP-Cron is disabled or broken.', 'social-networks-auto-poster-facebook-twitter-g') ?>
      </div>
             <div class="nxs_box_inside"> 
             
              <div class="itemDiv">
               <input type="radio" name="nxsHTDP" value="I" <?php if (isset($options['nxsHTDP']) && $options['nxsHTDP']=='I') echo 'checked="checked"'; ?> /> <b><?php _e('Publish Immediately', 'social-networks-auto-poster-facebook-twitter-g') ?></b>  - <i><?php _e('No WP Cron will be used. Choose if WP Cron is disabled or broken on your website', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
              </div>  
              
              <div class="itemDiv">
              <input type="radio" name="nxsHTDP" value="S" <?php if (!isset($options['nxsHTDP']) || $options['nxsHTDP']=='S') echo 'checked="checked"'; ?> /> <b><?php _e('Use WP Cron to Schedule autoposts', 'social-networks-auto-poster-facebook-twitter-g') ?></b> - <i><?php _e('Recommended for most sites. Faster Performance - requires working WP Cron', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/> <?php /* ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="runNXSCron" value="1"> <b><?php _e('Try to process missed "Scheduled" posts.', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <i><?php _e('Usefull when WP Cron is disabled or broken, but can cause some short perfomance issues and duplicates. It is <b>highly</b> recomended to setup a proper cron job of fix WP Cron instead', 'social-networks-auto-poster-facebook-twitter-g') ?></i>. <?php */ ?>
              </div>         
              
              <div class="itemDiv">
              <div style="margin-left: 20px;">
              
              <?php $cr = get_option('NXS_cronCheck'); if (!empty($cr) && is_array($cr) && isset($cr['status']) && $cr['status']=='0') { ?> <span style="color: red"> *** <?php _e('Your WP Cron is not working correctly. This feature may not work properly, and might cause duplicate postings and stability problems.<br/> Please see the test results and recommendations here:', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl; echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span> <br/>
            <?php  } ?>
              
              <input type="checkbox" name="quLimit" value="1" <?php if (isset($options['quLimit']) && $options['quLimit']=='1') echo 'checked="checked"'; ?> /> <b><?php _e('Limit autoposting speed', 'social-networks-auto-poster-facebook-twitter-g') ?></b> - <i><?php _e('Recommended for busy sites with a lot of new posts.', 'social-networks-auto-poster-facebook-twitter-g') ?> </i><br/> 
              <div style="margin-left: 10px;">
              Do not autopost more then one post per network every <input name="quDays" style="width: 36px;" maxlength="3" value="<?php echo isset($options['quDays'])?$options['quDays']:'0'; ?>" /> Days,&nbsp;&nbsp;
              <input name="quHrs" style="width: 33px;" maxlength="3" value="<?php echo isset($options['quHrs'])?$options['quHrs']:'0'; ?>" /> Hours,&nbsp;&nbsp;
              <input name="quMins" style="width: 33px;" maxlength="3" value="<?php echo isset($options['quMins'])?$options['quMins']:'3'; ?>" /> Minutes.
                <div style="margin-left: 10px;">
                 <b><?php _e('Randomize posting time &#177;', 'social-networks-auto-poster-facebook-twitter-g'); ?> </b>
     <input type="text" name="quLimitRndMins" style="width: 35px;" value="<?php echo isset($options['quLimitRndMins'])?$options['quLimitRndMins']:'2'; ?>" />&nbsp;<?php _e('Minutes', 'social-networks-auto-poster-facebook-twitter-g'); ?>
                </div>
                 
                 <div style="margin-left: 10px;">
                 <?php _e('What to do with the rest of the posts if there are more posts then daily limit?', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
                    <input type="radio" name="nxsOverLimit" value="D" <?php if (!isset($options['nxsOverLimit']) || $options['nxsOverLimit']=='D') echo 'checked="checked"'; ?> /> <b><?php _e('Skip/Discard/Don\'t Autopost ', 'social-networks-auto-poster-facebook-twitter-g') ?></b><br/>
                    <input type="radio" name="nxsOverLimit" value="S" <?php if (isset($options['nxsOverLimit']) && $options['nxsOverLimit']=='S') echo 'checked="checked"'; ?> /> <b><?php _e('Schedule for tomorrow', 'social-networks-auto-poster-facebook-twitter-g') ?></b>  - <i><?php _e('Not recommended, may cause significant delays', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
                 </div>
              </div>
              </div>
              </div>                          
              
              
           </div></div>
     <!-- #### Who can see auto-posting options on the "New Post" pages? ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('User Privileges/Security', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> 
              <div class="itemDiv">
              
             <input value="set" id="skipSecurity" name="skipSecurity"  type="checkbox" <?php if (!empty($options['skipSecurity']) && (int)$options['skipSecurity'] == 1) echo "checked"; ?> />  <b><?php _e('Skip User Security Verification.', 'social-networks-auto-poster-facebook-twitter-g') ?></b>     
             <span style="font-size: 11px; margin-left: 1px;"><?php _e('NOT Recommended, but useful in some situations. This will allow autoposting for everyone even for the non-existent users.', 'social-networks-auto-poster-facebook-twitter-g') ?></span>  
              
              <h4><?php _e('Who can make autoposts without seeing any auto-posting options?', 'social-networks-auto-poster-facebook-twitter-g') ?></h4>
              
              <?php $editable_roles = get_editable_roles(); if (!isset($options['whoCanMakePosts']) || !is_array($options['whoCanMakePosts'])) $options['whoCanMakePosts'] = array(); ?>
    
<?php    foreach ( $editable_roles as $role => $details ) { $name = translate_user_role($details['name'] ); echo '<input type="checkbox" '; 
        if (in_array($role, $options['whoCanMakePosts']) || $role=='administrator') echo ' checked="checked" '; if ($role=='administrator') echo '  disabled="disabled" ';
        echo 'name="whoCanMakePosts[]" value="'.esc_attr($role).'" /> '.$name; 
        if ($role=='administrator') echo ' - Somebody who has access to all the administration features';
        if ($role=='editor') echo " - Somebody who can publish and manage posts and pages as well as manage other users' posts, etc. ";
        if ($role=='author') echo ' - Somebody who can publish and manage their own posts ';
        if ($role=='contributor') echo ' - Somebody who can write and manage their posts but not publish them';
        if ($role=='subscriber') echo ' - Somebody who can only manage their profile';        
        echo '<br/>';    
    } ?> <br/> <input type="checkbox" <?php if (!empty($options['zeroUser'])) echo ' checked="checked" '; ?> name="zeroUser" value="1" /><?php _e('User "0" - Sometimes used for Imported/Automated posts.', 'social-networks-auto-poster-facebook-twitter-g') ?>  <br/>
    
     <h4><?php _e('Who can see auto-posting options on the "New Post" and "Edit Post" pages and make autoposts?', 'social-networks-auto-poster-facebook-twitter-g') ?></h4>
              
              <?php $editable_roles = get_editable_roles(); if (!isset($options['whoCanSeeSNAPBox']) || !is_array($options['whoCanSeeSNAPBox'])) $options['whoCanSeeSNAPBox'] = array(); 

    foreach ( $editable_roles as $role => $details ) { $name = translate_user_role($details['name'] ); echo '<input type="checkbox" '; 
        if (in_array($role, $options['whoCanSeeSNAPBox']) || $role=='administrator') echo ' checked="checked" '; if ($role=='administrator' || $role=='subscriber') echo '  disabled="disabled" ';
        echo 'name="whoCanSeeSNAPBox[]" value="'.esc_attr($role).'"> '.$name; 
        if ($role=='administrator') echo ' - Somebody who has access to all the administration features';
        if ($role=='editor') echo " - Somebody who can publish and manage posts and pages as well as manage other users' posts, etc. ";
        if ($role=='author') echo ' - Somebody who can publish and manage their own posts ';
        if ($role=='contributor') echo ' - Somebody who can write and manage their posts but not publish them';
        if ($role=='subscriber') echo ' - Somebody who can only manage their profile';        
        echo '<br/>';    
    } ?>
    
    
    
    
              </div>
              
           </div></div>      
           
             <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Interface', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
            <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('How to show the Networks List in the "Add New Network" dropdown', 'social-networks-auto-poster-facebook-twitter-g') ?></span> <br/>
              <div class="itemDiv">
              <input type="radio" name="showNTListCats" value="1" <?php if (!empty($options['showNTListCats'])) echo 'checked="checked"'; ?> /> <b><?php _e('Categorized', 'social-networks-auto-poster-facebook-twitter-g') ?></b> - <?php _e('Please show supported networks with categories', 'social-networks-auto-poster-facebook-twitter-g') ?><br/>
              <input type="radio" name="showNTListCats" value="0" <?php if (empty($options['showNTListCats'])) echo 'checked="checked"'; ?> /> <b><?php _e('Plain', 'social-networks-auto-poster-facebook-twitter-g') ?></b> - <?php _e('Please don\'t confuse me, just show the plain list', 'social-networks-auto-poster-facebook-twitter-g') ?>            
              </div>
            </div></div>  
           
    <!-- #### Filters ##### --> 
                
   <?php //## Conver <3.5 settings to filters    
   if (empty($options['fltrs']) || empty($options['fltrs']['nxs_post_type'])) { $options['fltrs']=array(); $options['fltrs']['nxs_post_type'] = array(); $options['fltrs']['nxs_post_type'][] = 'post'; $options['fltrsOn']='1'; }
   if (isset($options['exclCats'])) { $ccts = maybe_unserialize($options['exclCats']); $options['fltrsOn']='1'; $options['fltrs']['nxs_ie_cats_names'] = 1;  $options['fltrs']['nxs_cats_names'] = array_merge($ccts, $options['fltrs']['nxs_cats_names']);  unset($options['exclCats']);}   
   if (isset($options['nxsCPTSeld'])) { $ccts = maybe_unserialize($options['nxsCPTSeld']);   $options['fltrs']['nxs_post_type'] = array_merge($ccts, $options['fltrs']['nxs_post_type']); unset($options['nxsCPTSeld']); }      
   if (isset($options['useForPages'])) { $options['fltrs']['nxs_post_type'][] = 'post'; $options['fltrsOn']='1'; if($options['useForPages'] =='1') $options['fltrs']['nxs_post_type'][] = 'page'; unset($options['useForPages']); } ?>
                
     <?php if (empty($options['fltrsOn'])) $options['fltrsOn'] = ''; if (empty($options['fltrAfter'])) $options['fltrAfter'] = '';?>                    
           <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Autoposting Filters', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>      
             <div class="nxs_box_inside"> 
             <div class="itemDiv"> 
               <span style="font-size: 11px; margin-left: 1px;"><?php _e('You can setup general criteria for what posts should be autoposted', 'social-networks-auto-poster-facebook-twitter-g') ?></span>  
                <div style="float: right;"> <a href="http://www.nextscripts.com/snap-features/filters" target="_blank">Instructions</a> </div><h3 style="padding-left: 0px;font-size: 16px;"> 
   <input value="1" name="fltrsOn" type="checkbox" onchange="if (jQuery(this).is(':checked')) jQuery('#nxs_flrts').show(); else jQuery('#nxs_flrts').hide();" <?php if ((int)$options['fltrsOn'] == 1) echo "checked"; ?> /> 
   <?php  _e('Filter Posts (Only posts that meet the following criteria will be autoposted)', 'social-networks-auto-poster-facebook-twitter-g'); ?> </h3><div id="nxs_flrts" style="margin-left: 30px;<?php if ((int)$options['fltrsOn'] != 1) echo "display:none;"; ?>"> 

   <?php nxs_Filters::print_posts_metabox(0,'fltrs','0',$options['fltrs']);?><hr/>
   <!--
   <input value="1" name="fltrAfter" type="checkbox" <?php if ((int)$options['fltrAfter'] == 1) echo "checked"; ?> /><?php  _e('[Not Recomended] Apply Filters at the time of autoposting, not at the time of publishing. Please use only if your posts are not ready at the time of publishing.', 'social-networks-auto-poster-facebook-twitter-g'); ?>
   -->
   </div>
             
             </div>
             </div>           
           </div>
    <!-- ##################### URL Shortener #####################-->
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('URL Shortener', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
            <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;">Please use %SURL% in "Message Format" to get shortened urls or check "Force Shortened Links". </span> <br/>
              <!-- <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="G" <?php if (!isset($options['nxsURLShrtnr']) || $options['nxsURLShrtnr']=='' || $options['nxsURLShrtnr']=='G') echo 'checked="checked"'; ?> /> <b>gd.is</b> (Default) - fast, simple, free, no configuration nessesary.            
              </div> -->
              <div class="itemDiv">
              
     <input type="checkbox" name="forceSURL" value="1" <?php if (isset($options['forceSURL']) && $options['forceSURL']=='1') echo 'checked="checked"'; ?> /> <b><?php _e('Force Shortened Links', 'social-networks-auto-poster-facebook-twitter-g') ?></b>
     <br/><br/>         
              <input type="radio" name="nxsURLShrtnr" value="O" <?php if (!isset($options['nxsURLShrtnr']) || (isset($options['nxsURLShrtnr']) && ($options['nxsURLShrtnr']=='O' || $options['nxsURLShrtnr']=='G'))) echo 'checked="checked"'; ?> /> <b>goo.gl</b>  - <i> Enter goo.gl <a target="_blank" href="https://developers.google.com/url-shortener/v1/getting_started#APIKey">API Key</a> below [Optional]</i><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;goo.gl&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="gglAPIKey" style="width: 20%;" value="<?php if (isset($options['gglAPIKey'])) _e(apply_filters('format_to_edit',$options['gglAPIKey']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
              </div>
              
              <?php if (function_exists('wp_get_shortlink')) { ?><div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="W" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='W')  echo 'checked="checked"'; ?> /> <b>Wordpress Built-in Shortener</b> (wp.me if you use Jetpack)<br/> 
              </div><?php } ?>
              <!-- ## bitly ##-->
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="B" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='B') echo 'checked="checked"'; ?> /> <b>bit.ly</b>  - <i>Enter bit.ly username and <a target="_blank" href="http://bitly.com/a/your_api_key">API Key</a> below.</i> (<i style="font-size: 12px;">If https://bitly.com/a/your_api_key is not working, please go for the API key to "Your Account->Advanced Settings->API Support"</i>)<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;bit.ly Username: <input name="bitlyUname" style="width: 20%;" value="<?php if (isset($options['bitlyUname'])) _e(apply_filters('format_to_edit',$options['bitlyUname']), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;bit.ly&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="bitlyAPIKey" style="width: 20%;" value="<?php if (isset($options['bitlyAPIKey'])) _e(apply_filters('format_to_edit',$options['bitlyAPIKey']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
              </div>
              
              <!-- ## u.to ##-->
              <div class="itemDiv">
                <input type="radio" name="nxsURLShrtnr" value="U" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='U') echo 'checked="checked"'; ?> /> <b>u.to</b>  <i>Simple and anonymous (no accounts, no stats) use only, No additional configuration required.</i>
              </div>
              
              <!-- ## x.co ##-->
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="X" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='X') echo 'checked="checked"'; ?> /> <b>x.co</b>  - <i>Enter x.co <a target="_blank" href="http://app.x.co/Settings.aspx">API Key</a> below. You can get API key from your x.co settings page: <a target="_blank" href="http://app.x.co/Settings.aspx">http://app.x.co/Settings.aspx</a>.</i><br/>              
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x.co&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="xcoAPIKey" style="width: 20%;" value="<?php if (isset($options['xcoAPIKey'])) _e(apply_filters('format_to_edit',$options['xcoAPIKey']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
              </div>
              <!-- ## clk.im ##-->
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="C" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='C') echo 'checked="checked"'; ?> /> <b>clk.im</b>  - <i>Enter clk.im <a target="_blank" href="http://clk.im/apikey">API Key</a> below. You can get API key from your clk.im page: <a target="_blank" href="http://clk.im/apikey">http://clk.im/apikey</a>. Please see the "Developers/Publishers" section on the right</i><br/>              
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;clk.im&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="clkimAPIKey" style="width: 20%;" value="<?php if (isset($options['clkimAPIKey'])) _e(apply_filters('format_to_edit',$options['clkimAPIKey']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
              </div>
              <!-- ## po.st ##-->
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="P" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='P') echo 'checked="checked"'; ?> /> <b>po.st</b>  - <i>Enter po.st <a target="_blank" href="https://re.po.st/partner/campaigns">API Key</a> below. You can get API key from your "Campaigns" page: <a target="_blank" href="https://re.po.st/partner/campaigns">https://re.po.st/partner/campaigns</a></i><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;po.st&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="postAPIKey" style="width: 20%;" value="<?php if (isset($options['postAPIKey'])) _e(apply_filters('format_to_edit',$options['postAPIKey']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
              </div>
              
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="A" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='A') echo 'checked="checked"'; ?> /> <b>adf.ly</b>  - <i>Enter adf.ly user ID and <a target="_blank" href="https://adf.ly/publisher/tools#tools-api">API Key</a> below</i><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;adf.ly User ID: <input name="adflyUname" style="width: 20%;" value="<?php if (isset($options['bitlyUname'])) _e(apply_filters('format_to_edit',$options['adflyUname']), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;adf.ly&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="adflyAPIKey" style="width: 20%;" value="<?php if (isset($options['adflyAPIKey'])) _e(apply_filters('format_to_edit',$options['adflyAPIKey']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
             <div style="width:100%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;adf.ly Domain: <select name="adflyDomain" id="adflyDomain">
            <?php  $adflyDomains = '<option value="adf.ly">adf.ly</option><option value="q.gs">q.gs</option>';
              if (isset($options['adflyDomain']) && $options['adflyDomain']!='') $adflyDomains = str_replace($options['adflyDomain'].'"', $options['adflyDomain'].'" selected="selected"', $adflyDomains);  echo $adflyDomains; 
            ?>
            </select> <i>Please note that j.gs is not availabe for API use.</i> </div>
              </div>
              
               <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="R" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='R') echo 'checked="checked"'; ?> /> <b>Rebrandly</b>  - <i>Enter Rebrandly API Key and <a target="_blank" href="https://www.rebrandly.com/api-settings">API Key</a> and domain below. If domain is not set, rebrand.ly will be used</i><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rebrandly&nbsp;&nbsp;API Key:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="rblyAPIKey" style="width: 20%;" value="<?php if (isset($options['rblyAPIKey'])) _e(apply_filters('format_to_edit',$options['rblyAPIKey']), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/>             
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rebrandly&nbsp;&nbsp;Domain:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="rblyDomain" style="width: 20%;" value="<?php if (isset($options['rblyDomain'])) _e(apply_filters('format_to_edit',$options['rblyDomain']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />&nbsp; 
             </div>
              
              <div class="itemDiv">
              <input type="radio" name="nxsURLShrtnr" value="Y" <?php if (isset($options['nxsURLShrtnr']) && $options['nxsURLShrtnr']=='Y')  echo 'checked="checked"'; ?> /> <b>YOURLS (Your Own URL Shortener)</b> - 
            &nbsp;<i>YOURLS API URL - usually sonething like http://yourdomain.cc/yourls-api.php; YOURLS API Secret Signature Token can be found in your YOURLS Admin Panel-&gt;Tools</i><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YOURLS API URL: <input name="YOURLSURL" style="width: 19.4%;" value="<?php if (isset($options['YOURLSURL'])) _e(apply_filters('format_to_edit',$options['YOURLSURL']), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YOURLS API Secret Signature Token:&nbsp;&nbsp;&nbsp;<input name="YOURLSKey" style="width: 13%;" value="<?php if (isset($options['YOURLSKey'])) _e(apply_filters('format_to_edit',$options['YOURLSKey']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
              </div>
              
            </div></div>
            
            <!-- ##################### Auto-Import comments from Social Networks #####################-->
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Auto-Import comments from Social Networks', 'social-networks-auto-poster-facebook-twitter-g') ?><span class="nxs_newLabel">[<?php _e('New', 'social-networks-auto-poster-facebook-twitter-g') ?>]</span></h3></div>
             <div class="nxs_box_inside"> 
             
             <?php $cr = get_option('NXS_cronCheck'); if (!empty($cr) && is_array($cr) && isset($cr['status']) && $cr['status']=='0') { ?> <span style="color: red"> *** <?php _e('Your WP Cron is not working correctly. This feature may not work properly, and might cause duplicate postings and stability problems.<br/> Please see the test results and recommendations here:', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl; echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span> <br/>
            <?php  } ?>             
             
             <span style="font-size: 11px; margin-left: 1px;">Plugin will automatically grab the comments posted on Social Networks and insert them as "Comments to your post". Plugin will check for the new comments every hour. </span> <br/>
              <div class="itemDiv">
              <input value="set" id="riActive" name="riActive"  type="checkbox" <?php if (!empty($options['riActive']) && $options['riActive'] == '1') echo "checked"; ?> /> 
              <strong>Enable "Comments Import"</strong>
              </div>
              <div class="itemDiv">  
             <strong style="font-size: 12px; margin: 10px; margin-left: 1px;">How many posts should be tracked:</strong>
<input name="riHowManyPostsToTrack" style="width: 50px;" value="<?php if (isset($options['riHowManyPostsToTrack'])) _e(apply_filters('format_to_edit', $options['riHowManyPostsToTrack']), 'social-networks-auto-poster-facebook-twitter-g'); else echo "10"; ?>" /> <br/>
              
             <span style="font-size: 11px; margin-left: 1px;">Setting two many will degrade your website's performance. 10-20 posts are recommended</span> 
              </div>
              <div class="itemDiv">  
             <strong style="font-size: 12px; margin: 10px; margin-left: 1px;">How Often should we check for new comments. Every:</strong>

             <select name="riHowOften" id="riHowOften">
              <option <?php if (empty($options['riHowOften']) || $options['riHowOften']=='15') echo "selected" ?> value="15">15 Minutes</option>
              <option <?php if (!empty($options['riHowOften']) && $options['riHowOften']=='30') echo "selected" ?> value ="30">30 Minutes</option>
              <option <?php if (!empty($options['riHowOften']) && $options['riHowOften']=='60') echo "selected" ?> value ="60">1 Hour</option>
              </select>

 <br/>
              
             <span style="font-size: 11px; margin-left: 1px;">Setting two many will degrade your website's performance. 10-20 posts are recommended</span> 
              </div>
              
           </div></div>
           
     <!-- ##################### Additional URL Parameters #####################-->   
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Additional URL Parameters', 'social-networks-auto-poster-facebook-twitter-g') ?> <span class="nxs_newLabel">[<?php _e('New', 'social-networks-auto-poster-facebook-twitter-g') ?>]</span></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('Will be added to backlinks.', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
                <b><?php _e('Additional URL Parameters:', 'social-networks-auto-poster-facebook-twitter-g') ?></b>  <input name="addURLParams" style="width: 800px;" value="<?php if (isset($options['addURLParams'])) _e(apply_filters('format_to_edit', $options['addURLParams']), 'social-networks-auto-poster-facebook-twitter-g'); ?>" />
              </div>               
             <span style="font-size: 11px; margin-left: 1px;"> <?php _e('You can use %NTNAME% for social network name, %NTCODE% for social network two-letter code, %ACCNAME% for account name,  %POSTID% for post ID,  %POSTTITLE% for post title, %SITENAME% for website name. <b>Any text must be URL Encoded</b><br/>Example: utm_source=%NTCODE%&utm_medium=%ACCNAME%&utm_campaign=SNAP%2Bfrom%2B%SITENAME%', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
           </div></div>   
           
           <!-- ##### HashTag Settings ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Auto-HashTags Settings', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('How to generate hashtags if tag is longer then one word', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
              <b><?php _e('Replace spaces in hashtags with ', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <select name="nxsHTSpace" id="nxsHTSpace">
              <option <?php if (empty($options['nxsHTSpace'])) echo "selected" ?> value="">Nothing</option>
              <option <?php if (!empty($options['nxsHTSpace']) && $options['nxsHTSpace']=='_') echo "selected" ?> value ="_">_ (Underscore)</option>
              <option <?php if (!empty($options['nxsHTSpace']) && $options['nxsHTSpace']=='-') echo "selected" ?> value ="-">- (Dash)</option>
              </select>
              </div>   
               <span style="font-size: 11px; margin-left: 1px;"><?php _e('How to separate hashtags', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
              <b><?php _e('Separate hashtags with ', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <select name="nxsHTSepar" id="nxsHTSepar">
              <option <?php if (!empty($options['nxsHTSepar']) && $options['nxsHTSepar']=='_') echo "selected" ?> value ="_">[ ] Space</option>
              <option <?php if (empty($options['nxsHTSepar']) || $options['nxsHTSepar']=='c_') echo "selected" ?> value="c_">[, ] Comma and Space</option>
              <option <?php if (!empty($options['nxsHTSepar']) && $options['nxsHTSepar']=='c') echo "selected" ?> value ="c">[,] Comma</option>              
              </select>
              </div>            
           </div></div> 
           
            <!-- ##### ANOUNCE TAG ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('%ANNOUNCE% tag settings', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('Plugin will take text untill the &lt;!--more--&gt; tag. Please specify how many characters should it get if &lt;!--more--&gt; tag is not found', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
              <b><?php _e('How many characters:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="anounTagLimit" style="width: 100px;" value="<?php if (isset($options['anounTagLimit'])) _e(apply_filters('format_to_edit',$options['anounTagLimit']), 'social-networks-auto-poster-facebook-twitter-g'); else echo "300"; ?>" />              
              </div>              
           </div></div>  
                           
     <!-- ##################### Open Graph #####################-->
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('"Open Graph" Tags', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('This is simple and useful implementation of "Open Graph" Tags, as this option will only add tags needed for "Auto Posting". If you use other specialized plugins, uncheck this option.', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
              <input value="1" id="nsOpenGraph" name="nsOpenGraph"  type="checkbox" <?php if (!empty($options['nsOpenGraph']) && (int)$options['nsOpenGraph'] == 1) echo "checked"; ?> /> <b><?php _e('Add Open Graph Tags', 'social-networks-auto-poster-facebook-twitter-g') ?></b>
              </div>                           
              <div class="itemDiv">
             <b><?php _e('Default Image URL for og:image tag:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> 
            <input name="ogImgDef" style="width: 30%;" value="<?php if (isset($options['ogImgDef'])) _e(apply_filters('format_to_edit',$options['ogImgDef']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
              </div>             
           </div></div>    
            <!-- #### "Featured" Image ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Advanced "Featured" Image Settings', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> 
              <div class="itemDiv">
              <input value="set" id="imgNoCheck" name="imgNoCheck"  type="checkbox" <?php /* ## Reversed Intentionally!!! */ if (empty($options['imgNoCheck']) || (int)$options['imgNoCheck'] != 1) echo "checked"; ?> /> <strong>Verify "Featured" Image</strong>             
              <br/><span style="font-size: 11px; margin-left: 1px;"><?php _e('Advanced Setting. Uncheck only if you are 100% sure that your images are valid or if you have troubles with image verification.', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              </div>
              
               <div class="itemDiv">
             <input value="1" id="useUnProc" name="useUnProc"  type="checkbox" <?php if (isset($options['useUnProc']) && (int)$options['useUnProc'] == 1) echo "checked"; ?> /> 
             <b><?php _e('Use advanced image finder', 'social-networks-auto-poster-facebook-twitter-g') ?></b>
              <br/>              
             <span style="font-size: 11px; margin-left: 1px;"> <?php _e('Check this if your images could be found only in the fully processed posts. <br/>This feature could interfere with some plugins using post processing functions incorrectly. Your site could become messed up, have troubles displaying content or start giving you "ob_start() [ref.outcontrol]: Cannot use output buffering in output buffering display handlers" errors.', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
              </div> 
              
              <div class="itemDiv"> 
             <b><?php _e('If there is a choice what image size should be used:', 'social-networks-auto-poster-facebook-twitter-g') ?></b><br/>              
             <b>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Image posts:', 'social-networks-auto-poster-facebook-twitter-g') ?></b><select name="imgSizeImg">
             <?php  $imgSizes = nxs_getImageSizes(); if (empty($options['imgSizeImg'])) $options['imgSizeImg'] = 'full'; ?>
             <option <?php if ($options['imgSizeImg']=='full') echo "selected" ?> value ="full"><?php _e('Original Size'); ?></option>
             <?php
             foreach ($imgSizes as $sn=>$sa) { 
                 ?><option <?php if ($options['imgSizeImg']==$sn) echo "selected" ?> value="<?php echo $sn; ?>"><?php echo ucfirst($sn); ?>&nbsp;(<?php echo $sa['width']." x ".$sa['height']; ?>)</option>
             <?php } ?>
              </select> <br/>
             <b>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Attachment images:', 'social-networks-auto-poster-facebook-twitter-g') ?></b><select name="imgSizeAttch">
             <?php if (empty($options['imgSizeAttch'])) $options['imgSizeAttch'] = 'medium'; ?>
             <option <?php if ($options['imgSizeAttch']=='full') echo "selected" ?> value="full"><?php _e('Original Size'); ?></option>
             <?php
             foreach ($imgSizes as $sn=>$sa) { 
                 ?><option <?php if ($options['imgSizeAttch']==$sn) echo "selected" ?> value="<?php echo $sn; ?>"><?php echo ucfirst($sn); ?>&nbsp;(<?php echo $sa['width']." x ".$sa['height']; ?>)</option>
             <?php } ?>
              </select>           
              
              </div>  
              
           </div></div>        
    
      <!-- ##### Alternative "Featured Image" location ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Alternative "Featured Image" location', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('Plugin uses standard Wordpress "Featured Image" by default. If your theme stores "Featured Image" in the custom field, please enter the name of it. Use prefix if your custom field has only partial location.', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              <div class="itemDiv">
              <b><?php _e('Custom field name:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="featImgLoc" style="width: 200px;" value="<?php if (isset($options['featImgLoc'])) _e(apply_filters('format_to_edit',$options['featImgLoc']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
              <br/>              
             <span style="font-size: 11px; margin-left: 1px;"><?php _e('Set the name of the custom field that contains image info', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
              </div>
              <div class="itemDiv">
             <b><?php _e('Custom field Array Path:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="featImgLocArrPath" style="width: 200px;" value="<?php if (isset($options['featImgLocArrPath'])) _e(apply_filters('format_to_edit',$options['featImgLocArrPath']), 'social-networks-auto-poster-facebook-twitter-g') ?>" /> 
              <br/>              
             <span style="font-size: 11px; margin-left: 1px;">[<?php _e('Optional', 'social-networks-auto-poster-facebook-twitter-g') ?>] <?php _e('If your custom field contain an array, please enter the path to the image field. For example: [\'images\'][\'image\']', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
              </div>
              <div class="itemDiv">
             <b><?php _e('Custom field Image Prefix:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="featImgLocPrefix" style="width: 200px;" value="<?php if (isset($options['featImgLocPrefix'])) _e(apply_filters('format_to_edit',$options['featImgLocPrefix']), 'social-networks-auto-poster-facebook-twitter-g') ?>" /> 
              <br/>              
             <span style="font-size: 11px; margin-left: 1px;">[<?php _e('Optional', 'social-networks-auto-poster-facebook-twitter-g') ?>] <?php _e('If your custom field contain only the last part of the image path, please enter the prefix', 'social-networks-auto-poster-facebook-twitter-g') ?></span> 
              </div>
           </div></div>    
           
            <!-- ##### Ext Debug/Report Settings ##### --> 
            <div class="nxs_box"> <div class="nxs_box_header"><h3><?php _e('Debug/Report Settings', 'social-networks-auto-poster-facebook-twitter-g') ?></h3></div>
             <div class="nxs_box_inside"> <span style="font-size: 11px; margin-left: 1px;"><?php _e('Debug/Report Settings', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
             
             <div class="itemDiv">
             <input value="1" id="brokenCntFilters" name="brokenCntFilters"  type="checkbox" <?php if (isset($options['brokenCntFilters']) && (int)$options['brokenCntFilters'] == 1) echo "checked"; ?> /> 
              <strong>My Content Filters (<i>apply_filters('the_content'</i>) are broken, don't use them</strong>
               - <span style="font-size: 11px; margin-left: 1px;"><?php _e('Some third party plugin break content filters. Check this if some networks do not post silently(without any errors in the log). This will make %EXCERPT% work as %RAWEXCERPT%, %FULLTEXT% as %RAWTEXT%, etc... ', 'social-networks-auto-poster-facebook-twitter-g') ?></span>               
              <br/>                             
              </div>
              
              <div class="itemDiv">
              <input value="set" id="errNotifEmailCB" name="errNotifEmailCB"  type="checkbox" <?php if (isset($options['errNotifEmailCB']) && (int)$options['errNotifEmailCB'] == 1) echo "checked"; ?> /> 
              <strong>Send Email notification for errors</strong>
               - <span style="font-size: 11px; margin-left: 1px;"><?php _e('Send Email notification for all autoposting errors. No more then one email per hour will be sent.', 'social-networks-auto-poster-facebook-twitter-g') ?></span>               
              <br/>               
              <div style="margin-left: 18px;">
              <b><?php _e('Email:', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <input name="errNotifEmail" style="width: 200px;" value="<?php if (isset($options['errNotifEmail'])) _e(apply_filters('format_to_edit',$options['errNotifEmail']), 'social-networks-auto-poster-facebook-twitter-g') ?>" />
              <span style="font-size: 11px; margin-left: 1px;"><?php _e('wp_mail will be used. Some email providers (gmail, hotmail) might have problems getting such mail', 'social-networks-auto-poster-facebook-twitter-g') ?> </span> <br/>
              </div>
              </div>
              
              <?php $cr = get_option('NXS_cronCheck'); if (!empty($cr) && is_array($cr) && isset($cr['status']) && $cr['status']=='0') { ?> 
                <div class="itemDiv">             
             <span style="color: red"> *** <?php _e('Your WP Cron is not working correctly.', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl; echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span> <br/>
             
              <input value="set" id="forceBrokenCron" name="forceBrokenCron"  type="checkbox" <?php if (isset($options['forceBrokenCron']) && (int)$options['forceBrokenCron'] == 1) echo "checked"; ?> /> 
              <strong>Enable Cron functions even if WP Cron is not working correctly.</strong>
               <br/><span style="color:red; font-weight: bold;"><?php _e('I understand that this could cause duplicate postings as well as perfomance and stability problems.', 'social-networks-auto-poster-facebook-twitter-g') ?></span> - 
               <span style="margin-left: 1px; color:red;"><?php _e('Please do not check this unless you absolutely sure that you know what are you doing.', 'social-networks-auto-poster-facebook-twitter-g') ?></span>
               <br/><span style="margin-left: 1px; color:#005800;"><?php _e('Setting up WP Cron correctly will be much better solution:', 'social-networks-auto-poster-facebook-twitter-g') ?>
                 <a href="http://www.nextscripts.com/tutorials/wp-cron-scheduling-tasks-in-wordpress/" target="_blank">WP-Cron: Scheduling Tasks in WordPress</a>
               </span>
               
               
               
               </div>              
             <?php  } ?> 
              
           </div></div>               
    
           
     
     <?php if (function_exists("nxs_showPRXTab")) { ?>          
      <h3 style="font-size: 14px; margin-bottom: 2px;">Show "Proxies" Tab</h3>             
        <p style="margin: 0px;margin-left: 5px;"><input value="set" id="showPrxTab" name="showPrxTab"  type="checkbox" <?php if ((int)$options['showPrxTab'] == 1) echo "checked"; ?> /> 
          <strong>Show "Proxies" Tab</strong> <span style="font-size: 11px; margin-left: 1px;">Advanced Setting. Check to enable "Proxies" tab where you can setup autoposting proxies.</span>            
        </p>    
      <?php } ?>       
           
      <div class="submitX nxclear" style="padding-bottom: 0px;">       
        <input type="button" id="svBtnSettings" onclick="nxs_savePluginSettings();" class="button-primary" value="<?php _e('Update Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" />      
        <div id="nxsSaveLoadingImg" class="doneMsg">Saving.....</div> <div id="doneMsg" class="doneMsg">Done</div>
      </div>
      
      </div>
      </form>
            
            <?php
        }
        
  function showLogHistoryTab() { global $nxs_snapAvNts, $nxs_snapThisPageUrl, $nxsOne, $nxs_isWPMU, $nxs_tpWMPU; $nxsOne = ''; $options = $this->nxs_options; ?>
         <div style="width:99%;">
    
    <div style="float: right"><a href="#" onclick="nxs_rfLog();return false;" class="NXSButton" id="nxs_clearLog">Refresh</a></div>
    
    
    Showing last 150 records &nbsp;&nbsp;&nbsp;<a href="#" onclick="nxs_clLog();return false;" class="NXSButton" id="nxs_clearLog">Clear Log</a><br/><br/>    
      <div style="overflow: auto; border: 1px solid #999; width: 100%; height: 800px; font-size: 11px;" class="logDiv" id="nxslogDiv">
        <?php //$logInfo = maybe_unserialize(get_option('NS_SNAutoPosterLog')); 
        $logInfo = nxs_getnxsLog(array(1,1,1,0,0));  //  prr($logInfo);
        if (is_array($logInfo)) 
          foreach ($logInfo as $logline) { 
            if ($logline['type']=='E') $actSt = "color:#FF0000;"; elseif ($logline['type']=='M') $actSt = "color:#585858;"; elseif ($logline['type']=='BG') $actSt = "color:#008000; font-weight:bold;";
              elseif ($logline['type']=='I') $actSt = "color:#0000FF;"; elseif ($logline['type']=='W') $actSt = "color:#DB7224;"; elseif ($logline['type']=='BI') $actSt = "color:#0000FF; font-weight:bold;"; 
              elseif ($logline['type']=='GR') $actSt = "color:#008080;"; elseif ($logline['type']=='S') $actSt = "color:#005800; font-weight:bold;"; else $actSt = "color:#585858;";              
            if ($logline['type']=='E') $msgSt = "color:#FF0000;"; elseif ($logline['type']=='BG') $msgSt = "color:#008000; font-weight:bold;"; else $msgSt = "color:#585858;";                            
            if ($logline['nt']!='') $ntInfo = ' ['.$logline['nt'].'] '; else $ntInfo = '';           
            if (!empty($logline['uid'])) $uu = ' [UserID:'.$logline['uid'].']'; else $uu = '';
            echo '<snap style="color:#008000">['.$logline['date'].']</snap>'.$uu.' - <snap style="'.$actSt.'">['.$logline['act'].']</snap>'.$ntInfo.'-  <snap style="'.$msgSt.'">'.$logline['msg'].'</snap> '.$logline['extInfo'].'<br/>'; 
          } ?>
      </div>        
      <?php $quPosts = maybe_unserialize(get_option('NSX_PostsQuery')); if (!is_array($quPosts)) $quPosts = array(); if (count($quPosts)>0) { ?>
      <br/>Query:<br/>
      <div style="overflow: auto; border: 1px solid #999; width: 920px; height: 200px; font-size: 11px;" class="logDiv" id="nxsQUDiv">
      <?php 
        $pstEvrySec = $options['quDays']*86400+$options['quHrs']*3600+$options['quMins']*60; $offSet = ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );  $currTime = time() + $offSet;
        if (count($quPosts)>0) { $nxTime = (isset($options['quNxTime']) && (int)$options['quNxTime']>0)?$options['quNxTime']:($currTime+$pstEvrySec); 
          echo  "<snap style='color:#008000;'>Current Time:</snap> ".date_i18n('Y-m-d H:i', $currTime)." | <snap style='color:#000080;'>Next Scheduled Time:</snap> ~".date_i18n('Y-m-d H:i', $nxTime)."  |  <snap style='color:#580058;'>Last Post made from query:</snap> ".date_i18n('Y-m-d H:i', $options['quLastShTime'])."<br/>----====== Query:<br/>";
          foreach ($quPosts as $spostID){  $pst = get_post($spostID);  echo $spostID." - ".$pst->post_title."<br/>";}
        } ?>
      </div>
      <?php } ?>      
    </div> <?php }        
  function showQueryTab() { global $nxs_snapAvNts, $nxs_snapThisPageUrl, $nxsOne, $nxs_isWPMU, $nxs_tpWMPU; $nxsOne = ''; $options = $this->nxs_options; 
          global $wpdb;  $quPosts = $wpdb->get_results( "SELECT * FROM ". $wpdb->prefix . "nxs_query ORDER BY timetorun DESC", ARRAY_A );        
        ?>
         <div style="width:99%;">
    <a href="#" style="float: right" onclick="nxs_rfLog();return false;" class="NXSButton" id="nxs_clearLog">Refresh</a>
      <?php if (!is_array($quPosts)) $quPosts = array(); if (count($quPosts)>0) { ?>
      <br/><?php _e('Future Posts Timeline', 'social-networks-auto-poster-facebook-twitter-g') ?><br/>
      <div style="overflow: auto; border: 1px solid #999; width: 99%; height: 800px; font-size: 11px;" class="logDiv" id="nxsQUDiv">
      <?php //prr($quPosts);
         if (is_array($quPosts)) 
          foreach (array_reverse($quPosts) as $logline) { $btns = ''; $actSt = ''; $typeTXT = ''; $pstLine = '';              
            if (!empty($logline['postid'])) { $post = get_post($logline['postid']); if (empty($post)) continue; $pstLine = $logline['postid'].' - '.$post->post_title;;} else $pstLine = $logline['descr'];
            $btnC = '<a href="#" id="nxs_PQ_'.$logline['id'].'" class="nxs_Cancel_Q">[Cancel]</a>';
            switch ( $logline['type'] ) {
              case 'Q': $typeTXT = 'Queried Post'; $actSt = "color:#0000FF;"; $btns = $btnC;

              break;
              case 'S': /* prr($logline); */ $typeTXT = 'Scheduled Autopost to '.$logline['nttype'].' - '.$logline['descr']; $actSt = "color:#DB7224;"; $btns = $btnC;

              break;
              case 'R': $typeTXT = 'Next Post from '.$logline['descr']; $actSt = "color:#0000FF;"; $btns = $btnC;

              break;
              case 'F': $typeTXT = 'Scheduled "Quick Form" Post'.$logline['refid'] ; $actSt = "color:#005800;"; $btns = $btns = $btnC.'&nbsp;<a href="#" >[Post NOW]</a>';

              break;
            }
            
            echo '<div id="nxs_QU_'.$logline['id'].'"><snap style="color:#008000">['.$logline['timetorun'].']</snap> '.$btns.' - <snap style="'.$actSt.'">['.$typeTXT.']</snap>&nbsp;'.$pstLine.'<br/></div>'; 
          }
      ?>
      </div>
      <?php } //prr($quPosts); 
      ?>
      
    </div>        
        <?php 
        }
        
  function showAboutTab(){ global $nxs_snapAvNts, $nxs_snapThisPageUrl, $nxsOne, $nxs_isWPMU, $nxs_tpWMPU; $nxsOne = ''; $options = $this->nxs_options; 
    $nxsVer = NextScripts_SNAP_Version; if (defined('NXSAPIVER')) $nxsVer .= " (<span id='nxsAPIUpd'>API</span> Version: ".NXSAPIVER.")"; ?>
    <div style="max-width:1000px;"> 
      <?php _e('Plugin Version', 'social-networks-auto-poster-facebook-twitter-g'); ?>: <span style="color:#008000;font-weight: bold;"><?php echo $nxsVer; ?></span> <?php if($this->sMode['l']=='P') { ?> [Pro - Multiple Accounts Edition]&nbsp;&nbsp;<?php } else { ?>
        <span style="color:#800000; font-weight: bold;">[Single Accounts Edition]</span> <?php } ?><br/> <?php  global $nxs_apiLInfo; if (isset($nxs_apiLInfo) && !empty($nxs_apiLInfo)) {
          if ($nxs_apiLInfo['1']==$nxs_apiLInfo['2']) echo "<b>API:</b> ".$nxs_apiLInfo['2']; else echo "<b>API:</b> (Google+, Pinterest, LinkedIn, Reddit, Flipboard): ".$nxs_apiLInfo['1']."<br/><b>API:</b> (Instragram): ".$nxs_apiLInfo['2']; echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
        } if(defined('NXSAPIVER')){ ?> 
          <img id="checkAPI2xLoadingImg" style="display: none;" src='<?php echo $nxs_plurl; ?>img/ajax-loader-sm.gif' /><a href="" id="checkAPI2x">[<?php _e('Check for API Update', 'social-networks-auto-poster-facebook-twitter-g');?>]</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="" id="showLic2x">[<?php _e('Change Activation Key', 'social-networks-auto-poster-facebook-twitter-g');?>]</a> <br/><br/>
        <?php } ?><br/>        
        <div class="nxscontainer">
          <div class="nxsright">
            <div style="padding-left: 0px; padding-bottom:5px;"><a style="font-size: 14px;" target="_blank" href="http://www.nextscripts.com/instructions/"><?php _e('Setup/Installation Instructions:', 'social-networks-auto-poster-facebook-twitter-g');?></a></div>
            <?php foreach ($nxs_snapAvNts as $avNt) { $clName = 'nxs_snapClass'.$avNt['code']; $nt = new $clName(); ?>
              <div style="padding-left: 10px; padding-top:5px;"><a style="background-image:url(<?php echo NXS_PLURL; ?>img/<?php echo $avNt['lcode']; ?>16.png) !important;" class="nxs_icon16" target="_parent" href="<?php echo $nt->ntInfo['instrURL']; ?>">  <?php echo $nt->ntInfo['name']; ?> </a></div>
            <?php } ?>
          </div>
          <div class="nxsleft">
            <h3 style="margin-top: 0px; padding-left: 0px; font-size: 18px;"><?php _e('System Tests', 'social-networks-auto-poster-facebook-twitter-g');?></h3>            
            <div style="padding-bottom: 10px;"><?php nxs_memCheck(); ?></div>
            <a target="_blank" href="<?php echo $nxs_snapThisPageUrl; ?>&do=test">Check HTTPS/SSL</a>&nbsp;&nbsp;<a target="_blank" href="<?php echo $nxs_snapThisPageUrl; ?>&do=crtest">Show Cron Test Results</a><br/>
          
            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('Plugin Features Documentation', 'social-networks-auto-poster-facebook-twitter-g');?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="http://www.nextscripts.com/Features">SNAP Features</a><br/>
    
            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('General Questions', 'social-networks-auto-poster-facebook-twitter-g');?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="http://www.nextscripts.com/faq">FAQ</a><br/>

            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('Solutions for the most common problems', 'social-networks-auto-poster-facebook-twitter-g');?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="http://www.nextscripts.com/troubleshooting-social-networks-auto-poster">Troubleshooting FAQ</a><br/>

            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('Have troubles/problems/found a bug?', 'social-networks-auto-poster-facebook-twitter-g');?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="http://www.nextscripts.com/support">===&gt; Open support ticket &lt;===</a>


            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;"><?php _e('Have questions/suggestions?', 'social-networks-auto-poster-facebook-twitter-g');?></h3>
            <a style="font-weight: normal; font-size: 18px; line-height: 24px;" target="_blank" href="http://www.nextscripts.com/contact-us">===&gt; Contact us &lt;===</a> <br/>

            <h3 style="margin-top: 20px; padding-left: 0px; font-size: 18px;;"><?php _e('Like the Plugin? Would you like to support developers?', 'social-networks-auto-poster-facebook-twitter-g');?></h3>
              <div style="line-height: 24px;">
              <b>Here is what you can do:</b><br/>
              <?php if(class_exists('nxsAPI_GP')) { ?><s><?php } ?><img src="<?php echo NXS_PLURL; ?>img/snap-icon12.png"/> Get the <a href="http://www.nextscripts.com/social-networks-auto-poster-for-wp-multiple-accounts/#getit">"Pro" Edition</a>. You will be able to add several accounts for each network as well as post to Google+, Pinterest and LinkedIn company pages.<?php if(class_exists('nxsAPI_GP')) { ?></s> <i>Done! Thank you!</i><?php } ?><br/>
              <img src="<?php echo NXS_PLURL; ?>img/snap-icon12.png"/> Rate the plugin 5 stars at <a href="http://wordpress.org/extend/plugins/social-networks-auto-poster-facebook-twitter-g/">wordpress.org page</a>.<br/>
              <img src="<?php echo NXS_PLURL; ?>img/snap-icon12.png"/> <a href="<?php echo nxs_get_admin_url(); ?>post-new.php">Write a blogpost</a> about the plugin and don't forget to auto-post this blogpost to all your social networks ;-).<br/>
            </div>
          </div>
        </div><br style="clear: both;"/>
        <div style="width:100%">       
          <h4><?php _e('Some evil buttons:', 'social-networks-auto-poster-facebook-twitter-g');?></h4>
          &nbsp;&nbsp;<a target="_blank" href="<?php echo $nxs_snapThisPageUrl; ?>&do=test">[<?php _e('Reset all SNAP metainfo in the posts', 'social-networks-auto-poster-facebook-twitter-g');?>]</a> - <?php _e('this will remove all SNAP data that was saved with all posts.', 'social-networks-auto-poster-facebook-twitter-g');?> <br/>
          &nbsp;&nbsp;<a target="_blank" href="<?php echo $nxs_snapThisPageUrl; ?>&do=crtest">[<?php _e('Delete all SNAP Data', 'social-networks-auto-poster-facebook-twitter-g');?>]</a> - <?php _e('This is a complete "Start Over". This will delete all SNAP data from the posts and all SNAP settings including all configured networks.', 'social-networks-auto-poster-facebook-twitter-g');?> 
        </div>
    </div><?php 
  }
        
  function NS_SNAP_AddPostMetaTags() { global $post, $nxs_snapAvNts, $nxs_SNAP; $post_id = $post; if (is_object($post_id))  $post_id = $post_id->ID; 
          if (!is_object($post) || empty($post->post_status)) $post = get_post($post_id);  if (!isset($nxs_SNAP)) return; $options = $nxs_SNAP->nxs_accts; 
          ?>
          <style type="text/css">div#popShAtt {display: none; position: absolute; width: 600px; padding: 10px; background: #eeeeee; color: #000000; border: 1px solid #1a1a1a; font-size: 90%; }
            .underdash {border-bottom: 1px #21759B dashed; text-decoration:none;} .underdash a:hover {border-bottom: 1px #21759B dashed}
          </style>

       <div id="NXS_MetaFields" class="NXSpostbox">  <input value="'" type="hidden" name="nxs_mqTest" /> <input value="" type="hidden" id="nxs_snapPostOptions" name="nxs_snapPostOptions" />
         <div id="nxs_gPopup"><span class="nxspButton bClose"><span>X</span></span><div id="nxs_gPopupContentX"></div></div>
         <div id="nxs_gPopupWrap" style="display: none;"><div id="nxs_gPopup4"><div id="nxs_gPopupContent"></div></div></div>
         <div id="NXS_MetaFieldsIN" class="NXSpostbox">
       <?php /* ################## WHAT URL to USE */ ###################### ?>
          <div style="text-align: left; font-size: 14px; " class="showURL">
          <div class="inside" style="border: 1px #E0E0E0 solid; padding: 5px;"><div id="postftfp">
          
          <b>URL to use for links, attachments and %MYURL%:&nbsp;</b>     
          <div style="float: right;"><a href="#" class="manualAllPostBtn" onclick="return false;">[Post to All Checked Networks]</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="nxs_doResetPostSettings('<?php echo $post_id; ?>'); return false;">[Reset all SNAP data]</a></div>
          
          <input type="checkbox" class="isAutoURL" <?php  $forceSURL = get_post_meta($post_id, '_snap_forceSURL', true); 
            if (empty($forceSURL) && !empty($nxs_SNAP->nxs_options['forceSURL']) || $forceSURL=='1') { ?>checked="checked"<?php } ?>  id="useSURL" name="useSURL" value="1"/> <?php _e('Shorten URL', 'social-networks-auto-poster-facebook-twitter-g'); ?>
          &nbsp;&nbsp;&nbsp;  
          <input type="checkbox" class="isAutoURL" <?php $urlToUse = get_post_meta($post_id, 'snap_MYURL', true); 
            if ($urlToUse=='') { ?>checked="checked"<?php } ?>  id="isAutoURL-" name="isAutoURL" value="A"/> <?php _e('Auto', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post URL will be used', 'social-networks-auto-poster-facebook-twitter-g'); ?></i>                  
                    <div class="nxs_prevURLDiv" <?php if (trim($urlToUse)=='') { ?> style="display:none;"<?php } ?> id="isAutoURLFld-">
                      &nbsp;&nbsp;&nbsp;<?php _e('URL:', 'social-networks-auto-poster-facebook-twitter-g') ?> <input size="90" type="text" name="urlToUse" value="<?php echo $urlToUse ?>" id="URLToUse" /> 
                    </div>
          </div></div></div>
          <div id="NXS_MetaFieldsBox" class="postbox"><div class="inside"><div id="postftfp"> <input value="1" type="hidden" name="snapEdIT" />   
          <div class="popShAtt" style="width: 200px;" id="popShAttFLT"><?php _e('Filters are "ON". Will be posted or skipped based on filters', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
          <div class="popShAtt" style="width: 200px;" id="popShAttSV"><?php _e('If you made any changes to the format, please "Update" the post before reposting', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
          
          <div style="float: left;">  <h3 style="margin-left: 0px; padding-left: 0px;display: inline-block;">Autopost to ....</h3>&nbsp;&nbsp;&nbsp;&nbsp;
          
           <a href="#" onclick="jQuery('#nxsLockIt').val('1'); jQuery('.nxs_acctcb').attr('checked','checked'); nxs_showHideMetaBoxBlocks(); jQuery('.nxs_acctcb').iCheck('update'); return false;">[<?php  _e('Select All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;<a href="#" onclick="jQuery('#nxsLockIt').val('1');jQuery('.nxs_acctcb').removeAttr('checked'); nxs_showHideMetaBoxBlocks(); jQuery('.nxs_acctcb').iCheck('update'); return false;">[<?php _e('Unselect All Accounts', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>          
          </div><input type="hidden" id="nxsLockIt" value="0" />       
          
          <?php if($post->post_status != "publish" ) { ?>
          
          <?php } else {
              ?> <script type="text/javascript"> jQuery(document).ready(function() {  nxs_hideMetaBoxBlocks(); }); </script> <?php
          } ?>
          
          <div id="nxsPostMetaData"><?php // prr($options['tw']); WHAT IS IT?????????????/
          foreach ($nxs_snapAvNts as $avNt) { $clName = 'nxs_snapClass'.$avNt['code'];
             if ( isset($avNt['lcode']) && isset($options[$avNt['lcode']]) && count($options[$avNt['lcode']])>0) { $ntClInst = new $clName(); if (method_exists($ntClInst, 'showPostMeta')) $ntClInst->showPostMeta($options[$avNt['lcode']], $post); }
          }
          ?></div>
        
        
          <div id="nxsMetaBox" style="display: block; clear:both;"><?php 
          foreach ($nxs_snapAvNts as $avNt) { $clName = 'nxs_snapClass'.$avNt['code'];
             if ( isset($avNt['lcode']) && isset($options[$avNt['lcode']]) && count($options[$avNt['lcode']])>0) { $ntClInst = new $clName(); $ntClInst->nt = $options[$avNt['lcode']];              
             //## Count only finsihed accounts. Get rid of unfinnished accounts...
             $cbo = 0; foreach ($ntClInst->nt as $indx=>$pbo){ 
               if (empty($pbo[$ntClInst->ntInfo['lcode'].'OK'])) $pbo[$ntClInst->ntInfo['lcode'].'OK'] = $ntClInst->checkIfSetupFinished($pbo); if (empty($pbo[$ntClInst->ntInfo['lcode'].'OK'])) continue; else $cbo++;
             } if ($cbo==0) continue; ?>
             
             <div class="nxs_box" onmouseover="jQuery('.selAll<?php echo $avNt['code']; ?>').show();" onmouseout="jQuery('.selAll<?php echo $avNt['code']; ?>').hide();">
               <div class="nxs_box_header">
                 <div class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo NXS_PLURL;?>img/<?php echo (!empty($avNt['imgcode']))?$avNt['imgcode']:$avNt['lcode']; ?>16.png);"><?php echo $avNt['name']; ?>
                   <?php if ($cbo>1){ ?><div class="nsBigText"><?php echo '(<span id="nxsNumOfAcc_'.$avNt['lcode'].'">'.$cbo."</span> "; _e('accounts', 'social-networks-auto-poster-facebook-twitter-g'); echo ")"; ?></div><?php } ?>
                   <span style="display: none;" class="selAll<?php echo $avNt['code']; ?>">&nbsp;&nbsp;
                   <a onclick="jQuery('.nxs_acctcb<?php echo $avNt['lcode'];?>').attr('checked','checked'); jQuery('.nxs_acctcb<?php echo $avNt['lcode'];?>').iCheck('update'); return false;" style="font-size: 12px; text-decoration: none;" href="#">[<?php  _e('Select All', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;<a onclick="jQuery('.nxs_acctcb<?php echo $avNt['lcode'];?>').removeAttr('checked'); jQuery('.nxs_acctcb<?php echo $avNt['lcode'];?>').iCheck('update'); return false;" style="font-size: 12px; text-decoration: none;" href="#">[<?php  _e('Unselect All', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>        
                   </span>
                 </div>
               </div>
               <div class="nxs_box_inside"><?php $jj = 0;  if(!$ntClInst->checkIfFunc()) echo $ntClInst->noFuncMsg; 
                 else { uasort($ntClInst->nt, 'nxsLstSort'); foreach ($ntClInst->nt as $indx=>$pbo){ 
                     if (empty($pbo[$ntClInst->ntInfo['lcode'].'OK'])) $pbo[$ntClInst->ntInfo['lcode'].'OK'] = $ntClInst->checkIfSetupFinished($pbo); if (empty($pbo[$ntClInst->ntInfo['lcode'].'OK'])) continue;
                     $jj++; $pbo['jj']=$jj; $pbo['cbo']=$cbo; $ntClInst->showEditNTLine($indx, $pbo, $post); }}
                 if ($jj>7) { ?> <div style="padding-left:5px;padding-top:5px;"><a href="#" onclick="jQuery('.showMore<?php echo $avNt['code']; ?>').show(); jQuery(this).parent().hide(); return false;">Show More[<?php echo ($cbo-5); ?>]</a></div>  <?php } 
                 if ($jj==0) {?> <span>&nbsp;&nbsp;&nbsp;--&nbsp;<?php  _e('No completed accounts available', 'social-networks-auto-poster-facebook-twitter-g'); ?></span> <?php }
                 ?>
               </div>
             </div><?php 
             }
          }
         ?></div>
        
        
          <table style="margin-bottom:40px; clear:both;" class="nxs_mainEdTable" width="100%" border="0"><?php 
          foreach ($nxs_snapAvNts as $avNt) { $clName = 'nxs_snapClass'.$avNt['code'];
             if ( isset($avNt['lcode']) && isset($options[$avNt['lcode']]) && count($options[$avNt['lcode']])>0) { $ntClInst = new $clName(); $ntClInst->showEdPostNTSettings($options[$avNt['lcode']], $post); }
          }
         ?></table>
         
         <div class="popShAtt" id="fbAttachType"><h3><?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3> <img src="<?php echo NXS_PLURL; ?>img/fb2wops.png" width="600" height="257" alt="<?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
           <div class="popShAtt" id="fbPostTypeDiff"><h3><?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/fbPostTypesDiff6.png" width="600" height="398" alt="<?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
         
         <div id="showSetTime" style="display: none;background-color: #fff; width: 350px; padding: 25px;"><span class="nxspButton bClose"><span>X</span></span>           
           Set Time: (Current Time: <?php echo date_i18n('Y-m-d H:i'); ?> ) <div id="nxs_timestampdiv" class="hide-if-js" style="display: block;"><div class="timestamp-wrap"><select id="nxs_mm" name="nxs_mm">
            <option value="1" <?php if (date_i18n('n')=='1') echo 'selected="selected"' ?>>01-Jan</option> <option value="2" <?php if (date_i18n('n')=='2') echo 'selected="selected"' ?>>02-Feb</option> 
            <option value="3" <?php if (date_i18n('n')=='3') echo 'selected="selected"' ?>>03-Mar</option> <option value="4" <?php if (date_i18n('n')=='4') echo 'selected="selected"' ?>>04-Apr</option> 
            <option value="5" <?php if (date_i18n('n')=='5') echo 'selected="selected"' ?>>05-May</option> <option value="6" <?php if (date_i18n('n')=='6') echo 'selected="selected"' ?>>06-Jun</option> 
            <option value="7" <?php if (date_i18n('n')=='7') echo 'selected="selected"' ?>>07-Jul</option> <option value="8" <?php if (date_i18n('n')=='8') echo 'selected="selected"' ?>>08-Aug</option> 
            <option value="9" <?php if (date_i18n('n')=='9') echo 'selected="selected"' ?>>09-Sep</option> <option value="10" <?php if (date_i18n('n')=='10') echo 'selected="selected"' ?>>10-Oct</option>
            <option value="11" <?php if (date_i18n('n')=='11') echo 'selected="selected"' ?>>11-Nov</option> <option value="12" <?php if (date_i18n('n')=='12') echo 'selected="selected"' ?>>12-Dec</option> </select>
            
<input type="text" id="nxs_jj" name="nxs_jj" value="<?php echo date_i18n('d'); ?>" size="2" maxlength="2" autocomplete="off">, <input type="text" id="nxs_aa" name="nxs_aa" value="<?php echo date_i18n('Y'); ?>" size="4" maxlength="4" autocomplete="off"> @ <input type="text" id="nxs_hh" name="nxs_hh" value="<?php echo date_i18n('H'); ?>" size="2" maxlength="2" autocomplete="off"> : <input type="text" id="nxs_mn" name="nxs_mn" value="<?php echo date_i18n('i'); ?>" size="2" maxlength="2" autocomplete="off"></div><input type="hidden" id="nxs_ss" name="nxs_ss" value="58">
<p>
<a href="#" class="button bClose" onclick="var tid = jQuery('#nxs_timeID').val(); var tmTxt = nxs_makeTimeTxt(); var d=new Date(tmTxt);  var tm = (d.getTime() / 1000); jQuery('#'+tid+'timeToRunTxt').html(tmTxt);  jQuery('#'+tid+'timeToRun').val(tm); return false;">OK</a>
<a href="#" class="bClose">Cancel</a>
<input type="hidden"  id="nxs_timeID" value="" />
</p>
</div></div></div></div></div> </div> </div> <?php 
        }
  function NS_SNAP_SavePostMetaTags($id) { global $nxs_snapAvNts, $nxs_SNAP;            
          if (!empty($_POST['nxs_snapPostOptions'])) { $NXS_POSTX = $_POST['nxs_snapPostOptions']; $NXS_POST = array(); $NXS_POST = NXS_parseQueryStr($NXS_POSTX); } else $NXS_POST = $_POST;
          if (count($NXS_POST)<1 || !isset($NXS_POST["snapEdIT"]) || empty($NXS_POST["snapEdIT"])) return; 
          if (get_magic_quotes_gpc() || (!empty($_POST['nxs_mqTest']) && $_POST['nxs_mqTest']=="\'")){ array_walk_recursive($NXS_POST, 'nsx_stripSlashes'); }  array_walk_recursive($NXS_POST, 'nsx_fixSlashes');  
          if (!isset($nxs_SNAP)) return; $options = $nxs_SNAP->nxs_accts; //  echo "| NS_SNAP_SavePostMetaTags - ".$id." |";
          $post = get_post($id); if ($post->post_type=='revision' && $post->post_status=='inherit' && $post->post_parent!='0') return; // prr($NXS_POST);          
          if (empty($NXS_POST["useSURL"])) $NXS_POST["useSURL"] = '2'; delete_post_meta($id, '_snap_forceSURL'); add_post_meta($id, '_snap_forceSURL', $NXS_POST["useSURL"]);  
          delete_post_meta($id, 'snap_MYURL'); add_post_meta($id, 'snap_MYURL', $NXS_POST["urlToUse"]);    delete_post_meta($id, 'snapEdIT'); add_post_meta($id, 'snapEdIT', '1' ); 
          $snap_isAutoPosted = get_post_meta($id, 'snap_isAutoPosted', true); if ($snap_isAutoPosted=='1' &&  $post->post_status=='future') { delete_post_meta($id, 'snap_isAutoPosted'); add_post_meta($id, 'snap_isAutoPosted', '2'); }
          foreach ($nxs_snapAvNts as $avNt) {// echo "--------------------------------------------";  prr($avNt);          
              if (isset($options[$avNt['lcode']]) && count($options[$avNt['lcode']])>0 && isset($NXS_POST[$avNt['lcode']]) && count($NXS_POST[$avNt['lcode']])>0) { $savedMeta = maybe_unserialize(get_post_meta($id, 'snap'.$avNt['code'], true)); //prr($savedMeta);
              if(is_array($NXS_POST[$avNt['lcode']])) {
                  foreach ($NXS_POST[$avNt['lcode']] as $ii=>$pst ) { // echo "###########";  prr($pst);
                    if (is_array($pst) && empty( $pst['do'.$avNt['code']]) && empty($NXS_POST[$avNt['lcode']][$ii]['do'.$avNt['code']])) $NXS_POST[$avNt['lcode']][$ii]['do'.$avNt['code']] = 0; 
                  }
              } $newMeta = $NXS_POST[$avNt['lcode']];  
              if (is_array($savedMeta) && is_array($newMeta)) $newMeta = nxsMergeArraysOV($savedMeta, $newMeta); // echo "#####~~~~~~~~~ ".$id."| snap".$avNt['code']; prr($savedMeta); echo "||"; prr($newMeta);// $newMeta = 'AAA';
              delete_post_meta($id, 'snap'.$avNt['code']); add_post_meta($id, 'snap'.$avNt['code'], str_replace('\\','\\\\',serialize($newMeta)));   
              }
            }         //   die('KK');
          // prr($_POST);
        }
  //## Add MetaBox to Post->Edit
  function addCustomBoxes() { global $nxs_SNAP;  if (!isset($nxs_SNAP)) return; $options = $nxs_SNAP->nxs_options; 
          //## Add to Posts
          add_meta_box( 'NS_SNAP_AddPostMetaTags',  __( 'NextScripts: Social Networks Auto Poster - Post Options', 'social-networks-auto-poster-facebook-twitter-g' ), array($this, 'NS_SNAP_AddPostMetaTags'), 'post' );
          //## Add to pages          
          if (!empty($options['useForPages']) && $options['useForPages']=='1') add_meta_box( 'NS_SNAP_AddPostMetaTags',  __( 'NextScripts: Social Networks Auto Poster - Post Options', 'social-networks-auto-poster-facebook-twitter-g' ), array($this, 'NS_SNAP_AddPostMetaTags'), 'page' );
          //## Add to Custom Post Types
          $args=array('public'=>true, '_builtin'=>false);  $output = 'names';  $operator = 'and';  $post_types = array(); if (function_exists('get_post_types')) $post_types=get_post_types($args, $output, $operator);
          if ((isset($options['nxsCPTSeld'])) && $options['nxsCPTSeld']!='') $nxsCPTSeld = unserialize($options['nxsCPTSeld']); else $nxsCPTSeld = array_keys($post_types);  // prr($nxsCPTSeld); prr($post_types);
          foreach ($post_types as $cptID=>$cptName) if (in_array($cptID, $nxsCPTSeld)){ 
              add_meta_box( 'NS_SNAP_AddPostMetaTags',  __('NextScripts: Social Networks Auto Poster - Post Options', 'social-networks-auto-poster-facebook-twitter-g'), array($this, 'NS_SNAP_AddPostMetaTags'), $cptID );
          }    
        }
        
  
  function setSettingsFromPOST(){ $options = $this->nxs_options; 
            if (isset($_POST['apCats']))      $options['apCats'] = $_POST['apCats'];                
            if (isset($_POST['nxsHTDP']))     $options['nxsHTDP'] = $_POST['nxsHTDP'];                
            if (isset($_POST['ogImgDef']))    $options['ogImgDef'] = $_POST['ogImgDef'];  
            if (isset($_POST['featImgLoc']))  $options['featImgLoc'] = $_POST['featImgLoc'];            
            if (isset($_POST['imgSizeImg']))  $options['imgSizeImg'] = $_POST['imgSizeImg'];            
            if (isset($_POST['imgSizeAttch']))  $options['imgSizeAttch'] = $_POST['imgSizeAttch'];            
            if (isset($_POST['anounTagLimit']))  $options['anounTagLimit'] = $_POST['anounTagLimit'];                        
            if (isset($_POST['nxsHTSpace']))  $options['nxsHTSpace'] = $_POST['nxsHTSpace']; else $options['nxsHTSpace'] = "";
            if (isset($_POST['nxsHTSepar']))  $options['nxsHTSepar'] = $_POST['nxsHTSepar']; else $options['nxsHTSepar'] = "c_";
            if (isset($_POST['featImgLocPrefix']))  $options['featImgLocPrefix'] = $_POST['featImgLocPrefix'];
            if (isset($_POST['featImgLocArrPath']))  $options['featImgLocArrPath'] = $_POST['featImgLocArrPath'];
            
            if (isset($_POST['errNotifEmailCB']))   $options['errNotifEmailCB'] = 1;  else $options['errNotifEmailCB'] = 0;
            if (isset($_POST['errNotifEmail']))$options['errNotifEmail'] = $_POST['errNotifEmail']; 
            
            if (isset($_POST['forceBrokenCron']))   $options['forceBrokenCron'] = 1;  else $options['forceBrokenCron'] = 0;            
            
            if (isset($_POST['nxsURLShrtnr']))$options['nxsURLShrtnr'] = $_POST['nxsURLShrtnr']; 
            if (isset($_POST['bitlyUname']))  $options['bitlyUname'] = $_POST['bitlyUname']; 
            if (isset($_POST['bitlyAPIKey'])) $options['bitlyAPIKey'] = $_POST['bitlyAPIKey']; 
            
            if (isset($_POST['adflyUname']))  $options['adflyUname'] = $_POST['adflyUname']; 
            if (isset($_POST['adflyAPIKey'])) $options['adflyAPIKey'] = $_POST['adflyAPIKey']; 
            if (isset($_POST['adflyDomain'])) $options['adflyDomain'] = $_POST['adflyDomain']; 
            
            if (isset($_POST['YOURLSKey'])) $options['YOURLSKey'] = $_POST['YOURLSKey']; 
            if (isset($_POST['YOURLSURL'])) $options['YOURLSURL'] = $_POST['YOURLSURL'];
            
            if (isset($_POST['clkimAPIKey'])) $options['clkimAPIKey'] = $_POST['clkimAPIKey']; 
            if (isset($_POST['postAPIKey'])) $options['postAPIKey'] = $_POST['postAPIKey'];
                        
            if (isset($_POST['gglAPIKey'])) $options['gglAPIKey'] = $_POST['gglAPIKey'];             
            
            if (isset($_POST['fltrs']))  $options = nxs_adjFilters($_POST['fltrs'][0], $options);   
            if (isset($_POST['fltrsOn']))  $options['fltrsOn'] = 1;  else $options['fltrsOn'] = 0;                        
            
            if (!isset($options['nxsURLShrtnr'])) $options['nxsURLShrtnr'] = 'G';                                     
            if ($options['nxsURLShrtnr']=='B' && (trim($_POST['bitlyAPIKey'])=='' || trim($_POST['bitlyAPIKey'])=='')) $options['nxsURLShrtnr'] = 'G';            
            if ($options['nxsURLShrtnr']=='Y' && (trim($_POST['YOURLSKey'])=='' || trim($_POST['YOURLSURL'])=='')) $options['nxsURLShrtnr'] = 'G';
            if ($options['nxsURLShrtnr']=='A' && (trim($_POST['adflyAPIKey'])=='' || trim($_POST['adflyAPIKey'])=='')) $options['nxsURLShrtnr'] = 'G';          
            
            if ($options['nxsURLShrtnr']=='C' && trim($_POST['clkimAPIKey'])=='') $options['nxsURLShrtnr'] = 'G';
            if ($options['nxsURLShrtnr']=='P' && trim($_POST['postAPIKey'])=='') $options['nxsURLShrtnr'] = 'G';      
            
            if (isset($_POST['forceSURL']))   $options['forceSURL'] = 1;  else $options['forceSURL'] = 0;                       
            if (isset($_POST['brokenCntFilters']))   $options['brokenCntFilters'] = 1;  else $options['brokenCntFilters'] = 0;      
            
            if (isset($_POST['nsOpenGraph']))   $options['nsOpenGraph'] = $_POST['nsOpenGraph']; else $options['nsOpenGraph'] = 0;                
            if (isset($_POST['imgNoCheck']))   $options['imgNoCheck'] = 0;  else $options['imgNoCheck'] = 1;
            if (isset($_POST['useForPages']))  $options['useForPages'] = 1;  else $options['useForPages'] = 0;
                        
            if (isset($_POST['showPrxTab']))   $options['showPrxTab'] = 1;  else $options['showPrxTab'] = 0;
            if (isset($_POST['useRndProxy']))   $options['useRndProxy'] = 1;  else $options['useRndProxy'] = 0;
            
            if (!empty($_POST['showNTListCats']))  $options['showNTListCats'] = 1;  else $options['showNTListCats'] = 0;            
            
            if (isset($_POST['prxList'])) $options['prxList'] = $_POST['prxList']; 
            if (isset($_POST['addURLParams'])) $options['addURLParams'] = $_POST['addURLParams']; 
            
            if (isset($_POST['riActive']))   $options['riActive'] = 1;  else $options['riActive'] = 0;
            if (isset($_POST['riHowManyPostsToTrack'])) $options['riHowManyPostsToTrack'] = $_POST['riHowManyPostsToTrack'];             
            if (isset($_POST['riHowOften'])) $options['riHowOften'] = $_POST['riHowOften'];             
            
            if (isset($_POST['useUnProc']))   $options['useUnProc'] = $_POST['useUnProc']; else $options['useUnProc'] = 0;    
            if (!empty($_POST['nxsCPTSeld']) && is_array($_POST['nxsCPTSeld'])) $cpTypes = $_POST['nxsCPTSeld']; else $cpTypes = array();  $options['nxsCPTSeld'] = serialize($cpTypes); 
            if (isset($_POST['post_category']))  { $pk = $_POST['post_category']; if (!is_array($pk)) { $pk = urldecode($pk); parse_str($pk); } 
              remove_action( 'get_terms', 'order_category_by_id', 10); $cIds = get_terms( 'category', array('fields' => 'ids', 'get' => 'all') );
              if(is_array($pk) && $cIds) $options['exclCats'] = serialize(array_diff($cIds, $pk)); else $options['exclCats'] = '';
            }  //prr($options['exclCats']);
            if (!isset($_POST['whoCanSeeSNAPBox'])) $_POST['whoCanSeeSNAPBox'] = array(); $_POST['whoCanSeeSNAPBox'][] = 'administrator';            
            if (isset($_POST['whoCanSeeSNAPBox'])) $options['whoCanSeeSNAPBox'] = $_POST['whoCanSeeSNAPBox']; 
            if (!isset($_POST['whoCanMakePosts'])) $_POST['whoCanMakePosts'] = array(); $_POST['whoCanMakePosts'][] = 'administrator';            
            if (isset($_POST['whoCanMakePosts'])) $options['whoCanMakePosts'] = $_POST['whoCanMakePosts']; 
            if (!isset($_POST['whoCanHaveOwnSNAPAccs'])) $_POST['whoCanHaveOwnSNAPAccs'] = array(); $_POST['whoCanHaveOwnSNAPAccs'][] = 'administrator';            
            if (isset($_POST['whoCanHaveOwnSNAPAccs'])) $options['whoCanHaveOwnSNAPAccs'] = $_POST['whoCanHaveOwnSNAPAccs'];             
            
            if (isset($_POST['skipSecurity'])) $options['skipSecurity'] = 1;  else $options['skipSecurity'] = 0;
            if (!empty($_POST['zeroUser'])) $options['zeroUser'] = 1; else $options['zeroUser'] = 0;        
            
            if (isset($_POST['quLimit'])) $options['quLimit'] = 1;  else $options['quLimit'] = 0; 
            
            //## Query has been activated
              $isTimeChanged = ((isset($_POST['quDays']) && isset($options['quDays']) && $_POST['quDays']!=$options['quDays']) || (!isset($options['quDays']) && !empty($_POST['quDays']))) ||  
                ((isset($_POST['quHrs']) && isset($options['quHrs']) && $_POST['quHrs']!=$options['quHrs']) || (!isset($options['quHrs']) && !empty($_POST['quHrs']))) || 
                ((isset($_POST['quMins']) && isset($options['quMins']) && $_POST['quMins']!=$options['quMins']) || (!isset($options['quMins']) && !empty($_POST['quMins'])));
              
              if (isset($_POST['nxsOverLimit'])) $options['nxsOverLimit'] = $_POST['nxsOverLimit']; 
              if (isset($_POST['quLimitRndMins'])) $options['quLimitRndMins'] = $_POST['quLimitRndMins'];           
              if (isset($_POST['quDays'])) $options['quDays'] = $_POST['quDays']; else $options['quDays'] = 0;
              if (isset($_POST['quHrs'])) $options['quHrs'] = $_POST['quHrs']; else $options['quHrs'] = 0;
              if (isset($_POST['quMins'])) $options['quMins'] = $_POST['quMins'];else $options['quMins'] = 0;
            
              if ($isTimeChanged)  { $currTime = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ); 
                $pstEvrySec = $options['quDays']*86400+$options['quHrs']*3600+$options['quMins']*60;  $options['quNxTime'] = $currTime + $pstEvrySec; // ???????? Do we still need it if we have nxs_recountQueryTimes()?
                global $nxs_SNAP; $nxs_SNAP->nxs_options = $options; nxs_recountQueryTimes();
              }
            
            
            if (isset($_POST['rpstActive'])) $options['rpstActive'] = 1;  else $options['rpstActive'] = 0;      //     prr($options);
            
//            $options = nxs_adjRpst($options, $_POST);
            
            
            if (!empty($nxs_isWPMU) && $nxs_isWPMU && (!isset($options['suaMode'])||$options['suaMode'] == '')) $options['suaMode'] = $nxs_tpWMPU; 
            $editable_roles = get_editable_roles(); foreach ( $editable_roles as $roleX => $details ) {$role = get_role($roleX); $role->remove_cap('see_snap_box');  $role->remove_cap('make_snap_posts');  $role->remove_cap('haveown_snap_accss');   }
            
            foreach ($options['whoCanSeeSNAPBox'] as $uRole) { $role = get_role($uRole); $role->add_cap('see_snap_box'); $role->add_cap('make_snap_posts'); }            
            foreach ($options['whoCanMakePosts'] as $uRole) { $role = get_role($uRole); $role->add_cap('make_snap_posts'); }           
            foreach ($options['whoCanHaveOwnSNAPAccs'] as $uRole) { $role = get_role($uRole); $role->add_cap('haveown_snap_accss'); }            
            $this->nxs_options = $options; return $options;
        }
        
}}

if(!class_exists('WP_List_Table')){ require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );}
class nxs_QPListTable extends WP_List_Table {

    function __construct(){ global $status, $page;
        @parent::__construct( array(
            'singular'  => 'nxs_qpost',     //singular name of the listed records
            'plural'    => 'nxs_qposts',    //plural name of the listed records
            'ajax'      => true        //does this table support ajax?
        ) );
    }
    function column_default($item, $column_name){
        switch($column_name){
            case 'post':            
            case 'post_date':
                return $item->$column_name;
            case 'summary':
                return $item->guid;
            case 'author':
                return $item->$column_name;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_post_title($item){        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&item=%s">Edit</a>',$_REQUEST['page'],'edit',$item->ID),
            'delete'    => sprintf('<a href="?page=%s&action=%s&item=%s">Delete</a>',$_REQUEST['page'],'delete',$item->ID),
        );        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item->post_title,
            /*$2%s*/ $item->ID,
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item->ID                //The value of the checkbox should be the record's id
        );
    }
    
    function column_summary($item){ $outTxt = '';
       $snapData = maybe_unserialize(get_post_meta( $item->ID, '_nxs_snap_data', true ));   $info = new nxs_snapPostResults($snapData['posts']);
        
       $outTxt .= $info->summary;
        
       return $outTxt;
    }
    
    function column_author($item){ return get_the_author_meta('display_name', $item->post_author); }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'post_title'     => 'Title',
            'author'     => 'Author',
            'summary'    => 'Summary',
            'post_date'  => 'Date'
        );
        return $columns;
    }
    function get_sortable_columns() {
        $sortable_columns = array(
            'post_title'     => array('post_title',false),     //true means it's already sorted
          //  'summary'    => array('summary',false),
            'author'    => array('post_author',false),
            'post_date'  => array('post_date',false)
        );
        return $sortable_columns;
    }
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
    function process_bulk_action() {
        if( 'delete'===$this->current_action() ) {               
            foreach ($_REQUEST['nxs_qpost'] as $qp) { wp_delete_post($qp, true); }     $url = nxs_get_admin_url().'admin.php?page=nxssnap-post';    echo '<script type="text/javascript">parent.location.replace(\''.$url.'\');</script>'; die();
                
        }
    }

    function prepare_items() {
        $per_page = 50;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();

        $args=array('post_type' => 'nxs_qp', 'posts_per_page' => 500, 'orderby'=> 'date',  'order' => 'DESC');
        $query = new WP_Query($args);  $data = get_posts($args); //  prr($data);

        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'post_date'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //prr($order); //If no order, default to asc
            if (is_array($a)) $result = strcmp($a[$orderby], $b[$orderby]); else $result = strcmp($a->$orderby, $b->$orderby); //prr($a);//Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }                                    //  prr($data);  
        usort($data, 'usort_reorder');       
                                                //   prr($data);
        $current_page = $this->get_pagenum();
        $total_items = count($data);        
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page),   //WE have to calculate the total number of pages
            'orderby'    => ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'post_title',
            'order'        => ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'desc'
        ) );
    }
    
    function display() {
        wp_nonce_field( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );
        echo '<input type="hidden" id="order" name="order" value="' . $this->_pagination_args['order'] . '" />';
        echo '<input type="hidden" id="orderby" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';
        parent::display();
    }
    function ajax_response() {              
        check_ajax_referer( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );
        $this->prepare_items();             
        extract( $this->_args );
        extract( $this->_pagination_args, EXTR_SKIP );  
        ob_start();
        if ( ! empty( $_REQUEST['no_placeholder'] ) ) $this->display_rows(); else $this->display_rows_or_placeholder();
        $rows = ob_get_clean();
        ob_start();                               
        $this->print_column_headers();                  
        $headers = ob_get_clean();
        ob_start();
        $this->pagination('top');
        $pagination_top = ob_get_clean();
        ob_start();                        
        $this->pagination('bottom');
        $pagination_bottom = ob_get_clean();
        $response = array( 'rows' => $rows );
        $response['pagination']['top'] = $pagination_top;
        $response['pagination']['bottom'] = $pagination_bottom;
        $response['column_headers'] = $headers;
        if ( isset( $total_items ) )
            $response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );
        if ( isset( $total_pages ) ) {
            $response['total_pages'] = $total_pages;
            $response['total_pages_i18n'] = number_format_i18n( $total_pages );
        }
        die( json_encode( $response ) );
    }


}    

function nxs_ajax_fetch_custom_list_callback() {
    $wp_list_table = new nxs_QPListTable();
    $wp_list_table->ajax_response();
}
add_action('wp_ajax__ajax_fetch_custom_list', 'nxs_ajax_fetch_custom_list_callback');
/**
 * This function adds the jQuery script to the plugin's page footer
 */
function nxs_qp_ajax_script() { $screen = get_current_screen(); if ( 'snapautoposter_page_nxssnap-post' != $screen->id ) return false; ?>
<script type="text/javascript">

function nxs_doNP(){ jQuery("#nxsNPLoaderPost").show(); var mNts = []; jQuery('input[name=nxs_NPNts]:checked').each(function(i){ mNts[i] = jQuery(this).val(); }); var ddt = nxs_makeTimeTxt(); var qpid = jQuery('#nxsQPID').html();
  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"doNewPost", qpid:qpid, mText: jQuery('#nxsNPText').val(), mTitle: jQuery('#nxsNPTitle').val(), mType: jQuery('input[name=nxsNPType]:checked').val(), mLink: jQuery('#nxsNPLink').val(), mImg: jQuery('#nxsNPImg').val(), mNts: mNts, ddt:ddt, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, 
    function(j){  jQuery("#nxsNPResult").html(j); jQuery("#nxsNPLoaderPost").hide(); jQuery("#nxsNPCloseBt").val('Close'); 
           
            
            var data = {
                paged:  '1',
                order:  'desc',
                orderby: 'post_date'
            };
            list.update( data );
    
    }
  , "html")     
}
function nxs_doSaveQP(){ jQuery("#nxsNPLoaderPost").show(); var mNts = []; jQuery('input[name=nxs_NPNts]:checked').each(function(i){ mNts[i] = jQuery(this).val(); }); var ddt = nxs_makeTimeTxt(); var qpid = jQuery('#nxsQPID').html();
  jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"nxs_doSaveQP", qpid:qpid, mText: jQuery('#nxsNPText').val(), mTitle: jQuery('#nxsNPTitle').val(), mType: jQuery('input[name=nxsNPType]:checked').val(), mLink: jQuery('#nxsNPLink').val(), mImg: jQuery('#nxsNPImg').val(), mNts: mNts, ddt:ddt, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  jQuery("#nxsNPResult").html(j); jQuery("#nxsNPLoaderPost").hide(); jQuery("#nxsNPCloseBt").val('Close'); }, "html")     
}

(function($) {
list = {
   
    init: function() {
        // This will have its utility when dealing with the page number input
        var timer;
        var delay = 500;
        
        $('.edit a').on('click', function(e) { e.preventDefault();            
            var query = this.search.substring( 1 );
            
            var data = { page: list.__query( query, 'page' ) || 'nxssnap-post', action: list.__query( query, 'action' ) || 'edit', item: list.__query( query, 'item' ) || '0' };            
            
            jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"getQP", id:data.item, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val(), dataType: 'json'}, function(j){ var res = JSON.parse(j);                 
                 if (res.title.indexOf('uick post [')!=1) jQuery('#nxsNPTitle').val(res.title);                  
                 jQuery('#nxsNPText').val(res.text);  jQuery('#nxsNPLink').val(res.linkURL); jQuery('#nxsNPImg').val(res.imgURL);                                      
                 jQuery('input[name=nxsNPType][value='+res.postType+']').prop('checked',true);
                 jQuery('#nxsNPRowNetworks').html(res.nts);  jQuery('#nxsNPResult2').html(res.oldResults);   
                 jQuery('#nxsQPNewSave').show(); jQuery('#nxsQPID').html(data.item);
                                  
                 jQuery('#nsx_tab1_ttl').click();
            }, "html");
        });
        
        $('.delete a').on('click', function(e) { e.preventDefault();            
            var query = this.search.substring( 1 );            
            var data = { page: list.__query( query, 'page' ) || 'nxssnap-post', action: list.__query( query, 'action' ) || 'delete', item: list.__query( query, 'item' ) || '0' };            
            
            var answer = confirm("Remove post?");
            if (answer){            
              jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"delQP", id:data.item, nxs_mqTest:"'", _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}, function(j){  console.log( jQuery(e.target).parent().parent().parent().parent());    console.log( j);
                 if (j=='OK') jQuery(e.target).parent().parent().parent().parent().fadeOut("slow");                 
              }, "html");
            }
        });
        
        // Pagination links, sortable link
        $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function(e) {
            // We don't want to actually follow these links
            e.preventDefault();
            // Simple way: use the URL to extract our needed variables
            var query = this.search.substring( 1 );
            
            var data = {
                paged: list.__query( query, 'paged' ) || '1',
                order: list.__query( query, 'order' ) || 'asc',
                orderby: list.__query( query, 'orderby' ) || 'post_title'
            };
            list.update( data );
        });
        // Page number input
        $('input[name=paged]').on('keyup', function(e) {
            // If user hit enter, we don't want to submit the form
            // We don't preventDefault() for all keys because it would
            // also prevent to get the page number!
            if ( 13 == e.which )
                e.preventDefault();
            // This time we fetch the variables in inputs
            var data = {
                paged: parseInt( $('input[name=paged]').val() ) || '1',
                order: $('input[name=order]').val() || 'asc',
                orderby: $('input[name=orderby]').val() || 'post_title'
            };
            // Now the timer comes to use: we wait half a second after
            // the user stopped typing to actually send the call. If
            // we don't, the keyup event will trigger instantly and
            // thus may cause duplicate calls before sending the intended
            // value
            window.clearTimeout( timer );
            timer = window.setTimeout(function() {
                list.update( data );
            }, delay);
        });
    },
    /** AJAX call
     * 
     * Send the call and replace table parts with updated version!
     * 
     * @param    object    data The data to pass through AJAX
     */
    update: function( data ) {
        $.ajax({
            // /wp-admin/admin-ajax.php
            url: ajaxurl,
            // Add action and nonce to our collected data
            data: $.extend(
                {
                    _ajax_custom_list_nonce: $('#_ajax_custom_list_nonce').val(),
                    action: '_ajax_fetch_custom_list',
                    page: 'nxssnap-post',
                },
                data
            ),
            // Handle the successful result
            success: function( response ) { // WP_List_Table::ajax_response() returns json
                var response = $.parseJSON( response );
                // Add the requested rows
                if ( response.rows.length )  $('#the-list').html( response.rows );
                // Update column headers for sorting
                if ( response.column_headers.length ) { $('thead tr, tfoot tr').html( response.column_headers ); console.log( response.column_headers); }
                // Update pagination for navigation
                if ( response.pagination.bottom.length ) $('.tablenav.top .tablenav-pages').html( $(response.pagination.top).html() );
                if ( response.pagination.top.length ) $('.tablenav.bottom .tablenav-pages').html( $(response.pagination.bottom).html() );
                // Init back our event handlers
                list.init();
            }
        });
    },
    /**
     * Filter the URL Query to extract variables
     * 
     * @see http://css-tricks.com/snippets/javascript/get-url-variables/
     * 
     * @param    string    query The URL query part containing the variables
     * @param    string    variable Name of the variable we want to get
     * 
     * @return   string|boolean The variable value if available, false else.
     */
    __query: function( query, variable ) {
        var vars = query.split("&");
        for ( var i = 0; i <vars.length; i++ ) {
            var pair = vars[ i ].split("=");
            if ( pair[0] == variable )
                return pair[1];
        }
        return false;
    },
}
// Show time!
list.init();
})(jQuery);
</script>
<?php
}
//add_action('admin_footer', 'nxs_qp_ajax_script');

add_action( 'admin_menu', 'hook_that' );
function hook_that()
{
    add_action('admin_footer', 'nxs_qp_ajax_script');
} 

//## WP Pointer for V3-V4 Upgrade
function nxs_snap_pointer_admin_enqueue_scripts() { return;  wp_enqueue_style( 'wp-pointer' ); wp_enqueue_script( 'wp-pointer' ); add_action( 'admin_print_footer_scripts', 'nxs_snap_pointer_admin_print_footer_scripts' );}
function nxs_snap_pointer_admin_print_footer_scripts() { return; $pointer_content = '<h3>Social Network Autoposter (SNAP)</h3>';
  $pointer_content .= '<p>1. Social Networks Autoposter (SNAP) is now here</p><p>2. Auto-Reposting of existing posts functionality from Version 3 has been removed. <a target="_blank" href="http://gd.is/v4rpst">Please see here form more info</a>.</p>';
  ?><script type="text/javascript">
   //<![CDATA[
   jQuery(document).ready( function($) {
    $('#toplevel_page_nxssnap').pointer({ content: '<?php echo $pointer_content; ?>', position: {edge: 'left', align: 'center'},
        close: function() { jQuery.post(ajaxurl,{action: 'nxs_snap_aj',"nxsact":"dismPointer", pid: 'V4Notice', _wpnonce: jQuery('#nxsSsPageWPN_wpnonce').val()}) }
      }).pointer('open');
   });
   //]]>
   </script> <?php
}
?>