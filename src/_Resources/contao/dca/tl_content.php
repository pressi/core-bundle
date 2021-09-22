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

if( $config->get('includeElementFields') )
{
    $arrFields      = \Contao\StringUtil::deserialize( $config->get('elementFields'), true);

    $arrAnimations  = [];


    // Animation
    if( in_array('animation', $arrFields) )
    {
        $includeDisabled = ($article && $article->enableAnimation) || ($page && $page->enableAnimation) || ($rootPage && $rootPage->enableAnimation);

        if( $includeDisabled )
        {
            $arrAnimations[] = 'disableAnimation';
        }

        $arrAnimations[] = 'enableAnimation';

        $objContentTable->addLegend('animation', 'expert', 'after', 'all');
        $objContentTable->addFieldToLegend($arrAnimations, 'animation', 'prepand', 'all');
    }
}



/**
 * Fields
 */

$GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['eval']['tl_class'] = str_replace('w50', 'long', $GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['eval']['tl_class']);
$GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['headline']['eval']['allowHtml'] = true;


$GLOBALS['TL_DCA'][ $strContentFileName ]['fields']['text']['eval']['rte'] = 'customTinyMCE';


//\IIDO\CoreBundle\Dca\Field::update('headline', $objContentTable)
//    ->addEval('tl_class', 'long', true)
//    ->addEval('allowHtml', true)
//    ->updateField();


$objContentTable->addAnimationsFields( true );


$objContentTable->updateDca();

//echo "<pre>"; print_r( $GLOBALS['TL_DCA'][ $strContentFileName ] ); exit;
