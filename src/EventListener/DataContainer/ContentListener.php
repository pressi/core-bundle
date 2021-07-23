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
use Contao\ContentModel;
use Contao\DataContainer;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\RequestStack;


class ContentListener
{
    protected RequestStack $requestStack;



    public function __construct( RequestStack $requestStack )
    {
        $this->requestStack = $requestStack;
    }



    /**
     * @Callback(table="tl_content", target="config.onload")
     */
    public function onLoad( ?DataContainer $dc = null ): void
    {
        if( null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act') )
        {
            return;
        }

        $element = ContentModel::findByPk( $dc->id );

        if( null === $element )
        {
            return;
        }

        $article = ArticleModel::findByPk( $element->pid );
        $page = PageModel::findByPk( $article->pid )->loadDetails();
        $parentRoot = PageModel::findByPk( $page->rootId );

        if( $parentRoot->enableAnimation )
        {
            $GLOBALS['TL_DCA']['tl_content']['fields']['enableAnimation']['label'] = ['Animation überschreiben', ''];
        }
    }
}
