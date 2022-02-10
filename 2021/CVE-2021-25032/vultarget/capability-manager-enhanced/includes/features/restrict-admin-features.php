<?php

class PP_Capabilities_Admin_Features
{

    /**
     * Get all admin features layout.
     *
     * @return array Elements layout.
     */
    public static function elementsLayout()
    {
        $elements = [];

        //Add toolbar
        $elements[__('Admin Toolbar', 'capsman-enhanced')] = self::formatAdminToolbar();

        //Add dashboard widget
        $elements[__('Dashboard widgets', 'capsman-enhanced')] = self::formatDashboardWidgets();

        return apply_filters('pp_capabilities_admin_features_elements', $elements);
    }

    /**
     * Retrieve all items icons.
     *
     * @return array Items icons.
     */
    public static function elementLayoutItemIcons()
    {
        $icons = [];

        $icons['admintoolbar']     = 'open-folder';
        $icons['dashboardwidgets'] = 'dashboard';
        $icons['menu-toggle']      = 'menu';
        $icons['wp-logo']          = 'wordpress';
        $icons['site-name']        = 'admin-home';
        $icons['updates']          = 'update';
        $icons['comments']         = 'admin-comments';
        $icons['new-content']      = 'plus';
        $icons['wpseo-menu']       = 'open-folder';
        $icons['top-secondary']    = 'admin-users';

        return apply_filters('pp_capabilities_admin_features_icons', $icons);
    }

    /**
     * Let provide support for known adminbar with empty title due to icon title only.
     *
     */
    public static function elementToolbarTitleFallback($id)
    {
        $title = [];

        $title['menu-toggle']      = __('Mobile Menu Toggle', 'capsman-enhanced');
        $title['wp-logo']          = __('WordPress Logo', 'capsman-enhanced');
        $title['wp-logo-external'] = __('WordPress External Links', 'capsman-enhanced');
        $title['updates']          = __('Updates', 'capsman-enhanced');
        $title['comments']         = __('Comments', 'capsman-enhanced');
        $title['top-secondary']    = __('Right bar', 'capsman-enhanced');
        $title['user-actions']     = __('User actions', 'capsman-enhanced');
        $title['new-content']      = __('New', 'capsman-enhanced');
        $title['new-content']      = __('New', 'capsman-enhanced');
        $title['user-info']        = __('User Display Name', 'capsman-enhanced');
        $title['wpseo-menu']       = __('Yoast SEO', 'capsman-enhanced');

        return isset($title[$id]) ? $title[$id] : $id;
    }

    /**
     * Get the list of dashboard widgets.
     *
     * @return array dashboard widgets.
     */
    public static function dashboardWidgets()
    {
        global $wp_meta_boxes;

        $screen = is_network_admin() ? 'dashboard-network' : 'dashboard';
		$action = is_network_admin() ? 'wp_network_dashboard_setup' : 'wp_dashboard_setup';
        $current_screen = get_current_screen();

		//set current screen as dashboard to get widgets
        if (!isset($wp_meta_boxes[$screen]) || !is_array($wp_meta_boxes[$screen])) {
            require_once ABSPATH . '/wp-admin/includes/dashboard.php';
            set_current_screen($screen);
			remove_action( $action, [ __CLASS__, 'disableDashboardWidgets' ], 99 );
            wp_dashboard_setup();
			add_action( $action, [ __CLASS__, 'disableDashboardWidgets' ], 99 );
        }

        $widgets = [];
        if (isset($wp_meta_boxes[$screen])) {
            $widgets = $wp_meta_boxes[$screen];
        }

		//set current screen to it original stage
        set_current_screen($current_screen);

        return $widgets;
    }

    /**
     * Format dashboard widgets.
     *
     * @return array Elements layout item.
     */
    public static function formatDashboardWidgets()
    {
        $widgets = self::dashboardWidgets();

        $elements_widget = [];
        //add widget that may not be part of wp_meta_boxes
        $elements_widget['dashboard_welcome_panel'] = ['label'  => __('Welcome panel', 'capsman-enhanced'), 'context' => 'normal', 'action' => 'ppc_dashboard_widget'];
        //loop other widgets
        foreach ($widgets as $context => $priority) {
            foreach ($priority as $data) {
                foreach ($data as $id => $widget) {
                    if ($widget) {
                        $widget_title         = isset($widget['title']) ? wp_strip_all_tags($widget['title']) : '';
                        $elements_widget[$id] = ['label' => $widget_title, 'context' => $context, 'action' => 'ppc_dashboard_widget'];
                    }
                }
            }
        }

        return $elements_widget;
    }

    /**
     * Format admin toolbar.
     *
     * @return array Elements layout item.
     */
    public static function formatAdminToolbar()
    {
        global $toolbar_items;

        $toolbars    = (array)$GLOBALS['ppcAdminBar'];
        $toolbarTree = self::formatAdminToolbarTree($toolbars);
        //set toolbar element with steps
        self::setAdminToolbarElement($toolbarTree);

        return $toolbar_items;
    }

