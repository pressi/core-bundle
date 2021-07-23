<?php
declare(strict_types=1);

/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\ContaoManager;


//use Contao\ManagerPlugin\Config\ContainerBuilder;
//use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use HeimrichHannot\FieldpaletteBundle\HeimrichHannotContaoFieldpaletteBundle;
use IIDO\CoreBundle\IIDOCoreBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\NewsBundle\ContaoNewsBundle;
use Contao\CalendarBundle\ContaoCalendarBundle;

use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
//use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;

use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Config\Loader\LoaderInterface;


/**
 * Plugin for the Contao Manager.
 *
 * @author Stephan Preßl <development@prestep.at>
 */
//class Plugin implements BundlePluginInterface, RoutingPluginInterface, ConfigPluginInterface, ExtensionPluginInterface
class Plugin implements BundlePluginInterface, RoutingPluginInterface, ConfigPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        $loadAfter  = [TwigBundle::class, ContaoCoreBundle::class];

        $vendorPath = \preg_replace('/2do\/core-bundle\/src\/ContaoManager/', '', __DIR__);
        $vendorPath = \preg_replace('/develop\/bundles\/core-bundle\/src\/ContaoManager/', 'vendor/', $vendorPath);

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

        if( is_dir( $vendorPath . 'heimrichhannot/contao-fieldpalette-bundle') )
        {
            $loadAfter[] = HeimrichHannotContaoFieldpaletteBundle::class;
        }
//echo "<pre>"; print_r( $loadAfter ); exit;
        return [
            BundleConfig::create( IIDOCoreBundle::class )
                ->setLoadAfter( $loadAfter )
        ];
    }



    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        $file = '@IIDOCoreBundle/Resources/config/routes.yml';
//        $file = __DIR__ . '/../Resources/config/routes.yml';
        $loader = $resolver->resolve( $file );

        if( false === $loader )
        {
            throw new \RuntimeException('Could not load IIDO Core routing configuration.');
        }

        return $loader->load( $file );
    }



    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig): void
    {
        $loader->load('@IIDOCoreBundle/Resources/config/config.yml');
//        $loader->load(__DIR__ . '/../Resources/config/config.yml');
    }



//    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container)
//    {
//        if( 'iido_core' !== $extensionName )
//        {
//            return $extensionConfigs;
//        }
//
////        $config = $container->getExtensionConfig('iido_core');
////        $config = array_merge(...$config);
//
////        echo "<pre>"; print_r( $config );
////        echo "<br>"; print_r( $extensionConfigs );
////        exit;
//
////        $config['storage'] = array_filter($config['storage'] ?? [], fn ($input) => 'Encrypted' !== $input['type']);
////        if (empty($config['storage'])) {
////            return $extensionConfigs;
////        }
//
////        $extensionConfigs[0]['storage'] += $config['storage'];
//
//        return $extensionConfigs;
//    }
}
