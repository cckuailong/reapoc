<?php if ( ! defined( 'ABSPATH' ) ) exit;

if (!function_exists('ulpb_loadAnaytics')) {
function  ulpb_loadAnaytics($postID, $dateRange = 7){

    $pluginOpsUserTimeZone = get_option('timezone_string');
    if ($pluginOpsUserTimeZone != '' && $pluginOpsUserTimeZone != null) {
    date_default_timezone_set($pluginOpsUserTimeZone);
    }
    $todaysDate =  date('d-m-Y');

    $uniqueImpressions = get_post_meta($postID,'ulpb_page_hit_counter',true);
    $allImpressions = get_post_meta($postID,'ulpb_page_views_counter',true);
    $totalConversions = get_post_meta($postID,'ssm_conversion_count',true);

    $uniqueImpressionsToday = get_post_meta($postID,"ulpb_page_hit_counter_$todaysDate",true);
    $allImpressionsToday = get_post_meta($postID,"ulpb_page_views_counter_$todaysDate",true);

    if ($uniqueImpressions == '') {
        $uniqueImpressions = 0;
    }
    if ($allImpressions == '') {
        $allImpressions = 0;
    }
    if ($allImpressions > 0) {
        $allImpressions = $allImpressions/2;
    }

    if ($totalConversions == '') {
        $totalConversions = 0;
    }

    if ($totalConversions > 0 && $allImpressions > 0) {
        $conversionRate = ((int)$totalConversions / $allImpressions)*100;
    } else{
        $conversionRate = 0;
    }
    $conversionRate =  round( $conversionRate, 1, PHP_ROUND_HALF_UP);


    // Divide conversions by date.
    $ssm_subscribers_list = get_post_meta($postID,'ssm_subscribers_list',true);
    $smfb_formBuilder_data_list = get_post_meta($postID,'ulpb_formBuilder_data_submission',true);


    $numberOfConversions = array();
    $lastThirtyDates = array();
    $lastThirtyDatesForChart = array();
    $lastThirtyDaysImpressions = array();
    for ($i=0; $i <=$dateRange ; $i++) {
        $numberOfConversions[$i] = 0;
        $lastThirtyDates[$i] = date('d-m-Y',strtotime("-$i days"));
        $lastThirtyDatesForChart[$i] = date('d-M',strtotime("-$i days"));

        $thisDate = date('d-m-Y',strtotime("-$i days"));
        $lastThirtyDaysImpressions[$i] = get_post_meta($postID,"ulpb_page_views_counter_$thisDate",true);
        if ($lastThirtyDaysImpressions[$i] > 0) {
           $lastThirtyDaysImpressions[$i] = $lastThirtyDaysImpressions[$i] / 2;
        }
    }

    if (is_array($ssm_subscribers_list)) {
        foreach ($ssm_subscribers_list as $ssm_result) {
            if (isset($ssm_result['date']) ) {
              $dateOfssm = $ssm_result['date'];
            }else{
              $dateOfssm = 'Not Set';
            }

            for ($i=0; $i <=$dateRange ; $i++) {
                if ($dateOfssm == $lastThirtyDates[$i]) {
                    $numberOfConversions[$i]++;
                }
            }
                
        }
    }
        

    if (is_array($smfb_formBuilder_data_list)) {
        foreach ($smfb_formBuilder_data_list as $smfb_formBuilder_each_data) {
            if (isset($smfb_formBuilder_each_data['date']) ) {
              $dateOfssm = $smfb_formBuilder_each_data['date'];
            }else{
              $dateOfssm = 'Not Set';
            }

            for ($i=0; $i <=$dateRange ; $i++) {
                if ($dateOfssm == $lastThirtyDates[$i]) {
                    $numberOfConversions[$i]++;
                }
            }
            
        }
    }




    $returnArray = array();
    $returnArray['uniqueImpressions'] = $uniqueImpressions;
    $returnArray['allImpressions'] = $allImpressions;
    $returnArray['conversionRate'] = $conversionRate;
    $returnArray['totalConversions'] = $totalConversions;
    $returnArray['lastThirtyDatesForChart'] = $lastThirtyDatesForChart;
    $returnArray['numberOfConversions'] = $numberOfConversions;
    $returnArray['lastThirtyDaysImpressions'] = $lastThirtyDaysImpressions;
    
    return $returnArray;
}

} // func check 


