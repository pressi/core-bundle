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
use IIDO\CoreBundle\Entity\ThemeDesignerEntity;


class ThemeDesignerConfig
{
    protected static $themeDesigner;



    public static function loadCurrentThemeDesigner( $pageId = null ): ?ThemeDesignerEntity
    {
        if( static::$themeDesigner )
        {
            return static::$themeDesigner;
        }

        if( !$pageId )
        {
            global $objPage;

            $pageId = $objPage->rootId;
        }

        $doctrine = System::getContainer()->get('doctrine');
//        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository( ThemeDesignerEntity::class );
        $themeDesigner = $repository->findOneBy(['page'=>$pageId]);

        if( $themeDesigner )
        {
            static::$themeDesigner = $themeDesigner;
        }

        return $themeDesigner;
    }
}
