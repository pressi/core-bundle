<?php

//use Contao\CoreBundle\DataContainer\PaletteManipulator;


$strArticleFileName = \IIDO\CoreBundle\Config\BundleConfig::getFileTable( __FILE__ );
$objArticleTable    = new \IIDO\CoreBundle\Dca\ExistTable( $strArticleFileName );

//$objArticleTable->setTableListener( 'iido.core.dca.article' );

$config  = System::getContainer()->get('iido.core.config');
$objArticle = $objArticlePage = $rootPage = false;

if( Input::get('act') === 'edit' )
{
    $objArticle = \Contao\ArticleModel::findByPk( Input::get('id') );
}

if( $objArticle )
{
    $objArticlePage = \Contao\PageModel::findByPk( $objArticle->pid );

    if( $objArticlePage )
    {
        $objArticlePage = $objArticlePage->loadDetails();

        $rootPage = \Contao\PageModel::findByPk( $objArticlePage->rootId );
    }
}



/**
 * Palettes
 */

$arrBGColor = [];
//$paletteManipulator = PaletteManipulator::create();

if( $config->get('includeArticleFields') )
//if( \IIDO\CoreBundle\Config\IIDOConfig::get('includeArticleFields') )
{
    $arrFields      = \Contao\StringUtil::deserialize( $config->get('articleFields'), true);
//    $arrFields      = StringUtil::deserialize( \IIDO\CoreBundle\Config\IIDOConfig::get('articleFields'), true);
    $arrDesign      = [];
    $arrDimensions  = [];
    $arrAnimations  = [];


    // Design

    if( in_array('bg_color', $arrFields) )
    {
        $arrDesign[] = 'bgColor';
        $arrDesign[] = 'addOwnBGColor';
    }

//    if( in_array('bg_gradient', $arrFields) )
//    {
//        $arrDesign[] = 'gradientColors';
//        $arrDesign[] = 'gradientAngle';
//    }

    if( in_array('bg_image', $arrFields) )
    {
        $arrDesign[] = 'bgImage';
        $arrDesign[] = 'extBGImage';

//        $arrDesign[] = 'bgPosition';
//        $arrDesign[] = 'bgRepeat';
//        $arrDesign[] = 'bgAttachment';
//        $arrDesign[] = 'bgSize';
    }

    if( count($arrDesign) )
    {
//        $paletteManipulator->addLegend('design_legend', 'layout_legend', PaletteManipulator::POSITION_AFTER);
//        $paletteManipulator->addField($arrDesign, 'design_legend', PaletteManipulator::POSITION_PREPEND);

        $objArticleTable->addLegend('design', 'layout');
        $objArticleTable->addFieldToLegend($arrDesign, 'design');
    }


    // Dimensions

    if( in_array('width', $arrFields) )
    {
        $arrDimensions[] = 'width';
    }

    if( in_array('height', $arrFields) )
    {
        $arrDimensions[] = 'height';
    }

    if( in_array('padding', $arrFields) )
    {
//        $arrDimensions[] = 'padding';
        $arrDimensions[] = 'paddingTop';
        $arrDimensions[] = 'paddingBottom';
    }

    if( in_array('margin', $arrFields) )
    {
//        $arrDimensions[] = 'margin';
        $arrDimensions[] = 'marginTop';
        $arrDimensions[] = 'marginBottom';
    }

    if( count($arrDimensions) )
    {
        $objArticleTable->addLegend('dimensions', 'layout');
        $objArticleTable->addFieldToLegend($arrDimensions, 'dimensions');

//        echo "<pre>"; print_r( $GLOBALS['TL_DCA'][ $strArticleFileName ] ); exit;

//        $paletteManipulator->addLegend('dimensions_legend', 'layout_legend', PaletteManipulator::POSITION_AFTER);
//        $paletteManipulator->addField($arrDimensions, 'dimensions_legend', PaletteManipulator::POSITION_PREPEND);
    }


    // Animation

    if( in_array('animation', $arrFields) )
    {
        $includeDisabled = ($objArticlePage && $objArticlePage->enableAnimation) || ($rootPage && $rootPage->enableAnimation);

        if( $includeDisabled )
        {
            $arrAnimations[] = 'disableAnimation';
        }

        $arrAnimations[] = 'enableAnimation';
//        $paletteManipulator->addLegend('animation_legend', 'expert_legend', PaletteManipulator::POSITION_AFTER, true);
//        $paletteManipulator->addField('addAnimation', 'animation_legend', PaletteManipulator::POSITION_PREPEND);

        $objArticleTable->addLegend('animation', 'expert');
        $objArticleTable->addFieldToLegend($arrAnimations, 'animation', 'prepand');
    }


    // Title
    if( in_array('frontendTitle', $arrFields) )
    {
//        $paletteManipulator->addField('frontendTitle', 'author', PaletteManipulator::POSITION_AFTER);
        $objArticleTable->addField('frontendTitle', 'author');
    }

    if( in_array('navTitle', $arrFields) )
    {
        $parentField = 'author';

        if( in_array('frontendTitle', $arrFields) )
        {
            $parentField = 'frontendTitle';
        }

//        $paletteManipulator->addField('navTitle', $parentField, PaletteManipulator::POSITION_AFTER);
        $objArticleTable->addField('navTitle', $parentField);
    }


    // Type

//    if( in_array('type', $arrFields) )
//    {
//        if( $objArticle )
//        {
//            $objArticlePage = \Contao\PageModel::findByPk( $objArticle->pid );

            if( $objArticlePage )
            {
                if( $objArticlePage->type === 'global_element' )
                {
//                    $paletteManipulator->addField('articleType', 'title', PaletteManipulator::POSITION_AFTER);
                    $objArticleTable->addField('articleType', 'title');
                }
            }
//        }
//    }

//    $paletteManipulator->addField('hideInNav', 'cssID', PaletteManipulator::POSITION_AFTER);
    $objArticleTable->addField('hideInNav', 'cssID');

//    $paletteManipulator->applyToPalette('default', $strArticleFileName);

    $arrBGColor = ['ownBGColor'];

    if( in_array('bg_gradient', $arrFields) )
    {
        $arrBGColor[] = 'gradientColors';
        $arrBGColor[] = 'gradientAngle';
    }

    $arrArticleTypeHeader =
    [
        'title'     => ['title', 'articleType', 'alias'],
        'layout'    => ['inColumn', 'layout'],
    ];

    if( count($arrDesign) )
    {
        $arrArticleTypeHeader['design'] = $arrDesign;
    }

    $arrArticleTypeHeader['template'] = ['customTpl'];
    $arrArticleTypeHeader['expert'] = ['cssStyleSelector', 'cssID'];

//    if( in_array('animation', $arrFields) )
//    {
//        $arrArticleTypeHeader['animation'] = ['addAnimation'];
//    }

    $arrArticleTypeHeader['publish'] = ['published'];

    $objArticleTable->addPalette('header', $arrArticleTypeHeader);
}



