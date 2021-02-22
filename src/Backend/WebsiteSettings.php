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


class WebsiteSettings
{
    public function renderSettings()
    {
//        $this->processForm();

        $table      = Input::get('table');
        $twig       = System::getContainer()->get('twig');
//        $values     = WebsiteSettingsUtil::getConfigFileValue();
//        $legends    = $this->getFormFields( $values );

//        $fs         = System::getContainer()->get('session')->getBag('contao_backend')->get('fieldset_states');
        $classPath  = '';

        Controller::loadLanguageFile( 'default' );
        Controller::loadLanguageFile( $table );

        Controller::loadDataContainer( $table );

        $tableConfig = $GLOBALS['TL_DCA'][ $table ];
        $dataContainer = $tableConfig['config']['dataContainer'];

        if( $dataContainer === 'YamlConfigFile' )
        {
            $classPath  = '\IIDO\CoreBundle\DataContainer';
        }

        $tableMode  = $classPath . '\DC_' . $dataContainer;
        $objTable   = new $tableMode( $table );

//        $config =
//        [
//            'tableContent' => $objTable->edit(),


//            'formToken' => REQUEST_TOKEN,
//            'fieldsets' => $legends,
//            'fs'        => $fs
//        ];

        return preg_replace('/<div id="tl_buttons">([A-Za-z0-9\s\n\-,;.:_öäüÖÄÜß!?="#\(\)\{\}\/<>%+&]{0,})<\/div>/', '', $objTable->edit()); //$twig->render('@IIDOCore\Backend\website_settings-settings.html.twig', $config);
    }

}
