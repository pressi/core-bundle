<?php
/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Controller\Backend;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;


/**
 * @Route("/contao",
 *     defaults={
 *          "_scope": "backend",
 *          "_token_check" = true,
 *          "_backend_module" = "website-settings"
 *     }
 * )
 */
class WebsiteSettingsController extends AbstractController
{

    /**
     * @Route("/website-settings", name=WebsiteSettingsController::class)
     */
    public function listAction(): Response
    {
        return new Response($this->render('@IIDOCore/Backend/website-settings.html.twig'), []);
    }
}
