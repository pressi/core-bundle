<?php
declare(strict_types=1);

/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/

namespace IIDO\CoreBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
//use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
//use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


/**
 * Configures the Contao IIDO Core Bundle.
 *
 * @author Stephan Preßl <https://github.com/pressi>
 */
//class IIDOCoreExtension extends Extension implements PrependExtensionInterface
class IIDOCoreExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader( $container, new FileLocator(__DIR__ . '/../Resources/config') );

        $loader->load('services.yml');
    }


//    /**
//     * {@inheritdoc}
//     *
//     * @throws \Exception
//     */
//    public function prepend(ContainerBuilder $container): void
//    {
//        $rootDir = $container->getParameter('kernel.project_dir');
//
//        if (file_exists($rootDir . '/config/parameters.yml') || !file_exists($rootDir . '/config/parameters.yml.dist'))
//        {
//            return;
//        }
//
//        $loader = new YamlFileLoader(
//            $container,
//            new FileLocator($rootDir . '/config')
//        );
//
//        $loader->load('parameters.yml.dist');
//    }
}
