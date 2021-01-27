<?php
/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Controller\Backend;


use Contao\Input;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Util\WebsiteSettingsUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/contao")
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
        $settings   = WebsiteSettingsUtil::getBackendWebsiteSettings();
        $router     = System::getContainer()->get('router');
        $setting    = $settings[ $settingAlias ];

        $callback   = $setting['callback'];
        $table      = Input::get('table');

        $templateConfig =
        [
//            'backlink'  => $table ? '' : $router->generate( WebsiteSettingsController::class ),
            'backlink'  => $router->generate( WebsiteSettingsController::class ),
            'headline'  => ['sub' => $setting['name'] ],
            'content'   => call_user_func( [System::importStatic( $callback[0] ), $callback[1]] )
        ];

        return $this->render( '@IIDOCore/Backend/website_settings-details.html.twig', $templateConfig);
    }
}
