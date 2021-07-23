<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener\DataContainer;


use Contao\ArticleModel;
use Contao\DataContainer;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Image;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use IIDO\CoreBundle\Util\ColorUtil;
use Symfony\Component\HttpFoundation\RequestStack;


//use Contao\PageModel;
//use IIDO\BasicBundle\Helper\PageHelper;
//use IIDO\BasicBundle\Helper\StyleSheetHelper;


class ArticleListener
{
    protected ColorUtil $colorUtil;


    protected RequestStack $requestStack;



    public function __construct( ColorUtil $colorUtil, RequestStack $requestStack )
    {
        $this->colorUtil = $colorUtil;
        $this->requestStack = $requestStack;
    }



    /**
     * @Callback(table="tl_article", target="fields.bgColor.options")
     */
    public function loadLabel( ?DataContainer $dc ): array
    {
        $options =
        [
            'black'   => 'Schwarz (#333)',
            'white'   => 'Weiß (#fff)'
        ];

        $colors = $this->colorUtil->getWebsiteColors( $dc );

        if( count($colors) )
        {
            foreach($colors as $id => $label)
            {
                $options[ $id ] = $label;
            }
        }

        return $options;
    }



    /**
     * @Callback(table="tl_article", target="config.onload")
     */
    public function onLoad( ?DataContainer $dc = null ): void
    {
        if( null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act') )
        {
            return;
        }

        $element = ArticleModel::findByPk( $dc->id );

        if( null === $element )
        {
            return;
        }

        $parent     = PageModel::findByPk( $element->pid )->loadDetails();
        $parentRoot = PageModel::findByPk( $parent->rootId );

        if( ($parent->enableAnimation || $parentRoot->enableAnimation) && !$parent->disableAnimation )
        {
            $GLOBALS['TL_DCA']['tl_article']['fields']['enableAnimation']['label'] = ['Animation überschreiben', ''];
        }
    }



//    /**
//     * Callback(table="tl_article", target="config.onsubmit")
//     */
//    public function onSubmit( DataContainer $dc ): void
//    {
//        $activeRecord = $dc->activeRecord;
//
//        if( $activeRecord->articleType === 'header' )
//        {
//            $objParentPage = PageModel::findByPk( $activeRecord->pid );
//
//            $value = '01';
//
//            if( $activeRecord->layout === 'left' )
//            {
//                $value = '02';
//            }
//            elseif( $activeRecord->layout === 'right' )
//            {
//                $value = '03';
//            }
//
//            StyleSheetHelper::addConfigVar('headerLayout', 'layout' . $value, $objParentPage->rootAlias);
//        }
//    }



    /**
     * @Callback(table="tl_article", target="list.label.label")
     * @return string|array
     */
    public function onLoadArticleLabel( array $row, string $label, DataContainer $dc, string $imageAttribute = '', bool $returnImage = false, ?bool $isProtected = null )
    {
        if( $label )
        {
            $parentPage = PageModel::findByPk( $row['pid'] );
            $image = 'articles';

            if( $parentPage && $parentPage->type === 'global_element' )
            {
                if( 'default' !== $row['articleType'] )
                {
                    $type = $GLOBALS['TL_LANG']['tl_article']['options']['articleType'][ $row['articleType'] ];

                    $image  = 'bundles/iidocore/images/icons/articles/' . $row['articleType'];

                    $label = \preg_replace('/\[([A-Za-z0-9öäüÖÄÜß\s\-]+)\]/', '[' . $type . ']', $label);
                }
            }

            $link = 'contao/preview.php?page=' . $row['pid'] . '&amp;article=' . ($row['alias'] ?: $row['id']);
            $unpublished = ($row['start'] && $row['start'] > time()) || ($row['stop'] && $row['stop'] <= time());

            if( $unpublished || !$row['published'] )
            {
                $image .= '_';
            }

            $label = '<a href="' . $link . '" title="' . StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['view']) . '" target="_blank">' . Image::getHtml($image . '.svg', '', 'data-icon="' . ($unpublished ? $image : rtrim($image, '_')) . '.svg" data-icon-disabled="' . rtrim($image, '_') . '_.svg"') . '</a> ' . $label;
        }

        return $label;
    }
}
//<div>Icons made by <a href="https://www.flaticon.com/authors/pixel-perfect" title="Pixel perfect">Pixel perfect</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