if (!function_exists('ulpb_RenderAnalytics')) {
function ulpb_RenderAnalytics($postID, $loadGraphs, $uniqID = 'default', $dateRange = 7){

    $defaultPageAnalytics = ulpb_loadAnaytics($postID, $dateRange);
    $lastThirtyDatesForChart = $defaultPageAnalytics['lastThirtyDatesForChart'];
    $numberOfConversions = $defaultPageAnalytics['numberOfConversions'];
    $lastThirtyDaysImpressions = $defaultPageAnalytics['lastThirtyDaysImpressions'];
    ob_start();
    ?>

    <div id="pluginops_analytics" style="margin:0 auto; padding:1% 12.5%; background: #E7E7E7;">
        <div class="analytics-card">
            <h3> Unique Impressions </h3>
            <p> <?php echo $defaultPageAnalytics['uniqueImpressions']; ?> </p>    
        </div>
        <div class="analytics-card">
            <h3> All Impressions </h3>
            <p> <?php echo $defaultPageAnalytics['allImpressions']; ?> </p>
        </div>
        <div class="analytics-card">
            <h3> Conversion Rate </h3>
            <p> <?php  echo $defaultPageAnalytics['conversionRate'] ?> % </p>
        </div>
        <div class="analytics-card" >
            <h3> Total Conversions </h3>
            <p> <?php  echo $defaultPageAnalytics['totalConversions'] ?> </p>
        </div>
        <?php 
        if ($loadGraphs == true) { ?>
        <div class="analytics-card" style="">
            <canvas id="sevenDayConversionImpressions_<?php echo $uniqID; ?>" width="250" height="250"></canvas>      
        </div>
        <div class="analytics-card" style="">
            <canvas id="sevenDayConversionRate_<?php echo $uniqID; ?>" width="250" height="250"></canvas>      
        </div>
    <?php } ?>
    </div>
    
    <?php 
    if ($loadGraphs == true) { ?>
    <script>

    var lastSevenDates_<?php echo $uniqID; ?> = [<?php for ($i=0; $i <$dateRange ; $i++) { echo "'".$lastThirtyDatesForChart[$i]."',";} ?>];
    lastSevenDates_<?php echo $uniqID; ?>.reverse();
    var lastSevenData_<?php echo $uniqID; ?> = [<?php for ($i=0; $i <$dateRange ; $i++) { echo "'".$numberOfConversions[$i]."',";} ?>];
    lastSevenData_<?php echo $uniqID; ?>.reverse();

    var lastSevenDataImpressions_<?php echo $uniqID; ?> = [<?php for ($i=0; $i <$dateRange ; $i++) { 
        if ($lastThirtyDaysImpressions[$i] > 0) {
            $impression = $lastThirtyDaysImpressions[$i];
        }else{
            $impression = 0;
        }
        echo "'".$impression."',";} ?>];
    lastSevenDataImpressions_<?php echo $uniqID; ?>.reverse();

    var lastSevenDataConversionRate_<?php echo $uniqID; ?> = [ <?php for ($i=0; $i <$dateRange ; $i++) {
        $Noc = $numberOfConversions[$i];
        $Ltdi = $lastThirtyDaysImpressions[$i];
        if ($numberOfConversions[$i] > 0 && $lastThirtyDaysImpressions[$i] > 0) {
        $conversionRate = ((int)$numberOfConversions[$i] / $lastThirtyDaysImpressions[$i])*100;
        $conversionRate =  round( $conversionRate, 1, PHP_ROUND_HALF_UP);
        } else{
            $conversionRate = 0;
        }
        echo "'".$conversionRate."',";} ?>];
    lastSevenDataConversionRate_<?php echo $uniqID; ?>.reverse();


    var lineChartData_<?php echo $uniqID; ?> = {
        labels: lastSevenDates_<?php echo $uniqID; ?>,
        datasets: [{
            label: "Conversions",
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1.5,
            backgroundColor: 'rgba(54, 162, 235, 0.4)',
            fill: true,
            data:lastSevenData_<?php echo $uniqID; ?>,
            yAxisID: "y-axis-1",
        }, {
            label: "Impressions",
            borderColor: '#ee2c47',
            borderWidth: 1.5,
            backgroundColor: 'rgba(232, 81, 101, 0.4)',
            fill: true,
            data: lastSevenDataImpressions_<?php echo $uniqID; ?>,
            yAxisID: "y-axis-2"
        }]
    };
        var ctx_<?php echo $uniqID; ?> = document.getElementById("sevenDayConversionImpressions_<?php echo $uniqID; ?>").getContext("2d");
        var chartOne_<?php echo $uniqID; ?> = Chart.Line(ctx_<?php echo $uniqID; ?>, {
            type: 'line',
            data: lineChartData_<?php echo $uniqID; ?>,
            options: {
                responsive: true,
                hoverMode: 'index',
                stacked: false,
                title: {
                    display: true,
                    text: 'Last <?php echo "$dateRange"; ?> Days Conversions & Impressions'
                },
                scales: {
                    yAxes: [{
                        type: "linear", 
                        display: true,
                        position: "left",
                        id: "y-axis-1",
                    }, {
                        type: "linear", 
                        display: true,
                        position: "right",
                        id: "y-axis-2",
                        gridLines: {
                            drawOnChartArea: false,
                        },
                    }],
                }
            }
        });


        var ctxtwo_<?php echo $uniqID; ?> = document.getElementById("sevenDayConversionRate_<?php echo $uniqID; ?>").getContext('2d');
        var chartTwo_<?php echo $uniqID; ?> = new Chart(ctxtwo_<?php echo $uniqID; ?>, {
            type: 'line',
            data: {
                labels: lastSevenDates_<?php echo $uniqID; ?>,
                datasets: [{
                    label: 'Conversion Rate %',
                    data: lastSevenDataConversionRate_<?php echo $uniqID; ?>,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    </script>
    <?php
    }

    $rendderredAnalytics = ob_get_contents();
    ob_end_clean();

    return $rendderredAnalytics;

}

} // func check


?>