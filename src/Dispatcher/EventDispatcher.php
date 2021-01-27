<?php
/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Dispatcher;


use Contao\System;


class EventDispatcher
{
    const WEBSITE_SETTINGS          = 'iidoCore_websiteSettings';
    const WEBSITE_SETTINGS_FIELDS   = 'iidoCore_websiteSettings_fields';



    /**
     * @param string      $name
     * @param mixed|null  $model
     *
     * @return false|mixed|string|void
     */
    public function dispatch( string $name, $model = null )
    {
        $output = $model ? $model : '';

        if( !is_array($GLOBALS['TL_HOOKS'][ $name ]) )
        {
            return $output;
        }

        foreach( $GLOBALS['TL_HOOKS'][ $name ] as $callback )
        {
            if( is_array($callback) )
            {
                $output = call_user_func( [System::importStatic($callback[0]), $callback[1]], $model );
            }
        }

        return $output;
    }
}
