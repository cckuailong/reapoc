<?php
$get_params = $_GET;

global $wp_rewrite, $wp_query, $wpdb;

$states_table = "{$wpdb->prefix}ahm_download_stats";
$posts_table = "{$wpdb->prefix}posts";

/**
 * GET Parameters
 */
$from_date_string = wpdm_query_var('from_date'); // format Y-m-d 2020-05-31
$to_date_string = wpdm_query_var('to_date');
$pid = wpdm_query_var('pid', 'int');
$uid = wpdm_query_var('uid', 'int');

$min_timestamp = $wpdb->get_var("select min(timestamp) from $states_table") ?: time();

// DateTime Objects
$selected_from_date = $from_date_string ? new DateTime($from_date_string) : (new DateTime())->setTimestamp($min_timestamp);
$selected_to_date = $to_date_string ? (new DateTime($to_date_string)) : (new DateTime())->setTimestamp(time());
// we need to get the timestamp of the end of selected to_date
$selected_to_date->modify('tomorrow');
$end_of_selected_to_date_timestamp = $selected_to_date->getTimestamp() - 1;
$selected_to_date->setTimestamp($end_of_selected_to_date_timestamp);
//pd($selected_to_date->getTimestamp());

$timestamp_filter = "s.timestamp >= {$selected_from_date->getTimestamp()}  and s.timestamp <= {$selected_to_date->getTimestamp()}";
$pid_filter = ($pid > 0) ? " and s.pid = '{$pid}'" : "";
$uid_filter = ($uid > 0) ? " and s.uid = '{$uid}'" : "";

$items_per_page = 30;
$start = isset($_GET['page_no']) ? ((int)$_GET['page_no'] - 1) * $items_per_page : 0;

