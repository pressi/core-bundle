<?php
/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener;


use Contao\CoreBundle\Event\MenuEvent;
use Contao\CoreBundle\Event\ContaoCoreEvents;
use IIDO\CoreBundle\Controller\Backend\WebsiteSettingsController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;


/**
 * @ServiceTag("kernel.event_listener", event=ContaoCoreEvents::BACKEND_MENU_BUILD, priority=-256)
 */
class BackendMenuListener
{
    protected $router;
    protected $requestStack;


    public function __construct( RouterInterface $router, RequestStack $requestStack )
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }



    public function __invoke( MenuEvent $event ): void
    {
        $factory = $event->getFactory();
        $tree = $event->getTree();

        if( 'mainMenu' !== $tree->getName() )
        {
            return;
        }

        $systemNode = $tree->getChild('system');

        $node = $factory->createItem('website-settings')
            ->setUri( $this->router->generate(WebsiteSettingsController::class) )
            ->setLabel('Website-Einstellungen')
            ->setLinkAttribute('title', 'Website-Einstellungen')
            ->setLinkAttribute('class', 'website-settings')
            ->setCurrent( $this->requestStack->getCurrentRequest()->get('_controller') === WebsiteSettingsController::class );

        $systemNode->addChild( $node );
    }

}
