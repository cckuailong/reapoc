<?php

add_action('wp_ajax_wprss_feed_source_table_ajax', function () {
    $response = [];

    if (!current_user_can('edit_feed_sources') || empty($_POST['wprss_heartbeat'])) {
        return [];
    }

    $request = isset($_POST['wprss_heartbeat']) ? $_POST['wprss_heartbeat'] : null;
    if (!is_array($request)) {
        return [];
    }

    $action = isset($request['action']) ? $request['action'] : '';
    $action = filter_var($action, FILTER_SANITIZE_STRING);

    $params = isset($request['params']) ? $request['params'] : '';
    $params = filter_var($params, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

    switch ($action) {
        case 'feed_sources':
        {
            $feeds = [];

            foreach ($params as $feedId) {
                $nextFetch = wprss_get_next_feed_source_update($feedId, false);
                $isFetching = wprss_is_feed_source_updating($feedId);
                $isDeleting = wprss_is_feed_source_deleting($feedId);
                $fetchesSoon = $nextFetch !== false && $nextFetch < 2 && $nextFetch > 0;
                $isActive = wprss_is_feed_source_active($feedId);
                $numItems = wprss_get_feed_items_for_source($feedId)->post_count;
                $lastUpdateTime = get_post_meta($feedId, 'wprss_last_update', true);
                $lastUpdateItems = get_post_meta($feedId, 'wprss_last_update_items', true);
                $errors = get_post_meta($feedId, 'wprss_error_last_import', true);

                if ($nextFetch === false) {
                    $nextFetchText = __('None', 'wprss');
                } elseif ($isActive) {
                    $nextFetchText = $fetchesSoon
                        ? _x('now', 'Next update: now', 'wprss')
                        : human_time_diff($nextFetch, time());
                } else {
                    $nextFetchText = __('...', 'wprss');
                }

                $feeds[$feedId] = [
                    'active' => $isActive,
                    'fetching' => $isFetching || $fetchesSoon,
                    'deleting' => $isDeleting,
                    'items' => $numItems,
                    'next-update' => $nextFetchText,
                    'last-update' => empty($lastUpdateTime) ? '' : human_time_diff($lastUpdateTime, time()),
                    'last-update-imported' => $lastUpdateItems,
                    'errors' => $errors,
                ];

                update_post_meta($feedId, 'wprss_items_imported', $numItems);
                update_post_meta($feedId, 'wprss_next_update', $nextFetchText);
            }

            $response = [
                'wprss_feed_sources_data' => $feeds,
            ];
            break;
        }
    }

    echo json_encode($response);
    die;
});
