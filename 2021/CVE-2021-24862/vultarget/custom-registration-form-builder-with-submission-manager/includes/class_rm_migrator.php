<?php

class RM_Migrator
{

    public $value_map;
    public $reg_forms;
    public $all_forms;
    public $created;

    public function __construct()
    {
        $this->value_map = array('options' => array());  //Enter identifier for the table type the data is to be migrated between.
        $this->reg_forms = array();
        $this->all_forms = array();
        $this->created = array();
        $this->value_map['options'] = $this->get_options_mapping();
        $this->value_map['paypal_fields'] = $this->get_pp_fields_mapping();
        $this->value_map['paypal_logs'] = $this->get_pp_logs_mapping();
        $this->value_map['stats'] = $this->get_stats_mapping();

        $this->value_map['forms'] = $this->get_forms_mapping();
        $this->value_map['notes'] = $this->get_notes_mapping();
        $this->value_map['front_users'] = $this->get_front_users_mapping();
        $this->value_map['fields'] = $this->get_fields_mapping();
        $this->value_map['submissions'] = $this->get_submissions_mapping();
        $this->value_map['field_types'] = $this->get_field_type_migrated();
    }

    public function initiate_migration_with_progrees_logging()
    {
        $status = get_site_option('rm_mig_log', '<br>');
        error_log("in: " . $status);

        update_site_option('rm_mig_log', $status);


//Start migration.

        $status .= "IN THE PI: Migration progress log:<br>";
        update_site_option('rm_mig_log', $status);
        $status .= "Initiating migration...<br>";
        update_site_option('rm_mig_log', $status);
        $status .= "Class loaded.<br>";
        update_site_option('rm_mig_log', $status);
        $status .= "Migrating old crf...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migration_old_crf();
        $status .= "Migrating Global settings...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migrate_options();
        sleep(5);
        $status .= "Migrating PayPal fields...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migrate_paypal_fields();
        sleep(5);
        $status .= "Migrating PayPal logs...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migrate_paypal_logs();
        sleep(5);
        $status .= "Migrating Stats...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migrate_stats();
        sleep(5);

        $status .= "Migrating Forms...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migrate_forms();
        sleep(5);
        $status .= "Migrating Fields...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migrate_fields();
        sleep(5);
        $status .= "Migrating Notes...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migrate_notes();
        sleep(5);
        $status .= "Migrating Front users...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migrate_front_users();
        sleep(5);
        $status .= "Migrating Submissions...<br>";
        update_site_option('rm_mig_log', $status);
        $this->migrate_submissions();
        sleep(5);
        $status .= "Migration finished.<br>";
        update_site_option('rm_mig_log', $status);

//update_option('rm_option_rm_version', RM_PLUGIN_VERSION);
        return 'migrate_success';
    }

//Do not provide "wp_" prepend names, as that prefix may vary from user to user.
    public function migrate($identifier, $src_tbl_name, $dest_tbl_name)
    {
        global $wpdb;
        $wpfx = $wpdb->prefix;

        $src_tbl_name = $wpfx . $src_tbl_name;
        $dest_tbl_name = $wpfx . $dest_tbl_name;

        $data_mapping = $this->value_map[$identifier];

        $srcdata = $wpdb->get_results("SELECT * FROM $src_tbl_name");

        if ($srcdata)
        {
            $insert_cols_src = '';
            $insert_cols_dst = '(';

            foreach ($data_mapping as $src_name => $dst_name)
            {
                if ($dst_name)
                {
                    $insert_cols_src .= "`$src_name`,";
                    $insert_cols_dst .= "`$dst_name`,";
                }
            }
            $insert_cols_src = trim($insert_cols_src, ',');
            $insert_cols_dst = trim($insert_cols_dst, ',') . ')';

            $qry = "INSERT INTO $dest_tbl_name $insert_cols_dst SELECT $insert_cols_src FROM $src_tbl_name";
//die($qry);
//foreach ($srcdata as $srcrow)
            {
                $qry = esc_sql($qry);
                $wpdb->query($qry);
            }
        } else
            error_log("No data found in the table: " . $src_tbl_name);
    }

// Specific Migrators
    public function migrate_options()
    {
        $data_mapping = $this->value_map['options'];

        foreach ($data_mapping as $src_name => $dst_name)
        {
            if ($dst_name)
            {
                if ($dst_name === 'smtp_password')
                {
                    $oldp = get_option($src_name, null);
                    if (!$oldp)
                    {
                        $oldp = $this->crf_encrypt_decrypt_pass('decrypt', $oldp);
                        $newp = $this->enc_str($oldp);
                        update_option("rm_option_" . $dst_name, $newp);
                    }
                }
                update_option("rm_option_" . $dst_name, get_option($src_name, null));
//var_dump(get_option($src_name, null));
//var_dump(get_option("".$dst_name, null));
//echo "<br>------------------------<br>";
// die('here');
            }
        }

        global $wpdb;
        $crf_option = $wpdb->prefix . "crf_option";
        $select = "select * from $crf_option where 1";
        $data = $wpdb->get_results($select);
//var_dump($data);
//return;
        if (!$data)
            return;
        foreach ($data as $row)
        {
            if (isset($data_mapping[$row->fieldname]) && $data_mapping[$row->fieldname])
            {
                if ($row->fieldname === 'crf_theme')
                {
                    if ($row->value === 'default')
                        update_option("rm_option_theme", 'matchmytheme');
                    else
                        update_option("rm_option_theme", 'classic');
                } else
                    update_option("rm_option_" . $data_mapping[$row->fieldname], $row->value);
                /* var_dump($row->fieldname);
                  var_dump($row->value);
                  var_dump(get_option("".$data_mapping[$row->fieldname], null));
                  echo "<br>------------------------<br>"; */
            }
        }

        $x = get_option('crf_smtp_from_email_name', null);
        if ($x)
        {
            update_option("rm_option_senders_display_name", $x);
        }

        $x = get_option('crf_smtp_from_email_address', null);
        if ($x)
        {
            update_option("rm_option_senders_email", $x);
        }

        $x = get_option('crf_smtp_autentication', null);
        if ($x)
        {
            if ($x === 'true')
                update_option("rm_option_smtp_auth", 'yes');
            else
                update_option("rm_option_smtp_auth", 'no');
        }

//        $x = get_option('crf_theme', null);
//        if ($x)
//        {
//            if ($x === 'default')
//                update_option("rm_option_theme", 'matchmytheme');
//            else
//                update_option("rm_option_theme", 'classic');
//        }

        $x = get_option('crf_smtp_encription', null);

        switch ($x)
        {
            case 'tls':
                update_option("rm_option_smtp_encryption_type", 'enc_tls');
                break;

            case 'ssl':
                update_option("rm_option_smtp_encryption_type", 'enc_ssl');
                break;

            default:
                update_option("rm_option_smtp_encryption_type", 'enc_none');
                break;
        }
    }

    public function migrate_paypal_fields()
    {
        global $wpdb;

        $wpfx = $wpdb->prefix;

        $data_mapping = $this->value_map['paypal_fields'];
//var_dump($data_mapping);
//$this->migrate('paypal_fields', 'crf_paypal_fields', 'rm_paypal_fields');

        $res = $wpdb->get_results("SELECT * FROM `" . $wpfx . "crf_paypal_fields` WHERE 1", ARRAY_A);
        if (!$res)
            return;
//echo "<pre>", var_dump($res), "</pre>";

        $type_mapping = array('single' => 'fixed',
            'checkbox' => 'multisel',
            'dropdown' => 'dropdown',
            'userdefine' => 'userdef');

        foreach ($res as $index => $row)
        {
//   echo "<pre>", var_dump($row), "</pre>";

            switch ($res[$index]['Type'])
            {
                case 'single':
                    $extra_option = maybe_unserialize($res[$index]['extra_options']);
                    $res[$index]['Type'] = 'fixed';
                    $res[$index]['Option_Label'] = maybe_serialize(array(''));
                    $res[$index]['Option_Price'] = maybe_serialize(array(''));
                    $res[$index]['Option_Value'] = null;
                    $res[$index]['extra_options'] = $extra_option['field_visible'] ? 'yes' : 'no';
                    break;

                case 'checkbox':
                    $res[$index]['Type'] = 'multisel';
                case 'dropdown':
                    $res[$index]['Option_Label'] = maybe_serialize(explode(',', $res[$index]['Option_Label']));
                    $res[$index]['Option_Price'] = maybe_serialize(explode(',', $res[$index]['Option_Price']));
                    $res[$index]['Option_Value'] = null;
                    $res[$index]['extra_options'] = 'yes';
                    break;

                case 'userdefine':
                    $res[$index]['Type'] = 'userdef';
                    $res[$index]['Option_Label'] = maybe_serialize(array(''));
                    $res[$index]['Option_Price'] = maybe_serialize(array(''));
                    $res[$index]['Option_Value'] = null;
                    $res[$index]['extra_options'] = 'yes';
                    break;
            }
        }
//echo "<pre>", var_dump($res), "</pre>";
        $this->insert_array('paypal_fields', $wpfx . "rm_paypal_fields", $res);
    }

