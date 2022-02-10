<?php

if (!defined('ABSPATH')) exit;

class AsgarosForumOnline {
    private $asgarosforum = null;
    private $current_user_id = null;
    private $current_time_stamp = null;
    public $functionality_enabled = false;
    private $interval_update = false;
    private $interval_online = false;
    private $online_users = array();
    private $online_guests = array();
    private $online_guests_changed = false;

    public function __construct($object) {
		$this->asgarosforum = $object;
        $this->interval_update = (1 * MINUTE_IN_SECONDS);
        $this->interval_online = (10 * MINUTE_IN_SECONDS);

        add_action('init', array($this, 'initialize'));
        add_action('clear_auth_cookie', array($this, 'delete_user_time_stamp'));
    }

    public function initialize() {
        $this->functionality_enabled = $this->asgarosforum->options['show_who_is_online'];
    }

    public function update_online_status() {
        if ($this->functionality_enabled) {
            // Set some initial data.
            $this->current_user_id = get_current_user_id();
            $this->current_time_stamp = $this->asgarosforum->current_time();

            // Load list of online users.
            $this->load_online_users();

            if ($this->current_user_id) {
                // Clean guest-counter data for logged-in user.
                if (isset($_COOKIE['asgarosforum_unique_id'])) {
                    $unique_id = sanitize_key($_COOKIE['asgarosforum_unique_id']);

                    // Delete cookie first.
                    unset($_COOKIE['asgarosforum_unique_id']);
                    setcookie('asgarosforum_unique_id', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);

                    // Remove him from online-guests list.
                    if (isset($this->online_guests[$unique_id])) {
                        unset($this->online_guests[$unique_id]);
                        $this->online_guests_changed = true;
                    }
                }

                // Check if the online-flag exists. If not, set it.
                $user_online_flag = get_user_meta($this->current_user_id, 'asgarosforum_online', true);

                if (!$user_online_flag) {
                    update_user_meta($this->current_user_id, 'asgarosforum_online', true);
                }

                // Get the timestamp of the current user.
                $user_time_stamp = get_user_meta($this->current_user_id, 'asgarosforum_online_timestamp', true);

                // If there is no timestamp for that user or the update interval passed, create/update it.
                if (!$user_time_stamp || ((strtotime($this->current_time_stamp) - strtotime($user_time_stamp)) > $this->interval_update)) {
                    update_user_meta($this->current_user_id, 'asgarosforum_online_timestamp', $this->current_time_stamp);
                }

                // Add the user to the online list when he is not already included.
                if (!in_array($this->current_user_id, $this->online_users)) {
                    $this->online_users[] = $this->current_user_id;
                }
            } else {
                $unique_id = uniqid();

                // For guests we need a cookie with a unique id first to ensure that we do not count him multiple times.
                if (!isset($_COOKIE['asgarosforum_unique_id'])) {
                    setcookie('asgarosforum_unique_id', $unique_id, 2147483647, COOKIEPATH, COOKIE_DOMAIN);
                } else {
                    $unique_id = sanitize_key($_COOKIE['asgarosforum_unique_id']);
                }

                // Add the user to the online list when he is not already included.
                if (!isset($this->online_guests[$unique_id]) || ((strtotime($this->current_time_stamp) - strtotime($this->online_guests[$unique_id])) > $this->interval_update)) {
                    $this->online_guests[$unique_id] = $this->current_time_stamp;
                    $this->online_guests_changed = true;
                }
            }

            // Clean up existing guests-entries.
            foreach ($this->online_guests as $key => $value) {
                if ((strtotime($this->current_time_stamp) - strtotime($value)) > $this->interval_online) {
                    unset($this->online_guests[$key]);
                    $this->online_guests_changed = true;
                }
            }

            // Save guest-timestamps back into database.
            if ($this->online_guests_changed) {
                update_option('asgarosforum_guests_timestamps', $this->online_guests);
            }
        }
    }

