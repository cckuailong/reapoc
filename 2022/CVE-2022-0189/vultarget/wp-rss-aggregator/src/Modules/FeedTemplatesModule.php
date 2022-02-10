<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use RebelCode\Entities\Properties\Property;
use RebelCode\Entities\Schemas\Schema;
use RebelCode\Wpra\Core\Data\ArrayDataSet;
use RebelCode\Wpra\Core\Data\Collections\NullCollection;
use RebelCode\Wpra\Core\Entities\Collections\FeedTemplateCollection;
use RebelCode\Wpra\Core\Entities\Stores\BuiltInTemplateStore;
use RebelCode\Wpra\Core\Handlers\AddCptMetaCapsHandler;
use RebelCode\Wpra\Core\Handlers\FeedTemplates\AjaxRenderFeedsTemplateHandler;
use RebelCode\Wpra\Core\Handlers\FeedTemplates\CreateDefaultFeedTemplateHandler;
use RebelCode\Wpra\Core\Handlers\FeedTemplates\HidePublicTemplateContentHandler;
use RebelCode\Wpra\Core\Handlers\FeedTemplates\PreviewTemplateRedirectHandler;
use RebelCode\Wpra\Core\Handlers\FeedTemplates\RenderAdminTemplatesPageHandler;
use RebelCode\Wpra\Core\Handlers\FeedTemplates\RenderTemplateContentHandler;
use RebelCode\Wpra\Core\Handlers\FeedTemplates\ReSaveTemplateHandler;
use RebelCode\Wpra\Core\Handlers\NullHandler;
use RebelCode\Wpra\Core\Handlers\RegisterCptHandler;
use RebelCode\Wpra\Core\Handlers\RegisterSubMenuPageHandler;
use RebelCode\Wpra\Core\RestApi\EndPoints\EndPoint;
use RebelCode\Wpra\Core\RestApi\EndPoints\FeedTemplates\CreateUpdateTemplateEndPoint;
use RebelCode\Wpra\Core\RestApi\EndPoints\FeedTemplates\DeleteTemplateEndPoint;
use RebelCode\Wpra\Core\RestApi\EndPoints\FeedTemplates\GetTemplatesEndPoint;
use RebelCode\Wpra\Core\RestApi\EndPoints\FeedTemplates\PatchTemplateEndPoint;
use RebelCode\Wpra\Core\RestApi\EndPoints\FeedTemplates\RenderTemplateEndPoint;
use RebelCode\Wpra\Core\Templates\Feeds\MasterFeedsTemplate;
use RebelCode\Wpra\Core\Templates\Feeds\TemplateTypeTemplate;
use RebelCode\Wpra\Core\Templates\Feeds\Types\ListTemplateType;
use RebelCode\Wpra\Core\Wp\Asset\ScriptAsset;
use RebelCode\Wpra\Core\Wp\Asset\StyleAsset;
use WP_Post;

/**
 * The templates module for WP RSS Aggregator.
 *
 * @since 4.13
 */
