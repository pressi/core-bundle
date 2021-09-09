<?php
/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener;



use Contao\ArticleModel;
use Contao\StringUtil;
use Contao\System;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use IIDO\CoreBundle\Entity\WebsiteColorEntity;
use IIDO\CoreBundle\Permission\BackendPermissionChecker;


/**
 * IIDO Article Listener
 *
 * @package IIDO\CoreBundle
 * @author Stephan Preßl <development@prestep.at>
 */
class ArticleListener
{

    /**
     * @Hook("getArticle")
     */
    public function onGetArticle( ArticleModel $article ):void
    {
        $permission = System::getContainer()->get('iido.core.backend.permission_checker');
        /* @var $permission BackendPermissionChecker */

        $classes    = $article->classes?:[];
        $cssID      = StringUtil::deserialize( $article->cssID, true );
        $classes[]  = 'article-element';

//        $classes[]  = "row";
//        $classes[] = "row-direction-$objRow->layout_direction";

        if( $permission->hasFullAccessTo('article', 'padding') )
        {
//            $padding = strtolower( $article->padding );
            $paddingTop     = strtolower( $article->paddingTop );
            $paddingBottom  = strtolower( $article->paddingBottom );

            if( $paddingTop )
            {
                $classes[] = 'padding-top-' . $paddingTop;
            }

            if( $paddingBottom )
            {
                $classes[] = 'padding-bottom-' . $paddingBottom;
            }

//            if( $padding )
//            {
//                $classes[] = 'padding-top-' . $padding;
//                $classes[] = 'padding-bottom-' . $padding;
//            }
        }

        if( $permission->hasFullAccessTo('article', 'bg_color') )
        {
//            $bgColor    = ColorHelper::compileColor( $article->bgColor );
            $bgColor    = $article->bgColor;

            if( $bgColor !== 'transparent' )
            {
                $classes[] = 'has-bg-color';
            }

            if( is_numeric($bgColor) )
            {
                $entityManager = System::getContainer()->get('doctrine.orm.entity_manager');
                $color = $entityManager->find( WebsiteColorEntity::class, $bgColor);

                if( $color && $color->getClassName() )
                {
                    $classes[] = $color->getClassName();
                }
            }
        }
//
//        if( $objPermission->hasFullAccessTo('article', 'bg_image') )
//        {
//            if( $objRow->bgImage )
//            {
//                $classes[] = 'has-bg-image';
//            }
//        }

        if( $permission->hasFullAccessTo('article', 'width') )
        {
            if( $article->width )
            {
                $classes[] = 'width-' . $article->width;
            }
        }

//        if( $objPermission->hasFullAccessTo('article', 'height') )
//        {
//            if( $objRow->height )
//            {
//                $classes[] = 'height-' . $objRow->height;
//            }
//        }
//
//        if( ScriptHelper::hasPageFullPage( true ) )
//        {
//            if( !$objRow->noSection && false === strpos($cssID[1], 'no-section') )
//            {
//                $classes[] = 'section';
//            }
//
//            if( false !== strpos($cssID[1], 'show-from-') )
//            {
////                preg_match_all('/show-from-([A-Za-z]+)/', $cssID[1], $matches);
//
//                $classes[] = 'nav-section';
//            }
//        }

//        $cssID[1] = trim(preg_replace('/no-section/', '', $cssID[1]));

        $article->cssID      = serialize( $cssID );
        $article->classes    = $classes;
    }


}
