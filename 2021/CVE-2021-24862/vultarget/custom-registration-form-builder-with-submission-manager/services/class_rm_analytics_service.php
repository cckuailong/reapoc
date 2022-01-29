<?php

/**
 *
 *
 * @author CMSHelplive
 */
class RM_Analytics_Service extends RM_Services
{

    public function get_all($model_identifier = null, $offset = 0, $limit = 15, $column = '*', $sort_by = '', $descending = false)
    {
        return parent::get_all($model_identifier, $offset, $limit, $column, $sort_by, $descending);
    }

    public function get_form_stats($form_id, $offset = 0, $limit = 99999)
    {
        return RM_DBManager::get('STATS', array('form_id' => $form_id), array('%d'), 'results', $offset, $limit, '*', 'visited_on', true);
    }

    public function get_average_filling_time($form_id)
    {
        $avg = RM_DBManager::get_average_value('STATS', 'time_taken', array('form_id' => $form_id));

        //Round up to 2 digits after decimal point.
        return round($avg, 2);
    }

    public function reset($form_id)
    {
        //RM_DBManager::delete_and_reset_table('STATS');
        RM_DBManager::delete_rows('STATS', array('form_id' => $form_id), array('%d'));
    }

    public function get_field_stats($form_id)
    {
        if(defined('REGMAGIC_ADDON'))
            $fields = RM_DBManager::get_fields_filtered_by_types($form_id, array('Select','Checkbox','Radio','Country','Gender','Multi-Dropdown'));
        else
            $fields = RM_DBManager::get_fields_filtered_by_types($form_id, array('Select', 'Checkbox', 'Radio', 'Country'));
        $stat_arr = array();

        $subf_table = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');
        $subs_table = RM_Table_Tech::get_table_name_for('SUBMISSIONS');

        if (is_array($fields) || is_object($fields))
            foreach ($fields as $field) {
                $res = array();
                $tmp_obj = new stdClass;

                $vals = maybe_unserialize($field->field_value);
                $tmp_obj->label = $field->field_label;
                $tmp_obj->total_sub = 0;

                switch ($field->field_type)
                {
                    case 'Checkbox':
                        if(defined('REGMAGIC_ADDON'))
                            $vals = RM_Utilities::process_field_options($vals);
                        $temp = RM_DBManager::run_query("SELECT value FROM `$subf_table` sf, `$subs_table` ss WHERE  `field_id` = $field->field_id AND sf.submission_id = ss.submission_id AND ss.child_id = 0 ORDER BY `sub_field_id`", 'col');

                        if (!$temp)
                            break;

                        foreach ($vals as $val)
                        {
                            $res[$val] = 0;
                        }

                        $other_option_submission = 0;

                        foreach ($temp as $single_sub)
                        {
                            if ($single_sub == NULL)
                                continue;
                            $single_sub = maybe_unserialize($single_sub);

                            //Fix for rare case of CRF migration not working properly
                            if ($single_sub && !is_array($single_sub))
                                $single_sub = explode(',', $single_sub);

                            foreach ($single_sub as $f_v) {
                                if(defined('REGMAGIC_ADDON'))
                                    $f_v = stripslashes($f_v);
                                if (isset($vals[$f_v], $res[$f_v]))
                                    $res[$f_v] += 1;
                                else
                                    $other_option_submission++;
                            }
                        }

                        $res['Other'] = $other_option_submission;

                        //IMPORTANT: Checkbox is a multiple value type submission,
                        //hence we can't just add up the submissions of individual option to get the total submissions.              
                        $tmp_obj->total_sub = (int) RM_DBManager::run_query("SELECT COUNT(sub_field_id) FROM `$subf_table` sf, `$subs_table` ss WHERE `field_id` = $field->field_id AND sf.submission_id = ss.submission_id AND ss.child_id = 0", 'var');

                        break;

                    case 'Country':
                        $temp = RM_DBManager::run_query("SELECT `value`, COUNT(*) AS `count` FROM `$subf_table` sf, `$subs_table` ss WHERE `field_id` = $field->field_id AND sf.submission_id = ss.submission_id AND ss.child_id = 0 GROUP BY `value`");

                        if (!$temp)
                            break;

                        foreach ($temp as $single_sub)
                        {
                            $res[$single_sub->value] = (int) $single_sub->count;
                            $tmp_obj->total_sub += (int) $single_sub->count;
                        }

                        //Compensate for the cases when Country field was not submitted (blank values).
                        if (isset($res[''])) {
                            $tmp_obj->total_sub -= $res[''];
                            //We dont need this in stat, unset it.
                            unset($res['']);
                        }

                        break;
                    case 'Gender':
                         //Set options for Gender field and let it fall through. Note that it must be same as in the Field Factory caode for Gender field.
                         $vals = array("Male" => RM_UI_Strings::get("LABEL_GENDER_MALE"), "Female" => RM_UI_Strings::get("LABEL_GENDER_FEMALE"));
                    case 'Radio':
                    case 'Select':
                        if(defined('REGMAGIC_ADDON'))
                            $vals = RM_Utilities::process_field_options($vals);
                        else
                            if ($field->field_type == 'Select')
                                $vals = explode(',', $field->field_value);

                        $temp = RM_DBManager::run_query("SELECT `value`, COUNT(*) AS `count` FROM `$subf_table` sf, `$subs_table` ss WHERE `field_id` = $field->field_id AND sf.submission_id = ss.submission_id AND ss.child_id = 0 GROUP BY `value`");

                        if (!$temp)
                            break;

                        $other_option_submission = 0;
                        foreach ($temp as $single_sub)
                        {
                            if(defined('REGMAGIC_ADDON'))
                                $single_sub->value = stripslashes($single_sub->value);
                            if (in_array($single_sub->value, $vals))
                            {
                                $res[$single_sub->value] = (int) $single_sub->count;
                                $tmp_obj->total_sub += (int) $single_sub->count;
                            }
                            elseif ($single_sub->value !== null)
                            {
                                $other_option_submission += (int) $single_sub->count;
                                $tmp_obj->total_sub += (int) $single_sub->count;
                            }
                        }

                        if ($field->field_type == 'Radio')
                            $res['Other'] = $other_option_submission;

                        break;
                        
                        case 'Multi-Dropdown':
                            $temp = RM_DBManager::run_query("SELECT `value`, COUNT(*) AS `count` FROM `$subf_table` sf, `$subs_table` ss WHERE `field_id` = $field->field_id AND sf.submission_id = ss.submission_id AND ss.child_id = 0 GROUP BY `value`");

                            if(!$temp)
                              break;

                            foreach($temp as $single_sub)
                            {
                                $sub_values=maybe_unserialize($single_sub->value);
                                if(is_array($sub_values))
                                {
                                    foreach($sub_values as $values)
                                    {
                                            if (array_key_exists($values,$res))
                                            {
                                                 $res[$values] = $res[$values]+(int)$single_sub->count;;
                                            }
                                            else
                                            {
                                                $res[$values] = (int)$single_sub->count;
                                            }
                                    }
                                    $tmp_obj->total_sub += (int)$single_sub->count;
                                }
                            }

                           break;
                }
                $tmp_obj->sub_stat = $res;
                $stat_arr[] = $tmp_obj;
            }
        return $stat_arr;
    }

