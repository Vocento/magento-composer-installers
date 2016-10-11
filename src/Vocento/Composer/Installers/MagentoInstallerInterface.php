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

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
interface MagentoInstallerInterface
{
    /**
     * Installs the package
     */
    public function install();

    /**
     * Installs the package
     */
    public function uninstall();
}
