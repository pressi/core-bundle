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
use Contao\PageModel;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Model\CompanyModel;
use IIDO\CoreBundle\Util\WebsiteSettingsUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * @Route("/contao", defaults={"_scope": "backend", "_token_check": true})
 */
class CompanyController extends AbstractController
{
    protected TokenStorageInterface $tokenStorage;


    protected RouterInterface $router;



    public function __construct( TokenStorageInterface $tokenStorage, RouterInterface $router )
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }



    /**
     * @Route("/company", name=CompanyController::class)
     */
    public function listAction( Request $request ): Response
    {
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        if( !$user->isAdmin )
        {
            $pages = PageModel::findBy(['id IN (?)', 'type=?', 'fallback=?'], [implode(',', $user->pagemounts), 'root', '1']);
        }
        else
        {
            $pages = PageModel::findBy(['type=?', 'fallback=?'], ['root', '1']);
        }

        $arrCompanies = [];
        if( $pages )
        {
            while( $page = $pages->next() )
            {
                $company = CompanyModel::findByPid( $page->id );

                if( $company )
                {
                    $company->url = $this->router->generate('iido.core.company.details', ['companyId' => $company->id]);
                    $company->page = $page->current();

                    $arrCompanies[] = $company;
                }
            }
        }

        if( !$user->isAdmin )
        {
            $pages->reset();

            if( count($arrCompanies) === 0 )
            {
                if( $pages->count() === 1 )
                {
                    $page = $pages->first();

                    $company = new CompanyModel();

                    $company->tstamp    = time();
                    $company->pid       = $page->id;
                    $company->ptable    = 'tl_page';
                    $company->pfield    = 'company';
                    $company->dateAdded = time();
                    $company->published = '1';

                    $company = $company->save();

                    //                $this->redirect( $this->router->generate('iido.core.company.details', ['companyId' => $company->id ]) );
                    //                echo "<pre>"; print_r( $this->router->generate('iido.core.company.details', ['companyId' => 5 ], 0) ); exit;
                    //                $this->redirect( $this->router->generate('iido.core.company.details', ['companyId' => 5 ], 0) );
                    \Contao\Controller::redirect( $this->router->generate('iido.core.company.details', ['companyId' => $company->id ], 0) );
                }
            }
            elseif( count($arrCompanies) === 1 && $pages->count() === 1 )
            {
                \Contao\Controller::redirect( $this->router->generate('iido.core.company.details', ['companyId' => $arrCompanies[0]->id ], 0) );
            }
        }

        $templateConfig =
        [
            'user'      => $user,
            'companies' => $arrCompanies
        ];

        return $this->render( '@IIDOCore/Backend/company-list.html.twig', $templateConfig);
    }



    /**
     * @Route("/company/{companyId}", name="iido.core.company.details")
     */
    public function detailsAction( string $companyId ): Response
    {
        $templateConfig =
        [
            'company' => $companyId
        ];

        return $this->render( '@IIDOCore/Backend/company-details.html.twig', $templateConfig);
    }
}
