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
use Composer\Installer\BinaryInstaller;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Util\Filesystem;
use Vocento\Composer\Installers\Exceptions\FileAlreadyExistsException;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class Installer extends LibraryInstaller
{
    const SUPPORTED_TYPES = array(
        'vocento-magento-core' => MagentoCoreInstaller::class,
        'vocento-magento-community' => MagentoCommunityInstaller::class,
        'vocento-magento-statics' => MagentoStaticsInstaller::class,
        'project' => MagentoProjectInstaller::class
    );

    /** @var PackageInstallerFactory */
    private $packageInstallerFactory;

    /**
     * Installer constructor.
     *
     * @param IOInterface $io
     * @param Composer $composer
     * @param string $type
     * @param Filesystem $filesystem
     * @param BinaryInstaller $binaryInstaller
     */
    public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null, BinaryInstaller $binaryInstaller = null)
    {
        parent::__construct($io, $composer, $type);

        $this->packageInstallerFactory = new PackageInstallerFactory(self::SUPPORTED_TYPES, $io, $composer);
    }

    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);

        $packageInstaller = $this->getPackageInstaller($package);

        try {
            $packageInstaller->install();
        } catch (FileAlreadyExistsException $e) {
            parent::uninstall($repo, $package);
            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $packageInstaller = $this->getPackageInstaller($package);
        $packageInstaller->uninstall();

        parent::uninstall($repo, $package);
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $packageInstaller = $this->getPackageInstaller($initial);
        $packageInstaller->uninstall();

        parent::update($repo, $initial, $target);

        $packageInstaller = $this->getPackageInstaller($target);
        $packageInstaller->install();
    }

    /**
     * Get I/O object
     *
     * @return IOInterface
     */
    private function getIO()
    {
        return $this->io;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($packageType)
    {
        return $this->packageInstallerFactory->supports($packageType);
    }

    /**
     * {@inheritDoc}
     */
    public function getPackageInstaller(PackageInterface $package)
    {
        return $this->packageInstallerFactory->create($package);
    }
}