/**
 * Subpalettes
 */

//Contao\CoreBundle\DataContainer\PaletteManipulator::create()
//    ->applyToSubpalette();

//$objArticleTable->addSubpalette("addAnimation", "animationType,animateRun,animationWait,animationOffset");
$objArticleTable->addSubpalette("extBGImage", "bgPosition,bgRepeat,bgAttachment,bgSize");
$objArticleTable->addSubpalette("addOwnBGColor", implode(',', $arrBGColor));



/**
 * Fields
 */

\IIDO\CoreBundle\Dca\Field::create('hideInNav', 'checkbox')
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('frontendTitle')
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('navTitle')
    ->addToTable( $objArticleTable );



// DESIGN

\IIDO\CoreBundle\Dca\Field::create('bgColor', 'select')
    ->addEval('includeBlankOption', true)
    ->addEval('tl_class', 'w33', true)
    ->addToTable( $objArticleTable );


\IIDO\CoreBundle\Dca\Field::create('ownBGColor', 'color')
    ->addEval('tl_class', 'long', true)
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('addOwnBGColor', 'checkbox')
    ->setAsSelector()
    ->addEval('tl_class', 'w33 m12 subpal-no-clr', true)
    ->addToTable( $objArticleTable );


\IIDO\CoreBundle\Dca\Field::create('gradientAngle')
    ->addEval('maxlength', 32)
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('gradientColors')
    ->addEval('maxlength', 128)
    ->addEval('multiple', true)
    ->addEval('size', 4)
    ->addEval('decodeEntities', true)
    ->addEval('tl_class', 'clr')
    ->addToTable( $objArticleTable );




\IIDO\CoreBundle\Dca\Field::create('bgImage', 'image')
    ->addEval('tl_class', 'clr hauto w50')
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('extBGImage', 'checkbox')
    ->setAsSelector()
    ->addEval('tl_class', 'w50 m12', true)
    ->addToTable( $objArticleTable );