    public function load_online_users() {
        $minimum_check_time = date_i18n('Y-m-d H:i:s', (strtotime($this->current_time_stamp) - $this->interval_online));

        // Get list of online users.
        $query = new AsgarosForumUserQuery(array('meta_key' => 'asgarosforum_online'));
        $results_flag = $query->results;

        $query = new AsgarosForumUserQuery(array('meta_key' => 'asgarosforum_online_timestamp', 'meta_value' => $minimum_check_time, 'meta_compare' => '>='));
        $results_stamp = $query->results;

        // Do an intersection by the keys of both arrays so we have all users with an online-flag and a valid timestamp.
        $users_intersection = array_intersect_key($results_flag, $results_stamp);

        // The array-keys are actually the user IDs.
        $this->online_users = array_keys($users_intersection);

        // Get list of online guests.
        $this->online_guests = get_option('asgarosforum_guests_timestamps', array());
    }

    public function render_statistics_element() {
        if ($this->functionality_enabled) {
            $counter = count($this->online_users) + count($this->online_guests);
            AsgarosForumStatistics::renderStatisticsElement(__('Online', 'asgaros-forum'), $counter, 'far fa-lightbulb');
        }
    }

    public function render_online_information() {
        if ($this->functionality_enabled) {
            $currently_online_users = (!empty($this->online_users)) ? get_users(array('include' => $this->online_users)) : false;
            $currently_online_guests = (!empty($this->online_guests)) ? $this->online_guests : false;

            echo '<div id="statistics-online-users">';

            if ($this->asgarosforum->options['show_newest_member']) {
                $newest_member = get_users(array('orderby' => 'ID', 'order' => 'DESC', 'number' => 1));

                echo '<span class="online-users-icon fas fa-user"></span>';
                echo esc_html__('Newest Member:', 'asgaros-forum').'&nbsp;<i>'.$this->asgarosforum->renderUsername($newest_member[0]).'</i>';
                echo '&nbsp;&middot;&nbsp;';
            }

            echo '<span class="online-users-icon fas fa-users"></span>';

            if ($currently_online_users || $currently_online_guests) {
                echo esc_html__('Currently Online:', 'asgaros-forum').'&nbsp;<i>';

                $loop_counter = 0;

                if ($currently_online_users) {
                    foreach ($currently_online_users as $online_user) {
                        $loop_counter++;

                        if ($loop_counter > 1) {
                            echo ', ';
                        }

                        echo $this->asgarosforum->renderUsername($online_user);
                    }
                }

                if ($currently_online_guests) {
                    $loop_counter++;

                    if ($loop_counter > 1) {
                        echo ', ';
                    }

                    $guests_counter = count($currently_online_guests);
                    $guests_counter_output = sprintf(_n('%s Guest', '%s Guests', $guests_counter, 'asgaros-forum'), number_format_i18n($guests_counter));
                    echo esc_html($guests_counter_output);
                }

                echo '</i>';
            } else {
                echo '<i>'.esc_html__('Currently nobody is online.', 'asgaros-forum').'</i>';
            }

            echo '</div>';
        }
    }

    public function is_user_online($user_id) {
        if ($this->functionality_enabled && in_array($user_id, $this->online_users)) {
            return true;
        } else {
            return false;
        }
    }

    public function last_seen($user_id) {
        if ($this->is_user_online($user_id)) {
            return __('Currently online', 'asgaros-forum');
        } else {
            $user_time_stamp = get_user_meta($user_id, 'asgarosforum_online_timestamp', true);

            // Use registration date when there is no timestamp yet.
            if (!$user_time_stamp) {
                $user_data = get_userdata($user_id);
                $user_time_stamp = $user_data->user_registered;
            }

            return sprintf(__('Seen %s ago', 'asgaros-forum'), human_time_diff(strtotime($user_time_stamp), current_time('timestamp')));
        }
    }

    public function delete_user_time_stamp() {
        delete_user_meta(get_current_user_id(), 'asgarosforum_online');
    }
}
