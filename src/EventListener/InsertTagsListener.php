<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener;


use Contao\CoreBundle\ServiceAnnotation\Hook;
use IIDO\CoreBundle\Renderer\SectionRenderer;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;


class InsertTagsListener implements ServiceAnnotationInterface
{

    private const TAG = 'iido';



    /**
     * @Hook("replaceInsertTags")
     */
    public function onReplaceInsertTags( string $tag )
    {
        $chunks = explode('::', $tag);
        $return = false;

        if (self::TAG === $chunks[0])
        {
            switch( $chunks[1] )
            {
                case "section":
                    $return = '';

                    if( $chunks[2] === 'header' )
                    {
                        $return = SectionRenderer::renderHeader();
                    }
                    break;
            }
        }

//        if( $chunks[0] === 'icon' )
//        {
//            $rootDir    = BasicHelper::getRootDir( true );
//            $iconPath   = 'files/' . BasicHelper::getFilesCustomerDir() . '/Uploads/Icons/';
//            $iconName   = ucfirst($chunks[1]) . '.svg';
//
//            if( file_exists( $rootDir . $iconPath . $iconName ) )
//            {
//                $return = file_get_contents( $rootDir . $iconPath . $iconName );
//            }
//        }

        return $return;
    }

}
