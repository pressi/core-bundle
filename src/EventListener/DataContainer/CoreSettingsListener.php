<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener\DataContainer;


use Contao\Controller;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use IIDO\CoreBundle\Config\BundleConfig;


class CoreSettingsListener
{
    protected $strTable = 'tl_iido_core_settings';



    /**
     * @Callback(table="tl_iido_config", target="config.onload")
     */
    public function loadTable( DataContainer $dc ): void
    {
        $count = 2;

        if( BundleConfig::isActiveBundle('contao/news-bundle') )
        {
            $count++;
        }

        if( BundleConfig::isActiveBundle('contao/calendar-bundle') )
        {
            $count++;
        }

        if( BundleConfig::isActiveBundle('delahaye/dlh_googlemaps') )
        {
            $count++;
        }

        if( BundleConfig::isActiveBundle('madeyourday/contao-rocksolid-slider') )
        {
            $count++;
        }

//        $GLOBALS['TL_DCA']['tl_iido_config']['fields']['navLabels']['eval']['multiColumnEditor']['minRowCount'] = $count;
//        $GLOBALS['TL_DCA']['tl_iido_config']['fields']['navLabels']['eval']['multiColumnEditor']['maxRowCount'] = $count;
        $GLOBALS['TL_DCA']['tl_iido_config']['fields']['navLabels']['eval']['minCount'] = $count;
        $GLOBALS['TL_DCA']['tl_iido_config']['fields']['navLabels']['eval']['maxCount'] = $count;
    }



    /**
     * @Callback(table="tl_iido_core_settings", target="fields.navLabels.load")
     */
    public function loadNavFields( $varValue, DataContainer $dc )
    {
        Controller::loadLanguageFile('modules');

        $arrLabels = [
            ['value'=>'article', 'label'=> $GLOBALS['TL_LANG']['MOD']['article'][0] ],
            ['value'=>'form', 'label'=> $GLOBALS['TL_LANG']['MOD']['form'][0] ],
        ];

        if( BundleConfig::isActiveBundle('contao/news-bundle') )
        {
            $arrLabels[] = ['value'=>'news', 'label'=> $GLOBALS['TL_LANG']['MOD']['news'][0] ];
        }

        if( BundleConfig::isActiveBundle('contao/calendar-bundle') )
        {
            $arrLabels[] = ['value'=>'calendar', 'label'=> $GLOBALS['TL_LANG']['MOD']['calendar'][0] ];
        }

        if( BundleConfig::isActiveBundle('delahaye/dlh_googlemaps') )
        {
            $arrLabels[] = ['value'=>'dlh_googlemaps', 'label'=> $GLOBALS['TL_LANG']['MOD']['dlh_googlemaps'][0] ];
        }

        if( BundleConfig::isActiveBundle('madeyourday/contao-rocksolid-slider') )
        {
            $arrLabels[] = ['value'=>'rocksolid_slider', 'label'=> $GLOBALS['TL_LANG']['MOD']['rocksolid_slider'][0] ];
        }

        if( !$varValue )
        {
            //            $varValue = StringUtil::deserialize( $arrLabels );
            $varValue = $arrLabels;
        }

        return $varValue;
    }
}
