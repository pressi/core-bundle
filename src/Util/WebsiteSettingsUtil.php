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
    protected static array $cache = [];
    protected static string $configFilePath = 'config/iido.settings.yml';



    public static function getBackendWebsiteSettings()
    {
        $container  = System::getContainer();
        $router     = $container->get('router');
        $defaultSettings = [];

        Controller::loadLanguageFile('website-settings');

//        $themeDesignerConfig = $container->getParameter('iido_core.themeDesigner');
//
//        if( !$themeDesignerConfig['disabled'] )
//        {
//            $defaultSettings['themeDesigner'] =
//            [
//                'name'       => $GLOBALS['TL_LANG']['IIDO']['website-settings']['themeDesigner']['name'],
//                'icon'       => 'theme-designer.svg',
//                'href'       => $router->generate('iido.core.website-settings.details', ['settingAlias'=>'themeDesigner']),
//                'callback'   => ['iido.core.website-settings.themeDesigner', 'renderThemeDesigner']
//            ];
//        }

        $defaultSettings['settings'] =
        [
            'name'       => $GLOBALS['TL_LANG']['IIDO']['website-settings']['settings']['name'],
            'icon'       => 'settings.svg',
            'href'       => $router->generate('iido.core.website-settings.details', ['settingAlias'=>'settings']),
            'table'      => 'tl_iido_core_settings',
            'callback'   => ['iido.core.website-settings.settings', 'renderSettings']
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



    public static function getConfigFilePath( bool $onlyFilePath = false ): string
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



    public static function getConfigFileValue(): array
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



    public static function getWebsiteSettings( string $name = '', string $table = '' ): mixed
    {
        $settings = self::getConfigFileValue();

        if( $table )
        {
            $settings = $settings[ $table ]??[];

            if( $name )
            {
                $settings = $settings[ $name ]??[];
            }
        }

        if( !$table && $name )
        {
            $settings = $settings[ 'tl_iido_core_settings' ][ $name ];
        }

        return $settings ?? null;
    }



    public static function updateWebsiteSetting( string $name, string $table, $value = '' ): void
    {
        $settings = self::getWebsiteSettings();

        $settings[ $table ][ $name ] = $value;

        self::saveConfigFileValue( $settings );
    }
}
