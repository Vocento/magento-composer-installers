<?php
/*
 * This file is part of the Vocento Software.
 *
 * (c) Vocento S.A., <desarrollo.dts@vocento.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Vocento\Composer\Installers;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Vocento\Composer\Installers\Exceptions\FileAlreadyExistsException;

/**
 * @author Emilio Fernández <efernandez@vocento.com>
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
abstract class MagentoInstaller implements MagentoInstallerInterface
{
    /** @var string */
    private $baseDir;

    /** @var Composer */
    protected $composer;

    /** @var array */
    protected $excludedFiles;

    /** @var IOInterface */
    private $io;

    /** @var PackageInterface */
    private $package;

    /** @var Filesystem */
    private $filesystem;

    /** @var GitIgnore */
    private $gitIgnore;
    
    /**
     * MagentoInstaller constructor.
     * @param PackageInterface|null $package
     * @param Composer|null $composer
     * @param IOInterface|null $io
     */
    public function __construct(PackageInterface $package = null, Composer $composer = null, IOInterface $io = null)
    {
        $this->package = $package;
        $this->composer = $composer;
        $this->io = $io;

        $this->filesystem = new Filesystem();

        $vendorDirectoryDepth = count(explode('/', $composer->getConfig()->get('vendor-dir', 1)));
        $baseDir = $composer->getConfig()->get('vendor-dir');
        for ($i = 0; $i < $vendorDirectoryDepth; $i++) {
            $baseDir = dirname($baseDir);
        }
        $this->baseDir = $baseDir;

        $this->excludedFiles = [
            '.env',
            '.gitignore',
            'composer.json',
            'composer.lock',
            'phpunit.xml.dist',
        ];

        $configExcludedFiles = $this->composer->getConfig()->get('exclude-magento-files');
        if (is_array($configExcludedFiles)) {
            $this->excludedFiles = array_merge($this->excludedFiles, $configExcludedFiles);
        }

        $this->gitIgnore = new GitIgnore($this->baseDir, $this->filesystem);


    }

    public function install()
    {
        $this->io->write('<info>Installing package files</info>');

        $finder = $this->getFinder();

        foreach ($finder->files() as $file) {
            if ($this->isExcludedFile($file->getRelativePathname())) {
                // Exclude file
                continue;
            }

            $targetFile = $this->baseDir.DIRECTORY_SEPARATOR.$file->getRelativePathname();

            if ($this->filesystem->exists($targetFile)) {
                throw new FileAlreadyExistsException($file->getRelativePathname());
            }
        }

        foreach ($finder->files() as $file) {
            if ($this->isExcludedFile($file->getRelativePathname())) {
                // Excluded file
                continue;
            }

            $targetFile = $this->baseDir.DIRECTORY_SEPARATOR.$file->getRelativePathname();
            $this->io->write('  - Copying file <info>'.$file->getRelativePathname().'</info>');

            // Copy file
            $this->filesystem->copy($file->getRealPath(), $targetFile);

            $this->io->write('  - Adding file <info>'.$file->getRelativePathname().'</info> to .gitignore file');

            // Add file to .gitignore buffer
            $this->gitIgnore->addEntry($file->getRelativePathname());
        }
        // Dump .gitignore buffer into .gitignore file
        $this->gitIgnore->write();
    }

    /**
     * {@inheritDoc}
     */
    private function getInstallPath()
    {
        return $this->composer->getInstallationManager()->getInstallPath($this->package);
    }

    /**
     * @return Finder
     */
    private function getFinder()
    {
        $finder = new Finder();
        $finder->in($this->getInstallPath())->ignoreDotFiles(false)->ignoreVCS(true);

        return $finder;
    }

    /**
     * @param string $file
     * @return bool
     */
    private function isExcludedFile($file)
    {
        return in_array($file, $this->excludedFiles);
    }

    public function uninstall()
    {
        $this->io->write('<info>Uninstalling package files</info>');

        $finder = $this->getFinder();

        foreach ($finder->files() as $file) {
            if ($this->isExcludedFile($file->getRelativePathname())) {
                // Excluded file
                continue;
            }

            $targetFile = $this->baseDir.DIRECTORY_SEPARATOR.$file->getRelativePathname();

            $this->io->write('  - Removing file <info>'.$file->getRelativePathname().'</info>');
            $this->removeFile($targetFile);

            $this->io->write('  - Removing file <info>'.$file->getRelativePathname().'</info> from .gitignore file');

            // Remove file from .gitignore buffer
            $this->gitIgnore->removeEntry($file->getRelativePathname());
        }
        // Dump .gitignore buffer into .gitignore file
        $this->gitIgnore->write();
    }

    /**
     * @param $targetFile
     */
    private function removeFile($targetFile)
    {
        $this->filesystem->remove($targetFile);
        $targetDirectory = dirname($targetFile);

        $this->removeEmptyDirectory($targetDirectory);
    }

    /**
     * @param $targetDirectory
     */
    private function removeEmptyDirectory($targetDirectory)
    {
        if ($targetDirectory !== $this->baseDir && 0 === strpos($targetDirectory, $this->baseDir)) {
            $finder = new Finder();
            $finder->files()->in($targetDirectory)->ignoreDotFiles(false)->ignoreVCS(true);

            if (0 === count($finder->files())) {
                $this->filesystem->remove($targetDirectory);
                $this->removeEmptyDirectory(dirname($targetDirectory));
            }
        }
    }
}
