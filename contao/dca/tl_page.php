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

$config  = System::getContainer()->get('iido.core.config');

$page = $rootPage = false;

if( \Contao\Input::get('act') === 'edit' )
{
    $page = \Contao\PageModel::findByPk( \Contao\Input::get('id') );

    if( $page )
    {
        $page = $page->loadDetails();

        $rootPage = \Contao\PageModel::findByPk( $page->rootId );
    }
}

//TODO: move sizes, fonts and colors in an extra table => page_config_table ???



/**
 * Palettes
 */

$objPageTable->copyPalette('default', 'global_element');

$objPageTable->addFieldToPalette(['root', 'rootfallback'], '{company_legend},company;', '{layout_legend}');
$objPageTable->addFieldToPalette(['root', 'rootfallback'], '{design_legend},colors,fonts,sizes;', '{layout_legend}');
//$objPageTable->addFieldToPalette(['root', 'rootfallback'], '{themedesigner_legend},disableThemeDesigner;', '{layout_legend}');

$objPageTable->replacePaletteFields('regular', ',includeLayout', ',hideHeader,hideFooter,includeLayout');

if( $config->get('includePageFields') )
{
    $arrFields  = StringUtil::deserialize($config->get('pageFields'), true);

    $arrAnimations = [];

    if( in_array('fullpage', $arrFields) )
    {
        $objPageTable->replacePaletteFields('regular', ',includeLayout', ',includeLayout,enableFullPage');
    }

    if( in_array('animation', $arrFields) )
    {
        $includeDisabled = $rootPage && $rootPage->enableAnimation;

        if( $includeDisabled )
        {
            $arrAnimations[] = 'disableAnimation';
        }

        $arrAnimations[] = 'enableAnimation';

        $objPageTable->addLegend('animation', 'publish', 'before', 'all' );
        $objPageTable->addFieldToLegend($arrAnimations, 'animation', 'append', 'all');
    }

    if( in_array('navigation', $arrFields) )
    {
        $arrNavigation[] = 'enableOffsetNav';

        $objPageTable->replacePaletteFields(['root', 'rootfallback'], ',includeLayout', ',includeLayout,' . implode(',', $arrNavigation));
    }

    $objPageTable->addLegend('experts', 'publish', 'before', ['root', 'rootfallback']);
    $objPageTable->addFieldToLegend(['enablePreviewMode'], 'experts', 'append', ['root', 'rootfallback']);
}



/**
 * Fields
 */

\IIDO\CoreBundle\Dca\Field::update('includeLayout', $objPageTable)
    ->addEval('tl_class', 'w50 m12')
    ->updateField();

\IIDO\CoreBundle\Dca\Field::create('hideHeader', 'checkbox')
    ->addToTable( $objPageTable );

\IIDO\CoreBundle\Dca\Field::create('hideFooter', 'checkbox')
    ->addToTable( $objPageTable );


//\IIDO\CoreBundle\Dca\Field::create('disableThemeDesigner', 'checkbox')
//    ->setAsSelector()
//    ->addToTable( $objPageTable );


//\IIDO\CoreBundle\Dca\Field::create('disableThemeDesignerStyles', 'checkbox')
//    ->addToSubpalette('disableThemeDesigner')
//    ->addToTable( $objPageTable );



\IIDO\CoreBundle\Dca\Field::create('enableFullPage', 'checkbox')
    ->addEval('tl_class', 'clr')
    ->addToTable( $objPageTable );



\IIDO\CoreBundle\Dca\Field::create('colors', 'group')
    ->addConfig('storage', 'entity')
    ->addConfig('entity', \IIDO\CoreBundle\Entity\PageColorMapEntity::class)
    ->addConfig('palette', ['color', 'label', 'className'])
    ->addConfig('fields', [
        'color' =>
        [
            'label'     => ['Farbe'],
            'inputType' => 'text',
            'eval'      => ['maxlength'=>6, 'colorpicker'=>true, 'isHexColor'=>true, 'decodeEntities'=>true, 'tl_class'=>'w33 wizard']
        ],

        'label' =>
        [
            'label'     => ['Bezeichnung'],
            'inputType' => 'text',
            'eval'      => ['tl_class'=>'w33']
        ],

        'className' =>
        [
            'label'     => ['Element-Klasse'],
            'inputType' => 'text',
            'eval'      => ['tl_class'=>'w33']
        ]
    ])
    ->addEval('tl_class', 'long', true)
