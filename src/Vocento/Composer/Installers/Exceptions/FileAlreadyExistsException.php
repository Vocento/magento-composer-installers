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

namespace Vocento\Composer\Installers\Exceptions;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class FileAlreadyExistsException extends \RuntimeException
{

    /**
     * FileAlreadyExistsException constructor.
     */
    public function __construct($file)
    {
        parent::__construct(sprintf('The file "%s" already exists and would not be overwritten.', $file));
    }
}
