<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Util;


use Contao\Controller;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Dispatcher\EventDispatcher;
use IIDO\CoreBundle\Services\Services;
use Symfony\Component\Yaml\Yaml;


class WebsiteSettingsUtil
{
    protected static $cache = [];
    protected static $configFilePath = 'config/iido.settings.yml';



    public static function getBackendWebsiteSettings()
    {
        $router = System::getContainer()->get('router');
        Controller::loadLanguageFile('website-settings');

        $defaultSettings =
       [
           'settings' =>
           [
               'name'       => $GLOBALS['TL_LANG']['IIDO']['website-settings']['settings']['name'],
               'icon'       => 'settings.svg',
               'href'       => $router->generate('iido.core.website-settings.details', ['settingAlias'=>'settings']),
               'table'      => 'tl_iido_core_settings',
               'callback'   => ['iido.core.website-settings.settings', 'renderSettings']
           ]
       ];

        $settings = Services::getEventDispatcher()->dispatch(EventDispatcher::WEBSITE_SETTINGS, $defaultSettings );

//        if( $settings && is_array($settings) && count($settings) )
//        {
//            $settings = array_merge($defaultSettings, $settings);
//        }
//        else
//        {
//            $settings = $defaultSettings;
//        }

        foreach( $settings  as $key => $setting)
        {
            if( $setting['icon'] )
            {
                $parts = explode('/', $setting['icon']);

                if( count($parts) === 1 )
                {
                    $setting['icon'] = BundleConfig::getBundlePath( true, false ) . '/images/icons/' . $setting['icon'];
                }

                $iconName = array_pop( $parts );

                if( preg_match('/.svg$/', $iconName) )
                {
                    $setting['iconTag'] = file_get_contents( $setting['icon'] );
                }
            }

            if( isset($setting['table']) )
            {
                $setting['href'] = $setting['href'] . (preg_match('/\?([A-Za-z_\[\]]{1,})=/', $setting['href']) ? '&' : '?') . 'table=' . $setting['table'];
            }

            $setting['href'] = $setting['href'] . (preg_match('/\?([A-Za-z_\[\]]{1,})=/', $setting['href']) ? '&' : '?') . 'ref=' . TL_REFERER_ID;

            $settings[ $key ] = $setting;
        }

        return $settings;
    }



    public static function getConfigFilePath( $onlyFilePath = false )
    {
        return $onlyFilePath ? self::$configFilePath : BundleConfig::getRootDir( true ) . self::$configFilePath;
    }



    public static function checkIfConfigFileExists()
    {
        $filePath = self::getConfigFilePath();

        if( !file_exists( $filePath ) )
        {
            touch( $filePath );
        }
    }



    public static function getConfigFileValue()
    {
        self::checkIfConfigFileExists();

        if( count(static::$cache) )
        {
            $content = static::$cache;
        }
        else
        {
            $rootDir = BundleConfig::getRootDir( true );
            $content = (array) Yaml::parseFile($rootDir . self::$configFilePath);

            static::$cache = $content;
        }

        return $content;
    }



    public static function saveConfigFileValue( array $value )
    {
        static::$cache = $value;

        $value = Yaml::dump( $value );

        file_put_contents( self::getConfigFilePath(), $value );
    }



    public static function getWebsiteSettings( string $table = '', string $name = '' )
    {
        $settings = self::getConfigFileValue();

        if( $table )
        {
            $settings = $settings[ $table ];

            if( $name )
            {
                $settings = $settings[ $name ];
            }
        }

        if( !$table && $name )
        {
            //TODO: ERROR name without table does not work!
        }

        return $settings ?? null;
    }



    public static function updateWebsiteSetting( string $table, string $name, $value = '' )
    {
        $settings = self::getWebsiteSettings();

        $settings[ $table ][ $name ] = $value;

        self::saveConfigFileValue( $settings );
    }
}
