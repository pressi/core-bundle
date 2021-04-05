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
use Contao\PageRegular;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\StyleSheets;
use Contao\Template;
use IIDO\CoreBundle\Config\ThemeDesignerConfig;


class PageListener
{

    /**
     * @Hook("generatePage")
     */
    public function onGeneratePage( PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular ): void
    {
        $themeDesigner = ThemeDesignerConfig::loadCurrentThemeDesigner();
        $styles = [];

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
                    $style['selector'] = $selector;

                    $strStyles .= $styleSheet->compileDefinition( $style, true );
                }
            }

            $GLOBALS['TL_HEAD'][] = Template::generateInlineStyle( $strStyles );
        }
    }



    protected function renderColor( $color )
    {
        $alpha = '';

        if( false !== strpos($color, 'rgb') )
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
}
