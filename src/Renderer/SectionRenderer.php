<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Renderer;


use Contao\ArticleModel;
use Contao\Controller;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\Image\ResizeConfiguration;
use Contao\LayoutModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Config\ThemeDesignerConfig;
use IIDO\CoreBundle\Entity\ThemeDesignerEntity;


class SectionRenderer
{
    private static $themeDesigner = null;



    public static function renderHeader(): string
    {
        $themeDesigner = self::getThemeDesigner();

        if( !$themeDesigner )
        {
            $section = self::getSection('header');

            if( $section )
            {
                return self::renderSection( $section );
            }

            return '';
        }

        $twig = System::getContainer()->get('twig');

        $config =
        [
            'enableHeaderTop'   => !$themeDesigner->getTopDisabled(),
            'enableHeader'      => !$themeDesigner->getHeaderDisabled()
//            'header' =>
//            [
//                'layout' => $themeDesigner->getHeaderLayout() ? : 'layout01'
//            ]
        ];

        $figureBuilder = System::getContainer()->get( Studio::class )->createFigureBuilder();
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
            //TODO:
            $config['navLang'] = $navLanfConfig;
        }

        if( $themeDesigner->getHeaderEnableSearch() )
        {
            $searchConfig = ['enabled'=>true];
            //TODO:
            $config['search'] = $searchConfig;
        }

        if( $themeDesigner->getHeaderEnableSocials() )
        {
            $socialsConfig = ['enabled'=>true];
            //TODO:
            $config['socials'] = $socialsConfig;
        }

        return $twig->render('@IIDOCore/Frontend/Section/header.html.twig', $config);
    }



    public static function renderFooter(): string
    {
        $themeDesigner = self::getThemeDesigner();

        if( !$themeDesigner )
        {
            $section = self::getSection('footer');

            if( $section )
            {
                return self::renderSection( $section );
            }

            return '';
        }

        //TODO: THEME DESIGNER!!

        return '';
    }



    public static function renderFooterBottom(): string
    {
        $themeDesigner = self::getThemeDesigner();

        if( !$themeDesigner )
        {
            $section = self::getSection('footer-bottom');

            if( $section )
            {
                return self::renderSection( $section );
            }

            return '';
        }

        //TODO: THEME DESIGNER!!

        return '';
    }



    public static function renderFixedElements(): string
    {
        //TODO: THEME DESIGNER!!

        $section = self::getSection('fixed-elements');

        if( $section )
        {
            return self::renderSection( $section );
        }

        return '';
    }



    public static function renderOffsetNavigation(): string
    {
        global $objPage;

        $themeDesigner = self::getThemeDesigner();

        if( !$themeDesigner )
        {
            $section = self::getSection('offset-navigation');

            if( $section )
            {
                return self::renderSection( $section );
            }

            $objRootPage = PageModel::findByPk( $objPage->rootId );
            $enableOffsetNavigation = $objRootPage->enableOffsetNav;

            if( $enableOffsetNavigation )
            {
                if( $objRootPage->includeLayout )
                {
                    $layout = LayoutModel::findByPk( $objRootPage->layout );
                    $navigation = ModuleModel::findOneBy(['type=?', 'pid=?'], ['navigation', $layout->pid]);

                    $navContent = '';

                    if( $navigation )
                    {
                        $navigation->cssID = ['', 'nav-main'];

                        $navContent = Controller::getFrontendModule( $navigation );
                    }

                    if( BundleConfig::isActiveBundle('terminal42/contao-changelanguage') )
                    {
                        $langNav = ModuleModel::findOneBy(['type=?', 'pid=?'], ['changelanguage', $layout->pid]);

                        if( $langNav )
                        {
                            $langNav->cssID = ['', 'nav-lang'];

                            $navContent .= Controller::getFrontendModule( $langNav );
                        }
                    }

                    $company = System::getContainer()->get('iido.core.util.company')->getCurrentCompanyData();

                    if( $company )
                    {
                        $phone  = \file_get_contents('bundles/iidocore/images/icons/fe/phone.svg');
                        $email  = \file_get_contents('bundles/iidocore/images/icons/fe/email.svg');

                        if( $company->phone )
                        {
                            $basicUtil = System::getContainer()->get('iido.core.util.basic');

                            $phone = '<a href="tel:' . $basicUtil->renderPhoneNumber( $company->phone ) . '">' . $phone . '</a>';
                        }

                        if( $company->email )
                        {
                            $email = '<a href="&#109;&#97;&#105;&#108;&#116;&#111;&#58;{{email_url::' . $company->email . '}}">' . $email . '</a>';
                        }

                        $navContent .= '<div class="ce_gallery content-element mobile-contact-gallery"><div class="element-inside"><ul class="cols_2"><li>' . $phone . '</li><li>' . $email . '</li></ul></div></div>';
                    }

                    return $navContent;
                }
            }
        }

        return '';
    }



    protected static function getSection( $name ): ?ArticleModel
    {
        global $objPage;

        $section = ArticleModel::findOneByAlias('ge_' . $name . '_' . $objPage->rootAlias );

        if( !$section )
        {
            $parts = explode('-', $name);
            $name = $parts[0] . ucfirst($parts[1]);

            $section = ArticleModel::findBy('articleType', $name);

            if( $section )
            {
                if( $section->count() > 1 )
                {
                    //TODO: search for the right root page!! ? save root page id in article?? submit callback?
                }
                else
                {
                    $section = $section->first()->current();
                }
            }
        }

        return $section;
    }



    protected static function renderSection( ArticleModel $section ): string
    {
        $wrapperStart = $wrapperClose = '';

        if( $section->enableColumns )
        {
            //TODO: classes "g-4"

            $wrapperStart .= '<div class="container"><div class="row g-4">';
            $wrapperClose .= '</div></div>';
        }

        return $wrapperStart . Controller::getArticle($section, false, true) . $wrapperClose;
    }



    protected static function getThemeDesigner(): ?ThemeDesignerEntity
    {
        if( static::$themeDesigner )
        {
            return static::$themeDesigner;
        }

        $themeDesigner = ThemeDesignerConfig::loadCurrentThemeDesigner();

        static::$themeDesigner = $themeDesigner;

        return $themeDesigner;
    }
}