class FeedTemplatesModule implements ModuleInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getFactories()
    {
        return [
            /*
             * The properties for feed template entities.
             *
             * @since 4.16
             */
            'wpra/feeds/templates/properties' => function () {
                return [
                    'id' => new Property('ID'),
                    'name' => new Property('post_title'),
                    'slug' => new Property('post_name'),
                    'type' => new Property('wprss_template_type'),
                    'options' => new Property('wprss_template_options'),
                ];
            },
            /*
             * The default values for feed template entities.
             *
             * @since 4.16
             */
            'wpra/feeds/templates/defaults' => function () {
                return [
                    'id' => null,
                    'name' => '',
                    'slug' => '',
                    'type' => '',
                    'options' => [],
                ];
            },
            /*
             * The schema for feed templates.
             *
             * @since 4.16
             */
            'wpra/feeds/templates/schema' => function (ContainerInterface $c) {
                return new Schema(
                    $c->get('wpra/feeds/templates/properties'),
                    $c->get('wpra/feeds/templates/defaults')
                );
            },
            /*
             * The store to use for built in templates.
             *
             * @since 4.16
             */
            'wpra/feeds/templates/builtin_store_factory' => function (ContainerInterface $c) {
                $settings = $c->get('wpra/feeds/templates/display_settings');

                return function(WP_Post $post) use ($settings) {
                    return new BuiltInTemplateStore($post, $settings);
                };
            },
            /*
             * The data set that contains the legacy display settings.
             *
             * @since 4.16
             */
            'wpra/feeds/templates/display_settings' => function (ContainerInterface $c) {
                if ($c->has('wpra/settings/general/dataset')) {
                    return $c->get('wpra/settings/general/dataset');
                }

                return new ArrayDataSet([]);
            },
            /*
             * The name of the feeds template CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/cpt/name' => function (ContainerInterface $c) {
                return 'wprss_feed_template';
            },
            /*
             * The labels for the feeds template CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/cpt/labels' => function (ContainerInterface $c) {
                return [
                    'name' => __('Templates', 'wprss'),
                    'singular_name' => __('Template', 'wprss'),
                    'add_new' => __('Add New', 'wprss'),
                    'all_items' => __('Templates', 'wprss'),
                    'add_new_item' => __('Add New Template', 'wprss'),
                    'edit_item' => __('Edit Template', 'wprss'),
                    'new_item' => __('New Template', 'wprss'),
                    'view_item' => __('View Template', 'wprss'),
                    'search_items' => __('Search Feeds', 'wprss'),
                    'not_found' => __('No Templates Found', 'wprss'),
                    'not_found_in_trash' => __('No Templates Found In Trash', 'wprss'),
                    'menu_name' => __('Templates', 'wprss'),
                ];
            },
            /*
             * The capability for the feed templates CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/cpt/capability' => function () {
                return 'feed_template';
            },
            /*
             * The user roles that have the feed templates CPT capabilities.
             *
             * Equal to the feed sources' CPT capability roles, if available.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/cpt/capability_roles' => function (ContainerInterface $c) {
                if (!$c->has('wpra/feeds/sources/cpt/capability_roles')) {
                    return ['administrator'];
                }

                return $c->get('wpra/feeds/sources/cpt/capability_roles');
            },
            /*
             * The full arguments for the feeds template CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/cpt/args' => function (ContainerInterface $c) {
                return [
                    'exclude_from_search' => true,
                    'publicly_queryable' => true,
                    'show_in_nav_menus' => false,
                    'show_in_admin_bar' => false,
                    'has_archive' => false,
                    'show_ui' => false,
                    'query_var' => 'feed_template',
                    'menu_position' => 100,
                    'show_in_menu' => false,
                    'rewrite' => [
                        'slug' => 'feed-templates',
                        'with_front' => false,
                    ],
                    'capability_type' => $c->get('wpra/feeds/templates/cpt/capability'),
                    'map_meta_cap' => true,
                    'supports' => ['title'],
                    'labels' => $c->get('wpra/feeds/templates/cpt/labels'),
                ];
            },
            /*
             * The handler that registers the feeds template CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/register_cpt_handler' => function (ContainerInterface $c) {
                return new RegisterCptHandler(
                    $c->get('wpra/feeds/templates/cpt/name'),
                    $c->get('wpra/feeds/templates/cpt/args')
                );
            },
            /*
             * The handler that adds the feed templates CPT capabilities to the appropriate user roles.
             *
             * Resolves to a null handler if the WordPress role manager is not available.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/add_cpt_capabilities_handler' => function (ContainerInterface $c) {
                if (!$c->has('wp/roles')) {
                    return new NullHandler();
                }

                return new AddCptMetaCapsHandler(
                    $c->get('wp/roles'),
                    $c->get('wpra/feeds/templates/cpt/capability_roles'),
                    $c->get('wpra/feeds/templates/cpt/capability')
                );
            },
            /*
             * The admin feeds templates page information.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/submenu_info' => function (ContainerInterface $c) {
                return [
                    'parent' => 'edit.php?post_type=wprss_feed',
                    'slug' => 'wpra_feed_templates',
                    'page_title' => __('Templates', 'wprss'),
                    'menu_label' => __('Templates', 'wprss'),
                    'capability' => 'edit_feed_templates',
                    'callback' => $c->get('wpra/feeds/templates/admin/render_handler'),
                ];
            },
            /*
             * The handler that registers the feeds template submenu page.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/register_submenu_handler' => function (ContainerInterface $c) {
                return new RegisterSubMenuPageHandler($c->get('wpra/feeds/templates/submenu_info'));
            },

            /*
             * The default feed template's slug name.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/default_template' => function (ContainerInterface $c) {
                return 'default';
            },
            /*
             * The available template types.
             */
            'wpra/feeds/templates/template_types' => function (ContainerInterface $c) {
                return [
                    'list' => $c->get('wpra/feeds/templates/list_template_type'),
                ];
            },
            /*
             * The master feed template.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/master_template' => function (ContainerInterface $c) {
                return new MasterFeedsTemplate(
                    $c->get('wpra/feeds/templates/template_types'),
                    $c->get('wpra/feeds/templates/collection'),
                    $c->get('wpra/feeds/templates/feed_item_collection'),
                    $c->get('wpra/feeds/templates/container_template'),
                    $c->get('wpra/display/feeds/legacy_template'),
                    $c->get('wpra/feeds/templates/master_template_logger')
                );
            },
            /*
             * The generic template type template.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/generic_template' => function (ContainerInterface $c) {
                return new TemplateTypeTemplate(
                    $c->get('wpra/feeds/templates/generic_template_default_type'),
                    $c->get('wpra/feeds/templates/template_types'),
                    $c->get('wpra/feeds/templates/feed_item_collection'),
                    $c->get('wpra/feeds/templates/container_template')
                );
            },
            /*
             * The default template type to use for the generic template.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/generic_template_default_type' => function (ContainerInterface $c) {
                return 'list';
            },
            /*
             * The template for the HTML container that wraps all rendered templates.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/container_template' => function (ContainerInterface $c) {
                $twigTemplates = $c->get('wpra/feeds/templates/file_template_collection');

                return $twigTemplates['feeds/container.twig'];
            },
            /*
             * The feed item collection to use with the master template.
             *
             * Uses the collection from the feed items module, if available.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/feed_item_collection' => function (ContainerInterface $c) {
                if (!$c->has('wpra/importer/items/collection')) {
                    return new NullCollection();
                }

                return $c->get('wpra/importer/items/collection');
            },
            /*
             * The logger to use for the master template.
             *
             * Uses the core plugin's loader, if available.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/master_template_logger' => function (ContainerInterface $c) {
                if (!$c->has('wpra/logging/logger')) {
                    return new NullLogger();
                }

                return $c->get('wpra/logging/logger');
            },
            /*
             * The collection of file templates.
             *
             * Uses the core plugin's Twig template collection, if available.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/file_template_collection' => function (ContainerInterface $c) {
                if (!$c->has('wpra/twig/collection')) {
                    return new NullCollection();
                }

                return $c->get('wpra/twig/collection');
            },
            /*
             * The list template type.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/list_template_type' => function (ContainerInterface $c) {
                return new ListTemplateType(
                    $c->get('wpra/feeds/templates/file_template_collection')
                );
            },
            /*
             * The collection of feed templates.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/collection' => function (ContainerInterface $c) {
                return new FeedTemplateCollection(
                    $c->get('wpra/feeds/templates/cpt/name'),
                    $c->get('wpra/feeds/templates/schema'),
                    $c->get('wpra/feeds/templates/default_template_type'),
                    $c->get('wpra/feeds/templates/builtin_store_factory')
                );
            },
            /*
             * The handler that creates the default template if there are no user templates.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/create_default_template_handler' => function (ContainerInterface $c) {
                return new CreateDefaultFeedTemplateHandler(
                    $c->get('wpra/feeds/templates/collection'),
                    $c->get('wpra/feeds/templates/default_template_data')
                );
            },
            /*
             * The data for the default feed template.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/default_template_data' => function (ContainerInterface $c) {
                return [
                    'name' => __('Default', 'wprss'),
                    'type' => $c->get('wpra/feeds/templates/default_template_type'),
                ];
            },
            /*
             * The slug to use for the default template.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/default_template_slug' => function () {
                return 'default';
            },
            /*
             * The template type to use for the default template.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/default_template_type' => function () {
                return '__built_in';
            },
            /*
             * The handler that responds to AJAX requests with rendered feed items.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/ajax_render_handler' => function (ContainerInterface $c) {
                return new AjaxRenderFeedsTemplateHandler($c->get('wpra/feeds/templates/master_template'));
            },
            /*
             * The feeds template model structure.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/model_schema' => function (ContainerInterface $c) {
                return [
                    'id' => '',
                    'name' => '',
                    'slug' => '',
                    'type' => 'list',
                    'options' => [
                        'limit' => 15,
                        'title_max_length' => 0,
                        'title_is_link' => true,
                        'pagination' => false,
                        'pagination_type' => 'default',
                        'source_enabled' => true,
                        'source_prefix' => __('Source:', 'wprss'),
                        'source_is_link' => true,
                        'author_enabled' => false,
                        'author_prefix' => __('By', 'wprss'),
                        'date_enabled' => true,
                        'date_prefix' => __('Published on:', 'wprss'),
                        'date_format' => 'Y-m-d',
                        'date_use_time_ago' => false,
                        'links_behavior' => 'blank',
                        'links_nofollow' => false,
                        'links_video_embed_page' => false,
                        'bullets_enabled' => true,
                        'bullet_type' => 'default',
                        'custom_css_classname' => '',
                        'audio_player_enabled' => false,
                    ],
                ];
            },
            /*
             * Tooltips for feed template model fields.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/model_tooltips' => function (ContainerInterface $c) {
                return [
                    'name' => false,
                    'type' => false,
                    'options' => [
                        'limit' => __('The maximum number of feed items to display when using the shortcode. This enables pagination if set to a number smaller than the number of items to be displayed.', 'wprss'),
                        'title_max_length' => __('Set the maximum number of characters to show for feed item titles.<hr/><em>Leave empty for no limit.</em>', 'wprss'),
                        'title_is_link' => __('Check this box to make the feed item titles link to the original article.', 'wprss'),
                        'pagination' => __('Enable this option to show the pagination beneath feed items.', 'wprss'),
                        'pagination_type' =>  __('The type of pagination to use when showing feed items on multiple pages. The first shows two links, "Older" and "Newer", which allow you to navigate through the pages. The second shows links for all the pages, together with links for the next and previous pages.', 'wprss'),
                        'source_enabled' => __('Enable this option to show the feed source name for each feed item.', 'wprss'),
                        'source_prefix' => __('Enter the text that you want to show before the source name. A space is automatically added between this text and the feed source name.', 'wprss'),
                        'source_is_link' => __('Enable this option to link the feed source name to the RSS feed\'s source site.', 'wprss'),
                        'author_enabled' => __('Check this box to show the author for each feed item, if it is available.', 'wprss'),
                        'author_prefix' => __('Enter the text that you want to show before the author name. A space is automatically added between this text and the author name.', 'wprss'),
                        'date_enabled' => __('Enable this to show the feed item\'s date.', 'wprss'),
                        'date_prefix' => __('Enter the text that you want to show before the feed item date. A space is automatically added between this text and the date.', 'wprss'),
                        'date_format' => __('The format to use for the feed item dates, as a PHP date format.', 'wprss'),
                        'date_use_time_ago' => __('Enable this option to show the elapsed time from the feed item\'s date and time to the present time. <em>Eg. 2 hours ago</em>', 'wprss'),
                        'links_behavior' => __('Choose how you want links to be opened. This applies to the feed item title and the source link.', 'wprss'),
                        'links_nofollow' => __('Enable this option to set all links displayed as "NoFollow".<hr/>"Nofollow" provides a way to tell search engines to <em>not</em> follow certain links, such as links to feed items in this case.', 'wprss'),
                        'link_to_embed' => __('Tick this box to have feed items link to their embedded content, if they have any. This works especially well when links are set to <em>"Open in a lightbox"</em>.', 'wprss'),
                        'bullets_enabled' => __('Enable this option to show bullets next to feed items.', 'wprss'),
                        'bullet_type' => __('The bullet type to use for feed items.', 'wprss'),
                        'custom_css_classname' => __('You can add your own HTML class name to the template output. This lets you customize your template further using custom CSS styling or custom JS functionality.'),
                        'audio_player_enabled' => __('If the feed item or post has an audio attachment, you can choose to show an audio player that plays the attached audio file.'),
                    ],
                ];
            },
            /*
             * Feed template's types options.
             *
             * @since 4.13.2
             */
            'wpra/feeds/templates/template_types_options' => function (ContainerInterface $c) {
                // The built in type, which appears as "List"
                $types = [
                    '__built_in' => __('List', 'wprss'),
                ];
                // Add all other template types
                foreach ($c->get('wpra/feeds/templates/template_types') as $key => $templateType) {
                    $types[$key] = $templateType->getName();
                }

                return $types;
            },
            /*
             * Whether template type selection is available or not.
             *
             * @since 4.13.2
             */
            'wpra/feeds/templates/template_type_enabled' => function (ContainerInterface $c) {
                return false;
            },
            /*
             * Feed template's fields options.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/template_options' => function (ContainerInterface $c) {
                return [
                    'is_type_enabled' => $c->get('wpra/feeds/templates/template_type_enabled'),
                    'type' => $c->get('wpra/feeds/templates/template_types_options'),
                    'links_behavior' => [
                        'self' => __('Open in same tab/window', 'wprss'),
                        'blank' => __('Open in a new tab', 'wprss'),
                        'lightbox' => __('Open in a lightbox', 'wprss'),
                    ],
                    'pagination_type' => [
                        'default' => __('Older/Newer', 'wprss'),
                        'numbered' => __('Numbered', 'wprss'),
                    ],
                    'bullet_type' => [
                        'default' => __('Bullets', 'wprss'),
                        'numbers' => __('Numbers', 'wprss'),
                    ],
                    'links_video_embed_page' => [
                        'false' => __('Original page link', 'wprss'),
                        'true' => __('Embedded video player link', 'wprss'),
                    ],
                ];
            },
            /*
             * The list of JS modules to load.
             *
             * @since 4.13.2
             */
            'wpra/feeds/templates/admin/js_modules' => function (ContainerInterface $c) {
                return [
                    'templates-app',
                ];
            },

            /*
             * The assets used on the admin templates page.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/admin/assets' => function (ContainerInterface $c) {
                return [
                    $c->get('wpra/feeds/templates/admin/styles/main'),
                    $c->get('wpra/feeds/templates/admin/scripts/main'),
                ];
            },
            /*
             * The template style.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/admin/styles/main' => function () {
                return new StyleAsset('wpra-templates', WPRSS_APP_CSS . 'templates.min.css', ['wpra-common']);
            },
            /*
             * The template script.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/admin/scripts/main' => function (ContainerInterface $c) {
                $script = new ScriptAsset('wpra-templates', WPRSS_APP_JS . 'templates.min.js', [
                    'wpra-manifest',
                    'wpra-vendor',
                ]);

                $script = $script->localize('WpraTemplates', function () use ($c) {
                    return $c->get('wpra/feeds/templates/admin/states/main');
                });

                $script = $script->localize('WpraGlobal', function () use ($c) {
                    return $c->get('wpra/feeds/templates/admin/states/global');
                });

                return $script;
            },
            /*
             * Whether audio features are enabled.
             *
             * @since 4.18
             */
            'wpra/feeds/templates/audio_features_enabled' => function () {
                return false;
            },
            /*
             * The state for the templates script.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/admin/states/main' => function (ContainerInterface $c) {
                return [
                    'model_schema' => $c->get('wpra/feeds/templates/model_schema'),
                    'model_tooltips' => $c->get('wpra/feeds/templates/model_tooltips'),
                    'options' => $c->get('wpra/feeds/templates/template_options'),
                    'modules' => $c->get('wpra/feeds/templates/admin/js_modules'),
                    'base_url' => rest_url('/wpra/v1/templates'),
                    'audio_features_enabled' => $c->get('wpra/feeds/templates/audio_features_enabled'),
                ];
            },
            /*
             * The global state of the application.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/admin/states/global' => function (ContainerInterface $c) {
                $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                return [
                    'admin_base_url' => admin_url(),
                    'templates_url_base' => str_replace($url, '', menu_page_url('wpra_feed_templates', false)),
                    'is_existing_user' => !wprss_is_new_user(),
                    'nonce' => wp_create_nonce('wp_rest'),
                ];
            },
            /*
             * The handler that renders the admin templates page.
             *
             * @since 4.14
             */
            'wpra/feeds/templates/admin/render_handler' => function (ContainerInterface $c) {
                return new RenderAdminTemplatesPageHandler($c->get('wpra/feeds/templates/admin/assets'));
            },
            /*
             * The handler that renders template content.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/handlers/render_content' => function (ContainerInterface $c) {
                return new RenderTemplateContentHandler(
                    $c->get('wpra/feeds/templates/cpt/name'),
                    $c->get('wpra/feeds/templates/master_template'),
                    $c->get('wpra/feeds/templates/generic_template'),
                    $c->get('wpra/feeds/templates/collection')
                );
            },
            /*
             * The handler that hides template content from the public-facing side.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/handlers/hide_public_content' => function (ContainerInterface $c) {
                return new HidePublicTemplateContentHandler(
                    $c->get('wpra/feeds/templates/cpt/name'),
                    $c->get('wpra/feeds/templates/public_template_content_nonce')
                );
            },
            /*
             * The name of the nonce that allows template content to be shown on the public-facing side.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/public_template_content_nonce' => function (ContainerInterface $c) {
                return 'wpra_template_preview';
            },
            /*
             * The handler that listens to requests for previewing templates.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/handlers/preview_template_request' => function (ContainerInterface $c) {
                return new PreviewTemplateRedirectHandler(
                    $c->get('wpra/feeds/templates/preview_template_request_param'),
                    $c->get('wpra/feeds/templates/public_template_content_nonce'),
                    $c->get('wpra/feeds/templates/cpt/capability')
                );
            },
            /*
             * The name of the GET parameter to detect for previewing templates.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/preview_template_request_param' => function (ContainerInterface $c) {
                return 'wpra_preview_template';
            },
            /*
             * The handler that synchronizes the default template with the display settings.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/handlers/sync_default_template' => function (ContainerInterface $c) {
                return new ReSaveTemplateHandler(
                    $c->get('wpra/feeds/templates/collection'),
                    $c->get('wpra/feeds/templates/default_template_data')
                );
            },

            /*
             * The templates GET endpoint for the REST API.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/rest_api/v1/get_endpoint' => function (ContainerInterface $c) {
                return new GetTemplatesEndPoint($c->get('wpra/feeds/templates/collection'));
            },
            /*
             * The templates PATCH endpoint for the REST API.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/rest_api/v1/patch_endpoint' => function (ContainerInterface $c) {
                return new PatchTemplateEndPoint($c->get('wpra/feeds/templates/collection'));
            },
            /*
             * The templates POST endpoint for the REST API.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/rest_api/v1/post_endpoint' => function (ContainerInterface $c) {
                return new CreateUpdateTemplateEndPoint($c->get('wpra/feeds/templates/collection'), false);
            },
            /*
             * The templates PUT endpoint for the REST API.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/rest_api/v1/put_endpoint' => function (ContainerInterface $c) {
                return new CreateUpdateTemplateEndPoint($c->get('wpra/feeds/templates/collection'));
            },
            /*
             * The templates deletion endpoint for the REST API.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/rest_api/v1/delete_endpoint' => function (ContainerInterface $c) {
                return new DeleteTemplateEndPoint($c->get('wpra/feeds/templates/collection'));
            },
            /*
             * The templates rendering endpoint for the REST API.
             *
             * @since 4.13
             */
            'wpra/feeds/templates/rest_api/v1/render_endpoint' => function (ContainerInterface $c) {
                return new RenderTemplateEndPoint(
                    $c->get('wpra/display/feeds/template'),
                    $c->get('wpra/feeds/templates/generic_template')
                );
            },
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getExtensions()
    {
        return [
            /*
             * Register the templates UI assets for the admin-side.
             *
             * @since 4.14
             */
            'wpra/assets/admin' => function(ContainerInterface $c, $assets) {
                foreach ($c->get('wpra/feeds/templates/admin/assets') as $asset) {
                    $assets[] = $asset;
                }

                return $assets;
            },
            /*
             * Overrides the core display template with the master template.
             *
             * @since 4.13
             */
            'wpra/display/feeds/template' => function (ContainerInterface $c) {
                return $c->get('wpra/feeds/templates/master_template');
            },
            /*
             * Extends the list of REST API endpoints with the template endpoints.
             *
             * @since 4.13
             */
            'wpra/rest_api/v1/endpoints' => function (ContainerInterface $c, $endPoints) {
                $endPoints['get_templates'] = new EndPoint(
                    '/templates(?:/(?P<id>[^/]+))?',
                    ['GET'],
                    $c->get('wpra/feeds/templates/rest_api/v1/get_endpoint'),
                    null //$c->get('wpra/rest_api/v1/auth/user_is_admin')
                );
                $endPoints['patch_templates'] = new EndPoint(
                    '/templates/(?P<id>[^/]+)',
                    ['PATCH'],
                    $c->get('wpra/feeds/templates/rest_api/v1/patch_endpoint'),
                    $c->get('wpra/rest_api/v1/auth/user_is_admin')
                );
                $endPoints['put_templates'] = new EndPoint(
                    '/templates(?:/(?P<id>[^/]+))?',
                    ['PUT'],
                    $c->get('wpra/feeds/templates/rest_api/v1/put_endpoint'),
                    $c->get('wpra/rest_api/v1/auth/user_is_admin')
                );
                $endPoints['post_templates'] = new EndPoint(
                    '/templates(?:/(?P<id>[^/]+))?',
                    ['POST'],
                    $c->get('wpra/feeds/templates/rest_api/v1/post_endpoint'),
                    $c->get('wpra/rest_api/v1/auth/user_is_admin')
                );
                $endPoints['delete_templates'] = new EndPoint(
                    '/templates(?:/(?P<id>[^/]+))?',
                    ['DELETE'],
                    $c->get('wpra/feeds/templates/rest_api/v1/delete_endpoint'),
                    $c->get('wpra/rest_api/v1/auth/user_is_admin')
                );
                $endPoints['render_templates'] = new EndPoint(
                    '/templates/(?P<template>[^/]+)/render',
                    ['POST'],
                    $c->get('wpra/feeds/templates/rest_api/v1/render_endpoint')
                );

                return $endPoints;
            },
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function run(ContainerInterface $c)
    {
        // Register the CPT
        add_action('init', $c->get('wpra/feeds/templates/register_cpt_handler'));
        // Add the capabilities
        add_action('admin_init', $c->get('wpra/feeds/templates/add_cpt_capabilities_handler'));
        // Register the admin submenu, unless E&T is active
        add_action('admin_menu', $c->get('wpra/feeds/templates/register_submenu_handler'));

        // Hooks in the handler for server-side feed item rendering
        add_action('wp_ajax_wprss_render', [$this, 'serverSideRenderFeeds']);
        add_action('wp_ajax_nopriv_wprss_render', [$this, 'serverSideRenderFeeds']);

        // This ensures that there is always at least one template available, by constructing the core list template
        // from the old general display settings.
        add_action('wpra/admin_init', $c->get('wpra/feeds/templates/create_default_template_handler'));

        // Filters the front-end content for templates to render them
        add_action('the_content', $c->get('wpra/feeds/templates/handlers/render_content'));

        // Hooks in the handler that hides template content from the front-end by requiring a nonce
        add_action('wp_head', $c->get('wpra/feeds/templates/handlers/hide_public_content'));

        // Hooks in the handler that listens to template preview requests
        add_action('init', $c->get('wpra/feeds/templates/handlers/preview_template_request'));

        // After settings have been reset, the default template and the display settings need to be synchronized
        add_action('wprss_after_restore_settings', $c->get('wpra/feeds/templates/handlers/sync_default_template'));
    }
}
