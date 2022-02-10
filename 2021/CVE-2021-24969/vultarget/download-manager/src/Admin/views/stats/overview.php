<?php
global $wpdb;
$db_table_name = "{$wpdb->prefix}ahm_download_stats";

//minimum year data available in the db
$min_year  = $wpdb->get_var("select min(year) as my from $db_table_name");

$pid = isset($_GET['pid']) && $_GET['pid'] != '' ? intval($_GET['pid']) : null;
$pid_filter = $pid ? '1 and pid=' . $pid : '1';

if ($pid) {
    $package = get_post($pid);
}

$current_month = date("m");
$current_year = date("Y");
$current_day = date("d");

$selected_month = isset($_GET['m']) ? intval($_GET['m']) : $current_month;
$selected_year = isset($_GET['y']) ? intval($_GET['y']) : $current_year;

$total_downloads = $wpdb->get_var("select count(id) as total from $db_table_name where $pid_filter");
$total_downloads_month = $wpdb->get_var("select count(id) as total from $db_table_name where $pid_filter and `month` = {$selected_month} and `year` = {$selected_year}");
$total_downloads_year = $wpdb->get_var("select count(id) as total from $db_table_name where $pid_filter and `year` = {$selected_year}");
$total_downloads_date = $wpdb->get_var("select count(id) as total from $db_table_name where $pid_filter and `day` = {$current_day} and `month` = {$selected_month} and `year` = {$selected_year}");

/**
 *  Data for charts
 */
$data = $wpdb->get_results("select day,count(day) as downloads from $db_table_name where year = {$selected_year} and month= {$selected_month} and $pid_filter group by day");

$full_month_data_array = [];
// fill with empty values
for ($i = 1; $i <= 31; $i++) {
    $full_month_data_array[$i] = array($i, 0);
}

//fill with actual data
foreach ($data as $dd) {
    $full_month_data_array[$dd->day] = [
        intval($dd->day),
        intval($dd->downloads)
    ];
}

$full_month_data_array = array_values($full_month_data_array);

//pd($full_month_json_data);

?>
<?php if (isset($package)) : ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php echo "<b>Package: " . $package->post_title . "</b>"; ?>
        </div>
    </div>
<?php endif; ?>

<div class="row">

    <!-- downloads today -->
    <?php if ($current_month == $selected_month && $current_year == $selected_year) : ?>
        <div class="col-md-3">
            <div class="panel panel-default text-center">
                <div class="panel-body">
                    <h2><?php echo $total_downloads_date; ?></h2>
                </div>
                <div class="panel-footer">
                    <?= __('Today\'s downloads', 'download-manager'); ?>
                </div>
            </div>
        </div>
    <?php endif ?>

    <!-- downloads this month -->
    <div class="col-md-3">
        <div class="panel panel-default text-center">
            <div class="panel-body">
                <h2><?php echo $total_downloads_month; ?></h2>
            </div>
            <div class="panel-footer">
                <?php echo wpdm_query_var('m') ? date("F, {$selected_year}", mktime(0, 0, 0, $selected_month, 10)) : __('This month', 'download-manager'); ?>
            </div>
        </div>
    </div>

    <!-- downloads this year -->
    <div class="col-md-3">
        <div class="panel panel-default text-center">
            <div class="panel-body">
                <h2><?php echo $total_downloads_year; ?></h2>
            </div>
            <div class="panel-footer">
                <?php echo wpdm_query_var('y') ? "Year {$selected_year}" : __('This year', 'download-manager'); ?>
            </div>
        </div>
    </div>

    <!-- Total downloads -->
    <div class="col-md-3">
        <div class="panel panel-default text-center">
            <div class="panel-body">
                <h2><?php echo $total_downloads; ?></h2>
            </div>
            <div class="panel-footer">
                <?php _e('Total Downloads', 'download-manager'); ?>
            </div>
        </div>
    </div>
</div>


<div class="panel panel-default">
    <!-- chart -->
    <div id="monthlyoverview" class="panel-body" style="height: 500px"></div>

    <!-- Filters form -->
    <div class="panel-footer text-center">
        <form method="get" action="edit.php">
            <!-- hidden fields -->
            <input type="hidden" name="post_type" value="wpdmpro">
            <input type="hidden" name="page" value="wpdm-stats">

            <!-- YEAR selection -->
            &nbsp; Year:
            <select name="y" class="form-control wpdm-custom-select" style="width: 80px; min-width: 60px;display: inline;">
                <?php for ($i = $min_year; $i <= date('Y'); $i++) {
                    $selected = $selected_year == $i ? 'selected=selected' : '';
                    echo "<option $selected value='{$i}'>{$i}</option>";
                } ?>
            </select>

            <!-- MONTH selection -->
            &nbsp; Month: <select class="form-control wpdm-custom-select" name="m" style="min-width: 145px;width: 60px;display: inline;">
                <?php for ($i = 1; $i <= 12; $i++) {
                    $selected = $selected_month == $i ? 'selected=selected' : '';
                    $month_name = date("F", mktime(0, 0, 0, $i, 10));
                    echo "<option $selected value='{$i}'>{$month_name}</option>";
                } ?>
            </select>
            <input type="submit" class="btn btn-secondary" value="Submit">

        </form>
    </div>
</div>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {
        'packages': ['corechart']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('number', 'Date');
        data.addColumn('number', 'Downloads');
        data.addRows(<?= json_encode($full_month_data_array); ?>);

        var options = {
            curveType: 'function',
            legend: {
                position: 'bottom'
            },
            backgroundColor: {
                fill: '#ffffff'
            }
        };

        var chart = new google.visualization.AreaChart(document.getElementById('monthlyoverview'));
        chart.draw(data, options);
    }
</script>