    public function get_browser_usage($form_id)
    {
        $browsers['Chrome'] = (object) array("visits" => 0, "submissions" => 0);
        $browsers['Edge'] = (object) array("visits" => 0, "submissions" => 0);
        $browsers['Safari'] = (object) array("visits" => 0, "submissions" => 0);
        $browsers['Internet Explorer'] = (object) array("visits" => 0, "submissions" => 0);
        $browsers['Opera'] = (object) array("visits" => 0, "submissions" => 0);
        $browsers['Android'] = (object) array("visits" => 0, "submissions" => 0);
        $browsers['iPhone'] = (object) array("visits" => 0, "submissions" => 0);
        $browsers['Firefox'] = (object) array("visits" => 0, "submissions" => 0);
        $browsers['BlackBerry'] = (object) array("visits" => 0, "submissions" => 0);
        $browsers['Other'] = (object) array("visits" => 0, "submissions" => 0);


        $temp = RM_DBManager::count_multiple('STATS', "browser_name", array('form_id' => $form_id), array('browser_name' => 'Chrome,Edge,Safari,Opera,Internet Explorer,Firefox,iPhone,Android,BlackBerry'));
        $known_browser_visits = array();
        $total_known_browser_visits = 0;

        foreach ($temp as $stat)
        {
            $known_browser_visits[$stat->browser_name] = (int) $stat->count;
            $browsers[$stat->browser_name]->visits = (int) $stat->count;
            $total_known_browser_visits += $known_browser_visits[$stat->browser_name];
        }
        $browsers['Other']->visits = (int) $total_known_browser_visits;

        $temp = RM_DBManager::count_multiple('STATS', "browser_name", array('form_id' => $form_id, 'submitted_on' => 'not null', 'submitted_on' => '!banned'), array('browser_name' => 'Chrome,Edge,Safari,Opera,Internet Explorer,Firefox,iPhone,Android,BlackBerry'));
        $known_browser_submissions = array();
        $total_known_browser_submissions = 0;
//echo "<pre>",var_dump($browsers),"</pre>";

        foreach ($temp as $stat)
        {
            $known_browser_submissions[$stat->browser_name] = (int) $stat->count;
            $browsers[$stat->browser_name]->submissions = (int) $stat->count;
            $total_known_browser_submissions += $known_browser_submissions[$stat->browser_name];
        }
        $browsers['Other']->submissions = (int) $total_known_browser_submissions;
        //    echo "<pre>",var_dump($browsers),"</pre>";

        $usage = new stdClass;

        $usage->browser_submission = $known_browser_submissions;
        $usage->total_known_browser_submission = $total_known_browser_submissions;
        $usage->browser_usage = $known_browser_visits;
        $usage->total_known_browser_usage = $total_known_browser_visits;
        $usage->browsers = $browsers;

        return $usage;
    }

