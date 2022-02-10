<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\SqlSchema;

class Install
{
    /**
     * @param bool $isNetworkDeactivating
     * @return void
     */
    public function deactivate($isNetworkDeactivating)
    {
        if (!$isNetworkDeactivating) {
            $this->dropForeignConstraints();
            delete_option(glsr()->prefix.'activated');
            return;
        }
        foreach ($this->sites() as $siteId) {
            switch_to_blog($siteId);
            $this->dropForeignConstraints();
            delete_option(glsr()->prefix.'activated');
            restore_current_blog();
        }
    }

    /**
     * @return void
     */
    public function dropForeignConstraints()
    {
        glsr(SqlSchema::class)->dropForeignConstraint('assigned_posts', 'assigned_posts_rating_id');
        glsr(SqlSchema::class)->dropForeignConstraint('assigned_posts', 'assigned_posts_post_id');
        glsr(SqlSchema::class)->dropForeignConstraint('assigned_terms', 'assigned_terms_rating_id');
        glsr(SqlSchema::class)->dropForeignConstraint('assigned_terms', 'assigned_terms_term_id');
        glsr(SqlSchema::class)->dropForeignConstraint('assigned_users', 'assigned_users_rating_id');
        glsr(SqlSchema::class)->dropForeignConstraint('assigned_users', 'assigned_users_user_id');
        glsr(SqlSchema::class)->dropForeignConstraint('ratings', 'assigned_posts_review_id');
    }

    /**
     * @param bool $dropAll
     * @return void
     */
    public function dropTables($dropAll = true)
    {
        $tables = $this->tables();
        if (is_multisite() && $dropAll) {
            foreach ($this->sites() as $siteId) {
                switch_to_blog($siteId);
                $tables = array_unique(array_merge($tables, $this->tables()));
                delete_option(glsr()->prefix.'db_version');
                restore_current_blog();
            }
        }
        foreach ($tables as $table) {
            glsr(Database::class)->dbQuery(
                glsr(Query::class)->sql("DROP TABLE IF EXISTS {$table}")
            );
        }
        delete_option(glsr()->prefix.'db_version');
    }

    /**
     * @return void
     */
    public function run()
    {
        require_once ABSPATH.'/wp-admin/includes/plugin.php';
        if (is_plugin_active_for_network(plugin_basename(glsr()->file))) {
            foreach ($this->sites() as $siteId) {
                $this->runOnSite($siteId);
            }
            return;
        }
        $this->install();
    }

    /**
     * @param int $siteId
     * @return void
     */
    public function runOnSite($siteId)
    {
        switch_to_blog($siteId);
        $this->install();
        restore_current_blog();
    }

    /**
     * @return array
     */
    public function sites()
    {
        return get_sites([
            'fields' => 'ids',
            'network_id' => get_current_network_id(),
        ]);
    }

    /**
     * @return void
     */
    protected function createRoleCapabilities()
    {
        glsr(Role::class)->resetAll();
    }

    /**
     * @return void
     */
    protected function createTables()
    {
        glsr(SqlSchema::class)->createTables();
        glsr(SqlSchema::class)->addForeignConstraints();
        if (glsr(SqlSchema::class)->tablesExist() && empty(get_option(glsr()->prefix.'db_version'))) {
            $version = '1.0'; // @compat
            if (glsr(SqlSchema::class)->columnExists('ratings', 'terms')) {
                $version = Application::DB_VERSION;
            }
            add_option(glsr()->prefix.'db_version', $version);
        }
    }

    /**
     * @return void
     */
    protected function deleteInvalidAssignments()
    {
        glsr(Database::class)->deleteInvalidPostAssignments();
        glsr(Database::class)->deleteInvalidTermAssignments();
        glsr(Database::class)->deleteInvalidUserAssignments();
        glsr(Database::class)->deleteInvalidReviews();
    }

    /**
     * @return void
     */
    protected function install()
    {
        $this->createRoleCapabilities();
        $this->createTables();
        $this->deleteInvalidAssignments();
    }

    /**
     * @return array
     */
    protected function tables()
    {
        return [
            glsr(SqlSchema::class)->table('assigned_posts'),
            glsr(SqlSchema::class)->table('assigned_terms'),
            glsr(SqlSchema::class)->table('assigned_users'),
            glsr(SqlSchema::class)->table('ratings'),
        ];
    }
}
