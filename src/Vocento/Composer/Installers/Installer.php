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
    /**
     * {@inheritDoc}
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        parent::install($repo, $package);

        $magentoInstaller = new MagentoCoreInstaller($package, $this->composer, $this->getIO());

        try {
            $magentoInstaller->install();
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
        $magentoInstaller = new MagentoCoreInstaller($package, $this->composer, $this->getIO());
        $magentoInstaller->uninstall();

        parent::uninstall($repo, $package);
    }

    /**
     * {@inheritDoc}
     */
    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
        $magentoInstaller = new MagentoCoreInstaller($initial, $this->composer, $this->getIO());
        $magentoInstaller->uninstall();

        parent::update($repo, $initial, $target);

        $magentoInstaller = new MagentoCoreInstaller($target, $this->composer, $this->getIO());
        $magentoInstaller->install();
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
        return 'vocento-magento-core' === $packageType;
    }
}
