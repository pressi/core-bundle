<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Config;


use Contao\System;
use Doctrine\ORM\EntityManager;
use IIDO\CoreBundle\Entity\WebsiteColorEntity;
use IIDO\CoreBundle\Entity\WebsiteFontEntity;
use IIDO\CoreBundle\Entity\WebsiteSizeEntity;
use IIDO\CoreBundle\Repository\WebsiteColorRepository;
use IIDO\CoreBundle\Repository\WebsiteFontRepository;
use IIDO\CoreBundle\Repository\WebsiteSizeRepository;


class PageConfig
{
    protected static $pageConfig;



    public static function loadCurrentPageConfig( $pageId = null )
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
//        $entityManager = $doctrine->getManager();
//        /** @var $entityManager EntityManager */

//        $repo = $entityManager->getRepository( WebsiteFontEntity::class );
//        $fonts = $repo->findAll();
//        $fonts = $repo->findBy(['parent' => 1]);


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

//        static::$pageConfig = $pageConfig;

        return $pageConfig;
    }
}
