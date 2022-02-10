<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
Class LFB_SHOW_FORMS {
    function lfb_show_form_nonce(){
    $nonce = wp_create_nonce( '_nonce_verify' );
    return $nonce;
    }
    function lfb_show_all_forms($id) {
        $lfb_admin_url = admin_url();
        echo '<div class="wrap show-all-form"> <a class="button_lfb" target="_blank" href="//themehunk.com/product/lead-form-builder-pro/">Get Pro Version</a>
<h1>Lead Forms Builder<a href="' . $lfb_admin_url . 'admin.php?page=add-new-form&_wpnonce='.$this->lfb_show_form_nonce().'" class="add-new-h2">Add New</a></h1>
<div>
<table class="wp-list-table widefat fixed striped posts ">
	<thead>
	<tr>
		<th scope="col" id="title" class="manage-column column-title column-primary sortable asc"><a><span>Title</span><span class="sorting-indicator"></span></a></th>
		<th scope="col" id="shortcode" class="manage-column column-shortcode">Shortcode</th>
		<th scope="col" id="today_count" class="manage-column column-form-count sortable desc">Today\'s Lead</th>
		<th scope="col" id="total_count" class="manage-column column-form-count sortable desc">Total Lead</th>
        <th scope="col" id="email-settings" class="manage-column column-form-email sortable desc"><a><span>Form Settings</span><span class="sorting-indicator"></span></a></th>

        <th scope="col" id="email-settings" class="manage-column column-form-adon sortable desc"><a><span>Form Add-Ons</span><span class="sorting-indicator"></span></a></th>

        <th scope="col" id="date" class="manage-column column-form-date sortable desc">Date</th>
		</tr>
	</thead>
	<tbody id="the-list" data-wp-lists="list:post">';

        global $wpdb;
        $th_save_db = new LFB_SAVE_DB($wpdb);
        $table_name = LFB_FORM_FIELD_TBL;
		$start = 0;//
        $limit = 10;//
        $id = $id; 
        $start = ($id - 1) * $limit;
        $form_count = $start;
              $prepare_12 = $wpdb->prepare("SELECT * FROM $table_name WHERE form_status = %s ORDER BY id DESC LIMIT $start , $limit", 'ACTIVE' );
		$posts = $th_save_db->lfb_get_form_content($prepare_12);
        if ($posts){
            foreach ($posts as $results) {
            	$form_count++;
                $form_title = $results->form_title;
                $form_date = $results->date;
                $form_id = $results->id;
                $captcha_status = $results->captcha_status;
        $data_table = LFB_FORM_DATA_TBL;
        $today_date= date('Y/m/d');
        $newDate = date("Y/m/d H:i:s", strtotime($today_date));
        $th_save_db = new LFB_SAVE_DB($wpdb);
        $prepare_13 = $wpdb->prepare("SELECT id FROM $data_table WHERE date > %s and form_id = %d ", $newDate, $form_id );
        $count_result = $th_save_db->lfb_get_form_content($prepare_13);
        $lead_count = count($count_result);

        $prepare_14 = $wpdb->prepare("SELECT id FROM $data_table WHERE form_id = %d ", $form_id );
        $total_lead_result = $th_save_db->lfb_get_form_content($prepare_14);
        $total_lead_result = count($total_lead_result);
        $edit_url_nonce =$lfb_admin_url . 'admin.php?page=add-new-form&action=edit&formid=' . $form_id.'&_wpnonce='.$this->lfb_show_form_nonce();

        $advance_adons =$lfb_admin_url . 'admin.php?page=lfb-form-extension&fname=' . $form_title.'&fid=' . $form_id.'&_wpnonce='.$this->lfb_show_form_nonce();
        $form_color = $lfb_admin_url . 'admin.php?page=wplf-plugin-menu&action=show&formid=' . $form_id;

        echo '<tr><td class="title column-title has-row-actions column-primary" data-colname="Title"><strong>' . $form_count . '.  <a class="row-title" href="'.$edit_url_nonce. '" title="Edit “' . $form_title . '”">' . $form_title . '</a></strong>
		<div class="row-actions"><span class="edit"><a href="' . $edit_url_nonce. '">Edit</a></span>|<span class="edit"><a href="' . $lfb_admin_url . 'admin.php?page=wplf-plugin-menu&action=delete&page_id='.$id.'&formid=' . $form_id . '">Delete</a></span>|<span class="edit"><a href="'.$form_color.'" target="_blank" >View Form</a></span>
		</div>
		<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
		<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
		</td>
		<td class="shortcode column-shortcode" data-colname="Shortcode"><span class="shortcode">
		<input type="text" onfocus="this.select();" readonly="readonly" value="[lead-form form-id=' . $form_id . ' title=' . $form_title . ']" class="large-text code"></span>
		</td>

		<td class="form-date column-form-date" data-colname="Form-date">
		<abbr><a href="' . $lfb_admin_url . 'admin.php?page=wplf-plugin-menu&action=today_leads&formid=' . $form_id . '" target="_blank"><div class="lfb-counter">' . $lead_count . '</div></a></abbr>
		</td>
		<td class="form-date column-form-date" data-colname="Form-date">
		<abbr><a href="' . $lfb_admin_url . 'admin.php?page=wplf-plugin-menu&action=total_leads&formid=' . $form_id . '" target="_blank"><div class="lfb-counter">' . $total_lead_result . '</div></a></abbr>
		</td>
        <td class="form-config column-form-date" data-colname="Form-date">
        <abbr title="Configure"><button data-dropdown="#lfb-dropdown-with-'.$form_id.'" class="lfb-dropdown-btn">Form Settings</button>
</abbr>
        <div class="dropdown-menu dropdown-anchor-top-left dropdown-has-anchor" id="lfb-dropdown-with-'.$form_id.'">
    <ul>
        <li><a href="'.$edit_url_nonce.'&email-setting"><i class="fa fa-envelope-o" aria-hidden="true"></i>
 Email Notification (Auto-responders)</a></li>
                <li class="divider"></li>
        <li><a href="#"><i class="fa fa-repeat" aria-hidden="true"></i>
 Form Redirction Option'.LFB_FORM_PRO_FEATURE.'</li>
        <li class="divider"></li>
        <li><a href="'.$edit_url_nonce.'&form-setting"> <i class="fa fa-bullhorn" aria-hidden="true"></i> Lead Receiving Method (Email, Database, Both)</a></li>
                <li class="divider"></li>

        <li><a href="#"><i class="fa fa-commenting-o" aria-hidden="true"></i>
 Form Submit (Thank You Message) <span class="pro-feature"><i class="fa fa-lock"></i>

 Pro Feature<span></a></li>
 <li><a href="'.$edit_url_nonce.'&captcha-setting"><img width="18px" src="'.LFB_FORM_CAPTCHA_SVG.'" title="Form Import">
Spam Protection (Google Captcha)</a>';

if ( get_option('captcha-setting-sitekey') !== false ) {

       $on_status = ($captcha_status=='OFF')?'value="ON"': 'checked value="OFF"';
        echo '<div class="onoffswitch">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id ="lf-column-captcha-'.$form_id.'"  '.$on_status.'>
                    <label class="onoffswitch-label" for="lf-column-captcha-'.$form_id.'">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                    </label>
            </div>';
}else{
       echo '<span title="' . $captcha_status . '">Missing Keys</span>';
}
        
        echo '</li>
    </ul>
</div>
        </td>

        <td class="form-config column-form-date" data-colname="Form-date">
         <div class="dropdown-menu dropdown-anchor-top-left dropdown-has-anchor" id="dropdown-with-adon-'.$form_id.'">
    <ul>
        
<li><a href="#"><img width="18" src="'.LFB_FORM_COLOR_IMAGE.'" />
Form Color Customize'.LFB_FORM_PRO_FEATURE.'</li>
<li class="divider"></li>

        <li><a href="#"><img width="18px" src="'.LFB_MCPI_IMAGE.'" title="Mailchimp">
Mailchimp Adon Settings'.LFB_FORM_PRO_FEATURE.'</li>
<li class="divider"></li>

        <li><a href=""><img width="18px" src="'.LFB_SMTP_IMAGE.'" title="SMTP Configure">
SMTP Adon Settings <span class="pro-feature"><i class="fa fa-lock"></i>
 Pro Feature<span> </a></li>
<li class="divider"></li>

        <li><a href="#"><img width="18px" src="'.LFB_EXPORT_IMAGE.'" title="Entry Export">
Export (Form Leads) '.LFB_FORM_PRO_FEATURE.'</a></li>
<li class="divider"></li>

        <li><a href="#"><img width="18px" src="'.LFB_FORM_EXPORT_IMAGE.'" title="Form Export">
Form Export '.LFB_FORM_PRO_FEATURE.'</a></li>
    <li class="divider"></li>
<li><a href="#"><img width="18px" src="'.LFB_FORM_IMPORT_IMAGE.'" title="Form Import">
Form Import '.LFB_FORM_PRO_FEATURE.'</a></li>
<li class="divider"></li>


    </ul>
</div>
<button data-dropdown="#dropdown-with-adon-'.$form_id.'" class="lfb-dropdown-btn">View Add-Ons</button>

</td>

        <td class="form-date column-form-date" data-colname="Form-date">
        <span title="' . $form_date . '">' .  date("d M, Y", strtotime($form_date)). '</span>
        </td>
		</tr>';
            }
        }
        echo '</tbody>
  </table><div class="tablenav bottom"><br class="clear">';
  
            $prepare_15 = $wpdb->prepare("SELECT * FROM $table_name WHERE form_status = %s ", 'ACTIVE' );
            $rows = $th_save_db->lfb_get_form_content($prepare_15);
            $rows = count($rows);
            $total = ceil($rows / $limit);
            if ($id > 1) {
                echo "<a href='". $lfb_admin_url . "admin.php?page=wplf-plugin-menu&page_id=" . ($id - 1) . "' class='button'><i class='fa fa-chevron-right'></i></a>";
            }
            if ($id != $total) {
                echo "<a href='". $lfb_admin_url . "admin.php?page=wplf-plugin-menu&page_id=" . ($id + 1) . "' class='button'><i class='fa fa-chevron-left'></i></a>";
            }
            echo "<ul class='page'>";
            for ($i = 1; $i <= $total; $i++) {
                if ($i == $id) {
                    echo "<li class='lf-current'><a href='#'>" . $i . "</a></li>";
                } else {
                    echo "<li><a href='". $lfb_admin_url . "admin.php?page=wplf-plugin-menu&page_id=" .$i. "'>" . $i . "</a></li>";
                }
            }
             echo '</ul>';
echo '</div> </div>

</div>';
    }
}