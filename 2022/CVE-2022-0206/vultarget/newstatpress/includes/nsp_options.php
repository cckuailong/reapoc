<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

/**
 * Filter the given value for preventing XSS attacks
 *
 * @param _value the value to filter
 * @return filtered value
 */
function nsp_FilterForXss($_value){
  $_value=trim($_value);

  // Avoid XSS attacks
  $clean_value = preg_replace('/[^a-zA-Z0-9\,\.\/\ \-\_\?=&;]/', '', $_value);
  if (strlen($_value)==0) {
    return array();
  }
  else {
    $array_values = explode(',',$clean_value);
    array_walk($array_values, 'nsp_TrimValue');
    return $array_values;
  }
}


/**
 * Trim the given string
 */
function nsp_TrimValue(&$value) {
  $value = trim($value);
}


/**
 * Print the options
 * added by cHab
 *
 * @param option_title the title for option
 * @param option_var the variable for option
 * @param var variables
 */
function nsp_PrintOption($option_title, $option_var, $var) {
  if($option_var!='newstatpress_menuoverview_cap' AND $option_var!='newstatpress_menudetails_cap' AND $option_var!='newstatpress_menuvisits_cap' AND $option_var!='newstatpress_menusearch_cap' AND $option_var!='newstatpress_menutools_cap' AND $option_var!='newstatpress_menuoptions_cap')
    echo "<td>$option_title</td>\n";
  echo "<td><select name=$option_var>\n";
  if($option_var=='newstatpress_menuoverview_cap' OR $option_var=='newstatpress_menudetails_cap' OR $option_var=='newstatpress_menuvisits_cap' OR $option_var=='newstatpress_menusearch_cap' OR $option_var=='newstatpress_menutools_cap' OR $option_var=='newstatpress_menuoptions_cap') {
    $role = get_role('administrator');
    foreach($role->capabilities as $cap => $grant) {
      print "<option ";
      if($var == $cap) {
        print "selected ";
      }
      print ">$cap</option>";
    }
  } else {
    foreach($var as $option) {
      // list($i,$j) = $option;
      echo "<option value=\"$option[0]\"";
      if(get_option($option_var)==$option[0]) {
        echo " selected";
      }
      echo ">". $option[0];
      if ($option[1] !=  '') {
        echo " ";
        _e($option[1],nsp_TEXTDOMAIN);
      }
      echo "</option>\n";
    }
  }
  echo "</select></td>\n";
}


/**
 * Gets a sorted (according to interval) list of the cron schedules
 */
function nsp_GetSchedules() {
  $schedules = wp_get_schedules();
  uasort( $schedules, create_function( '$a, $b', 'return $a["interval"] - $b["interval"];' ) );
  return $schedules;
}

/**
 * Calculate interval // took from wp-control
 * added by cHab
 *
 */
function nsp_interval( $since ) {
  // array of time period chunks
  $chunks = array(
    array( 60 * 60 * 24 * 365, _n_noop( '%s year', '%s years', nsp_TEXTDOMAIN ) ),
    array( 60 * 60 * 24 * 30, _n_noop( '%s month', '%s months', nsp_TEXTDOMAIN ) ),
    array( 60 * 60 * 24 * 7, _n_noop( '%s week', '%s weeks', nsp_TEXTDOMAIN ) ),
    array( 60 * 60 * 24, _n_noop( '%s day', '%s days', nsp_TEXTDOMAIN ) ),
    array( 60 * 60, _n_noop( '%s hour', '%s hours', nsp_TEXTDOMAIN ) ),
    array( 60, _n_noop( '%s minute', '%s minutes', nsp_TEXTDOMAIN ) ),
    array( 1, _n_noop( '%s second', '%s seconds', nsp_TEXTDOMAIN ) ),
  );

  if ( $since <= 0 ) {
    return __( 'now', nsp_TEXTDOMAIN );
  }

  // we only want to output two chunks of time here, eg:
  // x years, xx months
  // x days, xx hours
  // so there's only two bits of calculation below:

  // step one: the first chunk
  for ( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) {
    $seconds = $chunks[ $i ][0];
    $name = $chunks[ $i ][1];

    // finding the biggest chunk (if the chunk fits, break)
    if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
      break;
    }
  }

  // set output var
  $output = sprintf( translate_nooped_plural( $name, $count, nsp_TEXTDOMAIN ), $count );

  // step two: the second chunk
  if ( $i + 1 < $j ) {
    $seconds2 = $chunks[ $i + 1 ][0];
    $name2 = $chunks[ $i + 1 ][1];

    if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
      // add to output var
      $output .= ' ' . sprintf( translate_nooped_plural( $name2, $count2, nsp_TEXTDOMAIN ), $count2 );
    }
  }

  return $output;
}

/**
 * Print a dropdown filled with the possible schedules, including non-repeating.
 * added by cHab
 *
 * @param boolean $current The currently selected schedule
 */
