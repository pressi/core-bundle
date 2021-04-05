<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Backend;


use Contao\Controller;
use Contao\Input;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Dispatcher\EventDispatcher;
use IIDO\CoreBundle\Services\Services;
use IIDO\CoreBundle\Util\WebsiteSettingsUtil;


class ThemeDesigner
{
    public function renderThemeDesigner()
    {
        $twig       = System::getContainer()->get('twig');
        $config     = [];

//        return preg_replace('/<div id="tl_buttons">([A-Za-z0-9\s\n\-,;.:_öäüÖÄÜß!?="#\(\)\{\}\/<>%+&]{0,})<\/div>/', '', $objTable->edit());
        return '';
//        return $twig->render('@IIDOCore\ThemeDesigner\Backend.html.twig', $config);
    }

}
