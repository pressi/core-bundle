<?php
/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventSubscriber;


use Contao\CoreBundle\Routing\ScopeMatcher;
use IIDO\CoreBundle\Config\BundleConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class KernelRequestSubscriber implements EventSubscriberInterface
{
    protected $scopeMatcher;


    public function __construct( ScopeMatcher $scopeMatcher )
    {
        $this->scopeMatcher = $scopeMatcher;
    }



    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }



    public function onKernelRequest( RequestEvent $e ): void
    {
        $request = $e->getRequest();

        if( $this->scopeMatcher->isBackendRequest( $request ) )
        {
            $GLOBALS['TL_CSS']['be_iido_styles'] = BundleConfig::getBundlePath( true, false ) . '/styles/backend.scss|static';
        }
    }

}
