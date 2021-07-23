<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\EventListener;


use Contao\CoreBundle\Event\MenuEvent;
use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\StringUtil;
use Contao\System;
use IIDO\CoreBundle\Config\BundleConfig;
use IIDO\CoreBundle\Controller\Backend\CompanyController;
use IIDO\CoreBundle\Controller\Backend\WebsiteSettingsController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;


/**
 * @ServiceTag("kernel.event_listener", event=ContaoCoreEvents::BACKEND_MENU_BUILD, priority=-255)
 */
class BackendMenuListener
{
    protected RouterInterface $router;
    protected RequestStack $requestStack;


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

        $key = 'website-settings';

        $node = $factory->createItem( $key )
            ->setUri( $this->router->generate(WebsiteSettingsController::class) )
            ->setLabel( $GLOBALS['TL_LANG']['MOD'][ $key ][0] )
            ->setLinkAttribute('title', $GLOBALS['TL_LANG']['MOD'][ $key ][0])
            ->setLinkAttribute('class', $key)
            ->setCurrent( (str_starts_with($this->requestStack->getCurrentRequest()->get('_controller'), WebsiteSettingsController::class)) );

        $systemNode->addChild( $node );

        $contentNode = $tree->getChild('content');
        $config = System::getContainer()->get('iido.core.config');

        if( $config->get('navLabels') && $contentNode )
        {
            $navLabels = StringUtil::deserialize($config->get('navLabels'), true);

            if( count($navLabels) )
            {
                foreach( $navLabels as $arrLabel )
                {
                    $key = $arrLabel['value'];

                    $node = $contentNode->getChild( $key );

                    if($node && $node->getLabel() !== $arrLabel['label'] )
                    {
                        $node->setLabel( $arrLabel['label'] );
                    }
                }
            }
        }

        $key = 'company';

        $node = $factory->createItem( $key )
            ->setUri( $this->router->generate( CompanyController::class ) )
            ->setLabel( $GLOBALS['TL_LANG']['MOD'][ $key ][0] )
            ->setLinkAttribute('title', $GLOBALS['TL_LANG']['MOD'][ $key ][0] )
            ->setLinkAttribute('class', $key)
            ->setCurrent( (str_starts_with($this->requestStack->getCurrentRequest()->get('_controller'), CompanyController::class)) );

        $contentNode->addChild( $node );
    }

}
