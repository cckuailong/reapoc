<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\ImportReviews;
use GeminiLabs\SiteReviews\Commands\ImportSettings;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\SqlSchema;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Console;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Migrate;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Modules\SystemInfo;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Role;

class ToolsController extends Controller
{
    /**
     * @return void
     * @action site-reviews/route/admin/clear-console
     */
    public function clearConsole()
    {
        glsr(Console::class)->clear();
        glsr(Notice::class)->addSuccess(_x('Console cleared.', 'admin-text', 'site-reviews'));
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/clear-console
     */
    public function clearConsoleAjax()
    {
        $this->clearConsole();
        wp_send_json_success([
            'console' => glsr(Console::class)->get(),
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/admin/convert-table-engine
     */
    public function convertTableEngine(Request $request)
    {
        $result = glsr(SqlSchema::class)->convertTableEngine($request->table);
        if (true === $result) {
            glsr(Notice::class)->addSuccess(
                sprintf(_x('The <code>%s</code> table was successfully converted to InnoDB.', 'admin-text', 'site-reviews'), $request->table)
            );
        }
        if (false === $result) {
            glsr(Notice::class)->addError(
                sprintf(_x('The <code>%s</code> table could not be converted to InnoDB.', 'admin-text', 'site-reviews'), $request->table)
            );
        }
        if (-1 === $result) {
            glsr(Notice::class)->addWarning(
                sprintf(_x('The <code>%s</code> table was not found in the database.', 'admin-text', 'site-reviews'), $request->table)
            );
        }
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/convert-table-engine
     */
    public function convertTableEngineAjax(Request $request)
    {
        $this->convertTableEngine($request);
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/admin/detect-ip-address
     */
    public function detectIpAddress()
    {
        $link = glsr(Builder::class)->a([
            'data-expand' => '#faq-ipaddress-incorrectly-detected',
            'href' => glsr_admin_url('documentation', 'faq'),
            'text' => _x('FAQ', 'admin-text', 'site-reviews'),
        ]);
        if ('unknown' === $ipAddress = Helper::getIpAddress()) {
            glsr(Notice::class)->addWarning(sprintf(
                _x('Site Reviews was unable to detect an IP address. To fix this, please see the %s.', 'admin-text', 'site-reviews'),
                $link
            ));
        } else {
            glsr(Notice::class)->addSuccess(sprintf(
                _x('Your detected IP address is %s. If this looks incorrect, please see the %s.', 'admin-text', 'site-reviews'),
                '<code>'.$ipAddress.'</code>', $link
            ));
        }
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/detect-ip-address
     */
    public function detectIpAddressAjax()
    {
        $this->detectIpAddress();
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/admin/download-console
     */
    public function downloadConsole()
    {
        $this->download(glsr()->id.'-console.txt', glsr(Console::class)->get());
    }

    /**
     * @return void
     * @action site-reviews/route/admin/download-system-info
     */
    public function downloadSystemInfo()
    {
        $this->download(glsr()->id.'-system-info.txt', glsr(SystemInfo::class)->get());
    }

    /**
     * @return void
     * @action site-reviews/route/admin/export-settings
     */
    public function exportSettings()
    {
        $this->download(glsr()->id.'-settings.json', glsr(OptionManager::class)->json());
    }

    /**
     * @return void
     * @action site-reviews/route/admin/fetch-console
     */
    public function fetchConsole()
    {
        glsr(Notice::class)->addSuccess(_x('Console reloaded.', 'admin-text', 'site-reviews'));
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/fetch-console
     */
    public function fetchConsoleAjax()
    {
        $this->fetchConsole();
        wp_send_json_success([
            'console' => glsr(Console::class)->getRaw(),
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/admin/import-reviews
     */
    public function importReviews(Request $request)
    {
        $this->execute(new ImportReviews($request));
    }

    /**
     * @return void
     * @action site-reviews/route/admin/import-settings
     */
    public function importSettings()
    {
        $this->execute(new ImportSettings());
    }

    /**
     * @return void
     * @action site-reviews/route/admin/migrate-plugin
     */
    public function migratePlugin(Request $request)
    {
        glsr(Queue::class)->cancelAll('queue/migration');
        if (wp_validate_boolean($request->alt)) {
            glsr(Migrate::class)->runAll();
            glsr(Notice::class)->clear()->addSuccess(_x('All plugin migrations have been run successfully, please reload the page.', 'admin-text', 'site-reviews'));
        } else {
            glsr(Migrate::class)->run();
            glsr(Notice::class)->clear()->addSuccess(_x('The plugin has been migrated sucessfully, please reload the page.', 'admin-text', 'site-reviews'));
        }
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/migrate-plugin
     */
    public function migratePluginAjax(Request $request)
    {
        $this->migratePlugin($request);
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/admin/repair-review-relations
     */
    public function repairReviewRelations()
    {
        glsr(Database::class)->deleteInvalidReviews();
        glsr(Notice::class)->clear()->addSuccess(_x('The review relationships have been repaired.', 'admin-text', 'site-reviews'));
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/repair-review-relations
     */
    public function repairReviewRelationsAjax()
    {
        $this->repairReviewRelations();
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/admin/reset-assigned-meta
     */
    public function resetAssignedMeta()
    {
        glsr(CountManager::class)->recalculate();
        glsr(Notice::class)->clear()->addSuccess(_x('The assigned meta values have been reset.', 'admin-text', 'site-reviews'));
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/reset-assigned-meta
     */
    public function resetAssignedMetaAjax()
    {
        $this->resetAssignedMeta();
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }

    /**
     * @return void
     * @action site-reviews/route/admin/reset-permissions
     */
    public function resetPermissions(Request $request)
    {
        if (wp_validate_boolean($request->alt)) {
            glsr(Role::class)->hardResetAll();
        } else {
            glsr(Role::class)->resetAll();
        }
        glsr(Notice::class)->clear()->addSuccess(_x('The permissions have been reset.', 'admin-text', 'site-reviews'));
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/reset-permissions
     */
    public function resetPermissionsAjax(Request $request)
    {
        $this->resetPermissions($request);
        $reloadLink = glsr(Builder::class)->a([
            'text' => _x('reload the page', 'admin-text', 'site-reviews'),
            'href' => 'javascript:window.location.reload(1)',
        ]);
        glsr(Notice::class)->clear()->addSuccess(
            sprintf(_x('The permissions have been reset, please %s for them to take effect.', 'admin-text', 'site-reviews'), $reloadLink)
        );
        wp_send_json_success([
            'notices' => glsr(Notice::class)->get(),
        ]);
    }
}
