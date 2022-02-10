<?php
if (isset($_POST["skip_wizard"])) {
    //header('Location: ' . admin_url() . 'plugins.php');
    header('Location: admin.php?page=ecwd_subscribe');
    die;
}
if (isset($_POST["ecwd_settings_general"]) && isset($_POST["ecwd_settings_general"]["install_sample_data"]) && $_POST["ecwd_settings_general"]["install_sample_data"] == "1") {
    //sample_data_creator();
}
if (isset($_POST["ecwd_settings_general"])) {
    $sett = get_option("ecwd_settings_general");
    $sett = isset($sett) ? $sett : array();
    $settings = array("date_format", "time_format", "week_starts", "events_slug", "event_slug", "cpt_order", "events_in_popup", "show_repeat_rate");
    foreach ($settings as $set) {
        if (isset($_POST["ecwd_settings_general"][$set])) {
            $sett[$set] = sanitize_text_field($_POST["ecwd_settings_general"][$set]);
        }
    }
    update_option("ecwd_settings_general", $sett);
//    header('Location: ?activate=false&plugin_status=all&paged=1&s=');
    header('Location: admin.php?page=ecwd_subscribe');
    //update_option("activation_page_option", "ok");
}

function sample_data_creator() {

    $posts = get_posts(array(
        'post_type' => 'ecwd_calendar'
            )
    );
    if (count($posts) == 0) {
        $ids = array();
        $cal = array(
            'post_title' => 'CALENDAR 1',
            'post_type' => 'ecwd_calendar',
            'post_status' => 'publish'
        );
        $cal_id = wp_insert_post($cal);
        $ids[] = $cal_id;
        update_post_meta($ids[0], 'ecwd_calendar_theme', $grey_theme_id);
        $ven = array(
            'post_title' => 'VENUE 1',
            'post_type' => 'ecwd_venue',
            'post_status' => 'publish'
        );
        $ids[] = wp_insert_post($ven);
        update_post_meta($ids[1], 'ecwd_venue_lat_long', '51.554448,-0.286331');
        update_post_meta($ids[1], 'ecwd_venue_location', '23A Wembley Hill Rd, Wembley, Greater London HA9 8AS, UK');
        $org1 = array(
            'post_title' => 'ORGANIZER 1',
            'post_type' => 'ecwd_organizer',
            'post_status' => 'publish'
        );
        $org_ids = array();
        $org_ids[] = wp_insert_post($org1);
        $org2 = array(
            'post_title' => 'ORGANIZER 2',
            'post_type' => 'ecwd_organizer',
            'post_status' => 'publish'
        );
        $org_ids[] = wp_insert_post($org2);
        $ids[] = $org_ids;
        $ev1 = array(
            'post_title' => 'EVENT 1',
            'post_type' => 'ecwd_event',
            'post_status' => 'publish'
        );
        $ev_ids = array();
        $ev_ids[] = wp_insert_post($ev1);
        $date1 = ECWD::ecwd_date('Y-m-d H:i', time());
        $date2 = ECWD::ecwd_date("Y-m-d H:i", strtotime("+1 week"));
        $date1 = str_replace('-', "/", $date1);
        $date2 = str_replace('-', "/", $date2);
        update_post_meta($ev_ids[0], 'ecwd_event_date_from', $date1);
        update_post_meta($ev_ids[0], 'ecwd_event_date_to', $date1);
        update_post_meta($ev_ids[0], 'ecwd_event_venue', $ids[1]);
        update_post_meta($ev_ids[0], 'ecwd_event_location', '23A Wembley Hill Rd, Wembley, Greater London HA9 8AS, UK');
        update_post_meta($ev_ids[0], 'ecwd_lat_long', '51.554448,-0.286331');
        $cals = array();
        $cals[0] = (string) $ids[0];
        $orgs = array();
        $orgs[0] = (string) $org_ids[0];
        $orgs[1] = (string) $org_ids[1];
        update_post_meta($ev_ids[0], 'ecwd_event_organizers', $orgs);
        update_post_meta($ev_ids[0], 'ecwd_event_calendars', $cals);
        $ev2 = array(
            'post_title' => 'EVENT 2',
            'post_type' => 'ecwd_event',
            'post_status' => 'publish'
        );
        $ev_ids[] = wp_insert_post($ev2);
        update_post_meta($ev_ids[1], 'ecwd_event_date_from', $date2);
        update_post_meta($ev_ids[1], 'ecwd_event_date_to', $date2);
        update_post_meta($ev_ids[1], 'ecwd_event_venue', $ids[1]);
        update_post_meta($ev_ids[1], 'ecwd_event_calendars', $cals);
        update_post_meta($ev_ids[1], 'ecwd_event_location', '23A Wembley Hill Rd, Wembley, Greater London HA9 8AS, UK');
        update_post_meta($ev_ids[1], 'ecwd_lat_long', '51.554448,-0.286331');
        update_post_meta($ev_ids[1], 'ecwd_event_organizers', $orgs);
        $ids[] = $ev_ids;
        update_option('auto_generated_posts', $ids);
    }
}

