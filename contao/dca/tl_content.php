<?php

$strContentFileName = \IIDO\CoreBundle\Config\BundleConfig::getFileTable( __FILE__ );
$objContentTable    = new \IIDO\CoreBundle\Dca\ExistTable( $strContentFileName );

$config  = System::getContainer()->get('iido.core.config');

$element = $article = $page = $rootPage = false;

if( \Contao\Input::get('act') === 'edit' )
{
    $element = \Contao\ContentModel::findByPk( \Contao\Input::get('id') );

    if( $element )
    {
        $article = \Contao\ArticleModel::findByPk( $element->pid );

        if( $article )
        {
            $page = \Contao\PageModel::findByPk( $article->pid );

            if( $page )
            {
                $page = $page->loadDetails();

                $rootPage = \Contao\PageModel::findByPk( $page->rootId );
            }
        }
    }
}



/**
 * Config
 */

$GLOBALS['TL_DCA'][ $strContentFileName ]['config']['markAsCopy'] = '';
//$objContentTable->addTableConfig('markAsCop', '', true);



/**
 * Palettes
 */

//TODO: edit this in onload callback??
//if( $config->get('includeElementFields') )
//{
//    $arrFields      = \Contao\StringUtil::deserialize( $config->get('elementFields'), true);

//    $arrAnimations  = [];

    // Headline
//    if( in_array('topHeadline', $arrFields) )
//    {
//        $objContentTable->addField('topHeadline', 'headline', 'before', 'all');
//    }


    // Animation
//    if( in_array('animation', $arrFields) )
//    {
//        $includeDisabled = ($article && $article->enableAnimation) || ($page && $page->enableAnimation) || ($rootPage && $rootPage->enableAnimation);
//
//        if( $includeDisabled )
//        {
//            $arrAnimations[] = 'disableAnimation';
//        }
//
//        $arrAnimations[] = 'enableAnimation';
//
//        $objContentTable->addLegend('animation', 'expert', 'after', 'all');
//        $objContentTable->addFieldToLegend($arrAnimations, 'animation', 'prepand', 'all');
//    }
//}

//$objContentTable->replacePaletteFields('all', ',headline', ',headline,headlineFontColor,headlineFontFamily,headlineFontSize,headlineStyle');



/**
 * Fields
 */

$GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['default'] = serialize(['value'=>'', 'unit'=>'h2']);
$GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['eval']['tl_class'] = str_replace('w50', 'long', $GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['eval']['tl_class']);
$GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['eval']['allowHtml'] = true;

unset($GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['eval']['maxlength']);
//$GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['sql'] = "mediumtext NOT NULL default 'a:2:{s:5:\"value\";s:0:\"\";s:4:\"unit\";s:2:\"h2\";}'";
$GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['sql'] = "mediumtext NOT NULL";


$GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['text']['eval']['rte'] = 'customTinyMCE';


//\IIDO\CoreBundle\Dca\Field::update('headline', $objContentTable)
//    ->addEval('tl_class', 'long', true)
//    ->addEval('allowHtml', true)
//    ->updateField();

//\IIDO\CoreBundle\Dca\Field::update('text', $objContentTable)
//    ->addEval('rte', 'customTinyMCE', true)
//    ->updateField();


\IIDO\CoreBundle\Dca\Field::create('topHeadline')
    ->addEval('tl_class', 'clr')
    ->addToTable( $objContentTable );

\IIDO\CoreBundle\Dca\Field::create('subHeadline')
    ->addEval('tl_class', 'clr')
    ->addToTable( $objContentTable );


$objContentTable->addAnimationsFields( true );

\IIDO\CoreBundle\Dca\Field::create('headlineFontColor', 'select')
    ->addEval('tl_class', 'w25', true)
    ->addToTable( $objContentTable );

\IIDO\CoreBundle\Dca\Field::create('headlineFontSize', 'select')
    ->addEval('tl_class', 'w25', true)
    ->addToTable( $objContentTable );

\IIDO\CoreBundle\Dca\Field::create('headlineFontFamily', 'select')
    ->addEval('tl_class', 'w25', true)
    ->addToTable( $objContentTable );

\IIDO\CoreBundle\Dca\Field::create('headlineStyle', 'select')
    ->addEval('tl_class', 'w25', true)
    ->addToTable( $objContentTable );

\IIDO\CoreBundle\Dca\Field::create('headlineImagePosition', 'select')
    ->addEval('maxlength', '60')
    ->addToTable( $objContentTable );



$objContentTable->updateDca();

//echo "<pre>"; print_r( $GLOBALS['TL_DCA'][ $strContentFileName ] ); exit;
