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
use Contao\ContentModel;
use Contao\DataContainer;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\PageModel;
use Contao\StringUtil;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Config\IIDOConfig;
use IIDO\CoreBundle\Config\PageConfig;
use IIDO\UtilsBundle\Helper\DcaHelper;
use Symfony\Component\HttpFoundation\RequestStack;


class ContentListener
{
    protected string $tableName = 'tl_content';

    protected RequestStack $requestStack;
    protected IIDOConfig $config;

    protected array $standardHeadlineFields =
    [
        'headlineFontColor',
        'headlineFontFamily',
        'headlineFontSize',
        'headlineStyle'
    ];



    public function __construct( RequestStack $requestStack, IIDOConfig $config )
    {
        $this->requestStack = $requestStack;
        $this->config = $config;
    }



    /**
     * @Callback(table="tl_content", target="config.onload", priority=-50)
     */
    public function onLoad( ?DataContainer $dc = null ): void
    {
        if( null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act') )
        {
            return;
        }

        $element = ContentModel::findByPk( $dc->id );

        if( null === $element )
        {
            return;
        }

        $article    = ArticleModel::findByPk( $element->pid );
        $page       = PageModel::findByPk( $article->pid )->loadDetails();
        $parentRoot = PageModel::findByPk( $page->rootId );

        if( $parentRoot->enableAnimation || $article->enableAnimation )
        {
            $GLOBALS['TL_DCA'][ $this->tableName ]['fields']['enableAnimation']['label'] = ['Animation überschreiben', ''];
        }

        if( $element->floating && 'left' != $element->floating && 'right' != $element->floating )
        {
            unset( $GLOBALS['TL_LANG'][ $this->tableName ]['options']['headlineImagePosition']['nextTo'] );
        }

        if( $this->config->get('includeElementFields') )
        {
            $fields = StringUtil::deserialize( $this->config->get('elementFields'), true);


            // ANIMATIONS
            if( in_array('animation', $fields) )
            {
                $arrAnimations = [];
                $includeDisabled = ($article && $article->enableAnimation) || ($page && $page->enableAnimation) || ($parentRoot && $parentRoot->enableAnimation);

                if( $includeDisabled )
                {
                    $arrAnimations[] = 'disableAnimation';
                }

                $arrAnimations[] = 'enableAnimation';

                DcaHelper::addLegend('animation', 'expert', 'after', 'all', $this->tableName);
                DcaHelper::addFieldToLegend($arrAnimations, 'animation', 'prepand', 'all', $this->tableName);
            }


            // TOP HEADLINE
            if( in_array('topHeadline', $fields) )
            {
                DcaHelper::addField('topHeadline', 'headline', 'before', 'all', $this->tableName);
            }


            // SUB HEADLINE
            if( in_array('subHeadline', $fields) )
            {
                DcaHelper::addField('subHeadline', 'headline', 'after', 'all', $this->tableName);
            }


            // IMAGE HEADLINE POSITION // TODO: in settings!!
            $GLOBALS['TL_DCA'][ $this->tableName ]['fields']['floating']['eval']['submitOnChange'] = true;
            DcaHelper::addFieldToSubpalette('headlineImagePosition', 'floating', 'after', 'addImage', $this->tableName);


            // HEADLINE FIELDS
            DcaHelper::replacePaletteFields(',headline(,|;)', ',headline,headlineFontColor,headlineFontFamily,headlineFontSize,headlineStyle$1', 'all', $this->tableName);
        }

//        if( str_starts_with($element->type, 'rsce_') )
//        {
//            //TODO: check if fields headline enabled
//
//            $palette = $GLOBALS['TL_DCA'][ $this->tableName ]['palettes'][ $element->type ];
//            $palette = \str_replace(',headline', ',headline,' . implode(',', $this->standardHeadlineFields), $palette);
//
//            $GLOBALS['TL_DCA'][ $this->tableName ]['palettes'][ $element->type ] = $palette;
//        }

        if( BundleConfig::isActiveBundle('heimrichhannot/contao-slick-bundle') )
        {
            $palette = $GLOBALS['TL_DCA'][ $this->tableName ]['palettes']['slick-slider'];
            $palette = \str_replace('{template_legend', '{text_legend},text;{template_legend', $palette);

            $GLOBALS['TL_DCA'][ $this->tableName ]['palettes']['slick-slider'] = $palette;

            if( 'slick-slider' === $element->type )
            {
                $GLOBALS['TL_DCA'][ $this->tableName ]['fields']['text']['eval']['mandatory'] = false;
            }
        }
    }



    /**
     * @Callback(table="tl_content", target="fields.headlineFontColor.options")
     */
    public function getFontColorOptions( ?DataContainer $dc ): array
    {
        $options = [];

        PageConfig::loadCurrentPageColors();

        return $options;
    }



//    /**
//     * C allback(table="tl_content", target="fields.headlineImagePosition.input_field")
//     */
//    public function parseHeadlineImagePostionField( ?DataContainer $dc, $label ): string
//    {
//        $content    = '';
//        $fieldName  = $dc->field;
//        $value      = $dc->activeRecord->{$fieldName};
//        $floating   = $dc->activeRecord->floating;
//        $options    = $GLOBALS['TL_LANG'][ $this->tableName ]['options'][ $fieldName ];
//
////        if( \Input::post("FORM_SUBMIT") == $this->tableName )
////        {
////            $varValue = $this->saveData($strFieldName, $dc);
////        }
//
//        if( $floating )
//        {
//            if( 'left' != $floating && 'right' != $floating )
//            {
//                unset( $options['nextTo'] );
//            }
//
//            $fieldConfig    = $GLOBALS['TL_DCA'][ $this->tableName ]['fields'][ $fieldName ];
//            $inputType	    = $fieldConfig['inputType'];
//            $strClass	    = $GLOBALS['BE_FFL'][ $inputType ];
//            /* @var $strClass \SelectMenu */
//
//            $objWidget = new $strClass( $strClass::getAttributesFromDca($fieldConfig, $dc->inputName, $value, $fieldName, $dc->table, $dc) );
//            $objWidget->options		= $options;
//            $objWidget->xlabel		= $label;
//
//            $content = '<div class="' . $fieldConfig['eval']['tl_class'] . ' widget">' . $objWidget->parse() . '</div>';
//        }
//
//        return $content;
//    }
}