function nsp_PrintSchedules($current){
  global $nsp_option_vars;
  $schedules = nsp_GetSchedules();
  $name=$nsp_option_vars['mail_notification_freq']['name'];
  $valu=$nsp_option_vars['mail_notification_freq']['value'];
    ?>
    <select name="newstatpress_mail_notification_freq" id="mail_freq">
    <option <?php selected( $current, '_oneoff' ); ?> value="_oneoff"><?php esc_html_e( 'Non-repeating', nsp_TEXTDOMAIN ); ?></option>
    <?php foreach ( $schedules as $sched_name => $sched_data ) { ?>
      <option <?php selected( $current, $sched_name ); ?> value="<?php echo esc_attr( $sched_name ); ?>"><?php printf('%s (%s)', esc_html( $sched_data['display'] ), esc_html( nsp_interval($sched_data['interval'] )));?></option>
    <?php } ?>
    </select>
    <?php
}

/**
 * Print a row of input
 * added by cHab
 *
 * @param option_title the title for options
 * @param nsp_option_vars the variables for options
 * @param input_size the size of input
 * @param input_maxlength the max length of the input
 ****************************************************/
function nsp_PrintRowInput($option_title, $nsp_option_vars, $input_size, $input_maxlength) {
  ?>
  <tr>
    <td>
    <?php
    echo "<label for=$nsp_option_vars[name]>$option_title</label></td>\n";
    echo "<td><input class='right' type='text' name=$nsp_option_vars[name] value=";
    echo (get_option($nsp_option_vars['name'])=='') ? $nsp_option_vars['value']:get_option($nsp_option_vars['name']);
    echo " size=$input_size maxlength=$input_maxlength />\n";
    ?>
    </td>
  </tr>
  <?php
}

function nsp_input_selected($name,$value,$default) {

  $status=get_option($name);
  if ($status=='')
    $status=$default;
  if ($status==$value)
    echo " checked";
}

/**
 * Print a row with given title
 *
 * @param option_title the title for option
 ******************************************/
function nsp_PrintRow($option_title) {
  echo "<tr>\n<td>$option_title</td>\n</tr>\n";
}

/**
 * Print a checked row
 * added by cHab
 *
 * @param option_title the title for options
 * @param option_var the variables for options
 */
function nsp_PrintChecked($option_title, $option_var) {
  echo "<tr>\n<td><input type=checkbox name='$option_var' value='checked' ".get_option($option_var)."> $option_title</td>\n</tr>\n";
}


/**
 * Print a text area
 * added by chab
 *
 * @param option_title the title for options
 * @param option_var the variables for options
 * @param option_description the descriotion for options
 */
function nsp_PrintTextaera($option_title, $option_var, $option_description) {
  echo "<tr><td>\n<p class='ign'><label for=$option_var>$option_title</label></p>\n";
  echo "<p>$option_description</p>\n";
  echo "<p><textarea class='large-text code' cols='40' rows='2' name=$option_var id=$option_var>";
  echo implode(',', get_option($option_var,array())); ?>
    </textarea>
    </p>
    </td>
  </tr>
  <?php
}

/**
 * Manages the options that the user can choose
 * Update by cHab : integrate JS tabulation
 */
