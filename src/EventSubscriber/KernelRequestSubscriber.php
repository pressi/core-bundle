<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventSubscriber;


use Contao\CoreBundle\Routing\ScopeMatcher;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Model\CompanyModel;
use IIDO\CoreBundle\Widget\InputUnitWidget;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class KernelRequestSubscriber implements EventSubscriberInterface
{
    protected ScopeMatcher $scopeMatcher;



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


        // backend form fields
        $GLOBALS['BE_FFL']['inputUnit'] = InputUnitWidget::class;


        // models
        $GLOBALS['TL_MODELS']['tl_iido_company'] = CompanyModel::class;
    }

}
