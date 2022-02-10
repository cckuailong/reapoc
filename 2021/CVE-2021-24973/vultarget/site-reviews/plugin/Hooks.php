<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\HooksContract;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestController;
use GeminiLabs\SiteReviews\Controllers\BlocksController;
use GeminiLabs\SiteReviews\Controllers\EditorController;
use GeminiLabs\SiteReviews\Controllers\IntegrationController;
use GeminiLabs\SiteReviews\Controllers\ListTableController;
use GeminiLabs\SiteReviews\Controllers\MainController;
use GeminiLabs\SiteReviews\Controllers\MenuController;
use GeminiLabs\SiteReviews\Controllers\MetaboxController;
use GeminiLabs\SiteReviews\Controllers\NoticeController;
use GeminiLabs\SiteReviews\Controllers\PrivacyController;
use GeminiLabs\SiteReviews\Controllers\PublicController;
use GeminiLabs\SiteReviews\Controllers\QueueController;
use GeminiLabs\SiteReviews\Controllers\ReviewController;
use GeminiLabs\SiteReviews\Controllers\RevisionController;
use GeminiLabs\SiteReviews\Controllers\SettingsController;
use GeminiLabs\SiteReviews\Controllers\TaxonomyController;
use GeminiLabs\SiteReviews\Controllers\ToolsController;
use GeminiLabs\SiteReviews\Controllers\TranslationController;
use GeminiLabs\SiteReviews\Controllers\WelcomeController;
use GeminiLabs\SiteReviews\Database\SqlSchema;
use GeminiLabs\SiteReviews\Modules\Translation;

class Hooks implements HooksContract
{
    protected $admin;
    protected $basename;
    protected $blocks;
    protected $editor;
    protected $integrations;
    protected $listtable;
    protected $main;
    protected $menu;
    protected $metabox;
    protected $notices;
    protected $privacy;
    protected $public;
    protected $queue;
    protected $rest;
    protected $review;
    protected $revisions;
    protected $router;
    protected $settings;
    protected $taxonomy;
    protected $tools;
    protected $translator;
    protected $welcome;

    public function __construct()
    {
        $this->admin = glsr(AdminController::class);
        $this->basename = plugin_basename(glsr()->file);
        $this->blocks = glsr(BlocksController::class);
        $this->editor = glsr(EditorController::class);
        $this->integrations = glsr(IntegrationController::class);
        $this->listtable = glsr(ListTableController::class);
        $this->main = glsr(MainController::class);
        $this->menu = glsr(MenuController::class);
        $this->metabox = glsr(MetaboxController::class);
        $this->notices = glsr(NoticeController::class);
        $this->privacy = glsr(PrivacyController::class);
        $this->public = glsr(PublicController::class);
        $this->queue = glsr(QueueController::class);
        $this->rest = glsr(RestController::class);
        $this->review = glsr(ReviewController::class);
        $this->revisions = glsr(RevisionController::class);
        $this->router = glsr(Router::class);
        $this->settings = glsr(SettingsController::class);
        $this->taxonomy = glsr(TaxonomyController::class);
        $this->tools = glsr(ToolsController::class);
        $this->translator = glsr(TranslationController::class);
        $this->welcome = glsr(WelcomeController::class);
    }