function ecwd_settings_init() {
    global $ecwd_settings;
    $useful = array("date_format", "time_format", "week_starts", "events_slug", "event_slug", "cpt_order", "events_in_popup", "show_repeat_rate");
    foreach ($ecwd_settings["general"] as $opt) {
        if (!in_array($opt["id"], $useful)) {
            unset($ecwd_settings["general"][$opt["id"]]);
        }
    }
    $ecwd_settings["general"][] = array(
        'id' => 'install_sample_data',
        'name' => __('Install sample data', 'event-calendar-wd'),
        'desc' => __('Check to install sample data.', 'event-calendar-wd'),
        'type' => 'radio',
        'default' => 1
    );
}

function activation_html_view() {
    ?>

    <style>
        table{
            border: #d7d7d7 solid 1px;
            background-color:#ebebeb;
            display: table;
            color: #959595;
            font-size: 0.875em;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            line-height: 1.5em;
            margin:auto;
        }
        tbody{
            display: table-row-group;
            vertical-align: middle;
            border-color: inherit;
            font-size: 0.875em;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            line-height: 1.5em;
        }
        tr{
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
        }
        th{
            font-weight: bold;
            vertical-align: middle;
            text-align: left;
            padding: 10px 40px;
            display: table-cell;
        }
        td{
            vertical-align: middle;
            text-align: left;
            padding: 7px 12px;
        }
        .submit{
            text-align: center;
        }
        h2{
            text-align: center;
            vertical-align: text-bottom;
            font-size: 2em;
            margin: 0px;
            font-weight: bold;
            color: #878787;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
            line-height: 1.5em;
        }
        a{
            height: auto;
            line-height: 1em;
            padding: 0.5em 1em;
            background: #f7f7f7 none repeat scroll 0 0;
            border-color: #ccc;
            box-shadow: 0 1px 0 #ccc;
            color: #555;
            border-radius: 3px;
            border-style: solid;
            border-width: 1px;
            box-sizing: border-box;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
            white-space: nowrap;
            text-align: center;
        }
        .skip_div{
            text-align: center;
            color: #193954;
            font-size: 0.875em;
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        }
        .pic_div{
            margin-top: 10px;
            background-repeat:no-repeat;
            background-size:  68px 75px;
            margin: auto;
            background-image: url('<?php echo ECWD_URL ?>/assets/CalendarWD.png');
            text-align: center;
            background-position-x: center;
            height: 75px;
        }
        .desc_par{
            margin:0px ;
            margin-bottom:10px;
            font-size:11px;
            color:#878787;
        }
        .skip_wiz_div{
            display: -webkit-inline-box;
            margin-left: 5px;
        }
        input{
            padding-left:8px;
        }
        .description{
            padding-left: 20px;
        }
        select{
            background-color:#ffffff;
        }
        .big_div{
            margin: auto;
        }
        .button_a{
            font-weight: bold;
            font-size: 15px;
            background-color: #959595;
            color: #fff;
            height: auto;
            border: 1px solid transparent;
            padding: 5px 25px;
            border-radius:0px;
        }
        .submit{
            margin: 0px;
            padding-left: 340px;
            padding-top:8px;
        }
        .button-primary{
            background: #959595 !important;
        }
        #submit{
            margin-left: 35px;
        }
    </style>    
    <link rel="stylesheet" href="<?php echo ECWD_URL ?>/css/admin/admin.css">
    <div class="wrap">
        <div id="ecwd-settings">
            <div id="ecwd-settings-content">
                <form method="post">
                    <?php wp_nonce_field('update-options'); ?>
                    <?php
                    settings_fields(ECWD_PLUGIN_PREFIX . '_settings_' . 'general');
                    do_settings_sections(ECWD_PLUGIN_PREFIX . '_settings_' . 'general');
                    ?>
                    <?php submit_button(); ?>

                </form>
            </div>
            <!-- #ecwd-settings-content -->
        </div>
        <!-- #ecwd-settings -->
    </div><!-- .wrap -->
    <script>

        document.getElementById('ecwd_settings_general[date_format]').value = 'Y-m-d';
        document.getElementById('ecwd_settings_general[time_format]').value = 'H:i';
        if(document.getElementsByTagName('h3')[0]){
            document.getElementsByTagName('h3')[0].innerHTML = '';
        }
        else{
            document.getElementsByTagName('h2')[0].innerHTML = '';
        }
        var parent = document.getElementsByTagName("form")[0];
        var html_div = document.createElement("div");
        html_div.className = "big_div";
        var html = '<div class="pic_div"></div>';
        html += '<h2>Welcome to Event Calendar WD<div></div></h2>';
        html += '<div class="skip_div"><p class="desc_par">In this quick wizard well help you with the basic configurations.</p></div>';
        html_div.innerHTML = html;
        var elem = document.getElementsByTagName('table')[0];
        parent.insertBefore(html_div, elem);
        var child_skip = document.createElement("div");
        child_skip.className = "skip_wiz_div";
        child_skip.innerHTML = "<input type='submit' class='button button-primary' value='Skip Wizard' name='skip_wizard'/>";
        var par = document.getElementsByClassName('submit')[0];
        par.appendChild(child_skip);

    </script>
    <?php
    die;
}
