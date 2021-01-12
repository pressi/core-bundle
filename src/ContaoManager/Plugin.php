<?php
declare(strict_types=1);

/*******************************************************************
 * (c) 2020 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\ContaoManager;


use IIDO\CoreBundle\IIDOCoreBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\NewsBundle\ContaoNewsBundle;
use Contao\CalendarBundle\ContaoCalendarBundle;

use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
//use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
//use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
//use Contao\ManagerPlugin\Config\ConfigPluginInterface;

//use Symfony\Component\Config\Loader\LoaderResolverInterface;
//use Symfony\Component\HttpKernel\KernelInterface;
//use Symfony\Component\Config\Loader\LoaderInterface;


/**
 * Plugin for the Contao Manager.
 *
 * @author Stephan Preßl <development@prestep.at>
 */
//class Plugin implements BundlePluginInterface, RoutingPluginInterface, ConfigPluginInterface
//class Plugin implements BundlePluginInterface, RoutingPluginInterface
final class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        $loadAfter  = [ContaoCoreBundle::class];
        $vendorPath = preg_replace('/2do\/core-bundle\/src\/ContaoManager/', '', __DIR__);

        if( is_dir( $vendorPath . 'contao/news-bundle') )
        {
            $loadAfter[] = ContaoNewsBundle::class;
        }

        if( is_dir( $vendorPath . 'contao/calendar-bundle') )
        {
            $loadAfter[] = ContaoCalendarBundle::class;
        }

//        if( is_dir( $vendorPath . 'delahaye/dlh_googlemaps') )
//        {
//            $arrLoadAfter[] = 'dlh_googlemaps';
//            $arrLoadAfter[] = 'delahaye/dlh_googlemaps';
//        }

        if( is_dir( $vendorPath . 'codefog/contao-news_categories') )
        {
            $loadAfter[] = \Codefog\NewsCategoriesBundle\CodefogNewsCategoriesBundle::class;
        }

        return [
            BundleConfig::create( IIDOCoreBundle::class )
                ->setLoadAfter( $loadAfter )
        ];
    }



//    /**
//     * {@inheritdoc}
//     *
//     * @throws \Exception
//     */
//    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
//    {
//        $file = __DIR__ . '/../Resources/config/routing.yml';
//
//        return $resolver->resolve($file)->load($file);
//    }



//    public function registerContainerConfiguration(LoaderInterface $loader, array $config)
//    {
//        $loader->load(__DIR__ . '/config/custom.yml');
//    }
}
