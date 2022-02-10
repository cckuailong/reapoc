<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class SqlSchema
{
    /**
     * @var array|null
     */
    protected $constraints;

    /**
     * @var \wpdb
     */
    protected $db;

    /**
     * @var array|null
     */
    protected $tables;

    public function __construct()
    {
        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * @return void
     */
    public function addAssignedPostsForeignConstraints()
    {
        glsr(Database::class)->deleteInvalidPostAssignments();
        $this->addForeignConstraint(
            $table = $this->table('assigned_posts'),
            $constraint = $this->foreignConstraint('assigned_posts_rating_id'),
            $foreignKey = 'rating_id',
            $foreignTable = $this->table('ratings'), 
            $foreignColumn = 'ID'
        );
        $this->addForeignConstraint(
            $table = $this->table('assigned_posts'),
            $constraint = $this->foreignConstraint('assigned_posts_post_id'),
            $foreignKey = 'post_id',
            $foreignTable = $this->db->posts, 
            $foreignColumn = 'ID'
        );
    }

    /**
     * @return void
     */
    public function addAssignedTermsForeignConstraints()
    {
        glsr(Database::class)->deleteInvalidTermAssignments();
        $this->addForeignConstraint(
            $table = $this->table('assigned_terms'),
            $constraint = $this->foreignConstraint('assigned_terms_rating_id'),
            $foreignKey = 'rating_id',
            $foreignTable = $this->table('ratings'), 
            $foreignColumn = 'ID'
        );
        $this->addForeignConstraint(
            $table = $this->table('assigned_terms'),
            $constraint = $this->foreignConstraint('assigned_terms_term_id'),
            $foreignKey = 'term_id',
            $foreignTable = $this->db->terms, 
            $foreignColumn = 'term_id'
        );
    }

    /**
     * @return void
     */
    public function addAssignedUsersForeignConstraints()
    {
        glsr(Database::class)->deleteInvalidUserAssignments();
        $this->addForeignConstraint(
            $table = $this->table('assigned_users'),
            $constraint = $this->foreignConstraint('assigned_users_rating_id'),
            $foreignKey = 'rating_id',
            $foreignTable = $this->table('ratings'), 
            $foreignColumn = 'ID'
        );
        $this->addForeignConstraint(
            $table = $this->table('assigned_users'),
            $constraint = $this->foreignConstraint('assigned_users_user_id'),
            $foreignKey = 'user_id',
            $foreignTable = $this->db->users, 
            $foreignColumn = 'ID'
        );
    }

    /**
     * @return void
     */
    public function addReviewsForeignConstraints()
    {
        glsr(Database::class)->deleteInvalidReviews();
        $this->addForeignConstraint(
            $table = $this->table('ratings'),
            $constraint = $this->foreignConstraint('assigned_posts_review_id'),
            $foreignKey = 'review_id',
            $foreignTable = $this->db->posts, 
            $foreignColumn = 'ID'
        );
    }

    /**
     * This method expects the fully formed foreign constraint key
     * @param string $table
     * @param string $constraint
     * @param string $foreignKey
     * @param string $foreignTable
     * @param string $foreignColumn
     * @return int|bool
     * @see $this->foreignConstraint()
     */
    public function addForeignConstraint($table, $constraint, $foreignKey, $foreignTable, $foreignColumn)
    {
        if ($this->foreignConstraintExists($constraint, $foreignTable)) {
            return false;
        }
        $this->removeOrphanedRows($table, $foreignKey, $foreignTable, $foreignColumn);
        return glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
            ALTER TABLE {$table}
            ADD CONSTRAINT {$constraint}
            FOREIGN KEY ({$foreignKey})
            REFERENCES {$foreignTable} ({$foreignColumn})
            ON DELETE CASCADE
        "));
    }

    /**
     * @return void
     */
    public function addForeignConstraints()
    {
        if (!defined('GLSR_UNIT_TESTS')) {
            $this->addAssignedPostsForeignConstraints();
            $this->addAssignedTermsForeignConstraints();
            $this->addAssignedUsersForeignConstraints();
            $this->addReviewsForeignConstraints();
        }
    }

    public function columnExists($table, $column)
    {
        $result = glsr(Database::class)->dbQuery(
            glsr(Query::class)->sql("SHOW COLUMNS FROM {$this->table($table)} LIKE '{$column}'")
        );
        return Cast::toBool($result);
    }

    /**
     * @param string $table
     * @return bool|int
     */
    public function convertTableEngine($table)
    {
        $result = -1;
        $table = $this->table($table);
        if ($this->isMyisam($table)) {
            $result = glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
                ALTER TABLE {$this->db->dbname}.{$table} ENGINE = InnoDB;
            "));
        }
        if (true === $result) {
            update_option(glsr()->prefix.'engine_'.$table, 'innodb');
            $this->addForeignConstraints(); // apply InnoDB constraints
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function createAssignedPostsTable()
    {
        if ($this->tableExists('assigned_posts')) {
            return false;
        }
        dbDelta(glsr(Query::class)->sql("
            CREATE TABLE {$this->table('assigned_posts')} (
                rating_id bigint(20) unsigned NOT NULL,
                post_id bigint(20) unsigned NOT NULL,
                is_published tinyint(1) NOT NULL DEFAULT '1',
                UNIQUE KEY {$this->prefix('assigned_posts_rating_id_post_id_unique')} (rating_id,post_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        "));
        glsr(Database::class)->logErrors();
        return true;
    }

    /**
     * @return bool
     */
    public function createAssignedTermsTable()
    {
        if ($this->tableExists('assigned_terms')) {
            return false;
        }
        dbDelta(glsr(Query::class)->sql("
            CREATE TABLE {$this->table('assigned_terms')} (
                rating_id bigint(20) unsigned NOT NULL,
                term_id bigint(20) unsigned NOT NULL,
                UNIQUE KEY {$this->prefix('assigned_terms_rating_id_term_id_unique')} (rating_id,term_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        "));
        glsr(Database::class)->logErrors();
        return true;
    }

    /**
     * @return bool
     */
    public function createAssignedUsersTable()
    {
        if ($this->tableExists('assigned_users')) {
            return false;
        }
        dbDelta(glsr(Query::class)->sql("
            CREATE TABLE {$this->table('assigned_users')} (
                rating_id bigint(20) unsigned NOT NULL,
                user_id bigint(20) unsigned NOT NULL,
                UNIQUE KEY {$this->prefix('assigned_users_rating_id_user_id_unique')} (rating_id,user_id)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        "));
        glsr(Database::class)->logErrors();
        return true;
    }

    /**
     * WordPress codex says there must be two spaces between PRIMARY KEY and the key definition.
     * @return bool
     * @see https://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    public function createRatingTable()
    {
        if ($this->tableExists('ratings')) {
            return false;
        }
        dbDelta(glsr(Query::class)->sql("
            CREATE TABLE {$this->table('ratings')} (
                ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                review_id bigint(20) unsigned NOT NULL,
                rating int(11) NOT NULL DEFAULT '0',
                type varchar(20) DEFAULT 'local',
                is_approved tinyint(1) NOT NULL DEFAULT '0',
                is_pinned tinyint(1) NOT NULL DEFAULT '0',
                name varchar(250) DEFAULT NULL,
                email varchar(100) DEFAULT NULL,
                avatar varchar(200) DEFAULT NULL,
                ip_address varchar(100) DEFAULT NULL,
                url varchar(250) DEFAULT NULL,
                terms tinyint(1) NOT NULL DEFAULT '1',
                PRIMARY KEY  (ID),
                UNIQUE KEY {$this->prefix('ratings_review_id_unique')} (review_id),
                KEY {$this->prefix('ratings_rating_type_is_pinned_index')} (rating,type,is_pinned)
            ) ENGINE=InnoDB {$this->db->get_charset_collate()};
        "));
        glsr(Database::class)->logErrors();
        return true;
    }

    /**
     * @return void
     */
    public function createTables()
    {
        $this->createAssignedPostsTable();
        $this->createAssignedTermsTable();
        $this->createAssignedUsersTable();
        $this->createRatingTable();
    }

    /**
     * @param string $table
     * @param string $constraint
     * @return int|bool
     */
    public function dropForeignConstraint($table, $constraint)
    {
        $table = $this->table($table);
        $constraint = $this->foreignConstraint($constraint);
        if (!$this->foreignConstraintExists($constraint)) {
            return false;
        }
        return glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
            ALTER TABLE {$table} DROP FOREIGN KEY {$constraint};
        "));
    }

    /**
     * @param string $constraint
     * @return string
     */
    public function foreignConstraint($constraint)
    {
        $constraint = Str::prefix($constraint, glsr()->prefix);
        $constraint = Str::suffix($constraint, '_foreign');
        if (is_multisite() && $this->db->blogid > 1) {
            return Str::suffix($constraint, '_'.$this->db->blogid);
        }
        return $constraint;
    }

    /**
     * This method expects the fully formed foreign constraint key
     * @param string $constraint
     * @param string $foreignTable
     * @return bool
     * @see $this->foreignConstraint()
     */
    public function foreignConstraintExists($constraint, $foreignTable = '')
    {
        if (!empty($foreignTable) && !$this->isInnodb($foreignTable)) {
            glsr_log()->debug("Cannot check for a foreign constraint because {$foreignTable} does not use the InnoDB engine.");
            return true; // we cannot create foreign contraints on MyISAM tables
        }
        // we don't need to cache this since it only runs on install
        if (!is_array($this->constraints)) {
            $this->constraints = $this->db->get_col("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = '{$this->db->dbname}'
            ");
        }
        return in_array($constraint, $this->constraints);
    }

    /**
     * @param string $table
     * @return bool
     */
    public function isMyisam($table)
    {
        return 'myisam' === $this->tableEngine($table);
    }

    /**
     * @param string $table
     * @return bool
     */
    public function isInnodb($table)
    {
        return 'innodb' === $this->tableEngine($table);
    }

    /**
     * @param string $table
     * @param string $prefix
     * @return string
     */
    public function prefix($table, $prefix = '')
    {
        $table = Str::prefix($table, glsr()->prefix);
        return Str::prefix($table, $prefix);
    }

    /**
     * @param string $table
     * @param string $foreignKey
     * @param string $foreignTable
     * @param string $foreignColumn
     * @return int|bool
     */
    public function removeOrphanedRows($table, $foreignKey, $foreignTable, $foreignColumn)
    {
        // Remove all rows from the custom table where the referenced foreign table row does not exist
        return glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
            DELETE t
            FROM {$this->table($table)} AS t
            LEFT JOIN {$this->table($foreignTable)} AS ft ON t.{$foreignKey} = ft.{$foreignColumn}
            WHERE ft.{$foreignColumn} IS NULL
        "));
    }

    /**
     * @param string $table
     * @return string
     */
    public function table($table)
    {
        if (in_array($table, $this->db->tables())) {
            return $table; // WordPress table is already prefixed
        }
        // do this next in case another plugin has created a similar table
        if (Str::endsWith(['ratings', 'assigned_posts', 'assigned_terms', 'assigned_users'], $table)) {
            $table = Str::removePrefix($table, $this->db->get_blog_prefix());
            $table = Str::removePrefix($table, glsr()->prefix);
            return $this->prefix($table, $this->db->get_blog_prefix());
        }
        if (array_key_exists($table, $this->db->tables())) {
            return $this->db->{$table}; // the prefixed WordPress table
        }
        glsr_log()->error("The {$table} table does not exist.");
        return $table; // @todo maybe throw an exception here instead...
    }

    /**
     * @param string $table
     * @return string (lowercased)
     */
    public function tableEngine($table)
    {
        $table = $this->table($table);
        $option = glsr()->prefix.'engine_'.$table;
        $engine = get_option($option);
        if (empty($engine)) {
            $engine = $this->db->get_var("
                SELECT ENGINE
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = '{$this->db->dbname}' AND TABLE_NAME = '{$table}'
            ");
            if (empty($engine)) {
                glsr_log()->warning(sprintf('DB Table Engine: The %s table does not exist in %s.', $table, $this->db->dbname));
                return '';
            }
            $engine = strtolower($engine);
            update_option($option, $engine);
        }
        return $engine;
    }

    /**
     * @param string $table
     * @return bool
     */
    public function tableExists($table)
    {
        if (!is_array($this->tables)) {
            $prefix = $this->db->get_blog_prefix().glsr()->prefix;
            $this->tables = $this->db->get_col(
                $this->db->prepare("SHOW TABLES LIKE %s", $this->db->esc_like($prefix).'%')
            );
        }
        return in_array($this->table($table), $this->tables);
    }

    /**
     * @param bool $removeDbPrefix
     * @return array
     */
    public function tableEngines($removeDbPrefix = false)
    {
        $results = $this->db->get_results("
            SELECT TABLE_NAME, ENGINE
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '{$this->db->dbname}'
            AND TABLE_NAME IN ('{$this->db->options}','{$this->db->posts}','{$this->db->terms}','{$this->db->users}')
        ");
        $engines = [];
        foreach ($results as $result) {
            if (!array_key_exists($result->ENGINE, $engines)) {
                $engines[$result->ENGINE] = [];
            }
            if ($removeDbPrefix) {
                $result->TABLE_NAME = Str::removePrefix($result->TABLE_NAME, $this->db->get_blog_prefix());
            }
            $engines[$result->ENGINE][] = $result->TABLE_NAME;
        }
        return $engines;
    }

    /**
     * @return bool
     */
    public function tablesExist()
    {
        return $this->tableExists('assigned_posts')
            && $this->tableExists('assigned_terms')
            && $this->tableExists('assigned_users')
            && $this->tableExists('ratings');
    }
}
