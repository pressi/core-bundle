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
class CompanyController extends AbstractController
{
    /**
     * @Route("/company", name=CompanyController::class)
     */
    public function listAction(): Response
    {
        $templateConfig = [];

        return $this->render( '@IIDOCore/Backend/company-list.html.twig', $templateConfig);
    }



    /**
     * @Route("/company/{companyId}", name="iido.core.company.details")
     */
    public function detailsAction( $companyId ): Response
    {
        $templateConfig = [];

        return $this->render( '@IIDOCore/Backend/company-details.html.twig', $templateConfig);
    }
}
