<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Config;


use \IIDO\UtilsBundle\Config\BundleConfig as ExtBundleConfig;


class BundleConfig extends ExtBundleConfig
{
    static string $namespace    = 'IIDO';
    static string $subNamespace = 'CoreBundle';

    static string $bundleName   = "core-bundle";
    static string $bundleGroup  = "2do";
}
