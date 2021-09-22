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
use Contao\StringUtil;
use Contao\System;
use IIDO\CoreBundle\Renderer\SectionRenderer;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;


class InsertTagsListener implements ServiceAnnotationInterface
{

    private const TAG = 'iido';


    protected string $iconPath = 'files/%s/Uploads/Icons/';



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
                case "link_void":
                    $return = 'javascript:void(0);';
                    break;

                case "section":
                    $return = '';

                    switch( $chunks[2] )
                    {
                        case "header":
                            $return = SectionRenderer::renderHeader();
                            break;

                        case "footer":
                            $return = SectionRenderer::renderFooter();
                            break;

                        case "footer_bottom":
                        case "footerbottom":
                        case "footer-bottom":
                            $return = SectionRenderer::renderFooterBottom();
                            break;

                        case "fixed-elements":
                            $return = SectionRenderer::renderFixedElements();
                            break;

                        case "offset-navigation":
                            $return = SectionRenderer::renderOffsetNavigation();
                            break;

                        case "canvas-top":
//                            $return = SectionRenderer::renderCanvasTop();
                            break;

                        case "pit-lane":
                            $return = SectionRenderer::renderPitLane();
                            break;
                    }
            }
        }

        if( $chunks[0] === 'icon' )
        {
            $basicUtil  = System::getContainer()->get('iido.utils.basic');
            $pageUtil   = System::getContainer()->get('iido.utils.page');

            $rootDir    = $basicUtil->getRootDir( true );
            $iconPath   = sprintf($this->iconPath, $pageUtil->getRootPageAlias( true ));
            $iconName   = ucfirst($chunks[1]) . '.svg';

            if( file_exists( $rootDir . $iconPath . $iconName ) )
            {
                $return = file_get_contents( $rootDir . $iconPath . $iconName );
            }
        }



        elseif( $chunks[0] === 'company' )
        {
            $company    = System::getContainer()->get('iido.core.util.company')->getCurrentCompanyData();

            if( $company )
            {
                if( $chunks[1] === 'name' )
                {
                    $chunks[1] = 'company';
                }

                $return = $company->{$chunks[1]};

                if( $chunks[1] === 'phone' )
                {
                    $basicUtil  = System::getContainer()->get('iido.utils.basic');

                    $return = '<a href="tel:' . $basicUtil->renderPhoneNumber( $return ) . '">' . $return . '</a>';
                }

                elseif( $chunks[1] === 'email' )
                {
                    $return = '{{email::' . $return . '}}';
                }
            }
        }

        return $return;
    }

}