    public function day_wise_submission_stats($form_id, $days)
    {
        $data = new stdClass;
        //global $wpdb;
        //Specifiying date format here is crucial as we can not rely upon default format being always same across different systems, and it is used to compare in php
        $sel_c = "DATE(FROM_UNIXTIME(`visited_on`,'%Y-%m-%d')) date, COUNT(stat_id) count";
        $wh_c = "`form_id` = $form_id GROUP BY DATE(FROM_UNIXTIME(`visited_on`,'%Y-%m-%d')) ORDER BY DATE(FROM_UNIXTIME(`visited_on`,'%Y-%m-%d')) DESC LIMIT $days";
       
        $visits = RM_DBManager::get_generic('STATS', $sel_c, $wh_c, OBJECT_K);

        $sel_c = "DATE(FROM_UNIXTIME(`submitted_on`,'%Y-%m-%d')) date, COUNT(stat_id) count";
        $wh_c = "`form_id` = $form_id GROUP BY DATE(FROM_UNIXTIME(`submitted_on`,'%Y-%m-%d')) ORDER BY DATE(FROM_UNIXTIME(`submitted_on`,'%Y-%m-%d')) DESC LIMIT $days";

        $submissions = RM_DBManager::get_generic('STATS', $sel_c, $wh_c, OBJECT_K);

        for ($n = $days - 1; $n >= 0; $n--)
        {
            $date = gmdate('Y-m-d', strtotime("-$n days"));
            $fdate = gmdate('d M', strtotime("-$n days"));
            $day_data = new stdClass;

            $day_data->visits = 0;
            $day_data->submissions = 0;

            if (isset($visits[$date]))
                $day_data->visits = $visits[$date]->count;

            if (isset($submissions[$date]))
                $day_data->submissions = $submissions[$date]->count;

            $data->$fdate = $day_data;
        }

        return $data;
    }
    
    public function day_wise_login_stats($days)
    {
        $data = new stdClass;
        //global $wpdb;
        //Specifiying date format here is crucial as we can not rely upon default format being always same across different systems, and it is used to compare in php
        $sel_c = "DATE_FORMAT(`time`,'%Y-%m-%d') date, COUNT(id) count";
        $wh_c =  "`status` = 1 GROUP BY DATE_FORMAT(`time`,'%Y-%m-%d') ORDER BY DATE_FORMAT(`time`,'%Y-%m-%d') DESC LIMIT $days";
        
        $success = RM_DBManager::get_generic('LOGIN_LOG', $sel_c, $wh_c, OBJECT_K);
        
        $wh_c =  "`status` = 0 GROUP BY DATE_FORMAT(`time`,'%Y-%m-%d') ORDER BY DATE_FORMAT(`time`,'%Y-%m-%d') DESC LIMIT $days";
         
        $fail = RM_DBManager::get_generic('LOGIN_LOG', $sel_c, $wh_c, OBJECT_K);      
       
        for($n=$days-1; $n>=0; $n--)
        {
            $date = gmdate('Y-m-d',strtotime("-$n days"));
            $fdate = gmdate('d M',strtotime("-$n days"));
            $day_data = new stdClass;
            
            $day_data->success = 0;
            $day_data->fail = 0;
            
            if(isset($success[$date]))
                $day_data->success = $success[$date]->count;
            
            if(isset($fail[$date]))
                $day_data->fail = $fail[$date]->count;
            
            $data->$fdate = $day_data;
        }
        return $data;
    }