    public function migrate_paypal_logs($offset = 0, $limit = 0)
    {
        global $wpdb;

        $wpfx = $wpdb->prefix;

        $data_mapping = $this->value_map['paypal_logs'];
//var_dump($data_mapping);
//$this->migrate('paypal_logs', 'crf_paypal_logs', 'rm_paypal_logs');

        $res = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $wpfx . "crf_paypal_log` WHERE 1 LIMIT %d, %d",intval($offset),intval($limit)), ARRAY_A);
        if (!$res)
            return;
//echo "<pre>", var_dump($res), "</pre>";

        foreach ($res as $index => $row)
        {
//   echo "<pre>", var_dump($row), "</pre>";
            $log = maybe_unserialize($row['log']);

            $a = array('id' => '',
                'submission_id' => null,
                'form_id' => null,
                'invoice' => '',
                'txn_id' => '',
                'status' => '',
                'total_amount' => '',
                'currency' => '',
                'log' => '',
                'posted_date' => '');

            $a['id'] = $row['id'];
            $a['txn_id'] = $row['txn_id'];
            $a['invoice'] = $log['invoice'];
            $a['status'] = $log['payment_status'];
            $a['total_amount'] = $log['mc_gross'];
            $a['currency'] = $log['mc_currency'];
            $a['log'] = $row['log'];
            $a['posted_date'] = $row['posted_date'];

            $res[$index] = $a;
        }
//echo "<pre>", var_dump($res), "</pre>";
        $this->insert_array('paypal_logs', $wpfx . "rm_paypal_logs", $res);

//Now update submission_id and form_id in the new table.
        $qry = "update `" . $wpfx . "rm_paypal_logs`, `" . $wpfx . "crf_submissions` set " . $wpfx . "rm_paypal_logs.submission_id = " . $wpfx . "crf_submissions.submission_id where " . $wpfx . "rm_paypal_logs.id = " . $wpfx . "crf_submissions.value and " . $wpfx . "crf_submissions.field = 'paypal_log_id'";

//$qry = esc_sql($qry);
        $wpdb->query($qry);

        $qry = "update `" . $wpfx . "rm_paypal_logs`, `" . $wpfx . "crf_submissions` set " . $wpfx . "rm_paypal_logs.form_id = " . $wpfx . "crf_submissions.form_id where " . $wpfx . "rm_paypal_logs.id = " . $wpfx . "crf_submissions.value and " . $wpfx . "crf_submissions.field = 'paypal_log_id'";

//$qry = esc_sql($qry);
        $wpdb->query($qry);
    }

    public function migrate_stats($offset = 0, $limit = 9999999)
    {
        global $wpdb;

        $wpfx = $wpdb->prefix;

        $data_mapping = $this->value_map['stats'];
//var_dump($data_mapping);
//$this->migrate('paypal_logs', 'crf_paypal_logs', 'rm_paypal_logs');

        $res = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $wpfx . "crf_stats` WHERE 1 LIMIT %d, %d",intval($offset),intval($limit)), ARRAY_A);

        if (!$res)
            return;
//echo "<pre>", var_dump($res), "</pre>";
//require_once 'Browser.php';
        require_once plugin_dir_path(plugin_dir_path(__FILE__)) . 'external/Browser/Browser.php';

        foreach ($res as $index => $row)
        {
//   echo "<pre>", var_dump($row), "</pre>";
            $details = maybe_unserialize($row['details']);
//var_dump($details['submit_time']);echo"<br>ONErun<br><br>";


            if (!$details['Browser'])
                $browser_name = 'Unknown';
            else
            {
                $browser = new RM_Browser($details['Browser']);
                $browser_name = $browser->getBrowser();
            }

            $a = array('stat_id' => '',
                'form_id' => '',
                'user_ip' => '',
                'ua_string' => '',
                'browser_name' => '',
                'visited_on' => '',
                'submitted_on' => null,
                'time_taken' => null);

            $a['stat_id'] = $row['id'];
            $a['form_id'] = $row['form_id'];
            $a['user_ip'] = $details['User_IP'];
            $a['ua_string'] = $details['Browser'];
            $a['browser_name'] = $browser_name;
            $a['visited_on'] = $details['timestamp'];
            $a['submitted_on'] = isset($details['submit_time']) ? $details['submit_time'] : null;
            $a['time_taken'] = isset($details['total_time']) ? $details['total_time'] : null;

            $res[$index] = $a;
        }
