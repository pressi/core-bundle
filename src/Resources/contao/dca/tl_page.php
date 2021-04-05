<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

$strPageTable = \IIDO\CoreBundle\Config\BundleConfig::getFileTable( __FILE__ );
$objPageTable = new \IIDO\CoreBundle\Dca\ExistTable( $strPageTable );



/**
 * Palettes
 */

$objPageTable->addFieldToPalette(['root', 'rootfallback'], '{themedesigner_legend},disableThemeDesigner;', '{layout_legend}');



/**
 * Fields
 */

\IIDO\CoreBundle\Dca\Field::create('disableThemeDesigner', 'checkbox')
    ->setSelector()
    ->addToTable( $objPageTable );


\IIDO\CoreBundle\Dca\Field::create('disableThemeDesignerStyles', 'checkbox')
    ->addToSubpalette('disableThemeDesigner')
    ->addToTable( $objPageTable );



////-- LOGO
//
//\IIDO\CoreBundle\Dca\Field::create('iido_td_logo', 'image')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('iido_td_logoSticky', 'image')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );
//
//
//
////-- COMPANY
//
//\IIDO\CoreBundle\Dca\Field::create('iido_td_companyName')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('iido_td_companyStreet')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('iido_td_companyPostal')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('iido_td_companyCity')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('iido_td_companyEmail')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('iido_td_companyPhone')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('iido_td_companyWebsite')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );
//
////TODO: öffnungszeiten
//
//
//
//
////TODO: socialmedia daten




$objPageTable->updateDca();
