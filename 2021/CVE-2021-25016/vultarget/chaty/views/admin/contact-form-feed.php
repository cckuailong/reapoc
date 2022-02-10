
<?php
global $wpdb;
$table_name = $wpdb->prefix . 'chaty_contact_form_leads';

$current = isset($_GET['paged'])&&!empty($_GET['paged'])&&is_numeric($_GET['paged'])&&$_GET['paged']>0?$_GET['paged']:1;
$current = intval($current);

$search_for = "all_time";
$search_list = array(
	'today' => 'Today',
	'yesterday' => 'Yesterday',
	'last_7_days' => 'Last 7 Days',
	'last_30_days' => 'Last 30 Days',
	'this_week' => 'This Week',
	'this_month' => 'This Month',
	'all_time' => 'All Time',
	'custom' => 'Custom Date'
);

if(isset($_GET['search_for']) && !empty($_GET['search_for']) && isset($search_list[$_GET['search_for']])) {
	$search_for = $_GET['search_for'];
}
$start_date = "";
$end_date = "";
if($search_for == "today") {
	$start_date = date("Y-m-d");
	$end_date = date("Y-m-d");
} else if($search_for == "yesterday") {
	$start_date = date("Y-m-d", strtotime("-1 days"));
	$end_date = date("Y-m-d", strtotime("-1 days"));
} else if($search_for == "last_7_days") {
	$start_date = date("Y-m-d", strtotime("-7 days"));
	$end_date = date("Y-m-d");
} else if($search_for == "last_30_days") {
	$start_date = date("Y-m-d", strtotime("-30 days"));
	$end_date = date("Y-m-d");
} else if($search_for == "this_week") {
	$start_date = date("Y-m-d", strtotime('monday this week'));
	$end_date = date("Y-m-d");
} else if($search_for == "this_month") {
	$start_date = date("Y-m-01");
	$end_date = date("Y-m-d");
} else if($search_for == "custom") {
	if(isset($_GET['start_date']) && !empty($_GET['start_date'])) {
		$start_date = $_GET['start_date'];
	}
	if(isset($_GET['end_date']) && !empty($_GET['end_date'])) {
		$end_date = $_GET['end_date'];
	}
} else if($search_for == "all_time") {
	$start_date = "";
	$end_date = "";
}

$hasSearch = isset($_GET['search'])&&!empty($_GET['search'])?$_GET['search']:false;

$query = "SELECT count(id) as total_records FROM ".$table_name;
$search = "";

$condition = "";
$conditionArray = array();
if($hasSearch !== false) {
	$search = $hasSearch;
	$hasSearch = "%".$hasSearch."%";
	$condition .= " (name LIKE %s OR email LIKE %s OR phone_number LIKE %s OR message LIKE %s)";
	$conditionArray[] = $hasSearch;
	$conditionArray[] = $hasSearch;
	$conditionArray[] = $hasSearch;
	$conditionArray[] = $hasSearch;
}

if(!empty($start_date) && !empty($end_date)) {
	if(!empty($condition)) {
		$condition .= " AND ";
	}
	$c_start_date = date("Y-m-d 00:00:00", strtotime($start_date));
	$c_end_date = date("Y-m-d 23:59:59", strtotime($end_date));
	$condition .= " created_on >= %s AND created_on <= %s";
	$conditionArray[] = $c_start_date;
	$conditionArray[] = $c_end_date;
}
if(!empty($condition)) {
	$query .= " WHERE ".$condition;
}
$query .= " ORDER BY ID DESC";

if(!empty($conditionArray)) {
	$query = $wpdb->prepare($query, $conditionArray);
}

$total_records = $wpdb->get_var($query);
$per_page = 15;
$total_pages = ceil($total_records / $per_page);

$query = "SELECT * FROM ".$table_name;
if(!empty($condition)) {
	$query .= " WHERE ".$condition;
}

if($current > $total_pages) {
	$current = 1;
}
$start_from = ($current-1)*$per_page;

$query .= " ORDER BY ID DESC";
$query .= " LIMIT $start_from, $per_page";

if(!empty($conditionArray)) {
	$query = $wpdb->prepare($query, $conditionArray);
}
?>
<style>
    #wpwrap {
        position: inherit;
    }
</style>
<style>
    body {
        background: #f0f0f1 !important;
    }
    #wpfooter {
        position: relative;
    }
    .chaty-updates-form {
        width: 768px;
        padding: 70px 40px;
        box-shadow: 0px 20px 25px rgb(0 0 0 / 10%), 0px 10px 10px rgb(0 0 0 / 4%);
        display: flex;
        margin: 100px auto 0;
        font-family: Rubik, sans-serif;
        align-items: center;
    }
    .update-title {
        font-style: normal;
        font-weight: 500;
        font-size: 26px;
        line-height: 150%;
        align-items: center;
        color: #334155;
        position: relative;
        padding: 0 0 10px 0;
    }
    .updates-form-form-left {
        padding: 0px 20px 0px 0;
    }
    .updates-form-form-right p {
        font-style: normal;
        font-weight: normal;
        font-size: 14px;
        line-height: 150%;
        position: relative;
        padding: 0 0 20px 0;
        color: #475569;
        margin: 30px 0;
    }
    .update-title:after {
        content: "";
        border: 1px solid #3C85F7;
        position: absolute;
        bottom: 5px;
        left: 0;
        width: 90px;
    }