//echo "<pre>", var_dump($res), "</pre>";
        $this->insert_array('stats', $wpfx . "rm_stats", $res);
    }

    public function migrate_forms()
    {
        global $wpdb;
        $qry = "";
        $data_mapping = $this->value_map['forms'];
        $dst_data = array();
        $valid_options = array('form_is_opt_in_checkbox', 'form_opt_in_text', 'form_should_user_pick', 'form_is_unique_token', 'form_description', 'form_user_field_label', 'form_custom_text', 'form_success_message', 'form_email_subject', 'form_email_content', 'form_submit_btn_label', 'form_submit_btn_color', 'form_submit_btn_bck_color', 'form_expired_by', 'form_submissions_limit', 'form_expiry_date', 'form_message_after_expiry', 'mailchimp_list', 'mailchimp_mapped_email', 'mailchimp_mapped_first_name', 'mailchimp_mapped_last_name');
        $dbcolumns = array('form_id', 'form_name', 'form_type', 'form_user_role', 'default_user_role', 'form_should_send_email', 'form_redirect', 'form_redirect_to_page', 'form_redirect_to_url', 'form_should_auto_expire', 'form_options', 'published_pages');
        $table_name_src = $wpdb->prefix . 'crf_forms';
        $table_name_dst = $wpdb->prefix . 'rm_forms';
        $table_name_fields = $wpdb->prefix . 'crf_fields';

        $srcdata = $wpdb->get_results("SELECT * FROM $table_name_src WHERE 1");
        $i = 0;
        if ($srcdata && is_array($srcdata))
            foreach ($srcdata as $s)
            {
                $this->all_forms[] = $s->id;
                $form_type = $s->form_type;
                $form_options = maybe_unserialize($s->form_option);
                $c = new stdClass;
                $dst_data[$i] = array();

                foreach ($data_mapping as $a => $b)
                {

                    if (in_array($b, $valid_options))
                    {
                        if ($a === 'expiry_date')
                        {
                            if (isset($form_options[$a]) && $form_options[$a])
                            {
                                $x = explode('-', $form_options[$a]);
                                $form_options[$a] = $x[1] . '/' . $x[2] . '/' . $x[0];
                            }
                        } elseif ($a === 'expiry_type')
                        {
                            if (isset($form_options[$a]))
                            {
                                if ('submission' === trim($form_options[$a]))
                                    $form_options[$a] = 'submissions';
                            }
                        }
                        elseif ($a === 'mailchimp_emailfield')
                        {
                            if (isset($form_options[$a]))
                            {
                                if ($form_type === 'reg_form')
                                {
                                    $form_options[$a] = null;
                                } else
                                {
                                    $e = explode('_', $form_options[$a]);
                                    $id = array_pop($e);
                                    if (is_numeric($id))
                                    {
                                        $form_options[$a] = 'Email_' . $id;
                                    }
                                }
                            }
                        } elseif ($a === 'mailchimp_firstfield' || $a === 'mailchimp_lastfield')
                        {
                            if (isset($form_options[$a]) && $form_options[$a] === 'first_name')
                            {
                                $f_id = $wpdb->get_var("SELECT `Id` FROM `$table_name_fields` WHERE `Form_Id` = $s->id AND `Type` LIKE 'first_name'");
                                if ($f_id)
                                {
                                    $form_options[$a] = 'Fname_' . $f_id;
                                }
                            } elseif (isset($form_options[$a]) && $form_options[$a] === 'last_name')
                            {
                                $f_id = $wpdb->get_var("SELECT `Id` FROM `$table_name_fields` WHERE `Form_Id` = $s->id AND `Type` LIKE 'last_name'");
                                if ($f_id)
                                {
                                    $form_options[$a] = 'Lname_' . $f_id;
                                }
                            } elseif (isset($form_options[$a]))
                            {
                                $el = explode('_', $form_options[$a]);
                                $f_id = array_pop($el);
                                if (is_numeric($f_id))
                                {
                                    $type = $wpdb->get_var("SELECT `Type` FROM `$table_name_fields` WHERE `Form_Id` = $s->id AND `Id` = $f_id");
                                    if (isset($this->value_map['field_types'][$type]))
                                        $form_options[$a] = $this->value_map['field_types'][$type] . '_' . $f_id;
                                    else
                                        $form_options[$a] = null;
                                }
                            }
                        }
                        if (isset($s->$a))
                            $c->$b = $s->$a;
                        elseif (isset($form_options[$a]))
                            $c->$b = $form_options[$a];
                        else
                            $c->$b = NULL;
                    }
                    else
                    {
                        if ($a === 'form_type')
                        {
                            if ($s->$a === 'reg_form')
                            {
                                $s->$a = 1;
                                $this->reg_forms[] = $s->id;
                            } else
                                $s->$a = 0;
                        }
                        if (isset($s->$a))
                            $dst_data[$i][$b] = "'" . esc_sql($s->$a) . "'";
                        elseif (isset($form_options[$a]))
                        {
                            if (is_array($form_options[$a]) || is_object($form_options[$a]))
                                $form_options[$a] = maybe_serialize($form_options[$a]);
                            $dst_data[$i][$b] = "'" . esc_sql($form_options[$a]) . "'";
                        } else
                            $dst_data[$i][$b] = 'null';
                    }
                }
                $dst_data[$i]['form_options'] = "'" . esc_sql(maybe_serialize($c)) . "'";
                $i++;
            }
        if ($dst_data && count($dst_data) !== 0)
        {
            $qry .= "INSERT INTO $table_name_dst (" . implode(',', $dbcolumns) . ") values ";

            $i = 0;

            foreach ($dst_data as $d)
            {
                if ($i === 0)
                {
                    if ($d)
                        $qry .= "(" . implode(',', $d) . ")";
                } else
                    $qry .= ", (" . implode(',', $d) . ")";

                $i++;
            }
        } else
        {
            error_log('no_data');
            return;
        }

        return $wpdb->query($qry);
    }

    public function migrate_notes()
    {
        global $wpdb;
        $qry = "";
        $data_mapping = $this->value_map['notes'];
        $dst_data = array();
        $valid_options = array('bg_color');
        $dbcolumns = array('note_id', 'submission_id', 'notes', 'status', 'publication_date', 'published_by', 'last_edit_date', 'last_edited_by', 'note_options');
        $table_name_src = $wpdb->prefix . 'crf_notes';
        $table_name_dst = $wpdb->prefix . 'rm_notes';

        $srcdata = $wpdb->get_results("SELECT * FROM $table_name_src WHERE 1");
        $i = 0;
        if ($srcdata && is_array($srcdata))
            foreach ($srcdata as $s)
            {
                $c = new stdClass;
                $dst_data[$i] = array();

                foreach ($data_mapping as $a => $b)
                {

                    if ($a === 'bg_color')
                    {
                        $s->$a = str_replace('#', '', $s->$a);
                    }

                    if (in_array($b, $valid_options))
                    {
                        if (isset($s->$a))
                            $c->$b = $s->$a;
                        else
                            $c->$b = NULL;
                    }
                    else
                    {
                        if ($a === 'last_edited_by')
                        {
                            if (is_email($s->$a))
                            {
                                $k = get_user_by('email', $s->$a);
                                if ($k)
                                    $s->$a = $k->ID;
                            }
                            elseif (!(int) $s->$a)
                            {
                                $s->$a = 'NULL';
                            }
                        }

                        if (isset($s->$a))
                            $dst_data[$i][$b] = "'" . $s->$a . "'";
                        else
                            $dst_data[$i][$b] = 'NULL';
                    }
                }

                $dst_data[$i]['note_options'] = "'" . maybe_serialize($c) . "'";
                $i++;
            }
        if ($dst_data && count($dst_data) !== 0)
        {
            $qry .= "INSERT INTO $table_name_dst (" . implode(',', $dbcolumns) . ") values ";

            $i = 0;

            foreach ($dst_data as $d)
            {
                if ($i === 0)
                {
                    if ($d)
                        $qry .= "(" . implode(',', $d) . ")";
                } else
                    $qry .= ", (" . implode(',', $d) . ")";

                $i++;
            }
        } else
        {
            error_log('no_data');
            return;
        }

//echo $qry;
//die;
//$qry = esc_sql($qry);
        return $wpdb->query($qry);
    }

    public function migrate_front_users($offset = 0, $limit = 9999999)
    {
        global $wpdb;
        $qry = "";
        $data_mapping = $this->value_map['front_users'];
        $dst_data = array();
        $dbcolumns = array('id', 'email', 'otp_code', 'last_activity_time', 'created_date');
        $table_name_src = $wpdb->prefix . 'crf_users';
        $table_name_dst = $wpdb->prefix . 'rm_front_users';

        $srcdata = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name_src WHERE 1 LIMIT %d, %d",intval($offset),intval($limit)));
        $i = 0;
        if ($srcdata && is_array($srcdata))
            foreach ($srcdata as $s)
            {
                $c = new stdClass;
                $dst_data[$i] = array();

                foreach ($data_mapping as $a => $b)
                {
                    if (isset($s->$a))
                        $dst_data[$i][$b] = "'" . $s->$a . "'";
                    else
                        $dst_data[$i][$b] = 'NULL';
                }

                $i++;
            }

        if ($dst_data && count($dst_data) !== 0)
        {
            $qry .= "INSERT INTO $table_name_dst (" . implode(',', $dbcolumns) . ") values ";

            $i = 0;

            foreach ($dst_data as $d)
            {
                if ($i === 0)
                {
                    if ($d)
                        $qry .= "(" . implode(',', $d) . ")";
                } else
                    $qry .= ", (" . implode(',', $d) . ")";

                $i++;
            }
        } else
        {
            error_log('no data');
            return;
        }

        return $wpdb->query($qry);
    }

    public function migrate_fields($offset = 0, $limit = 99999999)
    {
        global $wpdb;
        $qry = "";
        $f_t = $this->value_map['field_types'];
        $data_mapping = $this->value_map['fields'];
        $dst_data = array();
        $valid_options = array('field_placeholder', 'field_default_value', 'field_css_class', 'field_max_length', 'field_textarea_columns', 'field_textarea_rows', 'field_is_required', 'field_is_read_only', 'field_is_other_option');
        $dbcolumns = array('field_id', 'form_id', 'field_label', 'field_type', 'field_value', 'field_order', 'field_show_on_user_page', 'field_options');
        $table_name_src = $wpdb->prefix . 'crf_fields';
        $table_name_dst = $wpdb->prefix . 'rm_fields';

        $srcdata = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name_src WHERE 1 LIMIT %d, %d",intval($offset),intval($limit)));
        $i = 0;
        if ($srcdata && is_array($srcdata))
            foreach ($srcdata as $s)
            {
                if (!isset($s->Type) || !$s->Type)
                    continue;

                $c = new stdClass;
                $dst_data[$i] = array();

                foreach ($data_mapping as $b => $a)
                {
                    $value = $s->$a;

                    if ($a === 'Type')
                    {
                        if (isset($f_t[$value]))
                            $value = $f_t[$value];
                        else
                            $value = 'Textbox';
                    }
                    elseif ($a === 'Option_Value')
                    {

                        switch ($s->Type)
                        {
                            case 'checkbox' :
                                $tmp = explode(',', $value);
                                if (trim($tmp[count($tmp) - 1]) === 'chl_other')
                                {
                                    array_pop($tmp);
                                    if ($b === 'field_is_other_option')
                                    {
                                        $value = 1;
                                    } else
                                    {
                                        $value = maybe_serialize($tmp);
                                    }
                                } else
                                {
                                    if ($b === 'field_is_other_option')
                                        $value = null;
                                    else
                                        $value = maybe_serialize($tmp);
                                }

                                break;

                            case 'radio' :

                                if ($b === 'field_is_other_option')
                                    $value = null;
                                else
                                {
                                    $tmp = explode(',', $value);
                                    $value = maybe_serialize($tmp);
                                }
                                break;

                            case 'pricing' :
                            case 'heading' :
                                if ($b === 'field_is_other_option')
                                    $value = null;
                                else
                                    $value = $s->Value;
                                break;

                            case 'term_checkbox' :
                                if ($b === 'field_is_other_option')
                                    $value = null;
                                else
                                    $value = $s->Description;
                                break;

                            case 'select' :
                            case 'paragraph' :
                            case 'file' :
                                if ($b === 'field_is_other_option')
                                    $value = null;
                                break;

                            default :
                                $value = null;
                                break;
                        }
                    }elseif ($a === 'Value')
                    {
                        switch ($s->Type)
                        {
                            case 'text':
                            case 'textarea' :
                            case 'first_name' :
                            case 'last_name' :
                            case 'description' :
                                if ($b != 'field_placeholder')
                                {
                                    $value = null;
                                }
                                break;

                            case 'radio' :
                            case 'select' :
                            case 'checkbox' :
                                if ($b != 'field_default_value')
                                {
                                    $value = null;
                                } else
                                {
                                    
                                }
                                break;

                            default:
                                break;
                        }
                    } elseif ($a === 'Visibility')
                    {
                        if ($s->$a === 2)
                            $value = 1;
                        else
                            $value = null;
                    }

                    if (in_array($b, $valid_options))
                    {
                        if (isset($s->$a))
                            $c->$b = $value;
                        else
                            $c->$b = NULL;
                    }
                    else
                    {
                        if (isset($s->$a) && $value !== null)
                            $dst_data[$i][$b] = "'" . esc_sql($value) . "'";
                        else
                            $dst_data[$i][$b] = 'null';
                    }
                }

                $dst_data[$i]['field_options'] = "'" . esc_sql(maybe_serialize($c)) . "'";
                $i++;
            }
        if ($dst_data && count($dst_data) !== 0)
        {
            $qry .= "INSERT INTO $table_name_dst (" . implode(',', $dbcolumns) . ") values ";

            $i = 0;

            foreach ($dst_data as $d)
            {
                if ($i === 0)
                {
                    if ($d)
                        $qry .= "(" . implode(',', $d) . ")";
                } else
                    $qry .= ", (" . implode(',', $d) . ")";

                $i++;
            }
        }else
        {
            error_log('no_data');
            return;
        }

//echo $qry;
//die;
        return $wpdb->query($qry);
    }