//    ->addSQL( ['type' => 'blob', 'length' => \Doctrine\DBAL\Platforms\MySqlPlatform::LENGTH_LIMIT_BLOB, 'notnull' => false] )
    ->addToTable( $objPageTable );


\IIDO\CoreBundle\Dca\Field::create('fonts', 'group')
    ->addConfig('storage', 'entity')
    ->addConfig('entity', \IIDO\CoreBundle\Entity\PageFontMapEntity::class)
    ->addConfig('palette', ['label', 'name', 'isDefault'])
    ->addConfig('fields', [
        'label' =>
        [
            'label'     => ['Bezeichnung (Backend)'],
            'inputType' => 'text',
            'eval'      => ['tl_class'=>'w50']
        ],

        'name' =>
        [
            'label'     => ['Name (in Datei)'],
            'inputType' => 'text',
            'eval'      => ['tl_class'=>'w50']
        ],

        'isDefault' =>
        [
            'label'     => ['Standard'],
            'inputType' => 'checkbox',
            'eval'      => ['tl_class'=>'w50 m12']
        ]
    ])
    ->addEval('tl_class', 'long', true)
    ->addToTable( $objPageTable );


\IIDO\CoreBundle\Dca\Field::create('sizes', 'group')
    ->addConfig('storage', 'entity')
    ->addConfig('entity', \IIDO\CoreBundle\Entity\PageSizeMapEntity::class)
    ->addConfig('palette', ['size', 'inHeadline', 'inText', 'isDefault'])
    ->addConfig('fields', [
        'size' =>
        [
            'label'     => ['Größe (in px)'],
            'inputType' => 'text',
            'eval'      => ['rgxp'=>'digit', 'tl_class'=>'w50']
        ],

        'inHeadline' =>
        [
            'label'     => ['Überschrift'],
            'inputType' => 'checkbox',
            'eval'      => ['tl_class'=>'w25 m12']
        ],

        'inText' =>
        [
            'label'     => ['Text'],
            'inputType' => 'checkbox',
            'eval'      => ['tl_class'=>'w25 m12']
        ],

        'isDefault' =>
        [
            'label'     => ['Standard'],
            'inputType' => 'checkbox',
            'eval'      => ['tl_class'=>'o50 w50 m12']
        ]
    ])
    ->addEval('tl_class', 'long', true)
    ->addToTable( $objPageTable );


$objPageTable->addAnimationsFields( true );

\IIDO\CoreBundle\Dca\Field::create('enableOffsetNav', 'checkbox')
    ->addToTable( $objPageTable );



\IIDO\CoreBundle\Dca\Field::create('company', 'fieldpalette')
    ->addConfig('foreignKey', 'tl_iido_company.id')
    ->addConfig('relation', ['type' => 'hasMany', 'load' => 'eager'])
    ->addConfig('fieldpalette', [
        'config' =>
        [
            'table'     => 'tl_iido_company',
        ],

        'list' =>
        [
            'label'     => ['fields' => ['company'], 'format' => '%s'],
        ],

        'palettes' =>
        [
            'default'     => '{name_legend},company;{contact_legend},email,phone,fax;{{address_legend},street,postal,city,country;',
        ]
    ])
    ->addEval('tl_class', 'long', true)
    ->addSQL( ['type' => 'blob', 'length' => \Doctrine\DBAL\Platforms\MySqlPlatform::LENGTH_LIMIT_BLOB, 'notnull' => false] )
    ->addToTable( $objPageTable );

//\IIDO\CoreBundle\Dca\Field::create('enableAnimation', 'checkbox')
//    ->setAsSelector()
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animationType', 'select')
//    ->addToSubpalette('enableAnimation')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animationDuration')
//    ->addRegex('natural')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animationEasing', 'select')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animationOffset')
//    ->addRegex('natural')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animationDelay')
//    ->addRegex('natural')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animationAnchor')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animationAnchorPlacement', 'select')
//    ->addToTable( $objPageTable );
//
//\IIDO\CoreBundle\Dca\Field::create('disableAnimation', 'checkbox')
//    ->addToTable( $objPageTable );



//TODO: to Theme Designer?? or other table "tl_page_config" ??
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


// EXPERTS

\IIDO\CoreBundle\Dca\Field::create('enablePreviewMode', 'checkbox')
    ->addToTable( $objPageTable );



$objPageTable->updateDca();

//echo"<pre>"; print_r( $GLOBALS['TL_DCA'][ $strPageTable ] ); exit;