</style>
<div class="wrap">
	<?php
	$result = $wpdb->get_results ($query);
	?>
    <div>
		<?php if($result || !empty($search) || $search_for != 'all_time'){ ?>
            <h1 class="wp-heading">Contact Form Leads
                <div class="lead-search-box">
                    <form action="<?php echo admin_url("admin.php") ?>" method="get">
                        <label class="screen-reader-text" for="post-search-input">Search:</label>
                        <select class="search-input" name="search_for" style="" id="date-range">
							<?php foreach($search_list as $key=>$value) { ?>
                                <option <?php selected($key, $search_for) ?> value="<?php echo $key ?>"><?php echo $value ?></option>
							<?php } ?>
                        </select>
                        <input type="search" class="search-input" name="search" value="<?php echo esc_attr($search) ?>" class="">
                        <input type="submit" id="search-submit" class="button" value="Search">
                        <input type="hidden" name="page" value="chaty-contact-form-feed" />
                        <div class="date-range <?php echo ($search_for == "custom"?"active":"") ?>">
                            <input type="search" class="search-input" name="start_date" id="start_date" value="<?php echo esc_attr($start_date) ?>" autocomplete="off" placeholder="Start date">
                            <input type="search" class="search-input" name="end_date" id="end_date" value="<?php echo esc_attr($end_date) ?>" autocomplete="off" placeholder="End date">
                        </div>
                    </form>
                </div>
            </h1>
		<?php } ?>
        <form action="" method="post">
			<?php if($result){ ?>
                <div class="tablenav top">
                    <div class="alignleft actions bulkactions">
                        <select name="action" id="bulk-action-selector-top">
                            <option value="">Bulk Actions</option>
                            <option value="delete_message">Delete</option>
                        </select>
                        <input type="submit" id="doaction" class="button action" value="Apply">
                    </div>
                </div>
                <style>
                    body {
                        background: #ffffff;
                    }
                    #wpwrap {
                        position: inherit;
                    }
                </style>
			<?php } ?>
			<?php
			if($result){
				?>
                <table border="0" class="responstable">
                    <tr>
                        <th style="width:1%"><?php esc_html_e( 'Bulk', 'chaty' );?></th>
                        <th><?php esc_html_e( 'ID', 'chaty');?></th>
                        <th><?php esc_html_e( 'Widget Name', 'chaty');?></th>
                        <th><?php esc_html_e( 'Name', 'chaty');?></th>
                        <th><?php esc_html_e( 'Email', 'chaty');?></th>
                        <th><?php esc_html_e( 'Phone number', 'chaty');?></th>
                        <th><?php esc_html_e( 'Message', 'chaty');?></th>
                        <th><?php esc_html_e( 'Date', 'chaty');?></th>
                        <th class="text-center"><?php esc_html_e( 'URL', 'chaty');?></th>
                        <th class="text-center"><?php esc_html_e( 'Delete', 'chaty');?></th>
                    </tr>
					<?php
					foreach( $result as $res ) {
						if($res->widget_id == 0) {
							$widget_name = "Default";
						} else {
							$widget_name = get_option("cht_widget_title_".$res->widget_id);
							if(empty($widget_name)) {
								$widget_name = "Widget #".($res->widget_id+1);
							}
						}
						?>
                        <tr data-id="<?php echo $res->id ?>">
                            <td><input type="checkbox" value="<?php echo $res->id ?>" name="chaty_leads[]"></td>
                            <td><?php echo $res->id ?></td>
                            <td><?php echo $widget_name ?></td>
                            <td><?php echo $res->name ?></td>
                            <td><?php echo $res->email ?></td>
                            <td><?php echo $res->phone_number ?></td>
                            <td><?php echo nl2br($res->message) ?></td>
                            <td><?php echo $res->created_on ?></td>
                            <td class="text-center"><a class="url" target="_blank" href="<?php echo $res->ref_page ?>"><span class="dashicons dashicons-external"></span></a></td>
                            <td class="text-center"><a class="remove-record" href="#"><span class="dashicons dashicons-trash"></span></a></td>
                        </tr>
					<?php } ?>
                </table>
				<?php
				if($total_pages > 1) {
					$baseURL = admin_url("admin.php?paged=%#%&page=chaty-contact-form-feed");
					if(!empty($search)) {
						$baseURL .= "&search=".$search;
					}
					echo '<div class="custom-pagination">';
					echo paginate_links(array(
						'base' => $baseURL,
						'total' => $total_pages,
						'current' => $current,
						'format' => '?paged=%#%',
						'show_all' => false,
						'type' => 'list',
						'end_size' => 3,
						'mid_size' => 1,
						'prev_next' => true,
						'prev_text' => sprintf('%1$s', '<span class="dashicons dashicons-arrow-left-alt2"></span>'),
						'next_text' => sprintf('%1$s', '<span class="dashicons dashicons-arrow-right-alt2"></span>'),
						'add_args' => false,
						'add_fragment' => '',
					));
					echo "</div>";
				}
				?>
                <div class="leads-buttons">
                    <a href="<?php echo admin_url("?download_chaty_file=chaty_contact_leads&nonce=".wp_create_nonce("download_chaty_contact_leads")) ?>" class="wpappp_buton" id="wpappp_export_to_csv" value="Export to CSV">Download &amp; Export to CSV</a>
                    <input type="button" class="wpappp_buton" id="chaty_delete_all_leads" value="Delete All Data">
                </div>
			<?php } else if(!empty($search) || $search_for != "all_time")  { ?>
                <div class="chaty-updates-form">
                    <div class="updates-form-form-right">
                        <div class="update-title">Contact Form Leads</div>
                        <p>No records are found</p>
                    </div>
                </div>
			<?php } else { ?>
                <div class="chaty-updates-form">
                    <div class="updates-form-form-left">
                        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
                        <lottie-player src="https://assets2.lottiefiles.com/packages/lf20_5x2APt.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop  autoplay></lottie-player>
                    </div>
                    <div class="updates-form-form-right">
                        <div class="update-title">Contact Form Leads</div>
                        <p>Your contact form leads will appear here once you get some leads. Please make sure you've added the contact form channel to your Chaty channels in order to collect leads</p>
                    </div>
                </div>
			<?php } ?>
            <input type="hidden" name="remove_chaty_leads" value="<?php echo wp_create_nonce("remove_chaty_leads") ?>">
            <input type="hidden" name="paged" value="<?php echo esc_attr($current) ?>">
            <input type="hidden" name="search" value="<?php echo esc_attr($search) ?>">
        </form>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        var selectedURL = '<?php echo admin_url("admin.php?page=chaty-contact-form-feed&remove_chaty_leads=".wp_create_nonce("remove_chaty_leads")."&action=delete_message&paged={$current}&search={$search}&chaty_leads=") ?>';
        jQuery(document).on("click", ".remove-record", function(e){
            e.preventDefault();
            var redirectRemoveURL = selectedURL+jQuery(this).closest("tr").data("id");
            if(confirm("Are you sure you want to delete Record with ID# "+jQuery(this).closest("tr").data("id"))) {
                window.location = redirectRemoveURL;
            }
        });jQuery(document).on("click", "#chaty_delete_all_leads", function(e){
            e.preventDefault();
            var redirectRemoveURL = selectedURL+"remove-all";
            if(confirm("Are you sure you want to delete all Record from the database?")) {
                window.location = redirectRemoveURL;
            }
        });
        jQuery("#date-range").on("change", function(){
            if(jQuery(this).val() == "custom") {
                jQuery(".date-range").addClass("active");
            } else {
                jQuery(".date-range").removeClass("active");
            }
        });
        if(jQuery("#start_date").length) {
            jQuery("#start_date").datepicker({
                dateFormat: 'yy-mm-dd',
                altFormat: 'yy-mm-dd',
                maxDate: 0,
                onSelect: function(d,i){
                    var minDate = jQuery("#start_date").datepicker('getDate');
                    minDate.setDate(minDate.getDate()); //add two days
                    jQuery("#end_date").datepicker("option", "minDate", minDate);
                    if(jQuery("#end_date").val() <= jQuery("#start_date").val()) {
                        jQuery("#end_date").val(jQuery("#start_date").val());
                    }

                    if(jQuery("#end_date").val() == "") {
                        jQuery("#end_date").val(jQuery("#start_date").val());
                    }
                }
            });
        }
        if(jQuery("#end_date").length) {
            jQuery("#end_date").datepicker({
                dateFormat: 'yy-mm-dd',
                altFormat: 'yy-mm-dd',
                maxDate: 0,
                minDate: 0,
                onSelect: function(d,i){
                    if(jQuery("#start_date").val() == "") {
                        jQuery("#start_date").val(jQuery("#end_date").val());
                    }
                }
            });
        }
        // if(jQuery("#start_date").length) {
        //     if(jQuery("#start_date").val() != "") {
        //         var minDate = jQuery("#start_date").datepicker('getDate');
        //         minDate.setDate(minDate.getDate()); //add two days
        //         jQuery("#end_date").datepicker("option", "minDate", minDate);
        //         if(jQuery("#end_date").val() <= jQuery("#start_date").val()) {
        //             jQuery("#end_date").val(jQuery("#start_date").val());
        //         }
        //     }
        // }
    });
</script>