    /**
     * @return void
     */
    public function addActions()
    {
        add_action('plugins_loaded', [$this, 'myIsamFallback']);
        add_action('load-edit.php', [$this, 'translateAdminEditPage']);
        add_action('load-post.php', [$this, 'translateAdminPostPage']);
        add_action('plugins_loaded', [$this, 'translatePlugin']);
        add_action('site-reviews/export/cleanup', [$this->admin, 'cleanupAfterExport']);
        add_action('admin_enqueue_scripts', [$this->admin, 'enqueueAssets']);
        add_action('admin_head', [$this->admin, 'printInlineStyle']);
        add_action('admin_init', [$this->admin, 'registerTinymcePopups']);
        add_action('media_buttons', [$this->admin, 'renderTinymceButton'], 11);
        add_action('admin_init', [$this->admin, 'onActivation']);
        add_action('import_end', [$this->admin, 'onImportEnd']);
        add_action('admin_init', [$this->admin, 'scheduleMigration']);
        add_action('site-reviews/route/ajax/search-posts', [$this->admin, 'searchPostsAjax']);
        add_action('site-reviews/route/ajax/search-translations', [$this->admin, 'searchTranslationsAjax']);
        add_action('site-reviews/route/ajax/search-users', [$this->admin, 'searchUsersAjax']);
        add_action('site-reviews/route/ajax/toggle-filters', [$this->admin, 'toggleFiltersAjax']);
        add_action('site-reviews/route/ajax/toggle-pinned', [$this->admin, 'togglePinnedAjax']);
        add_action('site-reviews/route/ajax/toggle-status', [$this->admin, 'toggleStatusAjax']);
        add_action('init', [$this->blocks, 'registerAssets'], 9); // This must be done before the blocks are registered
        add_action('init', [$this->blocks, 'registerBlocks']);
        add_action('site-reviews/route/ajax/mce-shortcode', [$this->editor, 'mceShortcodeAjax']);
        add_action('edit_form_top', [$this->editor, 'renderReviewNotice']);
        add_action('elementor/init', [$this->integrations, 'registerElementorCategory']);
        add_action('elementor/widgets/widgets_registered', [$this->integrations, 'registerElementorWidgets']);
        add_action('wp_ajax_inline-save', [$this->listtable, 'overrideInlineSaveAjax'], 0);
        add_action('load-edit.php', [$this->listtable, 'overridePostsListTable']);
        add_action('pre_get_posts', [$this->listtable, 'setQueryForColumn']);
        add_action('restrict_manage_posts', [$this->listtable, 'renderColumnFilters']);
        add_action('manage_'.glsr()->post_type.'_posts_custom_column', [$this->listtable, 'renderColumnValues'], 10, 2);
        add_action('init', [$this->main, 'initDefaults'], 1);
        add_action('wp_insert_site', [$this->main, 'installOnNewSite']);
        add_action('admin_footer', [$this->main, 'logOnce']);
        add_action('wp_footer', [$this->main, 'logOnce']);
        add_action('plugins_loaded', [$this->main, 'registerAddons']);
        add_action('plugins_loaded', [$this->main, 'registerLanguages'], 1); // do this first (may not be needed)
        add_action('init', [$this->main, 'registerPostType'], 8);
        add_action('init', [$this->main, 'registerReviewTypes'], 7);
        add_action('init', [$this->main, 'registerShortcodes']);
        add_action('init', [$this->main, 'registerTaxonomy']);
        add_action('widgets_init', [$this->main, 'registerWidgets']);
        add_action('admin_menu', [$this->menu, 'registerMenuCount']);
        add_action('admin_menu', [$this->menu, 'registerSubMenus']);
        add_action('admin_menu', [$this->menu, 'removeSubMenus'], 20);
        add_action('admin_init', [$this->menu, 'setCustomPermissions'], 999);
        add_action('add_meta_boxes_'.glsr()->post_type, [$this->metabox, 'registerMetaBoxes']);
        add_action('do_meta_boxes', [$this->metabox, 'removeMetaBoxes']);
        add_action('post_submitbox_misc_actions', [$this->metabox, 'renderPinnedInPublishMetaBox']);
        add_action('admin_notices', [$this->notices, 'adminNotices']);
        add_action('site-reviews/route/admin/dismiss-notice', [$this->notices, 'dismissNotice']);
        add_action('site-reviews/route/ajax/dismiss-notice', [$this->notices, 'dismissNoticeAjax']);
        add_action('wp_enqueue_scripts', [$this->public, 'enqueueAssets'], 999);
        add_action('site-reviews/route/ajax/fetch-paged-reviews', [$this->public, 'fetchPagedReviewsAjax']);
        add_filter('site-reviews/builder', [$this->public, 'modifyBuilder']);
        add_action('wp_footer', [$this->public, 'renderModal'], 50);
        add_action('wp_footer', [$this->public, 'renderSchema']);
        add_action('site-reviews/route/public/submit-review', [$this->public, 'submitReview']);
        add_action('site-reviews/route/ajax/submit-review', [$this->public, 'submitReviewAjax']);
        add_action('admin_init', [$this->privacy, 'privacyPolicyContent']);
        add_action('site-reviews/queue/recalculate-meta', [$this->queue, 'recalculateAssignmentMeta']);
        add_action('site-reviews/queue/migration', [$this->queue, 'runMigration']);
        add_action('site-reviews/queue/notification', [$this->queue, 'sendNotification']);
        add_action('rest_api_init', [$this->rest, 'registerRoutes']);
        add_action('admin_action_approve', [$this->review, 'approve']);
        add_action('the_posts', [$this->review, 'filterPostsToCacheReviews']);
        add_action('set_object_terms', [$this->review, 'onAfterChangeAssignedTerms'], 10, 6);
        add_action('transition_post_status', [$this->review, 'onAfterChangeStatus'], 10, 3);
        add_action('site-reviews/review/updated/post_ids', [$this->review, 'onChangeAssignedPosts'], 10, 2);
        add_action('site-reviews/review/updated/user_ids', [$this->review, 'onChangeAssignedUsers'], 10, 2);
        add_action('site-reviews/review/created', [$this->review, 'onCreatedReview'], 10, 2);
        add_action('site-reviews/review/create', [$this->review, 'onCreateReview'], 10, 2);
        add_action('post_updated', [$this->review, 'onEditReview'], 10, 3);
        add_action('site-reviews/review/created', [$this->review, 'sendNotification'], 20);
        add_action('admin_action_unapprove', [$this->review, 'unapprove']);
        add_action('wp_restore_post_revision', [$this->revisions, 'restoreRevision'], 10, 2);
        add_action('_wp_put_post_revision', [$this->revisions, 'saveRevision']);
        add_action('admin_init', [$this->router, 'routeAdminPostRequest']);
        add_action('wp_ajax_'.glsr()->prefix.'action', [$this->router, 'routeAjaxRequest']);
        add_action('wp_ajax_nopriv_'.glsr()->prefix.'action', [$this->router, 'routeAjaxRequest']);
        add_action('init', [$this->router, 'routePublicPostRequest']);
        add_action('admin_init', [$this->settings, 'registerSettings']);
        add_action('site-reviews/route/admin/clear-console', [$this->tools, 'clearConsole']);
        add_action('site-reviews/route/ajax/clear-console', [$this->tools, 'clearConsoleAjax']);
        add_action('site-reviews/route/admin/convert-table-engine', [$this->tools, 'convertTableEngine']);
        add_action('site-reviews/route/ajax/convert-table-engine', [$this->tools, 'convertTableEngineAjax']);
        add_action('site-reviews/route/admin/detect-ip-address', [$this->tools, 'detectIpAddress']);
        add_action('site-reviews/route/ajax/detect-ip-address', [$this->tools, 'detectIpAddressAjax']);
        add_action('site-reviews/route/admin/download-console', [$this->tools, 'downloadConsole']);
        add_action('site-reviews/route/admin/download-system-info', [$this->tools, 'downloadSystemInfo']);
        add_action('site-reviews/route/admin/fetch-console', [$this->tools, 'fetchConsole']);
        add_action('site-reviews/route/ajax/fetch-console', [$this->tools, 'fetchConsoleAjax']);
        add_action('site-reviews/route/admin/migrate-plugin', [$this->tools, 'migratePlugin']);
        add_action('site-reviews/route/ajax/migrate-plugin', [$this->tools, 'migratePluginAjax']);
        add_action('site-reviews/route/admin/export-settings', [$this->tools, 'exportSettings']);
        add_action('site-reviews/route/admin/import-reviews', [$this->tools, 'importReviews']);
        add_action('site-reviews/route/admin/import-settings', [$this->tools, 'importSettings']);
        add_action('site-reviews/route/admin/repair-review-relations', [$this->tools, 'repairReviewRelations']);
        add_action('site-reviews/route/ajax/repair-review-relations', [$this->tools, 'repairReviewRelationsAjax']);
        add_action('site-reviews/route/admin/reset-assigned-meta', [$this->tools, 'resetAssignedMeta']);
        add_action('site-reviews/route/ajax/reset-assigned-meta', [$this->tools, 'resetAssignedMetaAjax']);
        add_action('site-reviews/route/admin/reset-permissions', [$this->tools, 'resetPermissions']);
        add_action('site-reviews/route/ajax/reset-permissions', [$this->tools, 'resetPermissionsAjax']);
        add_action('activated_plugin', [$this->welcome, 'redirectOnActivation'], 10, 2);
        add_action('admin_menu', [$this->welcome, 'registerPage']);
    }

