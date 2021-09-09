<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener;


use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Environment;
use Contao\FilesModel;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use IIDO\CoreBundle\Util\ContentUtil;
use IIDO\CoreBundle\Util\PageUtil;


class ContentListener
{
    private ContentUtil $contentUtil;


    private PageUtil $pageUtil;


    protected array $skipInsideElement = ['rocksolid_slider'];


    protected array $skipInsideElementClasses = ['parallax-img'];


    protected array $skipAnimation = ['rsce_scrolldown'];



    public function __construct( ContentUtil $contentUtil, PageUtil $pageUtil )
    {
        $this->contentUtil  = $contentUtil;
        $this->pageUtil     = $pageUtil;
    }



    /**
     * @Hook("getContentElement")
     */
    public function onGetContentElement( ContentModel $contentModel, string $buffer, $element ): string
    {
        $cssID          = StringUtil::deserialize( $contentModel->cssID, true );
        $isMobile       = Environment::get("agent")->mobile;

        if( $isMobile && ($contentModel->hideOnMobile || str_contains($cssID[1], 'hide-on-mobile')) )
        {
            return '';
        }
        elseif( !$isMobile && ($contentModel->showOnMobile || str_contains($cssID[1], 'show-on-mobile')) )
        {
            return '';
        }

        global $objPage;

        $article        = ArticleModel::findByPk( $contentModel->pid );

        $contentUtil    = $this->contentUtil; //System::getContainer()->get('iido.core.util.content');
        $pageUtil       = $this->pageUtil; //System::getContainer()->get('iido.core.util.page');
        $type           = $contentModel->type;

        if( 'html' === $type )
        {
            return $buffer;
        }

        $rootPage       = $pageUtil->getRootPage( $objPage );

        list($elementType, $insideClass, $tag) = $contentUtil->getContentElementData( $contentModel );
        $elementClasses = ['content-element'];

        if( 'headline' === $type )
        {
            $buffer = \preg_replace('/class="ce_headline' . ($cssID[1] ? ' ' . $cssID[1] : '' ) . '/', 'class="headline', $buffer);
            $buffer = '<div class="ce_headline' . ($cssID[1] ? ' ' . $cssID[1] : '' ) . '">' . $buffer . '</div>';
        }

        else
        {
            $buffer = System::getContainer()->get('iido.core.util.text')->renderHeadline( $buffer, $contentModel, $type );
        }


        if( 'image' === $type )
        {
            //TODO: checkbox "parallax"
            if( str_contains($cssID[1], 'parallax-img') )
            {
//                TODO: ScriptUtil
                $GLOBALS['TL_JAVASCRIPT']['jarallax_min'] = 'bundles/iidocore/scripts/library/jarallax/1.12.5/jarallax.min.js|static';
                $GLOBALS['TL_JAVASCRIPT']['jarallax_element_min'] = 'bundles/iidocore/scripts/library/jarallax/1.12.5/jarallax-element.min.js|static';

                $GLOBALS['TL_CSS']['jarallax'] = 'bundles/iidocore/styles/library/jarallax/1.12.5/jarallax.css||static';

                $buffer = str_replace('class="image_container', 'class="image_container jarallax', $buffer);

                $imageTag = 'img';

                if( \preg_match('/<picture/', $buffer) )
                {
                    $imageTag = 'picture';
                }

                $buffer = str_replace('<' . $imageTag, '<' . $imageTag . ' class="jarallax-img"', $buffer);
            }

            //TODO: checkbox "imageInline"
            elseif( str_contains($cssID[1], 'image-inline') )
            {
                $inlineImage = \file_get_contents( FilesModel::findByPk( $contentModel->singleSRC )->path );

                if( $inlineImage )
                {
                    $buffer = \preg_replace('/<img([A-Za-z0-9\s\-,;.:_\/\(\)="#?%]+)>/', $inlineImage, $buffer);
                }
            }
        }

        elseif( 'text' === $type )
        {
//            if( false !== strpos($cssID[1], 'text-nav') )
//            {
//                $strBuffer = $this->renderTextNavigation( $strBuffer, $objRow, $objElement );
//            }

            $buffer = preg_replace('/<p([A-Za-z0-9\s\-,;.:_#="]{0,})>(&lt;|<|\[lt\])--(&gt;|>|\[gt\])<\/p>/', '<div class="text-divider"><div class="td-inside"></div></div>', $buffer);

            if( str_contains($cssID[1], 'list-block-center') )
            {
                $buffer = str_replace(['<ul>', '</ul>'], ['<div class="list-block-container"><ul>', '</ul></div>'], $buffer);
            }

            if( $contentModel->addImage && $contentModel->singleSRC )
            {
                $elementClasses[] = 'has-image';
                $elementClasses[] = 'ip-' . $contentModel->floating;

                if( str_contains($cssID[1], 'boxed') )
                {
                    if( $contentModel->floating === 'below' )
                    {
                        $buffer = \preg_replace('/<p>/', '<div class="text_container"><p>', $buffer, 1);
                        $buffer = \str_replace('</figure>', '</div></figure>', $buffer);
                    }
                    else
                    {
                        $arrHeadline = StringUtil::deserialize( $contentModel->headline, true );
                        $headline = '';

                        if( $arrHeadline['value'] )
                        {
                            $headline = '<' . $arrHeadline['unit'] . ' class="headline">' . $arrHeadline['value'] . '</' . $arrHeadline['unit'] . '>';

                            $buffer = \preg_replace('/' . preg_quote($headline, '/') . '/', '', $buffer);
                        }

                        $buffer = \preg_replace('/<figure([A-Za-z0-9\s\-,;.:_\(\)\{\}="]+)>/', '<figure$1><div class="ic-inside c-inside">', $buffer);
                        $buffer = \str_replace('</figure>', '</div></figure><div class="text_container"><div class="tc-inside c-inside">' . $headline, $buffer);
                        $buffer .= '</div></div>';
                    }

                    if( str_contains($cssID[1], 'image-100') )
                    {
                        $buffer = \str_replace('image_container', 'image_container ic-w-100', $buffer);
                    }
                    elseif( str_contains($cssID[1], 'image-cover') )
                    {
                        $buffer = \str_replace('image_container', 'image_container bg-image bg-cover', $buffer);

                        $objImage = FilesModel::findByPk( $contentModel->singleSRC );

                        $buffer = \preg_replace('/<figure([A-Za-z0-9\s\-,;.:_="\(\)\{\}]+)>/', '<figure$1style="background-image:url(' . \preg_replace('/\s/', '%20', $objImage->path) . ');">', $buffer);
                    }
                }
            }

            if( str_contains($cssID[1], 'show-as-columns') )
            {
                $addTCInside = '';

                if( str_contains($cssID[1], 'text-valign') )
                {
                    $addTCInside = '<div class="ctable"><div class="ctable-cell">';
                    $buffer .= '</div></div>';
                }

                if( $contentModel->addImage && $contentModel->singleSRC )
                {
                    $buffer = \preg_replace('/<\/figure>/', '</figure><div class="text_container">' . $addTCInside, $buffer);
                    $buffer .= '</div>';

                    if( str_contains($cssID[1], 'seperator-headline-text') )
                    {
                        $buffer = \preg_replace('/<figure([A-Za-z0-9\s\-,;.:_="\(\)\{\}]+)style="([A-Za-z0-9\s\-:;]{0,})"([A-Za-z0-9\-,;.:_="\(\)\{\}]{0,})>/', '<figure$1$3><div class="ic-inside">', $buffer, 1, $countFC);

                        if( $countFC )
                        {
                            $buffer = \str_replace('</figure>', '</div></figure>', $buffer);
                        }
                        else
                        {
                            $buffer = \preg_replace('/<figure([A-Za-z0-9\s\-,;.:_="\(\)\{\}]+)>/', '<figure$1><div class="ic-inside">', $buffer);
                            $buffer = \str_replace('</figure>', '</div></figure>', $buffer);
                        }
                    }
                }
                else
                {
                    $arrHeadline = StringUtil::deserialize( $contentModel->headline, true );

                    $buffer = \preg_replace('/<\/' . $arrHeadline['unit'] . '>/', '</' . $arrHeadline['unit'] . '><div class="text_container">', $buffer, 1, $countHL);

                    if( $countHL )
                    {
                        $buffer .= '</div>';
                    }

                    $elementClasses[] = 'no-image';
                }
            }
        }

        elseif( 'gallery' === $type )
        {
            //TODO: checkbox + config!!
            if( str_contains($cssID[1], 'show-as-slider') )
            {
                $GLOBALS['TL_JAVASCRIPT']['slick_min']  = 'bundles/iidocore/scripts/library/slick/1.8.1/slick.min.js|static';
                $GLOBALS['TL_CSS']['slick']             = 'bundles/iidocore/styles/library/slick/1.8.1/slick.scss||static';
                $GLOBALS['TL_CSS']['slick-theme']       = 'bundles/iidocore/styles/library/slick/1.8.1/slick-theme.scss||static';

                $id = $cssID[0];

                if( !$id )
                {
                    $id = 'element_' . $contentModel->id;
                }

                $buffer = \preg_replace('/<ul([A-Za-z0-9\s\-="]+)class="/', '<div$1class="slider ', $buffer);
                $buffer = \preg_replace('/<li/', '<div', $buffer);

                $buffer = \preg_replace('/<\/ul>/', '</div>', $buffer);
                $buffer = \preg_replace('/<\/li>/', '</div>', $buffer);

//                $elementClasses[] = 'show-as-slider';
//                $elementClasses[] = 'slider-100';

                //centerPadding: "25%",centerMode: true,
                $script = 'document.addEventListener("DOMContentLoaded", function()
  {
    $("#' . $id . ' div.slider").slick({
      swipe: true,
      infinite: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      mobileFirst: true,
      responsive:
      [
        {
            breakpoint: 576,
            settings:
            {
                slidesToShow: 2,
            }
        },
        {
            breakpoint: 768,
            settings:
            {
                slidesToShow: 3,
            }
        },
        {
            breakpoint: 992,
            settings:
            {
                slidesToShow: 4,
            }
        }
      ]
});
});';
                $script = Template::generateInlineScript( $script );

                $buffer = \preg_replace('/<\/div>([\s]{0,})$/', $script . '</div>', $buffer);

                if( !str_contains($buffer, 'id="') )
                {
                    $buffer = \preg_replace('/<' . $tag . '([A-Za-z0-9\s\-,;.:\(\)?!_\{\}="\/]+)class="' . $elementType . '/', '<' . $tag . ' id="' . $id . '"$1class="' . $elementType, $buffer);
                }
            }
        }

