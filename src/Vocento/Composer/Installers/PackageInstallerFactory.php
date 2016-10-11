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
class PackageInstallerFactory
{
    /** @var array */
    private $supportedTypes = array();

    /** @var Composer */
    private $composer;

    /** @var IOInterface */
    private $io;

    /**
     * PackageInstallerFactory constructor.
     */
    public function __construct(array $supportedTypes, IOInterface $io, Composer $composer)
    {
        $this->supportedTypes = $supportedTypes;
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * PackageInstallerFactory create.
     *
     * @param PackageInterface $package
     * @return MagentoInstallerInterface
     */
    public function create($package)
    {
        $class = $this->supportedTypes[strtolower($package->getType())];

        return new $class($package, $this->composer, $this->io);
    }

    /**
     * @param $packageType
     * @return bool
     */
    public function supports($packageType)
    {
        $packageType = strtolower($packageType);

        return array_key_exists($packageType, $this->supportedTypes);
    }
}