    public function get_visitors_count($form_id)
    {
        return RM_DBManager::get_visitors_count($form_id);
    }

    public function form_stats($form_id, $time_range = 7)
    {
        $data = new stdClass;

        $data->forms = RM_Utilities::get_forms_dropdown($this);
        $data->current_form_id = $form_id;
        $data->timerange = $time_range;
        $data->analysis = $this->calculate_form_stats($data->current_form_id);

        $total_entries = $data->analysis->total_entries;

        if ($data->timerange > 90)
            $data->timerange = 90;

        $data->day_wise_stat = $this->day_wise_submission_stats($data->current_form_id, $data->timerange);
        return $data;
    }

    public function calculate_form_stats($form_id)
    {
        $data = new stdClass;
        $total_entries = (int) $this->count('STATS', array('form_id' => (int) $form_id));

        //Average and failure rate
        $failed_submission = (int) $this->count('STATS', array('form_id' => (int) $form_id, 'submitted_on' => null));

        if ($total_entries != 0)
            $data->failure_rate = round((double) $failed_submission * 100.00 / (double) $total_entries, 2);
        else
            $data->failure_rate = 0.00;

        $data->avg_filling_time = $this->get_average_filling_time((int) $form_id);

        $banned_submission = (int) $this->count('STATS', array('form_id' => (int) $form_id, 'submitted_on' => 'banned'));

        //Total = Successful + Failed + Banned
        $data->total_entries = $total_entries;
        $data->failed_submission = $failed_submission;
        $data->banned_submission = $banned_submission;
        $data->successful_submission = $total_entries - $failed_submission - $banned_submission;

        $browser_stats = $this->get_browser_usage($form_id);
        $data->browsers = $browser_stats->browsers; //browser_submission;
        $data->browsers['Other']->visits = $total_entries - $browser_stats->total_known_browser_usage;
        $data->browsers['Other']->submissions = $total_entries - $failed_submission - $browser_stats->total_known_browser_submission;
        return $data;
    }

    // Renders conversion chart
    public function conversion_chart($form_id, $data = null)
    {
        if ($data == null)
            $data = $this->form_stats($form_id);
        $dataset = array(RM_UI_Strings::get('LABEL_FAILED_SUBMISSIONS') => $data->analysis->failed_submission,
        RM_UI_Strings::get('LABEL_BANNED_SUBMISSIONS') => $data->analysis->banned_submission,
        RM_UI_Strings::get('LABEL_SUBMISSIONS') => $data->analysis->successful_submission);

        $json_table = RM_Utilities::create_json_for_chart(RM_UI_Strings::get('LABEL_SUBMISSIONS'), RM_UI_Strings::get('LABEL_FAILED_SUBMISSIONS'), $dataset);
        $chart_data = array(
            'table' => json_decode($json_table),
            'title' => strtoupper(RM_UI_Strings::get('LABEL_TOTAL_VISITS') . " " . $data->analysis->total_entries)
        );
        wp_enqueue_script('google_charts', 'https://www.gstatic.com/charts/loader.js');
        wp_enqueue_script("rm_chart_widget", RM_BASE_URL . "public/js/google_chart_widget.js");
        if (isset($_GET['action']) && $_GET['action'] == 'registrationmagic_embedform') {
            echo '<script>var rm_chart_conversion_data=' . json_encode($chart_data) . '; </script>';
        } else {
            wp_localize_script('rm_chart_widget', 'rm_chart_conversion_data', $chart_data);
        }
    }

