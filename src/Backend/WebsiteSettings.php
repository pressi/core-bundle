<?php
/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Backend;


use Contao\Controller;
use Contao\Input;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Dispatcher\EventDispatcher;
use IIDO\CoreBundle\Services\Services;
use IIDO\CoreBundle\Util\WebsiteSettingsUtil;


class WebsiteSettings
{
    public function renderSettings()
    {
//        $this->processForm();

        $table      = Input::get('table');
        $twig       = System::getContainer()->get('twig');
//        $values     = WebsiteSettingsUtil::getConfigFileValue();
//        $legends    = $this->getFormFields( $values );

//        $fs         = System::getContainer()->get('session')->getBag('contao_backend')->get('fieldset_states');
        $classPath  = '';

        Controller::loadLanguageFile( 'default' );
        Controller::loadLanguageFile( $table );

        Controller::loadDataContainer( $table );

        $tableConfig = $GLOBALS['TL_DCA'][ $table ];
        $dataContainer = $tableConfig['config']['dataContainer'];

        if( $dataContainer === 'YamlConfigFile' )
        {
            $classPath  = '\IIDO\CoreBundle\DataContainer';
        }

        $tableMode  = $classPath . '\DC_' . $dataContainer;
        $objTable   = new $tableMode( $table );

//        $config =
//        [
//            'tableContent' => $objTable->edit(),


//            'formToken' => REQUEST_TOKEN,
//            'fieldsets' => $legends,
//            'fs'        => $fs
//        ];

        return preg_replace('/<div id="tl_buttons">([A-Za-z0-9\s\n\-,;.:_öäüÖÄÜß!?="#\(\)\{\}\/<>%+&]{0,})<\/div>/', '', $objTable->edit()); //$twig->render('@IIDOCore\Backend\website_settings-settings.html.twig', $config);
    }



    protected function getFormFields( $values = array(), $onlyFields = false ): array
    {
        $legends =
        [
            'main_legend' =>
            [
                'previewMode'   => $onlyFields ? '' : $this->getFieldWidget('previewMode', 'checkbox', $values['previewMode']),
                'backendStyles' => '',
                'customLogin'   => ''
            ],

            'nav_legend' =>
            [
                'enableMobileNavigation'    => ''
            ],

            'content_legend' =>
            [
                'includeElementFields'      => '',
                'removeHeadlineFieldFromElements' => '',
                'enableLayout'              => ''
            ],

            'article_legend' =>
            [
                'includeArticleFields'      => ''
            ],

            'page_legend' =>
            [
                'includePageFields'         => ''
            ],

            'backend_legend' =>
            [
                'navLabels'                 => ''
            ]
        ];

        $legends = Services::getEventDispatcher()->dispatch(EventDispatcher::WEBSITE_SETTINGS_FIELDS, $legends );

//        if( !$onlyFields )
//        {
//            foreach( $legends as $legend => $fields )
//            {
//            }
//        }

        return $legends;
    }



    public function getFieldWidget( string $fieldName, string $fieldType = 'text', $value = '' )
    {
        $widgetClass = ' w50 cbx';

        $class  = $GLOBALS['BE_FFL'][ $fieldType ];
        $config = [ 'label' => $GLOBALS['TL_LANG']['IIDO']['settings_field'][ $fieldName ], 'inputType' => $fieldType ];

        if( $fieldType === 'checkbox' )
        {
            $widgetClass .= ' m12';
        }

        $widget = new $class( $class::getAttributesFromDca( $config, $fieldName, $value ) );

        return '<div class="widget' . $widgetClass . '">' . $widget->generate() . '</div>';
    }



    public static function getFieldWidgetStatic( string $fieldName, string $fieldType = 'text', $value = '')
    {
        $object = new self();
        return $object->getFieldWidget( $fieldName, $fieldType, $value );
    }



    protected function processForm()
    {
        if( Input::post('FORM_SUBMIT') === 'iido-settings' )
        {
            $values = [];
            foreach( $this->getFormFields([], true) as $legend => $fields )
            {
                foreach( $fields as $field => $widget )
                {
                    $values[ $field ] = Input::post( $field );
                }
            }

            if( count($values) )
            {
                WebsiteSettingsUtil::saveConfigFileValue( $values );
            }
        }
    }

}