        $attributes = '';

        $addAnimation = ($article->articleType === 'default' || $article->articleType === 'footer') && ($rootPage->enableAnimation || $objPage->enableAnimation || $article->enableAnimation || $contentModel->enableAnimation);

        if( $contentModel->disableAnimation )
        {
            $addAnimation = false;
        }

        if( $addAnimation && !str_contains($cssID[1], 'no-animation') && !in_array($type, $this->skipAnimation) )
        {
            //TODO:

            $animationType =  'fade-up';
            $animationOffset = $animationDelay = $animationDuration = $animationEasing = $animationAnchor = $animationAnchorPlacement = '';

            if( $rootPage->enableAnimation )
            {
                $animationType = $rootPage->animationType;
                $animationDuration = $rootPage->animationDuration;
                $animationEasing = $rootPage->animationEasing;
                $animationOffset = $rootPage->animationOffset;
                $animationDelay = $rootPage->animationDelay;
                $animationAnchor = $rootPage->animationAnchor;
                $animationAnchorPlacement = $rootPage->animationAnchorPlacement;
            }

            if( $objPage->enableAnimation )
            {
                $animationType = $objPage->animationType;
                $animationDuration = $objPage->animationDuration;
                $animationEasing = $objPage->animationEasing;
                $animationOffset = $objPage->animationOffset;
                $animationDelay = $objPage->animationDelay;
                $animationAnchor = $objPage->animationAnchor;
                $animationAnchorPlacement = $objPage->animationAnchorPlacement;
            }

            if( $article->enableAnimation )
            {
                $animationType = $article->animationType;
                $animationDuration = $article->animationDuration;
                $animationEasing = $article->animationEasing;
                $animationOffset = $article->animationOffset;
                $animationDelay = $article->animationDelay;
                $animationAnchor = $article->animationAnchor;
                $animationAnchorPlacement = $article->animationAnchorPlacement;
            }

            if( $contentModel->enableAnimation )
            {
                $animationType = $contentModel->animationType;
                $animationDuration = $contentModel->animationDuration;
                $animationEasing = $contentModel->animationEasing;
                $animationOffset = $contentModel->animationOffset;
                $animationDelay = $contentModel->animationDelay;
                $animationAnchor = $contentModel->animationAnchor;
                $animationAnchorPlacement = $contentModel->animationAnchorPlacement;
            }

            $attributes .= ' data-aos="' . $animationType . '"';

            if( $animationDuration )
            {
                $attributes .= ' data-aos-duration="' . $animationDuration . '"';
            }

            if( $animationDuration )
            {
                $attributes .= ' data-aos-duration="' . $animationDuration . '"';
            }

            if( $animationEasing && $animationEasing !== 'linear' )
            {
                $attributes .= ' data-aos-easing="' . $animationEasing . '"';
            }

            if( $animationOffset )
            {
                $attributes .= ' data-aos-offset="' . $animationOffset . '"';
            }

            if( $animationDelay )
            {
                $attributes .= ' data-aos-delay="' . $animationDelay . '"';
            }

            if( $animationAnchor )
            {
                $attributes .= ' data-aos-anchor="' . $animationAnchor . '"';
            }

            if( $animationAnchorPlacement && $animationAnchorPlacement !== 'top-bottom' )
            {
                $attributes .= ' data-aos-anchor-placement="' . $animationAnchorPlacement . '"';
            }
        }