    /**
     * Build multidimensional array for admin toolbar.
     *
     * @return array.
     */
    public static function formatAdminToolbarTree(array $items, $parentId = '')
    {
        $branch = [];

        foreach ($items as $item) {
            if ($item['parent'] == $parentId) {
                $children = self::formatAdminToolbarTree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }

        return $branch;
    }

    /**
     * Set admin toolbar element.
     *
     */
    public static function setAdminToolbarElement(array $toolbarTrees, $steps = 1, $step_list = [])
    {
        global $toolbar_items;

        $position = 0;
        foreach ($toolbarTrees as $toolbarTree) {
            $position++;
            $id        = $toolbarTree['id'];
            $itemTitle = self::cleanTitleText($toolbarTree['title']);

            //let fall back to known title/id if title still empty
            if (empty(trim($itemTitle))) {
                $itemTitle = self::elementToolbarTitleFallback($id);
            }

            $toolbar_items[$id] = ['label'    => $itemTitle,
                                   'parent'   => $toolbarTree['parent'],
                                   'step'     => $steps,
                                   'position' => $position,
                                   'action'   => 'ppc_adminbar'
            ];
            foreach ($toolbarTree as $key => $value) {
                if (is_array($value)) {
                    self::setAdminToolbarElement($value, $steps + 1, $step_list);
                }
            }
        }
    }

    /**
     * Process admin features title.
     *
     */
    public static function cleanTitleText($title)
    {
        //strip span and div content
        $title = preg_replace('#(<span.*?>).*?(</span>)#', '', $title);
        $title = preg_replace('#(<img.*?>)#', '', $title);

        //strip other html tags
        $title = strip_tags($title);

        return $title;
    }

    /**
     * Get array elements that starts with a specific word
	 * 
	 * @param array $restricted_features All restricted elements to check agains.
	 * @param string $start_with The word to look for in array.
	 * 
     * @return array Filtered array.
     */
    public static function adminFeaturesRestrictedElements($restricted_elements, $start_with = 'ppc_adminbar')
    {
		//get all items of the array starting with the specified string.  
		$new_elements = array_filter( 
			$restricted_elements,
			function($value, $key) use ($start_with) {return strpos($value, $start_with) === 0;}, ARRAY_FILTER_USE_BOTH
		);

		return $new_elements;
	}


    /**
     * Apply admin feature restrictions
     */
    public static function adminFeaturedRestriction()
    {
		global $ppc_disabled_toolbar, $ppc_disabled_widget;
        // Get all user roles.
        $user_roles = wp_get_current_user()->roles;
        $disabled_features = get_option("capsman_disabled_admin_features", []);

        $all_disabled_elements = [];

        foreach ($user_roles as $role) {
            if (!empty($disabled_features[$role])) {
                $all_disabled_elements[] = $disabled_features[$role];
            }
        }

		//merge all array values incase it's more than role
        //$all_disabled_elements = array_merge(...$all_disabled_elements);  // This is a PHP 7.4 operator
        $all_disabled_elements = (is_array($all_disabled_elements) && isset($all_disabled_elements[0])) ? array_merge($all_disabled_elements[0]) : [];

		//disable toolbar
		$ppc_disabled_toolbar = self::adminFeaturesRestrictedElements($all_disabled_elements, 'ppc_adminbar');
		if(count($ppc_disabled_toolbar) > 0){
			add_action( 'wp_before_admin_bar_render', [ __CLASS__, 'disableDashboardBar' ], 99 );
		}

		if(is_admin()){
			$ppc_disabled_widget = self::adminFeaturesRestrictedElements($all_disabled_elements, 'ppc_dashboard_widget');
			//disable widget
			if(count($ppc_disabled_widget) > 0){
				add_action( 'wp_dashboard_setup', [ __CLASS__, 'disableDashboardWidgets' ], 99 );
				add_action( 'wp_network_dashboard_setup', [ __CLASS__, 'disableDashboardWidgets' ], 99 );
			}
		}
    }

	/**
	 * Disable admin bar.
	 *
	 */
	public static function disableDashboardBar() {
		global $wp_admin_bar, $ppc_disabled_toolbar;
		$admin_bar_options = (array)$ppc_disabled_toolbar;
		$admin_bar_items   = (array)$GLOBALS['ppcAdminBar'];

		if (count($admin_bar_options) > 0 && ( is_array($admin_bar_items) || is_object($admin_bar_items) ) ) {
			foreach ($admin_bar_items as $barItem) {
				$id = $barItem['id'];
				$item_id = 'ppc_adminbar||'.$id;
				if ($id && in_array($item_id, $admin_bar_options)) {
					$wp_admin_bar->remove_menu($id);
				}
			}
		}

	}

	/**
	 * Disable dashboard widgets.
	 *
	 */
	public static function disableDashboardWidgets() {
		global $ppc_disabled_widget;
		
		$widgets = (array)$ppc_disabled_widget;

		if ( count($widgets) === 0 ) {
			return;
		}

		foreach ( $widgets as $widget) {
			$widget_data = explode("||", $widget);
			$widget_id = $widget_data[1];
			$widget_content = $widget_data[2];

			if ( $widget_id === 'dashboard_welcome_panel' ) {
				remove_action( 'welcome_panel', 'wp_welcome_panel' );
			}else{
				remove_meta_box( $widget_id, get_current_screen()->base, $widget_content );
			}
		}
	}

}
