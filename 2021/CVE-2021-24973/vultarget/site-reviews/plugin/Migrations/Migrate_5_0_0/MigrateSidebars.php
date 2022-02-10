<?php

namespace GeminiLabs\SiteReviews\Migrations\Migrate_5_0_0;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class MigrateSidebars
{
    public $db;
    public $limit;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->limit = 250;
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->migrateSidebarWidgets();
        $this->migrateThemeModWidgets();
        $this->migrateUserMeta();
        $this->migrateWidgets();
    }

    /**
     * @return array
     */
    protected function mapWidgetData(array $data)
    {
        $mappedKeys = [
            'assign_to' => 'assigned_posts',
            'assigned_to' => 'assigned_posts',
            'category' => 'assigned_terms',
            'per_page' => 'display',
            'user' => 'assigned_users',
        ];
        foreach ($mappedKeys as $oldKey => $newKey) {
            if (array_key_exists($oldKey, $data)) {
                $data[$newKey] = $data[$oldKey];
                unset($data[$oldKey]);
            }
        }
        return $data;
    }

    /**
     * @return void
     */
    protected function migrateSidebarWidgets()
    {
        $sidebars = Arr::consolidate(get_option('sidebars_widgets'));
        if ($this->widgetsExist($sidebars)) {
            $sidebars = $this->updateWidgetNames($sidebars);
            update_option('sidebars_widgets', $sidebars);
        }
    }

    /**
     * @return void
     */
    protected function migrateThemeModWidgets()
    {
        $themes = $this->queryThemeMods();
        foreach ($themes as $theme) {
            $themeMod = get_option($theme);
            $sidebars = Arr::consolidate(Arr::get($themeMod, 'sidebars_widgets.data'));
            if ($this->widgetsExist($sidebars)) {
                $themeMod['sidebars_widgets']['data'] = $this->updateWidgetNames($sidebars);
                update_option($theme, $themeMod);
            }
        }
    }

    /**
     * @return void
     */
    protected function migrateUserMeta()
    {
        $postType = glsr()->post_type;
        $metaKey = 'meta-box-order_'.$postType;
        $metaOrder = [
            'side' => [
                'submitdiv',
                $postType.'-categorydiv',
                $postType.'-postsdiv',
                $postType.'-usersdiv',
                $postType.'-authordiv',
            ],
            'normal' => [
                $postType.'-responsediv',
                $postType.'-detailsdiv',
            ],
            'advanced' => [],
        ];
        array_walk($metaOrder, function (&$order) {
            $order = implode(',', $order);
        });
        $userIds = get_users([
            'fields' => 'ID',
            'meta_compare' => 'EXISTS',
            'meta_key' => $metaKey,
        ]);
        foreach ($userIds as $userId) {
            update_user_meta($userId, $metaKey, $metaOrder);
        }
    }

    /**
     * @param mixed $option
     * @return string|array
     */
    protected function migrateWidgetData($option)
    {
        if (!is_array($option)) {
            return $option;
        }
        foreach ($option as $index => $values) {
            if (is_array($values)) {
                $option[$index] = $this->mapWidgetData($values);
            }
        }
        return $option;
    }

    /**
     * @return void
     */
    protected function migrateWidgets()
    {
        $widgets = [
            'site-reviews',
            'site-reviews-form',
            'site-reviews-summary',
        ];
        foreach ($widgets as $widget) {
            $oldWidget = 'widget_'.glsr()->id.'_'.$widget;
            $newWidget = 'widget_'.glsr()->prefix.$widget;
            if ($option = get_option($oldWidget)) {
                update_option($newWidget, $this->migrateWidgetData($option));
                delete_option($oldWidget);
            }
        }
    }

    /**
     * @return array
     */
    protected function queryThemeMods()
    {
        return $this->db->get_col("
            SELECT option_name 
            FROM {$this->db->options} 
            WHERE option_name LIKE '%theme_mods_%'
        ");
    }

    /**
     * @param array $sidebars
     * @return array
     */
    protected function updateWidgetNames(array $sidebars)
    {
        array_walk($sidebars, function (&$widgets) {
            array_walk($widgets, function (&$widget) {
                if (Str::startsWith(glsr()->id.'_', $widget)) {
                    $widget = Str::replaceFirst(glsr()->id.'_', glsr()->prefix, $widget);
                }
            });
        });
        return $sidebars;
    }

    /**
     * @return bool
     */
    protected function widgetsExist(array $sidebars)
    {
        $sidebars = array_filter($sidebars, 'is_array');
        $sidebars = array_values($sidebars);
        $widgets = call_user_func_array('array_merge', $sidebars);
        $widgets = Arr::consolidate($widgets); // ensure this is an array in case call_user_func_array() errors
        foreach ($widgets as $widget) {
            if (Str::startsWith(glsr()->id.'_', $widget)) {
                return true;
            }
        }
        return false;
    }
}
