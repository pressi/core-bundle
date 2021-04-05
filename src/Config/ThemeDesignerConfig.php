<?php


namespace IIDO\CoreBundle\Config;


use Contao\System;
use IIDO\CoreBundle\Entity\ThemeDesignerEntity;


class ThemeDesignerConfig
{
    public static function loadCurrentThemeDesigner( $pageId = null ): ?ThemeDesignerEntity
    {
        if( !$pageId )
        {
            global $objPage;

            $pageId = $objPage->rootId;
        }

        $doctrine = System::getContainer()->get('doctrine');
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository( ThemeDesignerEntity::class );

        $model = $repository->findOneBy(['page'=>$pageId]);

        return $model;
    }
}
