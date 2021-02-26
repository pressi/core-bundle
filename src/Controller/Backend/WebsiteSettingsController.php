<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Controller\Backend;


use Contao\Ajax;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Environment;
use Contao\Input;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Util\WebsiteSettingsUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/contao", defaults={"_scope": "backend", "_token_check": true})
 */
class WebsiteSettingsController extends AbstractController
{
    /**
     * @Route("/website-settings", name=WebsiteSettingsController::class, defaults={"_scope": "backend"})
     */
    public function listAction(): Response
    {
        $settings = WebsiteSettingsUtil::getBackendWebsiteSettings();

        $templateConfig =
        [
            'settings'      => $settings
        ];

        return $this->render( '@IIDOCore/Backend/website_settings.html.twig', $templateConfig);
    }



    /**
     * @Route("/website-settings/{settingAlias}", name="iido.core.website-settings.details", defaults={"_scope": "backend"})
     */
    public function detailsAction( $settingAlias ): Response
    {
        $this->runBefore();

//        /* @var $framework ContaoFramework */
//        $framework = System::getContainer()->get('contao.framework');
//        $framework->initialize();

        $settings   = WebsiteSettingsUtil::getBackendWebsiteSettings();
        $router     = System::getContainer()->get('router');
        $setting    = $settings[ $settingAlias ];

        $callback   = $setting['callback'];
//        $table      = Input::get('table');

        $templateConfig =
        [
//            'backlink'  => $table ? '' : $router->generate( WebsiteSettingsController::class ),
            'backlink'  => $router->generate( WebsiteSettingsController::class ),
            'headline'  => ['sub' => $setting['name'] ],
            'content'   => call_user_func( [System::importStatic( $callback[0] ), $callback[1]] )
        ];

        return $this->render( '@IIDOCore/Backend/website_settings-details.html.twig', $templateConfig);
    }



    protected function runBefore()
    {
        $table      = Input::get('table');

        // AJAX request
        if( $table && $_POST && Environment::get('isAjaxRequest') )
        {
            Controller::loadDataContainer( $table );

            $tableConfig = $GLOBALS['TL_DCA'][ $table ];
            $dataContainer = $tableConfig['config']['dataContainer'];

            if( $dataContainer === 'YamlConfigFile' )
            {
                $classPath  = '\IIDO\CoreBundle\DataContainer';
            }

            $tableMode  = $classPath . '\DC_' . $dataContainer;
            $objTable   = new $tableMode( $table );

            $objAjax = new Ajax( Input::post('action') );
            $objAjax->executePostActions( $objTable );
        }
    }
}