//Central utility
    public function insert_array($identifier, $table_name, $array_data)
    {
        global $wpdb;
        $data_mapping = $this->value_map[$identifier];
        $cols = '';
        foreach ($data_mapping as $src_name => $dst_name)
        {
            if ($dst_name)
            {
                $cols .= "`$dst_name`,";
            }
        }

        $cols = trim($cols, ',');
        $values = '';

        foreach ($array_data as $index => $row)
        {
            foreach ($row as $colname => $value)
            {
                if (!$value)
                    $array_data[$index][$colname] = 'null';
                else
                    $array_data[$index][$colname] = "'" . esc_sql($value) . "'";
            }
        }

        foreach ($array_data as $insData)
        {
//$escaped_values = array_map('mysql_real_escape_string', array_values($insData));
            $value = implode(", ", $insData);
            $value = "(" . $value . "),";
            $values .= $value;
        }
        $values = trim($values, ',');
        $sql = "INSERT INTO `$table_name`($cols) VALUES $values";
//die($sql);
//$sql = esc_sql($sql);
        $wpdb->query($sql);
    }

    public function migrate_submissions($offset = 0, $limit = 9999999)
    {
        global $wpdb;
        $qry = "";
        $fields = array();
        $pfields = array();
        $plogs = array();
        $form_field = array();
        $data_mapping = $this->value_map['submissions'];
        $dst_data = array();
        $dst_data_sub = array();
        $users = array();
        $emails = array();
        $dbcolumns = array('sub_field_id', 'submission_id', 'field_id', 'form_id', 'value');
        $dbcolumns_sub = array('submission_id', 'form_id', 'data', 'user_email', 'submitted_on', 'unique_token');
        //$dbcolumns_sub_ph = array('%d', '%d', '%s', '%s', '%s', '%s');
        //$dbcolumns_ph = array('%d', '%d', '%d', '%d', '%s');
        $dbcolumns_sub_ph = "(%d,%d,%s,%s,%s,%s)";
        $dbcolumns_ph = "(%d,%d,%d,%d,%s)";
        $all_sub = array();
        $all_sub_fields = array();
        $table_name_fields = $wpdb->prefix . 'crf_fields';
        $table_name_pfields = $wpdb->prefix . 'crf_fields';
        $table_name_plogs = $wpdb->prefix . 'crf_paypal_log';
        $table_name_src = $wpdb->prefix . 'crf_submissions';
        $table_name_dst = $wpdb->prefix . 'rm_submission_fields';
        $table_name_dst_sub = $wpdb->prefix . 'rm_submissions';
        $table_name_fields_rm = $wpdb->prefix . 'rm_fields';

        $options = new RM_Options;

        $srcdata = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name_src WHERE 1 LIMIT %d, %d",intval($offset),intval($limit)));
        $fieldsdata = $wpdb->get_results("SELECT * FROM $table_name_fields WHERE 1");
        $pfieldsdata = $wpdb->get_results("SELECT * FROM $table_name_pfields WHERE 1");
        $plogsdata = $wpdb->get_results("SELECT * FROM $table_name_plogs WHERE 1");

        if (is_array($fieldsdata)):
            foreach ($fieldsdata as $f_data)
            {
                $fields[$f_data->Id] = $f_data;
                if (!isset($form_field[$f_data->Form_Id]))
                    $form_field[$f_data->Form_Id] = array();

                if (!isset($form_field[$f_data->Form_Id][$f_data->Type]))
                    $form_field[$f_data->Form_Id][$f_data->Type] = $f_data->Id;
            }

            unset($fieldsdata);

            if (is_array($plogsdata))
            {
                foreach ($plogsdata as $p_data)
                {
                    $log = maybe_unserialize($p_data->log);
                    $plogs[$log['custom']] = $log['mc_currency'];
                }
            }

            unset($plogsdata);

            if (is_array($pfieldsdata))
            {
                foreach ($pfieldsdata as $p_data)
                {
                    $pfields[$p_data->Id] = $p_data;
                }
            }

            unset($pfieldsdata);

            $i = 0;

            if ($srcdata && is_array($srcdata))
            {
                foreach ($srcdata as $s)
                {
                    $dst_data[$i] = array();
                    $is_row_valid = true;

                    if (isset($form_field[$s->form_id]['email']))
                        $ef_id = $form_field[$s->form_id]['email'];
                    else
                        $ef_id = 0;

                    if (!isset($dst_data_sub[$s->submission_id]))
                    {
                        $dst_data_sub[$s->submission_id] = array();
                        foreach ($dbcolumns_sub as $index)
                            $dst_data_sub[$s->submission_id][$index] = null;

                        $dst_data_sub[$s->submission_id]['submission_id'] = $s->submission_id;
                        $dst_data_sub[$s->submission_id]['form_id'] = $s->form_id;
                        $dst_data_sub[$s->submission_id]['data'] = array();
                    }

                    foreach ($data_mapping as $a => $b)
                    {
                        $value = $s->$a;



                        if ($a === 'field')
                        {
                            switch ($s->$a)
                            {
                                case 'first_name' :
                                    if (isset($form_field[$s->form_id]['first_name']))
                                    {
                                        $f_id = $form_field[$s->form_id]['first_name'];
                                        $value = $fields[$f_id]->Id;

                                        $dst_data_sub[$s->submission_id]['data'][$value] = new stdClass;
                                        $dst_data_sub[$s->submission_id]['data'][$value]->label = stripslashes($fields[$f_id]->Name);
                                        $dst_data_sub[$s->submission_id]['data'][$value]->value = stripslashes($s->value);
                                    } else
                                    {
                                        $dst_data_sub[$s->submission_id]['data']['first_name'] = new stdClass;
                                        $dst_data_sub[$s->submission_id]['data']['first_name']->label = 'first_name';
                                        $dst_data_sub[$s->submission_id]['data']['first_name']->value = stripslashes($s->value);
                                    }

                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['first_name'] = $s->value;
                                    break;
                                case 'last_name' :
                                    if (isset($form_field[$s->form_id]['last_name']))
                                    {
                                        $f_id = $form_field[$s->form_id]['last_name'];
                                        $value = $fields[$f_id]->Id;

                                        $dst_data_sub[$s->submission_id]['data'][$value] = new stdClass;
                                        $dst_data_sub[$s->submission_id]['data'][$value]->label = stripslashes($fields[$f_id]->Name);
                                        $dst_data_sub[$s->submission_id]['data'][$value]->value = stripslashes($s->value);
                                    } else
                                    {
                                        $dst_data_sub[$s->submission_id]['data']['last_name'] = new stdClass;
                                        $dst_data_sub[$s->submission_id]['data']['last_name']->label = 'last_name';
                                        $dst_data_sub[$s->submission_id]['data']['last_name']->value = stripslashes($s->value);
                                    }
                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['last_name'] = $s->value;
                                    break;
                                case 'description' :
                                    if (isset($form_field[$s->form_id]['description']))
                                    {
                                        $f_id = $form_field[$s->form_id]['description'];
                                        $value = $fields[$f_id]->Id;
                                        $dst_data_sub[$s->submission_id]['data'][$value] = new stdClass;
                                        $dst_data_sub[$s->submission_id]['data'][$value]->label = stripslashes($fields[$f_id]->Name);
                                        $dst_data_sub[$s->submission_id]['data'][$value]->value = stripslashes($s->value);
                                    } else
                                    {
                                        $dst_data_sub[$s->submission_id]['data']['description'] = new stdClass;
                                        $dst_data_sub[$s->submission_id]['data']['description']->label = 'description';
                                        $dst_data_sub[$s->submission_id]['data']['description']->value = stripslashes($s->value);
                                    }

                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['bio'] = $s->value;
                                    break;
                                case 'token' :
                                    $dst_data_sub[$s->submission_id]['unique_token'] = $s->value;
                                    $is_row_valid = false;
                                    break;
                                case 'entry_time' :
                                    $dst_data_sub[$s->submission_id]['submitted_on'] = RM_Utilities::get_current_time($s->value);
                                    $is_row_valid = false;
                                    break;
                                case 'user_email' :
                                    $dst_data_sub[$s->submission_id]['user_email'] = $s->value;
                                    $dst_data_sub[$s->submission_id]['data']['user_email'] = new stdClass;
                                    $dst_data_sub[$s->submission_id]['data']['user_email']->label = 'user_email';
                                    $dst_data_sub[$s->submission_id]['data']['user_email']->value = stripslashes($s->value);
                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['user_email'] = $s->value;
                                    break;
                                case 'form_type' :
                                    $is_row_valid = false;
                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['form_type'] = $s->value;
                                    break;
                                case 'user_approval' :
                                    $is_row_valid = false;
                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['user_approval'] = $s->value;
                                    break;
                                case 'role' :
                                    $is_row_valid = false;
                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['role'] = $s->value;
                                    break;
                                case 'payment_status' :
                                    $is_row_valid = false;
                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['payment_status'] = $s->value;
                                    break;
                                case 'user_name' :
                                    $is_row_valid = false;
                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['user_name'] = $s->value;
                                    break;
                                case 'user_pass' :
                                    $is_row_valid = false;
                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['user_pass'] = $s->value;
                                    break;
                                case 'pass_encrypt' :
                                    $is_row_valid = false;
                                    if (!isset($users[$s->submission_id]))
                                        $users[$s->submission_id] = array();
                                    $users[$s->submission_id]['pass_encrypt'] = $s->value;
                                    break;

                                default :
                                    $tmp = explode('_', $s->$a);
                                    $f_id = array_pop($tmp);
                                    if (is_numeric($f_id))
                                    {
                                        $label = implode('_', $tmp);
                                        $value = $f_id;
                                        if (isset($fields[$f_id]) && $fields[$f_id]->Name === $label)
                                        {
                                            switch ($fields[$f_id]->Type)
                                            {
                                                case 'checkbox':
                                                case 'repeatable_text' :
                                                    $s->value = maybe_serialize(explode(',', $s->value));
                                                    break;

                                                case 'DatePicker' :
                                                    $x = explode('-', $s->value);
                                                    $s->value = $x[1] . '/' . $x[2] . '/' . $x[0];
                                                    break;

                                                case 'file' :
                                                    $x = explode(',', $s->value);
                                                    $x['rm_field_type'] = 'File';
                                                    $s->value = maybe_serialize($x);
                                                    break;

                                                case 'pricing' :
                                                    if (isset($plogs[$s->submission_id]))
                                                        $curr = $plogs[$s->submission_id];
                                                    else
                                                        $curr = get_option('crf_currency');
                                                    if (!$curr)
                                                        $curr = 'USD';

                                                    if ($pfields[$fields[$f_id]->Value]->Type === 'checkbox')
                                                    {
                                                        $v = array();
                                                        $ab = explode(',', $s->value);
                                                        foreach ($ab as $ba)
                                                        {
                                                            $ac = explode('_', $ba);
                                                            $pr = array_pop($ac);
                                                            $pr_name = implode('_', $ac);
                                                            $v[] = $pr_name . '(' . $options->get_formatted_amount($pr, $curr) . ')';
                                                        }

                                                        $s->value = maybe_serialize($v);
                                                    } elseif ($pfields[$fields[$f_id]->Value]->Type === 'checkbox')
                                                    {
                                                        $ac = explode('_', $ba);
                                                        $pr = array_pop($ac);
                                                        $pr_name = implode('_', $ac);
                                                        $s->value = $pr_name . '(' . $options->get_formatted_amount($pr, $curr) . ')';
                                                    } else
                                                        $s->value = $options->get_formatted_amount($s->value, $curr);
                                                    break;
                                            }


                                            if ($ef_id === $f_id && !isset($emails[$s->submission_id]))
                                            {
                                                $emails[$s->submission_id] = new stdClass();
                                                $emails[$s->submission_id]->id = $ef_id;
                                                $emails[$s->submission_id]->value = $s->value;
                                            }

                                            $dst_data_sub[$s->submission_id]['data'][$f_id] = new stdClass;
                                            $dst_data_sub[$s->submission_id]['data'][$f_id]->label = stripslashes($fields[$f_id]->Name);
                                            $dst_data_sub[$s->submission_id]['data'][$f_id]->value = maybe_unserialize($s->value);
                                        } else
                                        {
                                            $dst_data_sub[$s->submission_id]['data'][$f_id] = new stdClass;
                                            $dst_data_sub[$s->submission_id]['data'][$f_id]->label = stripslashes($label);
                                            $dst_data_sub[$s->submission_id]['data'][$f_id]->value = maybe_unserialize($s->value);
                                        }
                                    } else
                                        $is_row_valid = false;

                                    break;
                            }
                        }

                        if ($is_row_valid)
                        {
                            if (isset($s->$a) && $value !== null)
                                $dst_data[$i][$b] = $value;
                            else
                                $dst_data[$i][$b] = null;
                        }
                    }

                    if ($is_row_valid)
                        $i++;
                    else
                        $dst_data[$i] = array();
                }


                if ($dst_data && count($dst_data) !== 0)
                {
                    $qry .= "INSERT INTO $table_name_dst (`" . implode('`,`', $dbcolumns) . "`) values ";

                    $i = 0;


                    foreach ($dst_data as $d)
                    {

                        if (is_array($d) && count($d) !== 0)
                        {
                            foreach ($d as $d_single)
                                $all_sub_fields[] = $d_single;

                            if ($i === 0)
                                $qry .= $dbcolumns_ph;
                            else
                                $qry .= ", " . $dbcolumns_ph;

                            $i++;
                        }
                    }

                    $qry_sub = "INSERT INTO $table_name_dst_sub (`" . implode('`,`', $dbcolumns_sub) . "`) values ";

                    $i = 0;

                    foreach ($dst_data_sub as $sub_id => $d_sub)
                    {
                        if (!isset($d_sub['user_email']))
                        {
                            if (isset($emails[$sub_id]))
                            {
                                $dst_data_sub[$sub_id] = $emails[$sub_id]->value;
                                $this->created[] = $d_sub['form_id'];
                                $wpdb->update($table_name_fields_rm, array('is_field_primary' => 1), array('field_id' => $emails[$sub_id]->id), '%d', '%d');
                            } else
                                $dst_data_sub[$sub_id] = null;
                        }

                        if (isset($d_sub['data']))
                        {
                            $d_sub['data'] = maybe_serialize($d_sub['data']);
                        }

                        if (is_array($d_sub) && count($d_sub) !== 0)
                        {
                            foreach ($d_sub as $d_sub_single)
                                $all_sub[] = $d_sub_single;

                            if ($i === 0)
                                $qry_sub .= $dbcolumns_sub_ph;
                            else
                                $qry_sub .= ", " . $dbcolumns_sub_ph;

                            $i++;
                        }
                    }
                }else
                {
                    error_log('no_data_sub_fields');
                }

                if (count($users) !== 0)
                {
                    foreach ($users as $user)
                    {
                        if ($user['form_type'] === 'reg_form' && ((isset($user['user_approval']) && $user['user_approval'] != 'yes') || (isset($user['payment_status']) && $user['payment_status'] === 'pending')) && isset($user['user_email']) && is_email($user['user_email']) && !email_exists($user['user_email']) && isset($user['user_pass']) && isset($user['user_name']))
                        {
                            $password = null;
                            if (isset($user['pass_encrypt']) && $user['pass_encrypt'] == 1)
                            {
                                $password = $this->crf_encrypt_decrypt_pass('decrypt', $user['user_pass']);
                                $password = $this->enc_str($password);
                            } else
                                $password = $user['user_pass'];

                            $user_id = wp_create_user($user['user_name'], $password, $user['user_email']);
                            update_user_meta($user_id, 'rm_user_status', 1);

                            if (isset($user['first_name']) && $user['first_name'])
                                update_user_meta($user_id, 'first_name', 1);
                            if (isset($user['last_name']) && $user['last_name'])
                                update_user_meta($user_id, 'last_name', 1);
                            if (isset($user['bio']) && $user['bio'])
                                update_user_meta($user_id, 'description', 1);
                        }
                    }
                }

//$qry = esc_sql($qry);
//error_log("Query: ".$qry);
                $result = $wpdb->query($wpdb->prepare($qry, $all_sub_fields));
                if (!$result)
                    error_log('submission_fields_not_migrated');

                $result_sub = $wpdb->query($wpdb->prepare($qry_sub, $all_sub));
                if (!$result_sub)
                    error_log('submissions_not_migrated');

                return;
            }
        endif;
        return false;
    }

    public function migrate_user_meta()
    {
        $user_query = new WP_User_Query(array('meta_key' => 'crf_user_status', 'meta_value' => 'deactivate', 'fields' => 'ID'));
        $user_ids = $user_query->get_results();
        if (is_array($user_ids))
        {
            foreach ($user_ids as $user_id)
            {
                update_user_meta($user_id, 'rm_user_status', 1);
            }
        }
    }