//pd($selected_from_date->getTimestamp());
$limit = 10;
$top_packages = $wpdb->get_results("select p.post_title, s.pid, count(s.pid) as total_downloaded from $posts_table p, $states_table s where s.pid = p.ID 
                                    and {$timestamp_filter}  
                                    group by s.pid 
                                    order by `total_downloaded` desc limit $limit ");

$top_users = $wpdb->get_results("select s.uid, count(s.pid) as total_downloaded from $states_table s
                                    where {$timestamp_filter}  
                                    group by s.uid 
                                    order by `total_downloaded` desc limit $limit ");



$top_packages_array = [];
foreach ($top_packages as $p) {
    array_push($top_packages_array, [$p->post_title, intval($p->total_downloaded)]);
}
//pd($top_packages_array);

?>

<form method="get" action="<?php echo admin_url('edit.php'); ?>">

    <!-- hidden fields. Necessary for navigating this page -->
    <input type="hidden" name="post_type" value="wpdmpro" />
    <input type="hidden" name="page" value="wpdm-stats" />
    <input type="hidden" name="type" value="insight" />


    <!-- Top filters  -->
    <div class="row">

        <!-- From date -->
        <div class="col-md-3">
            <div class="input-group input-group-lg">
                <div class="input-group-addon">
                    <span class="input-group-text"><?php _e("From Date", "download-manager"); ?>:</span>
                </div>
                <input type="text" name="from_date" value="<?= $selected_from_date->format('Y-m-d') ?>" class="datepicker form-control bg-white text-right" readonly="readonly" />
            </div>
        </div>

        <!-- To date -->
        <div class="col-md-3">
            <div class="input-group input-group-lg">
                <div class="input-group-addon">
                    <span class="input-group-text"><?php _e("To Date", "download-manager"); ?>:</span>
                </div>
                <input type="text" name="to_date" value="<?= $selected_to_date->format('Y-m-d') ?>" class="datepicker form-control bg-white text-right" readonly="readonly" />
            </div>
        </div>

        <!-- Filter submit button -->
        <div class="col-md-2">
            <div class="btn-group">
                <button type="submit" class="btn btn-primary btn-lg"><?php echo __("Filter", "download-manager") ?></button>
                <a href="edit.php?post_type=wpdmpro&page=wpdm-stats&type=insight" class="btn btn-danger btn-lg"><?php echo __("Reset", "download-manager") ?></a>
            </div>
        </div>
    </div>
</form>


<div class="row" style="margin-top: 20px;">

</div>
<div class="row" style="margin-top: 20px;">
    <section class="col-md-6">
        <div class="panel panel-default dashboard-panel">
            <div class="panel-heading">
                <?= esc_attr__( 'Top Packages', WPDM_TEXT_DOMAIN ); ?>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="bg-white"><?php _e("Package Name", "download-manager"); ?></th>
                        <th class="bg-white"><?php _e("Downloaded", "download-manager"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_packages as $package) : ?>
                        <tr>
                            <!-- Package -->
                            <td>
                                <a title='View this package' class='ttip' href="<?php echo get_permalink($package->pid); ?>">
                                    <?php echo $package->post_title; ?>
                                </a>
                                <div class="show-on-hover pull-right">
                                    <a target='_blank' href="post.php?action=edit&post=<?php echo $package->pid; ?>" title='Edit Package' class='ttip'>
                                        <i class="fas fa-pen-square"></i>
                                    </a>
                                    &mdash;
                                    <a target='_blank' href="<?php echo get_permalink($package->pid); ?>" title='View Package' class='ttip'>
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <?= $package->total_downloaded . ' times' ?>
                            </td>
                        </tr>
                    <?php endforeach ?>

                </tbody>
            </table>
        </div>
    </section>

    <section class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="top-packages-chart" style="min-height: 400px;"></div>
            </div>
        </div>
    </section>
</div>

<div class="row">
    <!-- Top Users -->
    <section class="col-md-12">
        <div class="panel panel-default dashboard-panel">
            <div class="panel-heading">
                <?= esc_attr__( 'Top Users', WPDM_TEXT_DOMAIN ); ?>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="bg-white"><?php _e("User name", "download-manager"); ?></th>
                        <th class="bg-white"><?php _e("Downloaded", "download-manager"); ?></th>
                        <th class="bg-white"><?php _e("Last Active", "download-manager"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_users as $user) : ?>
                        <?php
                        $display_name = 'Guest';
                        if ($user->uid > 0) {
                            $u = get_user_by('id', $user->uid);
                            if (is_object($u))
                                $display_name = $u->display_name;
                            else
                                $display_name = esc_attr__( '[ Deleted User ]', 'download-manager' );
                        }
                        ?>
                        <tr>
                            <!-- Package -->
                            <td>
                                <a title='View this package' class='ttip' href="#">
                                    <?php echo $display_name; ?>
                                </a>
                            </td>
                            <td>
                                <?= $user->total_downloaded . ' times' ?>
                            </td>
                            <td>
                               <?= wp_date( get_option('date_format'), get_user_meta($user->uid, '__wpdm_last_login_time', true) ); ?>
                            </td>
                        </tr>
                    <?php endforeach ?>

                </tbody>
            </table>
        </div>
    </section>

    <section class="col-md-5">
        <div id="top-users-chart"></div>

    </section>
</div>

<style>
    .w3eden .panel th.bg-white {
        background: #ffffff !important;
    }
</style>

<script>
    jQuery(function($) {
        $(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: new Date(<?= intval($min_timestamp) * 1000 ?>),
            maxDate: new Date()
        });
    });
</script>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {
        'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', '<?=esc_attr__( 'Package Name', 'download-manager' );?>');
        data.addColumn('number', '<?=esc_attr__( 'Downloads', 'download-manager' ); ?>');
        data.addRows(<?= json_encode($top_packages_array) ?>);

        var options = {
            title: "<?=esc_attr__( 'Top Packages', 'download-manager' );?>",
            width: "50%",
            curveType: 'function',
            bars: 'horizontal',
            bar: {
                groupWidth: "90%"
            },
            legend: {
                position: 'bottom'
            },
            backgroundColor: {
                fill: '#ffffff'
            }
        };

        var chart = new google.charts.Bar(document.getElementById('top-packages-chart'));
        chart.draw(data, options);
    }
</script>
