<?php

$strConfTable = \IIDO\CoreBundle\Config\BundleConfig::getFileTable( __FILE__ );
$objConfTable = new \IIDO\CoreBundle\Dca\Table( $strConfTable );
$objConfTable->setDataContainer('YamlConfigFile');
$objConfTable->setWithoutSQL();

//$objTable->setTableListener('iido.basic.dca.iido_config');



/**
 * Config
 */

$objConfTable->addTableConfig('enableVersioning', true);
$objConfTable->addTableConfig('closed', true);
$objConfTable->addTableConfig('switchToEdit', false);



/**
 * Palettes
 */

$arrPalette =
[
    'default' =>
    [
        'previewMode', 'backendStyles', 'customLogin'
    ],

    'navigation' =>
    [
        'enableMobileNavigation',
    ],

    'elements' =>
    [
        'includeElementFields',
        'removeHeadlineFieldFromElements',
        'enableLayout'
    ],

    'articles' =>
    [
        'includeArticleFields'
    ],

    'pages' =>
    [
        'includePageFields'
    ],

    'backend' =>
    [
        'navLabels'
    ]
];

$objConfTable->addPalette('default', $arrPalette);



/**
 * Subpalettes
 */

$objConfTable->addSubpalette('enableMobileNavigation', 'showMobileNavOnTablet,showMobileNavBurgerDark');

$objConfTable->addSubpalette('includeElementFields', 'elementFields');
$objConfTable->addSubpalette('includeArticleFields', 'articleFields');
$objConfTable->addSubpalette('includePageFields', 'pageFields');

$objConfTable->addSubpalette('customLogin', 'loginImageSRC,loginLogoSRC,loginShowPublisherLink,loginShowImageCopyright');
$objConfTable->addSubpalette('loginShowPublisherLink', 'loginPublisher');



/**
 * Fields
 */

// DEFAULT

\IIDO\CoreBundle\Dca\Field::create('previewMode', 'checkbox')
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('backendStyles', 'checkbox')
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('customLogin', 'checkbox')
    ->setAsSelector()
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('loginImageSRC', 'images')
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('loginLogoSRC', 'image')
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('loginShowPublisherLink', 'checkbox')
    ->setAsSelector()
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('loginPublisher', 'select')
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('loginShowImageCopyright', 'checkbox')
    //    ->setAsSelector()
    ->addToTable( $objConfTable );



// NAVIGATION

\IIDO\CoreBundle\Dca\Field::create('enableMobileNavigation', 'checkbox')
    ->setAsSelector()
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('showMobileNavOnTablet', 'checkbox')
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('showMobileNavBurgerDark', 'checkbox')
    ->addToTable( $objConfTable );



// ELEMENTS

\IIDO\CoreBundle\Dca\Field::create('includeElementFields', 'checkbox')
    ->setAsSelector()
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('elementFields', 'checkbox')
    ->addEval('multiple', true)
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('removeHeadlineFieldFromElements', 'checkbox')
    ->addEval('tl_class', 'clr')
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('enableLayout', 'checkbox')
    //    ->addEval('tl_class', 'clr')
    ->addToTable( $objConfTable );



// ARTICLES

\IIDO\CoreBundle\Dca\Field::create('includeArticleFields', 'checkbox')
    ->setAsSelector()
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('articleFields', 'checkbox')
    ->addEval('multiple', true)
    ->addToTable( $objConfTable );



// PAGES

\IIDO\CoreBundle\Dca\Field::create('includePageFields', 'checkbox')
    ->setAsSelector()
    ->addToTable( $objConfTable );

\IIDO\CoreBundle\Dca\Field::create('pageFields', 'checkbox')
    ->addEval('multiple', true)
    ->addToTable( $objConfTable );



// BACKEND

\IIDO\CoreBundle\Dca\Field::create('navLabels', 'multiColumnEditor')
    ->addEval('multiColumnEditor',
          [
              'sortable'      => false,
              'class'         => 'nav-labels',
              'minRowCount'   => 0,
              'maxRowCount'   => 0,
              'skipCopyValuesOnAdd' => false,
              'palettes' =>
              [
                  'default' => 'value,label'
              ],
              'fields' =>
              [
                  'value' =>
                  [
                      'label'         => ['Wert'],
                      'inputType'     => 'text',
                      'eval'          => ['readonly'=>true,'groupStyle' => 'width:150px']
                  ],

                  'label' =>
                  [
                      'label'         => ['Bezeichnung'],
                      'inputType'     => 'text',
                      'eval'          => ['groupStyle' => 'width:250px']
                  ]
              ]
          ])
    ->addToTable( $objConfTable );



$objConfTable->createDca();