    /**
     * @return void
     */
    public function addFilters()
    {
        add_filter('plugin_action_links_'.$this->basename, [$this->admin, 'filterActionLinks']);
        add_filter('map_meta_cap', [$this->admin, 'filterCapabilities'], 10, 4);
        add_filter('dashboard_glance_items', [$this->admin, 'filterDashboardGlanceItems']);
        add_filter('export_args', [$this->admin, 'filterExportArgs'], 11);
        add_filter('mce_external_plugins', [$this->admin, 'filterTinymcePlugins'], 15);
        add_filter('allowed_block_types_all', [$this->blocks, 'filterAllowedBlockTypes'], 10, 2);
        add_filter('block_categories_all', [$this->blocks, 'filterBlockCategories']);
        add_filter('use_block_editor_for_post_type', [$this->blocks, 'filterUseBlockEditor'], 10, 2);
        add_filter('widget_types_to_hide_from_legacy_widget_block', [$this->blocks, 'replaceLegacyWidgets']);
        add_filter('wp_editor_settings', [$this->editor, 'filterEditorSettings']);
        add_filter('the_editor', [$this->editor, 'filterEditorTextarea']);
        add_filter('is_protected_meta', [$this->editor, 'filterIsProtectedMeta'], 10, 3);
        add_filter('post_updated_messages', [$this->editor, 'filterUpdateMessages']);
        add_filter('site-reviews/enqueue/public/inline-script/after', [$this->integrations, 'filterElementorPublicInlineScript'], 1);
        add_filter('site-reviews/defaults/star-rating/defaults', [$this->integrations, 'filterElementorStarRatingDefaults']);
        add_filter('heartbeat_received', [$this->listtable, 'filterCheckLockedReviews'], 20, 3);
        add_filter('manage_'.glsr()->post_type.'_posts_columns', [$this->listtable, 'filterColumnsForPostType']);
        add_filter('post_date_column_status', [$this->listtable, 'filterDateColumnStatus'], 10, 2);
        add_filter('default_hidden_columns', [$this->listtable, 'filterDefaultHiddenColumns'], 10, 2);
        add_filter('posts_clauses', [$this->listtable, 'filterPostClauses'], 10, 2);
        add_filter('post_row_actions', [$this->listtable, 'filterRowActions'], 10, 2);
        add_filter('screen_settings', [$this->listtable, 'filterScreenFilters'], 10, 2);
        add_filter('manage_edit-'.glsr()->post_type.'_sortable_columns', [$this->listtable, 'filterSortableColumns']);
        add_filter('site-reviews/devmode', [$this->main, 'filterDevmode'], 1);
        add_filter('wpmu_drop_tables', [$this->main, 'filterDropTables'], 999); // run last
        add_filter('site-reviews/config/forms/metabox-fields', [$this->metabox, 'filterFieldOrder'], 11);
        add_filter('wp_privacy_personal_data_erasers', [$this->privacy, 'filterPersonalDataErasers']);
        add_filter('wp_privacy_personal_data_exporters', [$this->privacy, 'filterPersonalDataExporters']);
        add_filter('script_loader_tag', [$this->public, 'filterEnqueuedScriptTags'], 10, 2);
        add_filter('site-reviews/config/forms/review-form', [$this->public, 'filterFieldOrder'], 11);
        add_filter('site-reviews/render/view', [$this->public, 'filterRenderView']);
        add_filter('wp_insert_post_data', [$this->review, 'filterReviewPostData'], 10, 2);
        add_filter('site-reviews/rendered/template/review', [$this->review, 'filterReviewTemplate'], 10, 2);
        add_filter('site-reviews/query/sql/clause/operator', [$this->review, 'filterSqlClauseOperator'], 1);
        add_filter('site-reviews/review/build/after', [$this->review, 'filterTemplateTags'], 10, 3);
        add_filter('wp_save_post_revision_check_for_changes', [$this->revisions, 'filterCheckForChanges'], 99, 3);
        add_filter('wp_save_post_revision_post_has_changed', [$this->revisions, 'filterReviewHasChanged'], 10, 3);
        add_filter('wp_get_revision_ui_diff', [$this->revisions, 'filterRevisionUiDiff'], 10, 3);
        add_filter(glsr()->taxonomy.'_row_actions', [$this->taxonomy, 'filterRowActions'], 10, 2);
        add_filter('plugin_action_links_'.$this->basename, [$this->welcome, 'filterActionLinks'], 11);
        add_filter('admin_title', [$this->welcome, 'filterAdminTitle']);
    }