        $insideTag = '';

        if( !in_array($type, $this->skipInsideElement) && ($cssID[1] && !$contentUtil->stringContains($cssID[1], $this->skipInsideElementClasses)) )
        {
            $insideTag = '<div class="' . $insideClass . '-inside">';
        }

        $buffer = \preg_replace('/<' . $tag . '([A-Za-z0-9\s\-,;.:\(\)?!_\{\}="\/]+)class="' . $elementType . '([A-Za-z0-9\s\-,;.:\(\)?!_\{\}]{0,})"([A-Za-z0-9\s\-,;.:\(\)?!_\{\}="\/]{0,})>/', '<' . $tag . $attributes . '$1class="' . $elementType . ($elementClasses ? ' ' . implode(' ', $elementClasses) : '') . '$2"$3>' . $insideTag, $buffer, 1, $count);

        if( $count && $insideTag )
        {
            $buffer .= '</' . $tag . '>';
        }

        if( Input::get('mode') === 'dev' )
        {
            preg_match_all('/<a([A-Za-z0-9\s\-,;.:_="\(\)\{\}\/#]+)href="([A-Za-z0-9\s\-,;.:_\(\)\{\}\/#]+)"/', $buffer, $matches);

            if( count($matches[0]) )
            {
                foreach( $matches[0] as $key => $value )
                {
                    $attributes = $matches[1][ $key ];
                    $link       = $matches[2][ $key ];
                    $addon      = '?mode=dev';

                    if( \preg_match('/.(png|jpg|JPG|jpeg|JPEG|tiff|TIFF|tif|TIF|gif|GIF|svg|webp)$/', $link) )
                    {
                        $addon = '';
                    }

                    $buffer = \preg_replace('/' . preg_quote($value, '/') . '/', '<a' . $attributes . 'href="' . $link . $addon . '"', $buffer);
                }
            }

//            $buffer = \preg_replace('/<a([A-Za-z0-9\s\-,;.:_="\(\)\{\}\/#]+)href="([A-Za-z0-9\s\-,;.:_\(\)\{\}\/#]+)"/', '<a$1href="$2?mode=dev"', $buffer);
        }

        return $buffer;
    }
}
