<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Config;


use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\PageModel;
use Contao\System;
use IIDO\CoreBundle\Entity\WebsiteColorEntity;
use IIDO\CoreBundle\Entity\WebsiteFontEntity;
use IIDO\CoreBundle\Entity\WebsiteSizeEntity;
use Symfony\Component\HttpFoundation\RequestStack;


class PageConfig
{
    protected static array $pageConfig = [];



    public static function loadCurrentPageConfig( $pageId = null ): array
    {
        if( static::$pageConfig )
        {
            return static::$pageConfig;
        }

        if( !$pageId )
        {
            global $objPage;

            $pageId = $objPage->rootId;
        }

        $doctrine = System::getContainer()->get('doctrine');

        $fontRepository     = $doctrine->getRepository( WebsiteFontEntity::class );
        $sizeRepository     = $doctrine->getRepository( WebsiteSizeEntity::class );
        $colorRepository    = $doctrine->getRepository( WebsiteColorEntity::class );

        $fonts  = $fontRepository->findByPage( $pageId );
        $sizes  = $sizeRepository->findByPage( $pageId );
        $colors = $colorRepository->findByPage( $pageId );

//        if( $themeDesigner )
//        {
//            static::$themeDesigner = $themeDesigner;
//        }

        $pageConfig = ['fonts'=>$fonts, 'sizes'=>$sizes, 'colors'=>$colors];
        static::$pageConfig = $pageConfig;

        return $pageConfig;
    }



    public static function loadCurrentPageColors( $pageId = null ): array
    {
        $colors =
        [
            'black'   => 'Schwarz (#333)',
            'white'   => 'Weiß (#fff)'
        ];

        $requestStack = Controller::getContainer()->get('request_stack');
        /** @var $requestStack RequestStack */

        $scopeMatcher = Controller::getContainer()->get('contao.routing.scope_matcher');
        /** @var $scopeMatcher ScopeMatcher */

        $doctrine = System::getContainer()->get('doctrine');
        $colorRepository = $doctrine->getRepository( WebsiteColorEntity::class );

        if( $scopeMatcher->isBackendRequest( $requestStack->getCurrentRequest() ) )
        {
            $do = $requestStack->getCurrentRequest()->query->get('do');
            $id = $requestStack->getCurrentRequest()->query->get('id');
            $table = $requestStack->getCurrentRequest()->query->get('table');

            if( 'article' === $do )
            {
                if( 'tl_content' === $table )
                {
                    $content = ContentModel::findByPk( $id );
                    $article = ArticleModel::findByPk( $content->pid );
                    $page = PageModel::findByPk( $article->pid );

                    $pageId = $page->id;
                }
                else
                {
                    $article = ArticleModel::findByPk( $id );
                    $page = PageModel::findByPk( $article->pid );

                    $pageId = $page->id;
                }
            }
        }

        if( $pageId )
        {
            // TODO: page settings => headline, text, usw. ????
            $colors = array_merge($colors, $colorRepository->findByPage( $pageId ) );
        }

        return $colors;
    }
}