//Mappers.
    public function get_options_mapping()
    {
        return array('ucf_enable_cpatcha_login' => 'enable_captcha',
            'crf_recaptcha_lang' => null,
            'crf_recaptcha_request_method' => null,
            'crf_note_notification' => 'user_notification_for_notes',
            'crf_f_sub_page_id' => 'front_sub_page_id',
            'crf_migrate_submission' => null,
            'ucf_allowfiletypes' => 'allowed_file_types',
            'ucf_repeatfilefields' => 'allow_multiple_file_uploads',
            'ucf_default_Registration_url' => 'default_registration_url',
            'ucf_redirect_after_login' => 'post_submission_redirection_url',
            'crf_whatsnewpage' => null,
            'crf_gateway' => 'payment_gateway',
            'crf_test_mode' => 'paypal_test_mode',
            'crf_paypal_email' => 'paypal_email',
            'crf_currency' => 'currency',
            'crf_paypal_page_style' => 'paypal_page_style',
            'crf_currency_position' => 'currency_symbol_position',
            /* These are under a diff table.. :( */
            'enable_captcha' => 'enable_captcha',
            'public_key' => 'public_key',
            'public_key' => 'public_key',
            'adminnotification' => 'admin_notification',
            'adminemail' => 'admin_email',
            'from_email' => 'senders_display_name',
            'autogeneratedepass' => 'auto_generated_password',
            'userautoapproval' => 'user_auto_approval',
            'userip' => null,
            'crf_theme' => 'theme',
            'enable_facebook' => 'enable_facebook',
            'facebook_app_id' => 'facebook_app_id',
            'facebook_app_secret' => 'facebook_app_secret',
            'enable_mailchimp' => 'enable_mailchimp',
            'mailchimp_key' => 'mailchimp_key',
            'send_password' => 'send_password',
            'crf_smtp' => 'enable_smtp',
            'crf_smtp_host' => 'smtp_host',
            'crf_smtp_encription' => 'smtp_encryption_type',
            'crf_smtp_port' => 'smtp_port',
            'crf_smtp_autentication' => 'smtp_auth',
            'crf_smtp_username' => 'smtp_user_name',
            'crf_smtp_from_email_name' => null,
            'crf_smtp_from_email_address' => null,
            'crf_smtp_password' => 'smtp_password'
        );
    }

    public function get_pp_fields_mapping()
    {
        return array('Id' => 'field_id',
            'Type' => 'type',
            'Name' => 'name',
            'Value' => 'value',
            'Class' => 'class',
            'Option_Label' => 'option_label',
            'Option_Price' => 'option_price',
            'Option_Value' => 'option_value',
            'Description' => 'description',
            'Require' => 'require',
            'Ordering' => 'order',
            'extra_options' => 'extra_options'
        );
    }

    public function get_pp_logs_mapping()
    {
        $a = array('id' => 'id',
            'null_1' => 'submission_id',
            'null_2' => 'form_id',
            'null_3' => 'invoice',
            'txn_id' => 'txn_id',
            'null_4' => 'status',
            'null_5' => 'total_amount',
            'null_6' => 'currency',
            'log' => 'log',
            'posted_date' => 'posted_date');

        return $a;
    }

    public function get_stats_mapping()
    {
        $a = array('id' => 'stat_id',
            'form_id' => 'form_id',
            'stats_key' => null,
            'details' => null,
            'null_1' => 'user_ip',
            'null_2' => 'ua_string',
            'null_3' => 'browser_name',
            'null_4' => 'visited_on',
            'null_5' => 'submitted_on',
            'null_6' => 'time_taken');

        return $a;
    }

    public function get_forms_mapping()
    {
        return array(
            'id' => 'form_id',
            'form_name' => 'form_name',
            'form_type' => 'form_type',
            'user_role_options' => 'form_user_role',
            'user_role' => 'default_user_role',
            'send_email' => 'form_should_send_email',
            'redirect_option' => 'form_redirect',
            'redirect_page_id' => 'form_redirect_to_page',
            'redirect_url_url' => 'form_redirect_to_url',
            'auto_expires' => 'form_should_auto_expire',
            'optin_box' => 'form_is_opt_in_checkbox',
            'optin_box_text' => 'form_opt_in_text',
            'let_user_decide' => 'form_should_user_pick',
            'showtoken' => 'form_is_unique_token',
            'form_desc' => 'form_description',
            'user_role_label' => 'form_user_field_label',
            'custom_text' => 'form_custom_text',
            'success_message' => 'form_success_message',
            'crf_welcome_email_subject' => 'form_email_subject',
            'crf_welcome_email_message' => 'form_email_content',
            'submit_button_label' => 'form_submit_btn_label',
            'submit_button_color' => 'form_submit_btn_color',
            'submit_button_bgcolor' => 'form_submit_btn_bck_color',
            'expiry_type' => 'form_expired_by',
            'submission_limit' => 'form_submissions_limit',
            'expiry_date' => 'form_expiry_date',
            'expiry_message' => 'form_message_after_expiry',
            'mailchimp_list' => 'mailchimp_list',
            'mailchimp_emailfield' => 'mailchimp_mapped_email',
            'mailchimp_firstfield' => 'mailchimp_mapped_first_name',
            'mailchimp_lastfield' => 'mailchimp_mapped_last_name'
        );
    }

    public function get_notes_mapping()
    {
        return array(
            'id' => 'note_id',
            'submission_id' => 'submission_id',
            'notes' => 'notes',
            'status' => 'status',
            'publish_date' => 'publication_date',
            'user_id' => 'published_by',
            'last_edit_date' => 'last_edit_date',
            'last_edited_by' => 'last_edited_by',
            'bg_color' => 'bg_color'
        );
    }

    public function get_front_users_mapping()
    {

        return array(
            'Id' => 'id',
            'email' => 'submission_id',
            'otp_code' => 'notes',
            'last_activity_time' => 'status',
            'created_date' => 'publication_date'
        );
    }

    public function get_fields_mapping()
    {

        return array(
            'field_id' => 'Id',
            'form_id' => 'Form_Id',
            'field_label' => 'Name',
            'field_type' => 'Type',
            'field_value' => 'Option_Value',
            'field_order' => 'Ordering',
            'field_show_on_user_page' => 'Visibility',
            'field_placeholder' => 'Value',
            'field_default_value' => 'Value',
            'field_css_class' => 'Class',
            'field_max_length' => 'Max_Length',
            'field_textarea_columns' => 'Cols',
            'field_textarea_rows' => 'Rows',
            'field_is_required' => 'Require',
            'field_is_read_only' => 'Readonly',
            'field_is_other_option' => 'Option_Value'
        );
    }

    public function get_field_type_migrated()
    {
        return array(
            'heading' => 'HTMLH',
            'paragraph' => 'HTMLP',
            'text' => 'Textbox',
            'select' => 'Select',
            'radio' => 'Radio',
            'textarea' => 'Textarea',
            'checkbox' => 'Checkbox',
            'DatePicker' => 'jQueryUIDate',
            'email' => 'Email',
            'number' => 'Number',
            'country' => 'Country',
            'timezone' => 'Timezone',
            'term_checkbox' => 'Terms',
            'file' => 'File',
            'pricing' => 'Price',
            'repeatable_text' => 'Repeatable',
            'first_name' => 'Fname',
            'last_name' => 'Lname',
            'description' => 'BInfo'
        );
    }

    public function get_submissions_mapping()
    {

        return array(
            'id' => 'sub_field_id',
            'submission_id' => 'submission_id',
            'field' => 'field_id',
            'form_id' => 'form_id',
            'value' => 'value'
        );
    }