    /**
     * @return void
     */
    public function myIsamFallback()
    {
        if (!glsr(SqlSchema::class)->isInnodb('posts')) {
            add_action('deleted_post', [$this->review, 'onDeletePost'], 10, 2);
        }
        if (!glsr(SqlSchema::class)->isInnodb('users')) {
            add_action('deleted_user', [$this->review, 'onDeleteUser'], 10);
        }
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->addActions();
        $this->addFilters();
    }

    /**
     * @return void
     * @action load-edit.php
     */
    public function translateAdminEditPage()
    {
        if (glsr()->post_type === glsr_current_screen()->post_type) {
            add_filter('bulk_post_updated_messages', [$this->translator, 'filterBulkUpdateMessages'], 10, 2);
            add_filter('display_post_states', [$this->translator, 'filterPostStates'], 10, 2);
            add_filter('gettext_default', [$this->translator, 'filterPostStatusLabels'], 10, 2);
            add_filter('ngettext_default', [$this->translator, 'filterPostStatusText'], 10, 4);
        }
    }

    /**
     * @return void
     * @action load-post.php
     */
    public function translateAdminPostPage()
    {
        if (glsr()->post_type === glsr_current_screen()->post_type) {
            add_filter('gettext_default', [$this->translator, 'filterPostStatusLabels'], 10, 2);
            add_action('admin_print_scripts-post.php', [$this->translator, 'translatePostStatusLabels']);
        }
    }

    /**
     * @return void
     * @action plugins_loaded
     */
    public function translatePlugin()
    {
        if (!empty(glsr(Translation::class)->translations())) {
            add_filter('gettext_'.glsr()->id, [$this->translator, 'filterGettext'], 10, 2);
            add_filter('gettext_with_context_'.glsr()->id, [$this->translator, 'filterGettextWithContext'], 10, 3);
            add_filter('ngettext_'.glsr()->id, [$this->translator, 'filterNgettext'], 10, 4);
            add_filter('ngettext_with_context_'.glsr()->id, [$this->translator, 'filterNgettextWithContext'], 10, 5);
        }
    }
}
