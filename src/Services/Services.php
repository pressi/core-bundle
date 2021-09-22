<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\Services;


use IIDO\CoreBundle\Automator\Automator;
use IIDO\CoreBundle\Dispatcher\EventDispatcher;
use IIDO\CoreBundle\Messages\FlashMessage;


class Services
{
    private static array $instances = [];

    private static array $initializations = [];



    /**
     * Get the automator
     *
     * @return Automator
     * @TODO ?????
     */
    public static function getAutomator(): Automator
    {
        return static::get(
            'automator',
            function ()
            {
                return new Automator(static::getNotificationSender(), static::getSubscriptionFactory());
            }
        );
    }



    public static function getEventDispatcher(): EventDispatcher
    {
        return static::get(
            'event-dispatcher',
            function ()
            {
                return new EventDispatcher();
            }
        );
    }



    /**
     * Get the exporter
     *
     * @return Exporter
     * @TODO: ????
     */
    public static function getExporter(): Exporter
    {
        return static::get(
            'exporter',
            function ()
            {
                return new Exporter(static::getEventDispatcher(), static::getSubscriptionFactory());
            }
        );
    }



    public static function getFlashMessage(): FlashMessage
    {
        return static::get(
            'flash-message',
            function ()
            {
                return new FlashMessage();
            }
        );
    }



    /**
     * Get the notification sender
     *
     * @return NotificationSender
     * @TODO: ????
     */
    public static function getNotificationSender()
    {
        return static::get(
            'notification-sender',
            function ()
            {
                return new NotificationSender(static::getSubscriptionFactory());
            }
        );
    }



    public static function setInitialization( string $key, callable $init ): void
    {
        static::$initializations[$key] = $init;
    }



    private static function get( string $key, callable $init )
    {
        if (!isset(static::$instances[$key]))
        {
            // Set the initialization instead of the default value
            if (isset(static::$initializations[$key]) && is_callable(static::$initializations[$key]))
            {
                $init = static::$initializations[$key];
            }

            static::$instances[$key] = $init();
        }

        return static::$instances[$key];
    }
}
