<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

$strTable = \IIDO\CoreBundle\Config\BundleConfig::getFileTable( __FILE__ );
$objTable = new \IIDO\CoreBundle\Dca\Table( $strTable );
$objTable->setWithoutSQL();



/**
 * Config
 */

$objTable->addTableConfig('enableVersioning', true);



/**
 * Palettes
 */




/**
 * Fields
 */

\IIDO\CoreBundle\Dca\Field::create('page')
    ->addConfig('foreignKey', 'tl_page.title')
//    ->addSQL("int(10) unsigned NOT NULL default 0")
    ->addConfig('relation', ['type'=>'hasOne', 'load'=>'lazy'])
    ->addToTable( $objTable );


// -- LOGO

\IIDO\CoreBundle\Dca\Field::create('enableLogo', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('logo', 'image')
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enableStickyLogo', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('stickyLogo', 'image')
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enableMobileLogo', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('mobileLogo', 'image')
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enableStickyMobileLogo', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('stickyMobileLogo', 'image')
    ->addToTable( $objTable );



//-- TOP

\IIDO\CoreBundle\Dca\Field::create('top_disabled', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_padding', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_padding')
    ->addEval('rgxp', 'digit')
    ->addEval('maxlength', 4)
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_fullwidth', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_border', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_border_color')
    ->addEval('maxlength', 30)
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_shadow', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_shadow')
    ->addEval('rgxp', 'prcnt')
    ->addEval('maxlength', 3)
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_phone', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_email', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_navmeta', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_login', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_socials', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_canvastrigger', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('top_enable_langswitcher', 'checkbox')
    ->addToTable( $objTable );



//-- HEADER

\IIDO\CoreBundle\Dca\Field::create('header_enable_layout', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('header_layout', 'select')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('header_disabled', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('header_enable_search', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('header_enable_langswitcher', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('header_enable_socials', 'checkbox')
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('stickyHeader_disabled', 'checkbox')
    ->addToTable( $objTable );



//-- FOOTER

\IIDO\CoreBundle\Dca\Field::create('footer_enable_columns', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('footer_columns', 'select')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('footer_disabled', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('footer_enable_padding', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('footer_padding')
    ->addEval('rgxp', 'digit')
    ->addEval('maxlength', 4)
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('footer_enable_border', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('footer_border_color')
    ->addEval('maxlength', 30)
    ->addToTable( $objTable );



//-- BOTTOM

\IIDO\CoreBundle\Dca\Field::create('bottom_disabled', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('bottom_center', 'checkbox')
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('bottom_enable_padding', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('bottom_padding')
    ->addEval('rgxp', 'digit')
    ->addEval('maxlength', 4)
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('bottom_enable_border', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('bottom_border_color')
    ->addEval('maxlength', 30)
    ->addToTable( $objTable );



//-- MENÜ

//TODO: breakpoint, use dekstop menü on tablet, dropdown??



//-- LAYOUT

\IIDO\CoreBundle\Dca\Field::create('enable_boxed_layout', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('boxed_layout', 'select')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('boxed_enable_shadow', 'checkbox')
    ->addToTable( $objTable );

//\IIDO\CoreBundle\Dca\Field::create('boxed_shadow')
//    ->addEval('rgxp', 'prcnt')
//    ->addEval('maxlength', 3)
//    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('enable_page_background_color', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('page_background_color')
    ->addEval('maxlength', 30)
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enable_page_background_repeat', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('page_background_repeat', 'select')
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enable_page_background_position', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('page_background_position', 'select')
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enable_page_background_size', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('page_background_size')
//    ->addEval('size', 2)
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('fixed_page_background_attachment', 'checkbox')
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enable_page_background_image', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('page_background_image', 'image')
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enable_page_content_width', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('page_content_width')
    ->addEval('rgxp', 'digit')
    ->addEval('maxlength', 4)
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enable_page_width', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('page_width')
    ->addEval('rgxp', 'digit')
    ->addEval('maxlength', 4)
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enable_boxed_margin_top', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('boxed_margin_top')
    ->addEval('rgxp', 'digit')
    ->addEval('maxlength', 4)
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enable_boxed_margin_top_negativ', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('boxed_margin_top_negativ')
    ->addEval('rgxp', 'digit')
    ->addEval('maxlength', 4)
    ->addToTable( $objTable );


\IIDO\CoreBundle\Dca\Field::create('enable_boxed_margin', 'checkbox')
    ->addToTable( $objTable );

\IIDO\CoreBundle\Dca\Field::create('boxed_margin')
    ->addEval('rgxp', 'digit')
    ->addEval('maxlength', 4)
    ->addToTable( $objTable );



$objTable->createDca();
