<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 *
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Controller\Page;


use Contao\CoreBundle\ServiceAnnotation\Page;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Page("global_element")
 */
class GlobalElementPageController
{

    public function __invoke( Request $request, PageModel $pageModel ): Response
	{
        $objRootPage = PageModel::findByPk( $pageModel->rootId );
		return new RedirectResponse($objRootPage->getFrontendUrl(), 301);
	}
}