function nsp_Options() {
  global $nsp_option_vars;
?>

<div class='wrap'>
  <h2><?php _e('NewStatPress Settings',nsp_TEXTDOMAIN); ?></h2>

    <?php
     
  if ( ! wp_next_scheduled( 'nsp_mail_notification' ) ) {
    $name=$nsp_option_vars['mail_notification']['name'];
    $status=get_option($name);

    if ($status=='enabled') {
      $name=$nsp_option_vars['mail_notification_freq']['name'];
      $freq=get_option($name);
      $name=$nsp_option_vars['mail_notification_time']['name'];
      $timeuser=get_option($name);
      $crontime_offest=nsp_calculationOffsetTime($t=time(),$timeuser);
      $crontime = time() + $crontime_offest ;
      if($freq=='_oneoff')
        wp_schedule_single_event( $crontime, 'nsp_mail_notification' );
      else
        wp_schedule_event( $crontime, $freq, 'nsp_mail_notification');
    }
  }
  else {
    $name=$nsp_option_vars['mail_notification']['name'];
    $status=get_option($name);

    if ($status=='disabled')
       nsp_mail_notification_deactivate();
    elseif ($status=='enabled') {
      if(isset($_POST['saveit']) && $_POST['saveit'] == 'all') {  
        check_admin_referer('nsp_submit', 'nsp_option_post');
        if (!current_user_can('administrator')) die("NO permission");
    
        $retrieved_nonce = $_REQUEST['nsp_option_post'];
        if (!wp_verify_nonce($retrieved_nonce, 'nsp_submit' ) ) die( 'Failed security check' );
      
        $name=$nsp_option_vars['mail_notification_freq']['name'];
        $freq=get_option($name);
        $name=$nsp_option_vars['mail_notification_time']['name'];
        $timeuser=get_option($name);
        $crontime_offest=nsp_calculationOffsetTime($t=time(),$timeuser);
        $crontime = time() + $crontime_offest ;
        remove_action( 'nsp_mail_notification', 'nsp_stat_by_email' );
        $timestamp = wp_next_scheduled( 'nsp_mail_notification' );
        wp_unschedule_event( $timestamp, 'nsp_mail_notification');
        if($freq=='_oneoff')
          wp_schedule_single_event( $crontime, 'nsp_mail_notification' );
        else
          wp_schedule_event( $crontime, $freq, 'nsp_mail_notification');
       }
    }
  }    
    
    
    
    if(isset($_POST['saveit']) && $_POST['saveit'] == 'all') { //option update request by user
      check_admin_referer('nsp_submit', 'nsp_option_post');
      if (!current_user_can('administrator')) die("NO permission");
      
      $retrieved_nonce = $_POST['nsp_option_post'];
      if (!wp_verify_nonce($retrieved_nonce, 'nsp_submit' ) ) die( 'Failed security check on option' );

      $i=isset($_POST['newstatpress_collectloggeduser']) ? ('checked'===$_POST['newstatpress_collectloggeduser'] ? 'checked' : '' ) : '';
      update_option('newstatpress_collectloggeduser', $i);

      $i=isset($_POST['newstatpress_donotcollectspider']) ? ('checked'===$_POST['newstatpress_donotcollectspider'] ? 'checked' : '' ) : '';
      update_option('newstatpress_donotcollectspider', $i);

      $i=isset($_POST['newstatpress_cryptip']) ? ('checked'===$_POST['newstatpress_cryptip'] ? 'checked' : '' ) : '';
      update_option('newstatpress_cryptip', $i);

      $i=isset($_POST['newstatpress_dashboard']) ? ('checked'===$_POST['newstatpress_dashboard'] ? 'checked' : '' ) : '';
      update_option('newstatpress_dashboard', $i);

      $i=isset($_POST['newstatpress_externalapi']) ? ('checked'===$_POST['newstatpress_externalapi'] ? 'checked' : '' ) : '';
      update_option('newstatpress_externalapi', $i);

      //global $nsp_option_vars;

      // $status['state'] = FALSE;
      // $status['freq'] = TRUE;
      // $status['address'] = TRUE;
      // $status['time'] = TRUE;
      // $status['info'] = TRUE;

      foreach($nsp_option_vars as $var) {
        if (isset($_POST[$var['name']])) {

          if ($var['name'] == 'newstatpress_ignore_ip')
            update_option('newstatpress_ignore_ip', nsp_FilterForXss($_POST['newstatpress_ignore_ip']));
          elseif ($var['name'] == 'newstatpress_ignore_users')
          update_option('newstatpress_ignore_users', nsp_FilterForXss($_POST['newstatpress_ignore_users']));
          elseif ($var['name'] == 'newstatpress_ignore_permalink')
            update_option('newstatpress_ignore_permalink', nsp_FilterForXss($_POST['newstatpress_ignore_permalink']));
   
          elseif ($var['name'] == 'newstatpress_stats_offsets') { 
            $temp = array();
            foreach( $_POST['newstatpress_stats_offsets'] as $key => $id ) {
              $temp[$key] = intval( $id );
            }       
            update_option('newstatpress_stats_offsets', $temp);      
   
          } 
          else update_option($var['name'], sanitize_text_field($_POST[$var['name']]));
        }
      }

      // update database too and print message confirmation
      nsp_BuildPluginSQLTable('update');
      print "<br /><div id='optionsupdated' class='updated'><p>".__('Options saved!',nsp_TEXTDOMAIN)."</p></div>";


    }
    elseif(isset($_POST['saveit']) && $_POST['saveit'] == 'mailme') { //option mailme request by user
      check_admin_referer('nsp_submit', 'nsp_option_post');
      if (!current_user_can('administrator')) die("NO permission");
    
      $retrieved_nonce = $_REQUEST['nsp_option_post'];
      if (!wp_verify_nonce($retrieved_nonce, 'nsp_submit' ) ) die( 'Failed security check on mail' );
    
      update_option('newstatpress_mail_notification_emailaddress', sanitize_email($_POST['newstatpress_mail_notification_emailaddress'])); //save the
      $mail_confirmation=nsp_stat_by_email('test');

      if ($mail_confirmation)
        print "<br /><div id='mailsent' class='updated'><p>".__('Email sent by the server!',nsp_TEXTDOMAIN)."</p></div>";
      else
        print "<br /><div id='mailsent' class='warning'><p>".__('Problem: Email not sent by the server!',nsp_TEXTDOMAIN)."</p></div>";

      //header ('Location: ' . $_SERVER['REQUEST_URI']);
    }
    ?>

    <form id="myoptions" method=post>

      <div id="usual1" class="usual">
      <ul>
        <?php
          $ToolsPage_tabs = array('general' => __('General',nsp_TEXTDOMAIN),
                                  'data' => __('Filters',nsp_TEXTDOMAIN),
                                  'overview' => __('Overview Menu',nsp_TEXTDOMAIN),
                                  'details' => __('Details Menu',nsp_TEXTDOMAIN),
                                  'visits' => __('Visits Menu',nsp_TEXTDOMAIN),
                                  'database' => __('Database',nsp_TEXTDOMAIN),
                                  'mail' => __('Email Notification',nsp_TEXTDOMAIN),
                                  'api' => __('API',nsp_TEXTDOMAIN)
                                  );
          foreach( $ToolsPage_tabs as $tab => $name ) {
              echo "<li><a href='#$tab' class='tab$tab'>$name</a></li>\n";
          }
        ?>
      </ul>

      <!-- tab 'general' -->
      <div id='general'>
        <table class='form-tableH'>
          <tr>

          <?php
            global $nsp_option_vars;

            // input parameters
            $input_size='2';
            $input_maxlength='3';

            echo "<th scope='row' rowspan='2'>"; _e('Dashboard',nsp_TEXTDOMAIN); echo "</th></tr>";
            $option_title=__('Enable NewStatPress widget',nsp_TEXTDOMAIN);
            $option_var='newstatpress_dashboard';
            nsp_PrintChecked($option_title,$option_var);

            echo "<tr><th scope='row' rowspan='1'>".__("Minimum capability to display each specific menu",nsp_TEXTDOMAIN)."(<a href='http://codex.wordpress.org/Roles_and_Capabilities' target='_blank'>".__("more info",nsp_TEXTDOMAIN)."</a>)</th><td colspan='2'><span class=\"dashicons dashicons-editor-help\"></span>Reminder: manage_network = Super Admin, manage_options = Administrator, edit_others_posts = Editor, publish_posts = Author, edit_posts = Contributor, Read = Everybody.</td></tr>";

            $option_title=__('Overview menu',nsp_TEXTDOMAIN);
            echo "<tr><th scope='row' rowspan='1' class='tab tab2'>"; echo $option_title."</th>";
            $option_var='newstatpress_menuoverview_cap';
            $val=get_option($option_var);
            nsp_PrintOption('',$option_var,$val);

            echo "</tr>";
            echo "<tr>";
            $option_title=__('Detail menu',nsp_TEXTDOMAIN);
            echo "<tr><th scope='row' rowspan='2' class='tab tab2'>"; echo $option_title."</th>";
            // $option_var=$nsp_option_vars['menudetails_cap']['name'];
            $option_var='newstatpress_menudetails_cap';
            $val=get_option($option_var);
            nsp_PrintOption('',$option_var,$val);
            echo "</tr>";
            echo "<tr>";

            $option_title=__('Visits menu',nsp_TEXTDOMAIN);
            echo "<tr><th scope='row' rowspan='2' class='tab tab2'>"; echo $option_title."</th>";
            $option_var='newstatpress_menuvisits_cap';
            $val=get_option($option_var);
            nsp_PrintOption('',$option_var,$val);
            echo "</tr>";
            echo "<tr>";

            $option_title=__('Search menu',nsp_TEXTDOMAIN);
            echo "<tr><th scope='row' rowspan='2' class='tab tab2'>"; echo $option_title."</th>";
            $option_var='newstatpress_menusearch_cap';
            $val=get_option($option_var);
            nsp_PrintOption('',$option_var,$val);
            echo "</tr>";
            echo "<tr>\n";

            $option_title=__('Tools menu',nsp_TEXTDOMAIN);
            echo "<tr><th scope='row' rowspan='2' class='tab tab2'>"; echo $option_title."</th>";
            $option_var='newstatpress_menutools_cap';
            $val=get_option($option_var);
            nsp_PrintOption('',$option_var,$val);
            echo "</tr>";
            echo "<tr>\n";

            $option_title=__('Options menu',nsp_TEXTDOMAIN);
            echo "<tr><th scope='row' rowspan='2' class='tab tab2'>"; echo $option_title."</th>";
            $option_var='newstatpress_menuvisits_cap';
            $val=get_option($option_var);
            nsp_PrintOption('',$option_var,$val);
          ?>
          </tr>
        </table>
        <br/>
      </div>

      <!-- tab 'overview' -->
      <div id='overview'>
        <table class='form-tableH'>
          <tr>
            <th scope='row' rowspan='2'> <?php _e('Visits calculation method',nsp_TEXTDOMAIN); ?> </th>
          </tr>
          <tr>
            <td>
              <fieldset>
              <?php
                $name=$nsp_option_vars['calculation']['name'];
                $valu=$nsp_option_vars['calculation']['value'];
                echo "
                  <p><input type='radio' name='$name' value=";

                        if ((get_option($name)=='') OR (get_option($name)==$valu)) {
                          // echo $nsp_option_vars['calculation']['value'];
                          echo $valu." checked";
                        }
                        echo " />
                        <label>"; _e('Simple sum of distinct IPs (Classic method)',nsp_TEXTDOMAIN); echo "</label>
                    </p>
                    <p>
                        <input type='radio' name='$name' value=";

                          echo 'sum';
                            if (get_option($name)=='sum') {
                              echo " checked";
                            }

                      echo " />
                      <label>";
                      _e('Sum of the distinct IPs of each day',nsp_TEXTDOMAIN);
                      echo "<br /> <span class=\"description\">";
                      _e('(slower than classic method for big database)',nsp_TEXTDOMAIN);
              ?>
            </span></label>
              </p>
              </fieldset>
            </td>
          </tr>

          <tr>
            <th scope='row' rowspan='2'> <?php _e('Graph',nsp_TEXTDOMAIN); ?></th>
          </tr>
          <tr>
            <?php
            $val=array(array(7,''),array(10,''),array(20,''),array(30,''),array(50,''));
            $option_title=__('Days number in Overview graph',nsp_TEXTDOMAIN);
            $option_var='newstatpress_daysinoverviewgraph';
            nsp_PrintOption($option_title,$option_var,$val);
            ?>
          </tr>
          <tr>
            <th scope='row' rowspan='3'> <?php _e('Overview',nsp_TEXTDOMAIN); ?></th>
          </tr>
          <tr>
            <?php
              $option_title=sprintf(__('Elements in Overview (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['overview']['value']);
              nsp_PrintRowInput($option_title,$nsp_option_vars['overview'],$input_size,$input_maxlength);

              echo "<tr><th scope='row' rowspan='6'>".__("Statistics offsets (Total visits)",nsp_TEXTDOMAIN)."</th></tr>";

              // input parameters
              $input_size='10';
              $input_maxlength='10';
              $name=$nsp_option_vars['stats_offsets']['name'];
              $val=get_option($name);

              //print_r($val);
              $alltotalvisits	= empty( $val['alltotalvisits'] ) ? 0 : $val['alltotalvisits'];
              $visitorsfeeds	= empty( $val['visitorsfeeds'] ) ? 0 : $val['visitorsfeeds'];
              $pageviews	= empty( $val['pageviews'] ) ? 0 : $val['pageviews'];
              $pageviewfeeds	= empty( $val['pageviewfeeds'] ) ? 0 : $val['pageviewfeeds'];
              $spy	= empty( $val['spy'] ) ? 0 : $val['spy'];

              $option_title=__('Visitors','newstatpress');
              echo "<tr><td class=\"tab2\"><label for=\"".$name."[alltotalvisits]\">".$option_title."</label></td>\n";
              echo "<td><input class='right' type='number' required name=\"newstatpress_stats_offsets[alltotalvisits]\" value=\"".$alltotalvisits;
              echo "\" size=\"$input_size\" maxlength=\"$input_maxlength\" />\n</td></tr>\n";

              $option_title=__('Visitors through Feeds','newstatpress');
              echo "<tr><td class=\"tab2\"><label for=\"".$name."[visitorsfeeds]\">".$option_title."</label></td>\n";
              echo "<td><input class='right' type='number' required name=\"newstatpress_stats_offsets[visitorsfeeds]\" value=\"".$visitorsfeeds;
              echo "\" size=\"$input_size\" maxlength=\"$input_maxlength\" />\n</td></tr>\n";

              $option_title=__('Pageviews','newstatpress');
              echo "<tr><td class=\"tab2\"><label for=\"".$name."[pageviews]\">".$option_title."</label></td>\n";
              echo "<td><input class='right' type='number' required name=\"newstatpress_stats_offsets[pageviews]\" value=\"".$pageviews;
              echo "\" size=\"$input_size\" maxlength=\"$input_maxlength\" />\n</td></tr>\n";

              $option_title=__('Pageviews through Feeds','newstatpress');
              echo "<tr><td class=\"tab2\"><label for=\"".$name."[pageviewfeeds]\">".$option_title."</label></td>\n";
              echo "<td><input class='right' type='number' required name=\"newstatpress_stats_offsets[pageviewfeeds]\" value=\"".$pageviewfeeds;
              echo "\" size=\"$input_size\" maxlength=\"$input_maxlength\" />\n</td></tr>\n";

              $option_title=__('Spiders','newstatpress');
              echo "<tr><td class=\"tab2\"><label for=\"".$name."[spy]\">".$option_title."</label></td>\n";
              echo "<td><input class='right' type='number' required name=\"newstatpress_stats_offsets[spy]\" value=\"".$spy;
              echo "\" size=\"$input_size\" maxlength=\"$input_maxlength\" />\n</td></tr>\n";
            ?>
        </table>
        <br />
      </div>

      <!-- tab 'data' -->
      <div id='data'>
        <table class='form-tableH'>
          <tr>
          <?php
            // traduction $variable addition for Poedit parsing
            __('Never',nsp_TEXTDOMAIN);
            __('All',nsp_TEXTDOMAIN);
            __('month',nsp_TEXTDOMAIN);
            __('months',nsp_TEXTDOMAIN);
            __('week',nsp_TEXTDOMAIN);
            __('weeks',nsp_TEXTDOMAIN);

            echo "<th scope='row' rowspan='4'>"; _e('Data collection',nsp_TEXTDOMAIN); echo "</th></tr>";

            $option_title=__('Crypt IP addresses',nsp_TEXTDOMAIN);
            $option_var='newstatpress_cryptip';
            nsp_PrintChecked($option_title,$option_var);
            // echo "<tr></tr>";
            $option_title=__('Collect data about logged users, too.',nsp_TEXTDOMAIN);
            $option_var='newstatpress_collectloggeduser';
            nsp_PrintChecked($option_title,$option_var);
            // echo "<tr></tr>";
            $option_title=__('Do not collect spiders visits',nsp_TEXTDOMAIN);
            $option_var='newstatpress_donotcollectspider';
            nsp_PrintChecked($option_title,$option_var);
          ?>
        </table>
        <table class='form-tableH'>
          <tr>
            <th class='padd' scope='row' rowspan='4'><?php _e('Data purge',nsp_TEXTDOMAIN); ?></th>
          </tr>
          <tr>
          <?php
            $val=array(array('0', 'Never'),array(1, 'month'),array(3, 'months'),array(6, 'months'),array(12, 'months'));
            $option_title=__('Automatically delete all visits older than',nsp_TEXTDOMAIN);
            $option_var='newstatpress_autodelete';
            nsp_PrintOption($option_title,$option_var,$val);
            echo "</tr>";
            echo "<tr>";

            $option_title=__('Automatically delete only spiders visits older than',nsp_TEXTDOMAIN);
            $option_var='newstatpress_autodelete_spiders';
            nsp_PrintOption($option_title,$option_var,$val);
          ?>
        </tr>
        </table>
        <table class='form-tableH'>
          <tr>
            <th class='padd' scope='row' rowspan='9'><?php _e('Parameters to ignore',nsp_TEXTDOMAIN); ?></th>
            <?php
              // echo '<tr><td><h3>'; _e('Parameters to ignore',nsp_TEXTDOMAIN); echo '</h3><td><td></td></tr></table>';
              // echo "<table class='option2'>";

              $option_title=__('Logged users',nsp_TEXTDOMAIN);
              $option_var='newstatpress_ignore_users';
              $option_description=__('Enter a list of users you don\'t want to track, separated by commas, even if collect data about logged users is on',nsp_TEXTDOMAIN);
              nsp_PrintTextaera($option_title,$option_var,$option_description);

              $option_title=__('IP addresses',nsp_TEXTDOMAIN);
              $option_var='newstatpress_ignore_ip';
              $option_description=__('Enter a list of networks you don\'t want to track, separated by commas. Each network <strong>must</strong> be defined using the CIDR notation (i.e. <em>192.168.1.1/24</em>). <br />If the format is incorrect, NewStatPress may not track pageviews properly.',nsp_TEXTDOMAIN);
              nsp_PrintTextaera($option_title,$option_var,$option_description);

              $option_title=__('Pages and posts',nsp_TEXTDOMAIN);
              $option_var='newstatpress_ignore_permalink';
              $option_description=__('Enter a list of permalinks you don\'t want to track, separated by commas. You should omit the domain name from these resources: <em>/about, p=1</em>, etc. <br />NewStatPress will ignore all the pageviews whose permalink <strong>contains</strong> at least one of them.',nsp_TEXTDOMAIN);
              nsp_PrintTextaera($option_title,$option_var,$option_description);
            ?>
        </table>
      </div>

      <!-- tab 'visits' -->
      <div id='visits'>
        <table class='form-tableH'>
          <tr>
            <th scope='row' rowspan='2'>
              <?php _e('Visitors by Spy',nsp_TEXTDOMAIN); ?>
            </th>
            <?php
              $val=array(array(20,''),array(50,''),array(100,''));
              $option_title=__('number of IP per page',nsp_TEXTDOMAIN);
              $option_var='newstatpress_ip_per_page_newspy';
              nsp_PrintOption($option_title,$option_var,$val);
            ?>
          </tr>
          <tr>
            <?php
              $option_title=__('number of visits for IP',nsp_TEXTDOMAIN);
              $option_var='newstatpress_visits_per_ip_newspy';
              nsp_PrintOption($option_title,$option_var,$val);
            ?>
          </tr>
          <tr>
            <th class='padd' scope='row' colspan='3'></th>
          </tr>
          <tr>
            <th class='padd' scope='row' rowspan='2'>
              <?php _e('Parameters to ignore',nsp_TEXTDOMAIN); ?>
            </th>
            <?php
              $option_title=__('number of bot per page',nsp_TEXTDOMAIN);
              $option_var='newstatpress_bot_per_page_spybot';
              nsp_PrintOption($option_title,$option_var,$val);
            ?>
          </tr>
          <tr>
          <?php
            $option_title=__('number of bot for IP',nsp_TEXTDOMAIN);
            $option_var='newstatpress_visits_per_bot_spybot';
            nsp_PrintOption($option_title,$option_var,$val);
          ?>
        </table>
      </div>

      <!-- tab 'details' -->
      <div id='details'>
        <table class='form-tableH'>
          <tr>
            <th class='padd' scope='row' rowspan='14'>
              <?php _e('Element numbers to display in',nsp_TEXTDOMAIN); ?>
            </th>
        <?php
          $option_title=sprintf(__('Top days (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['top_days']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['top_days'],$input_size,$input_maxlength);

          $option_title=sprintf(__('O.S. (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['os']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['os'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Browser (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['browser']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['browser'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Feed (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['feed']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['feed'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Search Engines (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['searchengine']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['searchengine'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Top Search Terms (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['search']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['search'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Top Referrer (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['referrer']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['referrer'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Countries/Languages (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['languages']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['languages'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Spiders (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['spiders']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['spiders'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Top Pages (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['pages']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['pages'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Top Days - Unique visitors (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['visitors']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['visitors'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Top Days - Pageviews (default %d)',nsp_TEXTDOMAIN), $nsp_option_vars['daypages']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['daypages'],$input_size,$input_maxlength);

          $option_title=sprintf(__('Top IPs - Pageviews (default %d)', nsp_TEXTDOMAIN), $nsp_option_vars['ippages']['value']);
          nsp_PrintRowInput($option_title,$nsp_option_vars['ippages'],$input_size,$input_maxlength);
        ?>
        </table>
      </div>

      <!-- tab 'database'  -->
      <div id='database'>
        <?php
          $option_description=__('Select the interval of date from today you want to use for updating your database with new definitions',nsp_TEXTDOMAIN);
          $option_description.=" ";
          $option_description.=__('(To update your database, go to Tools page)',nsp_TEXTDOMAIN);
          $option_description.=".";
          $option_description2=__('Note: Be aware, larger is the interval, longer is the update and bigger are the resources required.',nsp_TEXTDOMAIN);
        ?>


        <p><?php echo $option_description ?></p>
        <p><span class="dashicons dashicons-warning"></span><i><?php echo $option_description2 ?></i></p>

        <table class='form-tableH'>

          <tr >
           <?php
             $val= array(array('', 'All'),array(1, 'week'),array(2, 'weeks'),array(3, 'weeks'),array(1, 'month'),array(2, 'months'),array(3, 'months'),array(6, 'months'),array(9, 'months'),array(12, 'months'));
             $option_title=__('Update data in the given period',nsp_TEXTDOMAIN);
             $option_var='newstatpress_updateint';
             nsp_PrintOption($option_title,$option_var,$val);
           ?>
          </tr>
        </table>
        <br />
      </div>

      <!-- tab 'mail'  -->
      <div id='mail'>
        <?php
          $option_description=__('This option allows you to get periodic reports by email (dashboard informations). You can customize the frequency and the publishing time of the reports and also the description of Sender.',nsp_TEXTDOMAIN);
          $option_description2=__('Note: WP Cron job need to be operational in aim to schedule Email Notification.',nsp_TEXTDOMAIN);
          $mailaddress_description=__('Mailing address accept only one email address, check is well valid before reporting issues.',nsp_TEXTDOMAIN);
          $timepublishing_description=__('Notification will be sent at UTC time.',nsp_TEXTDOMAIN);
          $from_description=__('Sender could be personalized according your website (by default : \'NewStatPress\').',nsp_TEXTDOMAIN);

          $time_format = 'H:i:s';

          $tzstring = get_option( 'timezone_string' );
          $current_offset = get_option( 'gmt_offset' );

          if ( $current_offset >= 0 ) {
            $current_offset = '+' . $current_offset;
          }

          if ( '' === $tzstring )
            $tz = sprintf( 'UTC%s', $current_offset );
          else
            $tz = sprintf( '%s (UTC%s)', str_replace( '_', ' ', $tzstring ), $current_offset );

          $name=$nsp_option_vars['mail_notification_address']['name'];
          $email=get_option($name);
          if($email=='') {
            $current_user = wp_get_current_user();
            $email=$current_user->user_email;
          }
          $name=$nsp_option_vars['mail_notification_sender']['name'];
          $sender=get_option($name);
          if($sender=='')
        	 $sender=$nsp_option_vars['mail_notification_sender']['value'];

        //  (get_option($nsp_option_vars['name'])=='') ? $sender=$nsp_option_vars['value']:$sender=get_option($nsp_option_vars['name']);

        ?>

        <p><?php echo $option_description ?></p>
        <p><span class="dashicons dashicons-warning"></span><i><?php echo $option_description2 ?></i></p>

        <table class='form-tableH'>
          <tr>
            <th scope='row' rowspan='2'><?php _e('Statistics notification is',nsp_TEXTDOMAIN); ?></th>
          </tr>
          <tr>
            <td>
              <fieldset>
              <?php
                $name=$nsp_option_vars['mail_notification']['name'];
                $default=$nsp_option_vars['mail_notification']['value'];
              ?>
              <form id="myForm">
                <p>
                  <input class="tog" type='radio' id='dis' name='<?php echo $name ?>' value='disabled'<?php nsp_input_selected($name,'disabled',$default);?> /><label> <?php _e('Disabled',nsp_TEXTDOMAIN); ?></label>
                </p>
                <p>
                  <input class="tog" type='radio' id='ena' name='<?php echo $name ?>' value='enabled'<?php nsp_input_selected($name,'enabled',$default);?>  /><label> <?php _e('Enabled',nsp_TEXTDOMAIN) ?></label>
                </p>
              </form>
              </fieldset>
            </td>
          </tr>
          <tr>
            <th scope='row' rowspan='2'><?php _e('Event schedule',nsp_TEXTDOMAIN); ?></th>
          </tr>
          <tr>
            <td>
              <?php
                $name=$nsp_option_vars['mail_notification_freq']['name'];
                nsp_PrintSchedules(get_option($name));
              ?>
            </td>
          </tr>
          <tr>
            <th scope='row' rowspan='2'><?php _e('Publishing time',nsp_TEXTDOMAIN); ?></th>
          </tr>
          <tr>
            <td>
              <select name="newstatpress_mail_notification_time" id="mail_time">
            <option value="0">- <?php _e('Select',nsp_TEXTDOMAIN)?> -</option> ?>
          <?php
            $name=$nsp_option_vars['mail_notification_time']['name'];
            $timeuser=get_option($name);

              for ($h = 0; $h <= 23; $h++) {
                for($m = 0; $m <= 45; $m += 15) {
                  $value = sprintf('%02d', $h) . ':' . sprintf('%02d', $m);
                  if($timeuser==$value)
                    echo '<option value="'. $value.'" selected>'. $value.'</option>\n';
                  else
                    echo '<option value="'. $value.'">'. $value.'</option>\n';
                }
              }
          ?>
          </select>
          <span id="utc-time"><?php printf( esc_html__( 'UTC time is %s', 'wp-crontrol' ), '<code>' . esc_html( date_i18n( $time_format, false, true ) ) . '</code>' ); ?></span>
          <span id="local-time"><?php printf( esc_html__( 'Local time is %s', 'wp-crontrol' ), '<code>' . esc_html( date_i18n( $time_format ) ) . '</code>' ); ?></span>
          <p class="description"><?php echo $timepublishing_description ?></p>

            </td>
          </tr>
          <tr>
            <th scope='row' rowspan='2'><?php _e('Sender Description (From)',nsp_TEXTDOMAIN); ?></th>
          </tr>
          <tr>
            <td>
              <input id="sender" class='left' type='text' name='newstatpress_mail_notification_sender' value='<?php echo $sender; ?>' size=20 maxlength=60 />
              <p class="description"><?php echo $from_description ?></p>
            </td>
          </tr>
          <tr>
            <th scope='row' rowspan='2'><?php _e('Mailing address',nsp_TEXTDOMAIN); ?></th>
          </tr>
          <tr>
            <td>
              <input id="mail_address" class='left' type='email' name='newstatpress_mail_notification_emailaddress' value='<?php echo $email; ?>' size=20 maxlength=60 />
              <button id="testmail" class='button button-secondary' type=submit name=saveit value=mailme><?php _e('Email Test',nsp_TEXTDOMAIN);?></button>
              <p class="description"><?php echo $mailaddress_description ?></p>
            </label>
            <input type=hidden name='newstatpress_mail_notification_info' value=<?php $current_user = wp_get_current_user(); echo $current_user->display_name;?> />
            </td>
          </tr>
        </table>
      </div>

      <!-- tab 'API' -->
      <div id='api'>
        <?php
          $newstatpress_url=nsp_PluginUrl();
          $url2=$newstatpress_url."doc/external_api.pdf";
          $option_description=__('The external API is build to let you to use the collected data from your Newstatpress plugin in an other web server application (for example you can show data relative to your Wordpress blog, inside a Drupal site that run since an another server).',nsp_TEXTDOMAIN);
          $option_description.=' <strong>'.__('However the external API is also used by Newstatpress itself for speedup page rendering of queried data (when are processed AJAX calls), so \'overview page & widget dashboard\' will be not working if you not activate it.',nsp_TEXTDOMAIN);
          $option_description.='</strong><br /> '.__('To use it, a key is needed to allow NewStatpress to recognize that you and only you want the data and not the not authorized people (Let the input form blank means that you allow everyone to get data without authorization if external API is activated).',nsp_TEXTDOMAIN);
          $option_description.="<br/><br/><a target=\'_blank\' href='$url2'>". __('Full documentation (PDF)',nsp_TEXTDOMAIN) ."</a><br />";
          $option_description2=' '.__('Please be aware that the external API is also used by Newstatpress itself when are processed AJAX calls for speedup page rendering of queried data, so you will need to choose an key and activate it.',nsp_TEXTDOMAIN);

          $option_description3=__('You must generate or set manually a private key for the external API : only alphanumeric characters are allowed (A-Z, a-z, 0-9), length should be between 64 and 128 characters.',nsp_TEXTDOMAIN);

          $option_title=__('Enable External API',nsp_TEXTDOMAIN);
          $option_var='newstatpress_externalapi';
        ?>
        <div class="optiondescription">
          <p>
            <?php echo $option_description ?>
            <!-- <br /> -->
            <span><i><strong><?php //echo $option_description2 ?></strong></i></span>
          </p>
        </div>
        <table class='form-tableH'>
          <tr>
            <th scope='row' rowspan='2'><?php _e('Extern API',nsp_TEXTDOMAIN); ?></th>
          </tr>
          <?php
            nsp_PrintChecked($option_title,$option_var);

            $option_title=__('API key',nsp_TEXTDOMAIN);
            $option_var='newstatpress_apikey';
          ?>
          <tr>
            <th scope='row' rowspan='2'>
              <p class='ign'>
                <label for=<?php echo $option_var; ?>><?php echo $option_title; ?></label>
              <!-- </p> -->

              <?php //echo $option_description3 ?>
            </p>
          </th>
         </tr>


          <tr>
            <td>
              <div class='center'>
                <p class="textarealimited">
                  <textarea class='large-text code api' minlength='64' maxlength='128' cols='50' rows='3' name='<?php echo $option_var; ?>' id='<?php echo $option_var; ?>'><?php echo get_option($option_var);?></textarea>
                </p>
                <p class="description textarealimited"><?php echo $option_description3 ?></p>
              </div>

            <div class='left'>
              <div class='button' type='button' onClick='nspGenerateAPIKey()'><?php _e('Generate new API key',nsp_TEXTDOMAIN); ?></div>
            </div>
            <br/>
          </td>
        </tr>
        </table>
      </div>

      <!-- Save Options Button -->
      <?php wp_nonce_field('nsp_submit', 'nsp_option_post'); ?>
      <input type="hidden" name="page" value="newstatpress">
      <input type="hidden" name="newstatpress_action" value="options">
      <button class="button button-primary" type="submit" name="saveit" value="all"><?php _e('Save options',nsp_TEXTDOMAIN); ?></button>

      </div>
    </form>
    <?php nsp_load_time(); ?>
</div>

<script type="text/javascript">
  jQuery("#usual1 ul").idTabs(general);
</script>

<?php

}
