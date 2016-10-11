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

use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
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
    );

    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);


        $packageInstaller  = $this->getPackageInstaller($package);

        try {
            $packageInstaller->install();
        } catch (FileAlreadyExistsException $e) {
            parent::uninstall($repo ,$package);
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
        $packageType = strtolower($packageType);

        return array_key_exists($packageType, self::SUPPORTED_TYPES);
    }

    /**
     * {@inheritDoc}
     */
    public function getPackageInstaller(PackageInterface $package)
    {
        $class = self::SUPPORTED_TYPES[$package->getType()];

        return new $class($package, $this->composer, $this->getIO());
    }
}
