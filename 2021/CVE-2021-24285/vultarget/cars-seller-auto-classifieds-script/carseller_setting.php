<?php 





// create custom plugin settings menu
add_action('admin_menu', 'package_setting_menu');

function package_setting_menu() {

  //create new top-level menu

  add_submenu_page( 'edit.php?post_type=carsellers', 'Setting', 'Setting', 'manage_options', 'package_setting', 'baw_settings_page' );
  
  add_action( 'admin_init', 'register_package_settings' );
}


function register_package_settings() {
  //register our settings
  register_setting( 'package-settings-group', 'admin_email' );

register_setting( 'package-settings-group', 'currency_code' );


}

function baw_settings_page() {
?>
<div class="wrap">
<h2>Car Seller Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'package-settings-group' ); ?>
    <?php do_settings_sections( 'package-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Admin Email ID</th>
        <td><input type="text" name="admin_email" value="<?php echo esc_attr( get_option('admin_email') ); ?>" style=" width: 329px;height: 37px;"/>
        <br><span>This user will receive all mails from request information.</span>
        </td>
        </tr>


        <tr valign="top">
        <th scope="row">Currency</th>

        <?php 
        $currency=array('US Dollar'=>array('symbol'=>'$','suffix'=>'USD'),
                        'British Pound'=>array('symbol'=>'£','suffix'=>'GBP'),
                        'Euro'=>array('symbol'=>'€','suffix'=>'EUR'),
                        'Rupees'=>array('symbol'=>'₹','suffix'=>'INR'),
                        
                        );
        ?>
        <td>
          <select name="currency_code" style=" width: 329px;height: 37px;">

          <?php 
         $currency_code=esc_attr( get_option('currency_code') );

         foreach ($currency as $key => $value) {
            // print_r($value);
          if($currency_code==$value["suffix"])
           echo '<option value="'.$value["suffix"].'" selected>'.$key.' ('.$value["symbol"].')</option>';
         else
            echo '<option value="'.$value["suffix"].'" >'.$key.' ('.$value["symbol"].')</option>';
         }
            
         ?>
          </select>

        </td>
        </tr>



         
       
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } 
add_action( 'admin_init', 'get_currency_symbol' );
function get_currency_symbol()
{
    $currency_symbol='';
   $currency=array(     'US Dollar'=>array('symbol'=>'$','suffix'=>'USD'),
                        'British Pound'=>array('symbol'=>'£','suffix'=>'GBP'),
                        'Euro'=>array('symbol'=>'€','suffix'=>'EUR'),
                        'Rupees'=>array('symbol'=>'₹','suffix'=>'INR'),
                        );
    $currency_code=esc_attr( get_option('currency_code') );
    foreach ($currency as $key => $value) {
     if($currency_code==$value["suffix"])
     {
       $currency_symbol=array('suffix'=>$value["suffix"], 'symbol'=>$value["symbol"],'name'=>$key);
     }
    }
    if(empty($currency_symbol)){
        $currency_symbol=array('suffix'=>'USD', 'symbol'=>'$','name'=>'US Dollar');
    }
   return $currency_symbol;

}





add_action('admin_notices', 'carsell_slidingcat_admin_notice');
function carsell_slidingcat_admin_notice(){
    
    global $current_user ;
    
    $user_id = $current_user->ID;
    if ( ! get_user_meta($user_id, 'carsell_slidingcat_ignore_notice',true)) {
        
        echo '<div class="updated">
           <p>Thank you for installing Car Seller - Auto Classifieds Script Plugin. Please consider a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=helplive24x7@gmail.com&lc=CA&item_name=Donation%20for%20Car%20Seller%20-%20Auto%20Classifieds%20Script&amount=0&currency_code=USD&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank">
           donation</a> to support this plugin. <a href="?carsell_slidingcat_notice_ignore=0">Discard</a>
           
           

           </p>
           
           </div>';
    }       
}

add_action('admin_init', 'carsell_slidingcat_notice_ignore');
function carsell_slidingcat_notice_ignore() {
    global $current_user;
    $user_id = $current_user->ID;
    if ( isset($_GET['carsell_slidingcat_notice_ignore']) && $_GET['carsell_slidingcat_notice_ignore']=='0' ) {
          add_user_meta($user_id, 'carsell_slidingcat_ignore_notice', 'true', true);
    }
}
?>