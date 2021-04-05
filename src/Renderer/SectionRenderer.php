<?php


namespace IIDO\CoreBundle\Renderer;


use Contao\CoreBundle\Image\Studio\Studio;
use Contao\Image\ResizeConfiguration;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Config\ThemeDesignerConfig;


class SectionRenderer
{

    public static function renderHeader()
    {
        $twig = System::getContainer()->get('twig');
        $themeDesigner = ThemeDesignerConfig::loadCurrentThemeDesigner();

        $config =
        [
            'enableHeaderTop'   => !$themeDesigner->getTopDisabled(),
            'enableHeader'      => !$themeDesigner->getHeaderDisabled()
//            'header' =>
//            [
//                'layout' => $themeDesigner->getHeaderLayout() ? : 'layout01'
//            ]
        ];

        $figureBuilder = System::getContainer()->get(Studio::class )->createFigureBuilder();
        /* @var \Contao\CoreBundle\Image\Studio\FigureBuilder $figureBuilder */

        if( $themeDesigner->getEnableLogo() && $logo = $themeDesigner->getLogo() )
        {
            $figureBuilder
                ->fromFilesModel( $logo )
                ->setSize( [200, '', ResizeConfiguration::MODE_PROPORTIONAL] );

            $figure = $figureBuilder->build();
//            $content .= $twig->render('@ContaoCore/Image/Studio/figure.html.twig', ['figure' => $figure]);
            $config['logo'] = $figure;
        }

        if( $themeDesigner->getEnableMobileLogo() && $mobileLogo = $themeDesigner->getMobileLogo() )
        {
            $figureBuilder
                ->fromFilesModel( $mobileLogo )
                ->setSize( [200, '', ResizeConfiguration::MODE_PROPORTIONAL] );

            $figure = $figureBuilder->build();
//            $content .= $twig->render('@ContaoCore/Image/Studio/figure.html.twig', ['figure' => $figure]);
            $config['mobileLogo'] = $figure;
        }

        if( $themeDesigner->getHeaderEnableLangswitcher() )
        {
            $navLanfConfig = ['enabled'=>true];

            $config['navLang'] = $navLanfConfig;
        }

        if( $themeDesigner->getHeaderEnableSearch() )
        {
            $searchConfig = ['enabled'=>true];

            $config['search'] = $searchConfig;
        }

        if( $themeDesigner->getHeaderEnableSocials() )
        {
            $socialsConfig = ['enabled'=>true];

            $config['socials'] = $socialsConfig;
        }

        return $twig->render('@IIDOCore/Frontend/Section/header.html.twig', $config);
    }
}
