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
use Contao\FrontendTemplate;
use Contao\PageRegular;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\StyleSheets;
use Contao\Template;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Config\ThemeDesignerConfig;
use IIDO\CoreBundle\Util\PageUtil;


class PageListener
{
    protected PageUtil $pageUtil;




    public function __construct( PageUtil $pageUtil )
    {
        $this->pageUtil = $pageUtil;
    }



    /**
     * @Hook("generatePage")
     */
    public function onGeneratePage( PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular ): void
    {
        $this->pageUtil->addDefaultPageStyleSheets();
        $this->pageUtil->addDefaultPageScripts();

        $themeDesigner = ThemeDesignerConfig::loadCurrentThemeDesigner();
        $styles = [];

        //TODO:
        $GLOBALS['TL_JAVASCRIPT']['aos'] = 'bundles/iidocore/scripts/library/aos/2.3.4/aos.min.js|static';
        $GLOBALS['TL_CSS']['aos'] = 'bundles/iidocore/styles/library/aos/2.3.4/aos.min.css||static';


        //TODO:
        $GLOBALS['TL_JAVASCRIPT']['fancybox'] = 'bundles/iidocore/scripts/library/fancybox/3.2.10/jquery.fancybox.min.js|static';
        $GLOBALS['TL_CSS']['fancybox'] = 'bundles/iidocore/styles/library/fancybox/3.2.10/jquery.fancybox.min.css||static';

        $template = new FrontendTemplate('script_fancybox');
        $GLOBALS['TL_JQUERY']['fancybox'] = $template->parse();


        //TODO:
        $GLOBALS['TL_CSS']['hamburgers'] = 'bundles/iidocore/styles/library/hamburgers/1.1.3/hamburgers.scss||static';


        //TODO:
        $GLOBALS['TL_JAVASCRIPT']['smoothscroll'] = 'bundles/iidocore/scripts/library/smoothscroll/1.5.3/jquery.smoothscroll.min.js|static';


        //TODO:
        $GLOBALS['TL_JAVASCRIPT']['iido_page'] = 'bundles/iidocore/scripts/iido/IIDO.Page.js|static';


        if( !$themeDesigner )
        {
            return;
        }

        if( !$themeDesigner->getTopDisabled() )
        {
            if( $themeDesigner->getTopEnablePadding() )
            {
                $padding = $themeDesigner->getTopPadding();

                $row =
                [
                    'selector'      => 'header .header-top-bar > .header-top-bar-inside',

                    'alignment'     => true,
                    'padding'       => ['top' => $padding, 'bottom' => $padding, 'unit' => 'px']
                ];

                $styles[] = $row;
            }

            if( $themeDesigner->getTopEnableBorder() )
            {
                $borderColor = $themeDesigner->getTopBorderColor();

                $row =
                [
                    'selector'  => 'header .header-top-bar',

                    'border'    => true,
                    'bordercolor'   => ['bottom'=>$borderColor],
                    'borderwidth'   => ['bottom'=>1, 'unit'=>'px'],
                    'borderstyle'   => 'solid',
                ];

                $styles[] = $row;
            }
        }

        if( $themeDesigner->getEnablePageBackgroundColor() && $pageBackgroundColor = $themeDesigner->getPageBackgroundColor())
        {
            $styles['body']['background'] = true;
            $styles['body']['bgcolor'] = $this->renderColor( $pageBackgroundColor );
        }

        if( $themeDesigner->getEnablePageBackgroundImage() )
        {
            $pageImage = $themeDesigner->getPageBackgroundImage();

            if( $pageImage )
            {
                $styles['body']['background'] = true;
                $styles['body']['bgimage'] = $pageImage->path;

                if( $themeDesigner->getEnablePageBackgroundPosition() )
                {
                    $styles['body']['bgposition'] = $themeDesigner->getPageBackgroundPosition();
                }

                if( $themeDesigner->getEnablePageBackgroundRepeat() )
                {
                    $styles['body']['bgrepeat'] = $themeDesigner->getPageBackgroundRepeat();
                }
            }
        }

        if( count($styles) )
        {
            $styleSheet = new StyleSheets();

            $strStyles = '';
            foreach( $styles as $selector => $style )
            {
                if( !is_array($style) )
                {
                    $strStyles .= $style;
                }
                else
                {
                    if( !isset($style['selector']) || $style['selector'] == '' )
                    {
                        $style['selector'] = $selector;
                    }

                    $strStyles .= $styleSheet->compileDefinition( $style, true );
                }
            }

            $GLOBALS['TL_HEAD'][] = Template::generateInlineStyle( $strStyles );
        }
    }



    protected function renderColor( $color )
    {
        $alpha = '';

        if( str_contains($color, 'rgb') )
        {
            $parts = StringUtil::trimsplit(',', preg_replace('/^rgb(a|)\(([0-9\s,.]+)\)/', '$2', trim($color)) );

            if( count($parts) === 4 )
            {
                $alpha = ($parts[3] * 100);
            }

            $color = sprintf("#%02x%02x%02x", $parts[0], $parts[1], $parts[2]);
        }

        return [preg_replace('/^#/', '', $color), $alpha];
    }



    /**
     * @Hook("getPageStatusIcon")
     */
    public function onGetPageStatusIcon( $page, string $image ): string
    {
        if( 'global_element' === $page->type )
        {
            return 'bundles/iidocore/images/icons/folder.svg';
        }

        return $image;
    }
}