//Miscs.
    public function crf_encrypt_decrypt_pass($action, $string)
    {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';

// hash
        $key = hash('sha256', $secret_key);

// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action === 'encrypt')
        {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action === 'decrypt')
        {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public function enc_str($string)
    {
        if(function_exists('mcrypt_encrypt')) {
            $key = 'A Terrific tryst with tyranny';

            $iv = mcrypt_create_iv(
                    mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM
            );

            $encrypted = base64_encode($iv . mcrypt_encrypt(
                            MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), $string, MCRYPT_MODE_CBC, $iv
                    )
            );
            
        }
        else
        {
            $key= RM_Utilities::get_enc_key();
            $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($string, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
            $encrypted = base64_encode( $iv.$hmac.$ciphertext_raw );
        }
        return $encrypted;
        
    }

    public function migration_old_crf()
    {
        require_once( ABSPATH . 'wp-includes/wp-db.php');
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        global $wpdb;
        $crf_option = $wpdb->prefix . "crf_option";
        $crf_users = $wpdb->prefix . "crf_users";
        $crf_submissions = $wpdb->prefix . "crf_submissions";
        $crf_db_version = 3.6;

        $crf_option = $wpdb->prefix . "crf_option";
        $crf_fields = $wpdb->prefix . "crf_fields";
        $crf_forms = $wpdb->prefix . "crf_forms";
        $crf_entries = $wpdb->prefix . "crf_entries";

        $save_db_version = floatval(get_option('crf_db_version', '1.0'));
        if ($save_db_version < $crf_db_version)
        {
            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_forms (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `form_name` varchar(255) DEFAULT NULL,
                          `form_desc` longtext,
                          `form_type` varchar(255) NOT NULL,
                          `custom_text` longtext,
                          `crf_welcome_email_subject` varchar(255) NOT NULL,
                          `success_message` longtext,
                          `crf_welcome_email_message` longtext,
                          `redirect_option` varchar(255) NOT NULL,
                          `redirect_page_id` int(11) NOT NULL,
                          `redirect_url_url` longtext NOT NULL,
                          `send_email` int(11) NOT NULL,
                          `form_option` longtext,
                          PRIMARY KEY (`id`)
                        )";
            dbDelta($sqlcreate);

            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_fields (
	  `Id` int(11) NOT NULL AUTO_INCREMENT,
	  `Form_Id` int(11) NOT NULL,
	  `Type` varchar(50) DEFAULT NULL,
	  `Name` varchar(256) NOT NULL,
	  `Value` longtext DEFAULT NULL,
	  `Class` varchar(256) DEFAULT NULL,
	  `Max_Length` varchar(256) DEFAULT NULL,
	  `Cols` varchar(256) DEFAULT NULL,
	  `Rows` varchar(256) DEFAULT NULL,
	  `Option_Value` longtext DEFAULT NULL,
	  `Description` longtext DEFAULT NULL,
	  `Require` varchar(256) DEFAULT NULL,
	  `Readonly` varchar(256) DEFAULT NULL,
	  `Visibility` varchar(256) DEFAULT NULL,
	  `Ordering` int(11) DEFAULT NULL,
	  `Field_Key` varchar(256) DEFAULT NULL,
	  PRIMARY KEY (`Id`))";
            dbDelta($sqlcreate);

            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_option
	(
	`id` int NOT NULL AUTO_INCREMENT,
	`fieldname` varchar(255),
	`value` longtext,
	PRIMARY KEY(id)
	)";
            dbDelta($sqlcreate);
            $count_option = $wpdb->get_var("select count(*) from $crf_option");
            if ($count_option == 0)
            {
                $insert = "REPLACE INTO $crf_option VALUES
	(1, 'enable_captcha', 'no'),
	(2, 'public_key', ''),
	(3, 'public_key', ''),
	(4, 'autogeneratedepass', 'no'),
	(5, 'userautoapproval', 'yes'),
	(6, 'adminemail', ''),
	(7, 'adminnotification', 'no'),
	(8, 'from_email', ''),
	(9, 'userip', 'yes'),
	(10, 'crf_theme','default'),
	(11, 'enable_social', 'no'),
	(12, 'facebook_app_id', ''),
	(13, 'facebook_app_secret', ''),
	(14, 'enable_facebook', 'no'),
	(15, 'enable_twitter', 'no'),
	(16, 'consumer_key', ''),
	(17, 'consumer_secret', ''),
	(18, 'send_password', 'yes'),
	(19, 'enable_mailchimp', 'no'),
	(20, 'mailchimp_key', '')";
                $wpdb->query($insert);
            }

            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_entries
	(
	`id` int NOT NULL AUTO_INCREMENT,
	`form_id` int NOT NULL,
	`form_type` varchar(255),
	`user_approval` varchar(255),
	`value` longtext,
	 PRIMARY KEY (`id`)
	)";
            dbDelta($sqlcreate);

            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(6, 'adminemail', ''),
	(7, 'adminnotification', 'no')";
            $wpdb->query($insert);

            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(8, 'from_email', '')";
            $wpdb->query($insert);

            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(9, 'userip', 'no')";
            $wpdb->query($insert);

            $qry = "select `value` from $crf_option where fieldname='crf_theme'";
            $crf_theme = $wpdb->get_var($qry);

            if (isset($crf_theme) && $crf_theme != "")
            {
                if ($crf_theme === 'default')
                {
                    $wpdb->query("update $crf_option set value='classic' where fieldname='crf_theme'");
                } else
                {
                    $wpdb->query("update $crf_option set value='default' where fieldname='crf_theme'");
                }
            }

            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(10, 'crf_theme', 'default')";
            $wpdb->query($insert);

            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(11, 'enable_social', 'no')";
            $wpdb->query($insert);
            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(12, 'facebook_app_id', '')";
            $wpdb->query($insert);
            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(13, 'facebook_app_secret', '')";
            $wpdb->query($insert);
            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(14, 'enable_facebook', 'no')";
            $wpdb->query($insert);
            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(15, 'enable_twitter', 'no')";
            $wpdb->query($insert);
            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(16, 'consumer_key', '')";
            $wpdb->query($insert);
            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(17, 'consumer_secret', '')";
            $wpdb->query($insert);

            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(18, 'send_password', 'yes')";
            $wpdb->query($insert);

            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(19, 'enable_mailchimp', 'no')";
            $wpdb->query($insert);

            $insert = "INSERT IGNORE INTO $crf_option VALUES
	(20, 'mailchimp_key', '')";
            $wpdb->query($insert);

            $crf_stats = $wpdb->prefix . "crf_stats";
            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_stats
	(
	`id` int NOT NULL AUTO_INCREMENT,
	`form_id` int(11),
	`stats_key` varchar(255),
	`details` longtext,
	PRIMARY KEY(id)
	)";
            dbDelta($sqlcreate);
            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_users (
	  	`Id` int(11) NOT NULL AUTO_INCREMENT,
	  	`email` varchar(255) NOT	 NULL,
	  	`otp_code` varchar(255) NOT NULL,
	      	`last_activity_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	      	`created_date` timestamp, 
	  	PRIMARY KEY (`Id`))";
            dbDelta($sqlcreate);
            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_submissions (
	`id` int(11) NOT NULL AUTO_INCREMENT,
  	`submission_id` int(11),
  	`form_id` int(11),
  	`field` text,
  	`value` longtext,
	PRIMARY KEY(id)
	)";
            dbDelta($sqlcreate);

            $crf_paypal_fields = $wpdb->prefix . "crf_paypal_fields";
            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_paypal_fields (
	  `Id` int(11) NOT NULL AUTO_INCREMENT,
	  `Type` varchar(50) DEFAULT NULL,
	  `Name` varchar(256) NOT NULL,
	  `Value` longtext DEFAULT NULL,
	  `Class` varchar(256) DEFAULT NULL,
	  `Option_Label` longtext DEFAULT NULL,
	  `Option_Price` longtext DEFAULT NULL,
	  `Option_Value` longtext DEFAULT NULL,
	  `Description` longtext DEFAULT NULL,
	  `Require` varchar(256) DEFAULT NULL,
	  `Ordering` int(11) DEFAULT NULL,
	  `extra_options` longtext DEFAULT NULL,
	  PRIMARY KEY (`Id`))";
            dbDelta($sqlcreate);

            $crf_paypal_log = $wpdb->prefix . "crf_paypal_log";
            $crf_purchases = $wpdb->prefix . "crf_purchases";
            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_purchases (
	  `id` int(11) NOT NULL auto_increment,
	  `invoice` varchar(300) NOT NULL,
	  `trasaction_id` varchar(600) NOT NULL,
	  `log_id` int(10) NOT NULL,
	  `product_id` varchar(300) NOT NULL,
	  `product_name` varchar(300) NOT NULL,
	  `product_quantity` varchar(300) NOT NULL,
	  `product_amount` varchar(300) NOT NULL,
	  `payer_fname` varchar(300) NOT NULL,
	  `payer_lname` varchar(300) NOT NULL,
	  `payer_address` varchar(300) NOT NULL,
	  `payer_city` varchar(300) NOT NULL,
	  `payer_state` varchar(300) NOT NULL,
	  `payer_zip` varchar(300) NOT NULL,
	  `payer_country` varchar(300) NOT NULL,
	  `payer_email` text NOT NULL,
	  `payment_status` varchar(300) NOT NULL,
	  `posted_date` datetime NOT NULL,
	  PRIMARY KEY (`Id`))";
            dbDelta($sqlcreate);
            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_paypal_log (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `txn_id` varchar(600) NOT NULL,
	  `log` longtext NOT NULL,
	  `posted_date` datetime NOT NULL,
	  PRIMARY KEY (`Id`))";
            dbDelta($sqlcreate);

            $crf_notes = $wpdb->prefix . "crf_notes";
            $sqlcreate = "CREATE TABLE IF NOT EXISTS $crf_notes (
	 `id` int(11) NOT NULL AUTO_INCREMENT,
	  `submission_id` int(11) NOT NULL,
	  `userid` int(11) DEFAULT NULL,
	  `useremail` varchar(255) DEFAULT NULL,
	  `type` longtext NOT NULL,
	  `status` varchar(255) DEFAULT NULL,
	  `publish_date` datetime NOT NULL,
	  `last_edit_date` datetime DEFAULT NULL,
	  `last_edited_by` varchar(255) DEFAULT NULL,
      `notes` longtext DEFAULT NULL,
	  `bg_color` varchar(255) DEFAULT NULL,
	  `extra_option` longtext DEFAULT NULL,  
	  PRIMARY KEY (`Id`))";
            dbDelta($sqlcreate);

            $crf_forms = $wpdb->prefix . "crf_forms";
            $crf_fields = $wpdb->prefix . "crf_fields";
            $crfform = $wpdb->get_row("SELECT * FROM $crf_forms");
            $crffields = $wpdb->get_row("SELECT * FROM $crf_fields");
//Add column if not present.
            if (!isset($crfform->form_option))
            {
                $wpdb->query("ALTER TABLE $crf_forms ADD form_option longtext");
            }

//Add column if not present.
            if (!isset($crffields->Field_Key))
            {
                $wpdb->query("ALTER TABLE $crf_fields ADD Field_Key varchar(256) DEFAULT NULL");
                $this->crf_assign_key_for_previous_field();
            }

            $wpdb->query("ALTER TABLE $crf_forms CHANGE `success_message` `success_message` LONGTEXT");
            $wpdb->query("ALTER TABLE $crf_paypal_fields CHANGE `Option_Label` `Option_Label` LONGTEXT");
            $wpdb->query("ALTER TABLE $crf_paypal_fields CHANGE `Option_Price` `Option_Price` LONGTEXT");
            $wpdb->query("ALTER TABLE $crf_paypal_fields CHANGE `Option_Value` `Option_Value` LONGTEXT");
            $wpdb->query("ALTER TABLE $crf_fields CHANGE `Option_Value` `Option_Value` LONGTEXT");

            $submissions = get_option('crf_migrate_submission', 'no');
            if ($submissions === 'no')
            {
                $this->crf_migrate_entries_optimized();
                error_log('submission migrated successful');
                update_option("crf_migrate_submission", 'yes');
            }

            update_option('crf_db_version', $crf_db_version);
        }
    }

    public function crf_migrate_entries_optimized($offset = 0, $limit = 99999999)
    {
        global $wpdb;
        $wpfx = $wpdb->prefix;

        $entry_table = $wpfx . 'crf_entries';

        $src_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $entry_table . "` WHERE 1 LIMIT %d,%d",intval($offset),intval($limit)), ARRAY_A);
        $dst_data = array();
        $tmp_array = array('id' => null, 'submission_id' => null, 'form_id' => null, 'field' => null, 'value' => null);
        if (!$src_data || count($src_data) == 0)
            return "no_data_in_entries";

        $i = 1;
        $j = 0;
        foreach ($src_data as $row)
        {
            if (!$row['value'])
                continue;

            $sub_data = maybe_unserialize($row['value']);
            if (!$sub_data)
            {
                error_log("Damaged data found, trying to repair..");

                $sub_data = $this->unserialize_damaged($row['value']);
                if (!$sub_data)
                {
                    error_log("Repair failed..");
                    continue;
                } else
                {
                    error_log("Repair Successful..");
                }
            }
            foreach ($sub_data as $key => $value)
            {
                $tmp_array['id'] = $i++;
                $tmp_array['submission_id'] = $row['id'];
                $tmp_array['form_id'] = $row['form_id'];
                $tmp_array['field'] = $key;
                if (is_array($value))
                    $value = implode(",", $value);
                $tmp_array['value'] = $value;

                $dst_data[] = $tmp_array;
                $j++;
            }

            if ($j >= 1200)
            {
                $this->crf_entries_act_ins($dst_data);
                $dst_data = array();
                $j = 0;
            }
        }

        if ($j < 1200)
            $this->crf_entries_act_ins($dst_data);
    }

    public function crf_entries_act_ins($dst_data)
    {
        global $wpdb;
        $wpfx = $wpdb->prefix;
        $sub_table = $wpfx . 'crf_submissions';

        $cols = '`id`,`submission_id`,`form_id`,`field`,`value`';
        $value_ph = "(%d,%d,%d,%s,%s),";
        $values = '';
        $data_array_flatten = array();

        foreach ($dst_data as $index => $row)
        {
            $values .= $value_ph;

            foreach ($row as $colname => $value)
            {
                if (!$value)
                    $data_array_flatten[] = null; //$dst_data[$index][$colname] = null;
                else
                {
                    $data_array_flatten[] = $value;
                }
            }
        }

        $values = trim($values, ',');

        if (!$values)
            return;
        $sql = "INSERT INTO `$sub_table`($cols) VALUES $values";
        //print_r($sql);
        //s$wpdb->query($sql);

        $wpdb->query($wpdb->prepare($sql, $data_array_flatten));
    }

    public function unserialize_damaged($s)
    {
        $s = preg_replace_callback('/(s:)([0-9].*?)(:")(.*?)(";)/', array($this, 'replaceit'), $s);
        return maybe_unserialize($s);
    }

    public function replaceit($m)
    {
        $c = '';
        $a = strlen($m[4]);
        $c = $m[1] . $a . $m[3] . $m[4] . $m[5];

        return $c;
    }

    public function insert_primary_emails()
    {
        global $wpdb;
        $table_name_form = $wpdb->prefix . 'rm_forms';
        $table_name_field = $wpdb->prefix . 'rm_fields';

        if (is_array($this->all_forms))
        {
            foreach ($this->all_forms as $form_id)
            {

                if (!in_array($form_id, $this->created))
                {
                    $wpdb->insert(
                            $table_name_field, array(
                        'form_id' => $form_id,
                        'field_label' => 'Email',
                        'field_type' => 'Email',
                        'field_value' => NULL,
                        'field_order' => 0,
                        'field_show_on_user_page' => 0,
                        'is_field_primary' => 1,
                        'field_options' => 'O:8:"stdClass":9:{s:17:"field_placeholder";s:5:"Email";s:19:"field_default_value";N;s:15:"field_css_class";s:0:"";s:16:"field_max_length";N;s:22:"field_textarea_columns";N;s:19:"field_textarea_rows";N;s:17:"field_is_required";i:1;s:18:"field_is_read_only";i:0;s:21:"field_is_other_option";N;}'
                            ), array(
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%s'
                            )
                    );

                    $field_id = $wpdb->insert_id;
                    if (in_array($form_id, $this->reg_forms))
                    {
                        $form_options = $wpdb->get_var("SELECT form_options FROM $table_name_form WHERE `form_id` = $form_id");
                        $form_options = maybe_unserialize($form_options);
                        $form_options->mailchimp_mapped_email = 'Email_' . $field_id;
                        $wpdb->update($table_name_form, array('form_options' => maybe_serialize($form_options)), array('form_id' => $form_id), '%s', '%d');
                    }
                }
            }
        }
    }

    public function crf_assign_key_for_previous_field()
    {
        global $wpdb;
        $crf_fields = $wpdb->prefix . "crf_fields";
        $textdomain = 'custom-registration-form-builder-with-submission-manager';
        $qry = "select * from $crf_fields order by ordering asc";
        $reg = $wpdb->get_results($qry);
        foreach ($reg as $row)
        {
            $key = $this->crf_get_field_key($row);
            if (empty($row->Field_Key))
            {
                $wpdb->query($wpdb->prepare("update $crf_fields set Field_Key=%s where Id=%d", array($key, $row->Id)));
            }
        }
    }

    public function crf_get_field_key($row)
    {

        if ($row->Type == 'first_name' || $row->Type == 'last_name' || $row->Type == 'description')
        {
            $key = $row->Type;
        } else
        {
            if (isset($row->Field_Key) && $row->Field_Key != "")
            {
                $key = $row->Field_Key;
            } else
            {
                $key = sanitize_key($row->Name) . '_' . $row->Id;
            }
        }
        return $key;
    }

}
