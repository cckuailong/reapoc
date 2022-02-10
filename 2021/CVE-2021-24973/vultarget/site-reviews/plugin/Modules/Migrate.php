<?php

namespace GeminiLabs\SiteReviews\Modules;

use DirectoryIterator;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Migrate
{
    /**
     * @var string
     */
    public $currentVersion;

    /**
     * @var string[]
     */
    public $migrations;

    /**
     * @var string
     */
    public $migrationsKey;

    public function __construct()
    {
        $this->currentVersion = $this->currentVersion();
        $this->migrations = $this->availableMigrations();
        $this->migrationsKey = glsr()->prefix.'migrations';
    }

    /**
     * @return bool
     */
    public function isMigrationNeeded()
    {
        if (empty($this->migrations)) {
            return false;
        }
        if (!empty($this->pendingMigrations())) {
            // check if this is a fresh install of the plugin
            return '0.0.0' !== glsr(OptionManager::class)->get('version_upgraded_from');
        }
        return false;
    }

    /**
     * @return string
     */
    public function pendingVersions()
    {
        $versions = array_map(function ($migration) {
            return str_replace(['Migrate_', '_'], ['', '.'], $migration);
        }, $this->pendingMigrations());
        return implode(', ', $versions);
    }

    /**
     * @return void
     */
    public function reset()
    {
        delete_option($this->migrationsKey);
    }

    /**
     * @return void
     */
    public function run()
    {
        if (glsr(Database::class)->isMigrationNeeded()) {
            $this->runAll();
        } else {
            $this->runMigrations();
        }
    }

    /**
     * @return void
     */
    public function runAll()
    {
        $this->reset();
        $this->runMigrations();
    }

    /**
     * @return array
     */
    protected function availableMigrations()
    {
        $migrations = [];
        $dir = glsr()->path('plugin/Migrations');
        if (is_dir($dir)) {
            $iterator = new DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
                if ('file' === $fileinfo->getType()) {
                    $migrations[] = str_replace('.php', '', $fileinfo->getFilename());
                }
            }
            natsort($migrations);
        }
        return Arr::reindex($migrations);
    }

    /**
     * @return array
     */
    protected function createMigrations()
    {
        $migrations = [];
        foreach ($this->migrations as $migration) {
            $migrations[$migration] = false;
        }
        return $migrations;
    }

    /**
     * @return string
     */
    protected function currentVersion()
    {
        $fallback = '0.0.0';
        $majorVersions = range(glsr()->version('major'), 1);
        foreach ($majorVersions as $majorVersion) {
            $settings = get_option(OptionManager::databaseKey($majorVersion));
            $version = Arr::get($settings, 'version', $fallback);
            if (Helper::isGreaterThan($version, $fallback)) {
                return $version;
            }
        }
        return $fallback;
    }

    /**
     * @return string[]
     */
    protected function pendingMigrations(array $migrations = [])
    {
        if (empty($migrations)) {
            $migrations = $this->storedMigrations();
        }
        return array_keys(array_filter($migrations, function ($hasRun) {
            return !$hasRun;
        }));
    }

    /**
     * @return void
     */
    protected function runMigrations()
    {
        wp_raise_memory_limit('admin');
        $migrations = $this->storedMigrations();
        glsr()->action('migration/start', $migrations);
        foreach ($this->pendingMigrations($migrations) as $migration) {
            if (class_exists($classname = '\\GeminiLabs\\SiteReviews\\Migrations\\'.$migration)) {
                if (glsr($classname)->run()) {
                    $migrations[$migration] = true;
                    glsr_log()->debug("[$migration] has run successfully");
                    continue;
                }
                glsr_log()->error("[$migration] was unsuccessful");
            }
        }
        $this->storeMigrations($migrations);
        if ($this->currentVersion !== glsr()->version) {
            $this->updateVersionFrom($this->currentVersion);
        }
        glsr(OptionManager::class)->set('last_migration_run', current_time('timestamp'));
        glsr()->action('migration/end', $migrations);
    }

    /**
     * @return void
     */
    protected function storeMigrations(array $migrations)
    {
        update_option($this->migrationsKey, $migrations);
    }

    /**
     * @return array
     */
    protected function storedMigrations()
    {
        $migrations = Arr::consolidate(get_option($this->migrationsKey));
        if (!Arr::compare(array_keys($migrations), array_values($this->migrations))) {
            $newMigrations = $this->createMigrations();
            foreach ($newMigrations as $migration => &$hasRun) {
                $hasRun = Arr::get($migrations, $migration, false);
            }
            $migrations = $newMigrations;
            $this->storeMigrations($migrations);
        }
        return array_map('wp_validate_boolean', $migrations);
    }

    /**
     * @param string $previousVersion
     * @return void
     */
    protected function updateVersionFrom($previousVersion)
    {
        $storedPreviousVersion = glsr(OptionManager::class)->get('version_upgraded_from');
        glsr(OptionManager::class)->set('version', glsr()->version);
        if ('0.0.0' !== $previousVersion || empty($storedPreviousVersion)) {
            glsr(OptionManager::class)->set('version_upgraded_from', $previousVersion);
        }
    }
}
