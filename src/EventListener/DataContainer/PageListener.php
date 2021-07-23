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
use Contao\PageModel;
use Contao\System;
use IIDO\CoreBundle\Util\ColorUtil;
use Symfony\Component\HttpFoundation\RequestStack;


//use Contao\PageModel;
//use IIDO\BasicBundle\Helper\PageHelper;
//use IIDO\BasicBundle\Helper\StyleSheetHelper;


class PageListener
{
    protected RequestStack $requestStack;



    public function __construct( RequestStack $requestStack )
    {
        $this->requestStack = $requestStack;
    }



    /**
     * @Callback(table="tl_page", target="config.onload")
     */
    public function onLoad( ?DataContainer $dc = null ): void
    {
        if( null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act') )
        {
            return;
        }

        $element = PageModel::findByPk( $dc->id );

        if( null === $element )
        {
            return;
        }

        $element = $element->loadDetails();
        $parentRoot = PageModel::findByPk( $element->rootId );

        if( $parentRoot->enableAnimation )
        {
            $GLOBALS['TL_DCA']['tl_page']['fields']['enableAnimation']['label'] = ['Animation überschreiben', ''];
        }
    }
}
