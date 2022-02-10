<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\ProviderContract;
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
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Modules\Translation;
use GeminiLabs\SiteReviews\Modules\Translator;

class Provider implements ProviderContract
{
    /**
     * @return void
     */
    public function register(Application $app)
    {
        $app->bind(Application::class, function () use ($app) {
            return $app;
        });
        $app->singleton(Hooks::class);
        $app->singleton(Queue::class);
        $app->singleton(Style::class);
        $app->singleton(Translator::class);
        $app->singleton(Translation::class);
        $app->singleton(Router::class);
        // All controllers should be singletons to allow people to remove hooks!
        $app->singleton(AdminController::class);
        $app->singleton(BlocksController::class);
        $app->singleton(EditorController::class);
        $app->singleton(IntegrationController::class);
        $app->singleton(ListTableController::class);
        $app->singleton(MainController::class);
        $app->singleton(MenuController::class);
        $app->singleton(MetaboxController::class);
        $app->singleton(NoticeController::class);
        $app->singleton(PrivacyController::class);
        $app->singleton(PublicController::class);
        $app->singleton(QueueController::class);
        $app->singleton(RestController::class);
        $app->singleton(ReviewController::class);
        $app->singleton(RevisionController::class);
        $app->singleton(SettingsController::class);
        $app->singleton(TaxonomyController::class);
        $app->singleton(ToolsController::class);
        $app->singleton(TranslationController::class);
        $app->singleton(WelcomeController::class);
    }
}
