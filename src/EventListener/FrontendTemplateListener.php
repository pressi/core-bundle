<?php
declare(strict_types=1);

/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener;


use Contao\ArticleModel;
use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\Template;
use Haste\Input\Input;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Config\ThemeDesignerConfig;
use IIDO\CoreBundle\Entity\ThemeDesignerEntity;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;
//use IIDO\BasicBundle\Renderer\ArticleTemplateRenderer;


class FrontendTemplateListener implements ServiceAnnotationInterface
{

//    /**
//     * Hook("parseFrontendTemplate")
//     */
//    public function onParseFrontendTemplate($strBuffer, $templateName): string
//    {
//        /* @var \PageModel $objPage */
//        global $objPage;
//
//        if( 0 === strpos($templateName, 'mod_article') )
//        {
//            $strBuffer = ArticleTemplateRenderer::parseTemplate( $strBuffer, $templateName );
//        }
//
//        return $strBuffer;
//    }



    /**
     * @Hook("outputFrontendTemplate")
     */
    public function onOutputFrontendTemplate(string $content, string $template): string
    {
        /** @var \PageModel $objPage */
        global $objPage;

        $objRootPage = PageModel::findByPk( $objPage->rootId );
        $themeDesigner = ThemeDesignerConfig::loadCurrentThemeDesigner();
        $container = System::getContainer();

        if( 0 === strpos($template, 'fe_page') )
        {
            if( Input::get('iido_themeDesigner_frame') !== '1' )
            {
                $themeDesignerConfig = $container->getParameter('iido_core.themeDesigner');

                if( !$themeDesignerConfig['disabled'] && !$objRootPage->disableThemeDesigner )
                {
                    Controller::loadLanguageFile('iido');

                    $bundlePath = BundleConfig::getBundlePath(true, false);
                    $cssPath = $bundlePath . '/styles/themeDesigner.css';
                    $jsPath = $bundlePath . '/scripts/themeDesigner.js';

                    $fileTime = filemtime( $cssPath );
                    $fileTimeJs = filemtime( $jsPath );

//                    $doctrine = $container->get('doctrine');
//                    $entityManager = $doctrine->getManager();
//                    $repository = $doctrine->getRepository(ThemeDesignerEntity::class);

//                    $model = $repository->findOneBy(['page'=>$objRootPage->id]);
                    $tokenChecker = $container->get('contao.security.token_checker');

                    $beUserLoggedIn = $tokenChecker->hasBackendUser();

                    $themeDesignerTemplateConfig =
                    [
                        'pageTitle' => 'THEME DESIGNER :: ' . $objRootPage->title,
                        'baseUrl'   => Environment::get('base'),
                        'pageId'    => $objRootPage->id,
                        'iconPath'  => '../' . BundleConfig::getBundlePath( true ) . '/images/icons/',

                        'headStyles'    => Template::generateStyleTag( $cssPath, null, $fileTime ),
                        'headScripts'   => Template::generateScriptTag( $jsPath, false, $fileTimeJs ),

                        'themeModel'            => $themeDesigner,
                        'enableFilesManager'    => $beUserLoggedIn,

                        'fieldOptions'  =>
                        [
                            'repeat'            => $GLOBALS['TL_LANG']['IIDO']['options']['repeat'],
                            'position'          => $GLOBALS['TL_LANG']['IIDO']['options']['position'],
                            'backgroundSize'    => $GLOBALS['TL_LANG']['IIDO']['options']['backgroundSize'],
                            'boxedLayouts'      => $GLOBALS['TL_LANG']['IIDO']['layouts']['boxed'],
                            'headerLayouts'     => $GLOBALS['TL_LANG']['IIDO']['layouts']['header'],
                            'footerColumns'     => $GLOBALS['TL_LANG']['IIDO']['options']['footerColumns']
                        ]
                    ];

                    return $container->get('twig')->render('@IIDOCore/Frontend/ThemeDesigner/FePage.html.twig', $themeDesignerTemplateConfig);
                }
            }

            //HEADER
            if( $objPage->removeHeader )
            {
                $content = preg_replace('/<header([A-Za-z0-9öäüÖÄÜß\s="\-:\/\\.,;:_>\n<\{\}]{0,})<\/header>/', '', $content);
            }
            else
            {
                $headerLayout = $themeDesigner->getHeaderLayout() ?: 'layout01';

                if( preg_match('/<header([A-Za-z0-9\s\-,;.:_\(\)\{\}\/="]+)class="/', $content) )
                {
                    $content = $content;
                }
                else
                {
                    $content = preg_replace('/<header/', '<header class="layout-' . $headerLayout . '"', $content);
                }
            }

//            if( $themeDesigner->getTopDisabled() || !$themeDesigner->getTopEnableCanvastrigger() )
//            {
//                $content = preg_replace('/<div id="canvasTop">([\s\n]{0,})<div class="inside">([A-Za-z0-9\s\n\-:_\{\}]{0,})<\/div>([\s\n]{0,})<\/div>/', '', $content);
//            }


            // REMOVE EMPTY "custom" CONTAINER
            $content = preg_replace('/<div class="custom">([\s\n]{0,})<\/div>/', '', $content);


            // ENABLE PREVIEW MODE OPTIONS
            if( $container->getParameter('iido_core.previewMode') )
            {
                $content = preg_replace('/<meta name="robots" content="([a-z,]+)">/', '<meta name="robots" content="noindex,nofollow">', $content);
            }

//            if( IIDOConfig::get('previewMode') )
//            {
//                $strBuffer = preg_replace('/<meta name="robots" content="([a-z,]+)">/', '<meta name="robots" content="noindex,nofollow">', $strBuffer);
//            }

//            if( Input::get('mode') == 'dev' )
//            {
//                $folderName = $objPage->rootAlias;
//
//                if( $objPage->languageMain )
//                {
//                    $objLangPage    = PageModel::findByPk( $objPage->languageMain );
//                    $folderName     = $objLangPage->rootAlias;
//                }
//
//                $mainScssFile = BasicHelper::getRootDir(true ) . "files/{$folderName}/styles/main.scss";
//
//                if( file_exists($mainScssFile) )
//                {
//                    $mainScssFileContent = file_get_contents($mainScssFile);
//
//                    if (strpos($mainScssFileContent, "\n\n//cachebuster"))
//                    {
//                        $mainScssFileContent = str_replace("\n\n//cachebuster", "", $mainScssFileContent);
//                    }
//                    else
//                    {
//                        $mainScssFileContent = "$mainScssFileContent\n\n//cachebuster";
//                    }
//                    file_put_contents($mainScssFile, $mainScssFileContent);
//                }
//            }

//            if( ScriptHelper::hasPageFullPage( true ) )
//            {
////                $navi = '<div class="fullpage-navigation">
////    <div class="nav-label">Weiter</div>
////    <div class="nav-prev"><div class="label">Zurück</div></div>
////    <div class="nav-next"><div class="label">Weiter</div></div>
////</div>';
//
//                $objArticles = ArticleModel::findPublishedByPidAndColumn($objPage->id, 'main');
//                $arrAnchros = [];
//                $articleTitles = [];
//
//                if( $objArticles )
//                {
//                    while( $objArticles->next() )
//                    {
//                        $cssID = StringUtil::deserialize( $objArticles->cssID, true);
//
//                        $arrAnchros[] = $objArticles->alias;
//
//                        if( $objArticle->hideInNav || $objArticle->hideInNavigation || false !== strpos($cssID[1], 'hide-in-navigation') || false !== strpos($cssID[1], 'hide-in-nav') )
//                        {
//                            continue;
//                        }
//
//                        $articleTitles[] = '<span class="nav-title nav-title-' . $objArticles->alias . '">' . ($objArticles->frontendTitle?:$objArticles->title) . '</span>';
//                    }
//                }
//
//                $navi = '<div class="fullpage-navigation">
//    <div class="nav-prev"><div class="label"><span class="prev">Zurück</span></div></div>
//    <div class="nav-top"><div class="label"><span class="top">Nach Oben</span></div></div>
//    <div class="nav-next"><div class="label"><span class="next">Weiter</span>' . implode('', $articleTitles) . '</div></div>
//</div>';
//                $objScript = new FrontendTemplate('fullpage_default');
//
//                $objScript->selector = '#main > .inside';
//                $objScript->startSlideAlwaysOnFirst = true;
//                $objScript->anchors = $arrAnchros;
//
//                $strBuffer = preg_replace('/<\/main>/', $navi . '</main>', $strBuffer);
//                $strBuffer = preg_replace('/<\/body>/', $objScript->parse() . '</body>', $strBuffer);
//            }

//            if( preg_match("/<footer/", $strBuffer) )
//            {
////                    $strBuffer = preg_replace('/<body([^>]+)class="/', '<body$1class="footerpage ', $strBuffer);
//                $arrBodyClasses[] = 'footerpage';
//
//                $divsOpen   = '<div id="page"><div id="outer">';
//                $divsClose  = '</div></div></div>';
//
//                $strBuffer  = str_replace('<div id="wrapper">', $divsOpen . '<div id="wrapper">', $strBuffer);
//                $strBuffer  = str_replace('<footer', $divsClose . '<footer', $strBuffer);
//
//                $strBuffer  = preg_replace('/<\/footer>(\s){0,}<\/div>/im', '</footer>', $strBuffer);
//            }
        }

        return $content;

//        /** @var \PageModel $objPage */
//        global $objPage;
//
//        $arrBodyClasses     = array();
//        $objRootPage        = \PageModel::findByPk( $objPage->rootId );
//        $objLayout          = BasicHelper::getPageLayout( $objPage) ;
//        $isFullpage         = PageHelper::isFullPageEnabled( $objPage, $objLayout );
//
//        if( $strTemplate === 'fe_page' )
//        {
//            $strBuffer          = PageHelper::replaceBodyClasses( $strBuffer );
//            $strBuffer          = PageHelper::replaceWebsiteTitle( $strBuffer );
//
//            $layoutHasFooter    = $objLayout->rows;
//            $footerMode         = (($objLayout->footerAtBottom && ($layoutHasFooter == "2rwf" || $layoutHasFooter == "3rw")) ? true : false);
//
//            if( $isFullpage )
//            {
//                if( $objPage->fullpageDirection == "horizontal" )
//                {
//                    $strBuffer = preg_replace('/<main([A-Za-z0-9\s\-="\(\):;\{\}\/%.]{0,})id="main"([A-Za-z0-9\s\-="\(\):;\{\}\/%.]{0,})>([A-Za-z0-9\s\n]{0,})<div class="inside">/', '<main$1id="main"$2>$3<div class="inside section">', $strBuffer);
//                }
//
//                $strBuffer = preg_replace('/<html/', '<html class="enable-fullpage"', $strBuffer);
//            }
//
//            if($footerMode)
//            {
//                if( preg_match("/<footer/", $strBuffer) )
//                {
////                    $strBuffer = preg_replace('/<body([^>]+)class="/', '<body$1class="footerpage ', $strBuffer);
//                    $arrBodyClasses[] = 'footerpage';
//
//                    $divsOpen   = '<div id="page"><div id="outer">';
//                    $divsClose  = '</div></div></div>';
//
//                    $strBuffer  = str_replace('<div id="wrapper">', $divsOpen . '<div id="wrapper">', $strBuffer);
//                    $strBuffer  = str_replace('<footer', $divsClose . '<footer', $strBuffer);
//
//                    $strBuffer  = preg_replace('/<\/footer>(\s){0,}<\/div>/im', '</footer>', $strBuffer);
//                }
//            }
//            else
//            {
//                if($objLayout->addPageWrapperOuter)
//                {
//                    $strBuffer = str_replace('<div id="wrapper">', '<div id="outer"><div id="wrapper">', $strBuffer);
//
//                    if( preg_match('/<footer/', $strBuffer) )
//                    {
//                        $strBuffer = str_replace('</footer>',  '</footer></div>', $strBuffer);
//                    }
//                    else
//                    {
//                        $strBuffer = str_replace('</body>', '</div>' . "\n" . '</body>', $strBuffer);
//                    }
//                }
//
//                if($objLayout->addPageWrapperPage)
//                {
//                    $replaceID = "wrapper";
//
//                    if($objLayout->addPageWrapperOuter)
//                    {
//                        $replaceID = "outer";
//                    }
//
//                    $strBuffer = str_replace('<div id="' . $replaceID . '">', '<div id="page"><div id="' . $replaceID . '">', $strBuffer);
//
//                    if( preg_match('/<footer/', $strBuffer) )
//                    {
//                        $strBuffer = str_replace('</footer>',  '</footer></div>', $strBuffer);
//                    }
//                    else
//                    {
//                        $strBuffer = str_replace('</body>', '</div>' . "\n" . '</body>', $strBuffer);
//                    }
//                }
//            }
//
//            if( $objRootPage->enablePageFadeEffect )
//            {
//                $outerID    = 'container';
//                $strBuffer  = str_replace('<div id="' . $outerID . '">', '<div id="barba-wrapper"><div class="barba-container"><div id="' . $outerID . '">', $strBuffer);
//
//                if( preg_match('/<footer/', $strBuffer) )
//                {
//                    $strBuffer = str_replace('<footer',  '</div></div><footer', $strBuffer);
//                }
//                else
//                {
//                    $strBuffer = str_replace('</body>', '</div></div>' . "\n" . '</body>', $strBuffer);
//                }
//            }
//
//            if( $objPage->removeFooter )
//            {
//                $strBuffer = preg_replace('/<footer([A-Za-z0-9öäüÖÄÜß\s="\-:\/\\.,;:_>\n<\{\}]{0,})<\/footer>/', '', $strBuffer);
//            }
//            else
//            {
//                $objFooterArticle   = \ArticleModel::findByAlias("ge_footer_" . $objRootPage->alias);
//                $footerClass        = \StringUtil::deserialize($objFooterArticle->cssID, true)[1];
//                $arrFooterAttribute = array();
//
//                if( $objFooterArticle->isFixed )
//                {
//                    if( !$objFooterArticle->isAbsolute )
//                    {
//                        $footerClass = trim($footerClass . ' is-fixed');
//                    }
//                    else
//                    {
//                        $footerClass = trim($footerClass . ' pos-abs');
//                    }
//
//                    if( $objFooterArticle->position === "top" )
//                    {
//                        $footerClass = trim($footerClass . ' pos-top');
//                    }
//                    elseif( $objFooterArticle->position === "right" )
//                    {
//                        $footerClass = trim($footerClass . ' pos-right');
//                    }
//                    elseif( $objFooterArticle->position === "bottom" )
//                    {
//                        $footerClass = trim($footerClass . ' pos-bottom');
//                    }
//                    elseif( $objFooterArticle->position === "left" )
//                    {
//                        $footerClass = trim($footerClass . ' pos-left');
//                    }
//
//                    $arrFooterWidth = \StringUtil::deserialize($objFooterArticle->articleWidth, TRUE);
//
//                    if( $arrFooterWidth['value'] )
//                    {
//                        $arrFooterAttribute['style'] = trim($arrFooterAttribute['style'] . ' width:' . $arrFooterWidth['value'] . ($arrFooterWidth['unit'] . ';' ? : 'px;'));
//                    }
//
//                    $arrFooterHeight = \StringUtil::deserialize($objFooterArticle->articleHeight, TRUE);
//
//                    if( $arrFooterHeight['value'] )
//                    {
//                        $arrFooterAttribute['style'] = trim($arrFooterAttribute['style'] . ' height:' . $arrFooterHeight['value'] . ($arrFooterHeight['unit'] . ';' ? : 'px;'));
//                    }
//                }
//
//                if( strlen($footerClass) )
//                {
//                    $strAttributes = '';
//
//                    if( count($arrFooterAttribute) )
//                    {
//                        foreach($arrFooterAttribute as $key => $value)
//                        {
//                            $strAttributes .= ' ' . $key . '="' . $value . '"';
//                        }
//                    }
//
//                    $strBuffer = preg_replace('/<footer/', '<footer class="' . $footerClass . '"' . $strAttributes, $strBuffer);
//                }
//            }
//
//            if( $objPage->removeHeader )
//            {
//                $strBuffer = preg_replace('/<header([A-Za-z0-9öäüÖÄÜß\s="\-:\/\\.,;:_>\n<\{\}]{0,})<\/header>/', '', $strBuffer);
//            }
//            else
//            {
//                $strBuffer = HeaderHelper::renderHeader( $strBuffer );
//            }
//
//            if( $objPage->removeLeft )
//            { //TODO: check DIV tags!
//                $strBuffer = preg_replace('/<aside id="left"([A-Za-z0-9öäüÖÄÜß\s\-,:.;_\/\\="\n\>\<\{\}]{0,})<\/aside>/', '', $strBuffer);
//            }
//
//            if( preg_match('/<footer/', $strBuffer) && PageHelper::hasBodyClass("homepage", $strBuffer) )
//            {
//                if( preg_match('/<footer([A-Za-z0-9\s\-=",;.:_\(\)\{\}]{0,})class/', $strBuffer) )
//                {
//                    $strBuffer = preg_replace('/<footer([A-Za-z0-9\s\-=",;.:_\(\)\{\}]{0,})class="/', '<footer$1class="home ', $strBuffer);
//                }
//                else
//                {
//                    $strBuffer = preg_replace('/<footer/', '<footer class="home"', $strBuffer);
//                }
//            }
//
//            if( preg_match('/nav-sub/', $strBuffer) && !preg_match('/(ce_backlink|mod_newsearder)/', $strBuffer ))
//            {
//                $strBuffer = preg_replace('/nav-sub/', 'nav-sub has-bg-left', $strBuffer);
//                $strBuffer = preg_replace('/<nav([A-Za-z0-9\s\-=\",;.:_\{\}\/\(\)]{0,})class="mod_navigation([A-Za-z0-9\s\-\'\",;.:_\{\}\/\(\)]{0,})nav-sub([A-Za-z0-9\s\-\'\",;.:_\{\}\/\(\)]{0,})"([A-Za-z0-9\s\-=\",;.:_\{\}\/\(\)]{0,})>/', '<nav$1class="mod_navigation$2nav-sub$3"$4><div class="bg-subnav"></div>', $strBuffer);
//            }
//
//            if( preg_match('/homepage/', $objPage->cssClass) )
//            {
//                $strBuffer = preg_replace('/<div class="page-title-container">([A-Za-z0-9öäüÖÄÜß&!?\-\n\s_.,;:<>="\{\}\(\)\/]{0,})<\/div>([\s]{0,})<div([A-Za-z0-9\-\s="]{0,})class="mod_article/', '<div$3class="mod_article', $strBuffer);
//                $strBuffer = preg_replace('/<div class="custom">([\s]{0,})<div id="main_menu_container">([A-Za-z0-9öäüÖÄÜß&!?@#\-\n\s_.,;:<>="\{\}\(\)\/]{0,})<footer/', '</div></div><footer', $strBuffer);
//            }
//
//            $arrContainerClasses    = array();
//            $arrMainClasses         = array();
//
//            if( preg_match('/id="left"/', $strBuffer) )
//            {
//                $arrBodyClasses[] = 'has-left-col';
//
//                $arrContainerClasses[] = 'has-left-col';
//            }
//
//            if( preg_match('/id="right"/', $strBuffer) )
//            {
//                $arrBodyClasses[] = 'has-right-col';
//            }
//
//            if( !preg_match('/id="bg_image"/', $strBuffer) )
//            {
//                $arrContainerClasses[]  = 'has-no-bgimage-col';
//                $arrMainClasses[]       = 'has-no-bgimage-col';
//            }
//            else
//            {
//                $arrContainerClasses[]  = 'has-bgimage-col';
//                $arrMainClasses[]       = 'has-bgimage-col';
//            }
//
//            if( count($arrBodyClasses) )
//            {
//                $strBuffer = PageHelper::replaceBodyClasses($strBuffer, $arrBodyClasses);
//            }
//
//            if( PageHelper::hasBodyClass("homepage", $strBuffer) )
//            {
//                $arrContainerClasses[]  = 'home-container';
//                $arrMainClasses[]       = 'home-main';
//            }
//
//            if( PageHelper::hasBodyClass("projectpage", $strBuffer) )
//            {
//                $arrContainerClasses[]  = 'container-projectpage';
//                $arrMainClasses[]       = 'main-projectpage';
//            }
//
//            if( PageHelper::hasBodyClass("teampage", $strBuffer) )
//            {
//                $arrContainerClasses[]  = 'container-teampage';
//                $arrMainClasses[]       = 'main-teampage';
//            }
//
//            if( PageHelper::hasBodyClass("projectdetailpage", $strBuffer) )
//            {
//                $arrContainerClasses[]  = 'container-projectdetailpage';
//                $arrMainClasses[]       = 'main-projectdetailpage';
//            }
//
//            if( count($arrContainerClasses) )
//            {
//                $strBuffer = preg_replace('/id="container"/', 'id="container" class="' . implode(' ', $arrContainerClasses) . '"', $strBuffer);
//            }
//
//            if( count($arrMainClasses) )
//            {
//                $strBuffer = preg_replace('/id="main"/', 'id="main" class="' . implode(' ', $arrMainClasses) . '"', $strBuffer);
//            }
//
//            if( $objPage->addPageLoader )
//            {
//                preg_match_all('/<html([A-Za-z0-9\s\-_:.;,="%]{0,})>/', $strBuffer, $arrHtmlMatches);
//
//                if( count($arrHtmlMatches[0]) )
//                {
//                    if( preg_match('/class="/', $arrHtmlMatches[1][0]) )
//                    {
//                        $strBuffer = preg_replace('/<html([A-Za-z0-9\s\-_:.;,="%]{0,})class="/', '<html$1class="enable-pageloader ', $strBuffer);
//                    }
//                    else
//                    {
//                        $strBuffer = preg_replace('/<html/', '<html class="enable-pageloader"', $strBuffer);
//                    }
//                }
//            }
//
//            // Remove empty canvasTop && pitLane
//            $objCanvasTop = \ArticleModel::findByAlias("ge_canvas-top_" . $objRootPage->alias);
//
//            if( !$objCanvasTop )
//            {
//                $strBuffer = preg_replace('/<div class="custom">([\s\n]{0,})<div id="canvasTop">([\s\n]{0,})<div class="inside">([A-Za-z0-9\s\n\-:_\{\}]{0,})<\/div>([\s\n]{0,})<\/div>([\s\n]{0,})<\/div>/', '', $strBuffer);
//            }
//
//            $objPitLane = \ArticleModel::findByAlias("ge_pitlane_" . $objRootPage->alias);
//
//            if( !$objPitLane )
//            {
//                $strBuffer = preg_replace('/<div class="custom">([\s\n]{0,})<div id="pitLane">([\s\n]{0,})<div class="inside">([A-Za-z0-9\s\n\-:_\{\}]{0,})<\/div>([\s\n]{0,})<\/div>([\s\n]{0,})<\/div>/', '', $strBuffer);
//            }
//
//            if( preg_match('/id="footer"/', $strBuffer) )
//            {
//                $strBuffer = preg_replace('/<footer([A-Za-z0-9\s\-,;.:_=\'öäüÖÄÜß?!"\(\)\{\}]{0,})>([\s\n]{0,})<div class="inside">/', '<footer$1><div class="inside"><div class="footer-container"><div class="footer-container-inside">', $strBuffer, -1, $foReCo);
//
//                if( $foReCo )
//                {
//                    $strBuffer = preg_replace('/<\/footer>/', '</div></div></footer>', $strBuffer);
//                }
//            }
//
//            if( preg_match('/nav-cont-left-outside/', $strBuffer) )
//            {
//                $strBuffer = preg_replace('/<\/body>/', '<div class="open-left-side-navigation"><div class="olsn-inside"></div></div></body>', $strBuffer);
//            }
//
//            if( $objRootPage->addPageBorder )
//            {
//                $wrapperStyles = '';
//
//                $arrBorderWidth = \StringUtil::deserialize( $objRootPage->pageBorderWidth );
//                $borderColor    = ColorHelper::compileColor( $objRootPage->pageBorderColor );
//
//                if( $borderColor === "transparent" )
//                {
//                    $borderColor = '#fff';
//                }
//
//                if( $arrBorderWidth['value'] && $arrBorderWidth['unit'] )
//                {
//                    $wrapperStyles = 'border:' .$arrBorderWidth['value'] . $arrBorderWidth['unit'] . ' solid ' . $borderColor . ';';
//                }
//
//                if( $wrapperStyles )
//                {
//                    $strBuffer = preg_replace('/id="wrapper"/', 'id="wrapper" style="' . $wrapperStyles . '"', $strBuffer);
//
//                    $strBuffer = PageHelper::replaceBodyClasses($strBuffer, ['page-has-border']);
//                }
//            }
//
//            $pageColor = ColorHelper::compileColor( $objPage->pageColor );
//
//            if( $pageColor !== 'transparent' )
//            {
//                $strBuffer = preg_replace('/<body/', '<body style="background:' . $pageColor . ';"', $strBuffer);
//            }
//
//        }
//
//        return $strBuffer;
    }

}
