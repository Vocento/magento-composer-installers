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

/**
 * @author Emilio Fernández <efernandez@vocento.com>
 */
final class MagentoStaticsInstaller extends MagentoInstaller
{
    /**
     * MagentoStaticsInstaller constructor.
     * @param PackageInterface|null $package
     * @param Composer|null $composer
     * @param IOInterface|null $io
     */
    public function __construct(PackageInterface $package = null, Composer $composer = null, IOInterface $io = null)
    {
        parent::__construct($package, $composer, $io);

        $configExcludedFiles = $this->composer->getConfig()->get('exclude-magento-statics-files');
        if (is_array($configExcludedFiles)) {
            $this->excludedFiles = array_merge($this->excludedFiles, $configExcludedFiles);
        }
    }
}
