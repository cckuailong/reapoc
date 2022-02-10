<?php

namespace RebelCode\Composer;

use Composer\Composer;
use Composer\Config;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\BasePackage;
use Composer\Package\CompletePackage;
use Composer\Plugin\PluginInterface;
use Composer\Repository\WritableRepositoryInterface;
use Composer\Util\Filesystem;

class CleanupPlugin implements PluginInterface, EventSubscriberInterface
{
    /** @var  Composer $composer */
    protected $composer;

    /** @var  IOInterface $io */
    protected $io;

    /** @var  Config $config */
    protected $config;

    /** @var  Filesystem $filesystem */
    protected $filesystem;

    /** @var  array $rules */
    protected $rules;

    /**
     * {@inheritDoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->config = $composer->getConfig();
        $this->filesystem = new Filesystem();
        $this->rules = CleanupRules::getRules();
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL => 'onPostPackageInstall',
            PackageEvents::POST_PACKAGE_UPDATE => 'onPostPackageUpdate',
        ];
    }

    /**
     * Function to run after a package has been installed
     */
    public function onPostPackageInstall(PackageEvent $event)
    {
        /** @var CompletePackage $package */
        $package = $event->getOperation()->getPackage();

        $this->cleanPackage($package);
    }

    /**
     * Function to run after a package has been updated
     */
    public function onPostPackageUpdate(PackageEvent $event)
    {
        /** @var CompletePackage $package */
        $package = $event->getOperation()->getTargetPackage();

        $this->cleanPackage($package);
    }

    /**
     * Function to run after a package has been updated
     *
     * @param PackageEvent $event
     */
    public function onPostInstallUpdateCmd(PackageEvent $event)
    {
        /** @var WritableRepositoryInterface $repository */
        $repository = $this->composer->getRepositoryManager()->getLocalRepository();

        /** @var CompletePackage $package */
        foreach ($repository->getPackages() as $package) {
            if ($package instanceof BasePackage) {
                $this->cleanPackage($package);
            }
        }
    }

    /**
     * Clean a package, based on its rules.
     *
     * @param BasePackage  $package  The package to clean
     * @return bool True if cleaned
     */
    protected function cleanPackage(BasePackage $package)
    {
        // Only clean 'dist' packages
        if ($package->getInstallationSource() !== 'dist') {
            return false;
        }

        $vendorDir = $this->config->get('vendor-dir');
        $targetDir = $package->getTargetDir();
        $packageName = $package->getPrettyName();
        $packageDir = $targetDir ? $packageName . '/' . $targetDir : $packageName ;

        $rules = isset($this->rules[$packageName]) ? $this->rules[$packageName] : null;
        if(!$rules){
            return false;
        }

        $dir = $this->filesystem->normalizePath(realpath($vendorDir . '/' . $packageDir));
        if (!is_dir($dir)) {
            return false;
        }

        foreach((array) $rules as $part) {
            // Split patterns for single globs (should be max 260 chars)
            $patterns = explode(' ', trim($part));
            
            foreach ($patterns as $pattern) {
                try {
                    foreach (glob($dir.'/'.$pattern) as $file) {
                        $this->filesystem->remove($file);
                    }
                } catch (\Exception $e) {
                    $this->io->write("Could not parse $packageDir ($pattern): ".$e->getMessage());
                }
            }
        }

        return true;
    }
}
