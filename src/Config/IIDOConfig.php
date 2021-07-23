<?php


namespace IIDO\CoreBundle\Config;


use IIDO\CoreBundle\Util\BasicUtil;
use IIDO\CoreBundle\Util\WebsiteSettingsUtil;


class IIDOConfig
{
    const FILE_NAME = 'iido.settings.yml';
    const FILE_PATH = 'config/%s';

    protected BasicUtil $basic;



    public function __construct( BasicUtil $basicUtil )
    {
        $this->basic = $basicUtil;
    }



    public function get( $name = '', $table = 'tl_iido_core_settings' )
    {
        if( !str_starts_with($table, 'tl_iido_') )
        {
            $table = 'tl_iido_' . $table;
        }

        return WebsiteSettingsUtil::getWebsiteSettings( $name, $table );
    }

}