    // Renders browser usage chart
    public function browser_usage_chart($form_id, $data = null)
    {
        if ($data == null)
            $data = $this->form_stats($form_id);
        $dataset = array();
        foreach ($data->analysis->browsers as $name => $usage)
        {
            $formatted_name = RM_UI_Strings::get('LABEL_BROWSER_' . strtoupper($name));
            $dataset[$formatted_name] = $usage->visits;
        }

        wp_enqueue_script('google_charts', 'https://www.gstatic.com/charts/loader.js');
        wp_enqueue_script("rm_chart_widget", RM_BASE_URL . "public/js/google_chart_widget.js");
        $json_table = RM_Utilities::create_json_for_chart(RM_UI_Strings::get('LABEL_BROWSER'), RM_UI_Strings::get('LABEL_HITS'), $dataset);
        ?>
        <script>
            function browser_usage_chart() {
                var data = new google.visualization.DataTable('<?php echo $json_table; ?>');

                // Set chart options
                var options = {/*is3D : true,*/
                    /* width:400,*/
                    height: 300,
                    fontName: 'Titillium Web',
                    pieSliceTextStyle: {fontSize: 12},
                    colors: ['#87c2db', '#ebb293', '#93bc94', '#e69f9f', '#cecece', '#f0e4a5', '#d6c4df', '#e2a1c4', '#8eb2cc']};

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.PieChart(document.getElementById('rm_browser_usage_chart_div'));
                chart.draw(data, options);
            }
        </script>
        <?php
    }

    // Renders browser conversion chart
    public function browser_conversion($form_id, $data = null)
    {
        if ($data == null)
            $data = $this->form_stats($form_id);
        $data_string = '';
        foreach ($data->analysis->browsers as $name => $usage)
        {
            if ($usage->visits != 0)
            {
                $formatted_name = RM_UI_Strings::get('LABEL_BROWSER_' . strtoupper($name));
                $data_string .= ", ['$formatted_name', " . $usage->visits . ", $usage->submissions]";
            }
        }
        $data_string = substr($data_string, 2);
        wp_enqueue_script('google_charts', 'https://www.gstatic.com/charts/loader.js');
        wp_enqueue_script("rm_chart_widget", RM_BASE_URL . "public/js/google_chart_widget.js");
        ?>
        <script>
            function draw_browser_conversion()
            {
                var data = google.visualization.arrayToDataTable([
                    ['Browser', 'Total Visits', 'Submissions'],<?php echo $data_string; ?>]);

                var options = {
                    chartArea: {width: '50%'},
                    height: 500,
                    fontName: 'Titillium Web',
                    pieSliceTextStyle: {fontSize: 12},
                    hAxis: {
                        title: 'Hits',
                        minValue: 0
                    },
                    vAxis: {
                        title: 'Browser'
                    },
                    legend: {position: 'top', maxLines: 3},
                    colors: ['#8eb2cc', '#e2a1c4'],
                    bar: {
                        groupWidth: 20
                    }
                };

                var chart = new google.visualization.BarChart(document.getElementById('rm_browser_conversion_div'));
                chart.draw(data, options);
            }
        </script>
        <?php
    }

    // Render timewise submission chart
    public function sot($form_id, $time_range, $data = null)
    {
        wp_enqueue_script('google_charts', 'https://www.gstatic.com/charts/loader.js');
        wp_enqueue_script("rm_chart_widget", RM_BASE_URL . "public/js/google_chart_widget.js");
        if ($data == null)
            $data = $this->form_stats($form_id, $time_range);
        $data_string = '';

        foreach ($data->day_wise_stat as $date => $per_day)
        {

            $formatted_name = $date;
            $data_string .= ", ['$formatted_name', " . $per_day->visits . ", $per_day->submissions]";
        }
        $data_string = substr($data_string, 2);
        ?>
        <script>
            function draw_timewise_stat() {
                var data = google.visualization.arrayToDataTable([
                    ['Date', 'Visits', 'Submissions'],<?php echo $data_string; ?>]);

                var options = {
                    chartArea: {width: '90%'},
                    height: 500,
                    fontName: 'Titillium Web',
                    hAxis: {
                        title: '',
                        minValue: 0,
                        slantedText: false,
                        maxAlternation: 1,
                        maxTextLines: 1
                    },
                    vAxis: {
                        title: '',
                        viewWindow: {min: 0},
                        minValue: 4,
                    },
                    legend: {position: 'top', maxLines: 3},
                    colors: ['#8eb2cc', '#e2a1c4'],

                };

                var chart = new google.visualization.LineChart(document.getElementById('rm_sot_div'));
                chart.draw(data, options);

            }
        </script>

        <?php
    }

}
