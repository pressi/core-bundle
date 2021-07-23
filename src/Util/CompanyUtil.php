<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Util;


use Contao\PageModel;
use IIDO\CoreBundle\Model\CompanyModel;


class CompanyUtil
{

    public static function getCurrentCompanyData(): ?CompanyModel
    {
        global $objPage;

        $objRootPage = PageModel::findByPk( $objPage->rootId );

        return CompanyModel::findOneBy(['pid=?', 'ptable=?'], [$objRootPage->id, 'tl_page']);
    }
}