\IIDO\CoreBundle\Dca\Field::create('bgMode', 'select')
    ->addEval('includeBlankOption', true)
    ->addEval('tl_class', 'w25', true)
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('bgPosition', 'select')
    ->addEval('includeBlankOption', true)
    ->addEval('tl_class', 'clr')
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('bgRepeat', 'select')
    ->addEval('includeBlankOption', true)
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('bgAttachment', 'select')
    ->addEval('includeBlankOption', true)
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('bgSize', 'imageSize')
    ->addEval('includeBlankOption', true)
    ->addEval('tl_class', 'bg-size')
    ->addToTable( $objArticleTable );



// DIMENSIONS

//\IIDO\CoreBundle\Dca\Field::create('fullWidth', 'checkbox')->addToTable( $objArticleTable );
//\IIDO\CoreBundle\Dca\Field::create('fullHeight', 'checkbox')->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('width', 'select')
    ->addEval('includeBlankOption', true)
    ->addEval('blankOptionLabel', 'Standard')
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('height', 'select')
    ->addEval('includeBlankOption', true)
    ->addToTable( $objArticleTable );

//\IIDO\CoreBundle\Dca\Field::create('padding', 'select')
//    ->addOptionsName('distances')
//    ->addEval('includeBlankOption', true)
//    ->addEval('blankOptionLabel', 'Standard')
//    ->addEval('tl_class', 'clr')
//    ->addToTable( $objArticleTable );

//\IIDO\CoreBundle\Dca\Field::create('margin', 'select')
//    ->addOptionsName('distances')
//    ->addEval('includeBlankOption', true)
//    ->addEval('blankOptionLabel', 'Standard')
//    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('paddingTop', 'select')
    ->addOptionsName('distances')
    ->addEval('includeBlankOption', true)
    ->addEval('blankOptionLabel', 'Standard')
    ->addEval('tl_class', 'w25 clr')
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('paddingBottom', 'select')
    ->addOptionsName('distances')
    ->addEval('includeBlankOption', true)
    ->addEval('blankOptionLabel', 'Standard')
    ->addEval('tl_class', 'w25')
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('marginTop', 'select')
    ->addOptionsName('distances')
    ->addEval('includeBlankOption', true)
    ->addEval('blankOptionLabel', 'Standard')
    ->addEval('tl_class', 'w25')
    ->addToTable( $objArticleTable );

\IIDO\CoreBundle\Dca\Field::create('marginBottom', 'select')
    ->addOptionsName('distances')
    ->addEval('includeBlankOption', true)
    ->addEval('blankOptionLabel', 'Standard')
    ->addEval('tl_class', 'w25')
    ->addToTable( $objArticleTable );



// ANIMATION

//\IIDO\CoreBundle\Dca\Field::create('addAnimation', 'checkbox')
//    ->setAsSelector()
//    ->setLangTable( 'content' )
//    ->addToTable( $objArticleTable );


$objArticleTable->addAnimationsFields( true );

//\IIDO\CoreBundle\Dca\Field::create('animationType', 'select')
//    ->addEval('includeBlankOption', true)
//    ->setLangTable( 'content' )
//    ->addToTable( $objArticleTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animationOffset')
//    ->addEval('maxlength', 80)
//    ->setLangTable( 'content' )
//    ->addToTable( $objArticleTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animationWait', 'checkbox')
//    ->setLangTable( 'content' )
//    ->addToTable( $objArticleTable );
//
//\IIDO\CoreBundle\Dca\Field::create('animateRun', 'select')
//    ->setLangTable( 'content' )
//    ->addToTable( $objArticleTable );



// TYPE

\IIDO\CoreBundle\Dca\Field::create('articleType', 'select')
    ->setSelector()
    ->addDefault('default')
    ->addEval('tl_class', 'w50', true)
    ->addToTable( $objArticleTable );



// LAYOUT // TODO: ???

//\IIDO\CoreBundle\Dca\Field::create('layout', 'layoutWizard')
//    ->addDefault('top')
//    ->addEval('helpwizard', true)
//    ->addEval('imagePath', 'bundles/iidobasic/images/layout/header/')
//    ->addEval('tl_class', 'clr')
//    ->addSQL("varchar(8) NOT NULL default 'top'")
//    ->addToTable( $objArticleTable );

//if( $objArticle->articleType === 'footer' )
//{
//
//}



// GLOBAL ELEMENTS

//\IIDO\CoreBundle\Dca\Field::create('articleType', 'select')
//    ->addDefault('default')
//    ->addToTable( $objArticleTable );

$objArticleTable->updateDca();

//echo "<pre>"; print_r( $GLOBALS['TL_DCA'][ $strArticleFileName ] ); exit;
