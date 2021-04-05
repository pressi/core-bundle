<?php
/*******************************************************************
 * (c) 2021 Stephan Preßl, www.prestep.at <development@prestep.at>
 * All rights reserved
 * Modification, distribution or any other action on or with
 * this file is permitted unless explicitly granted by IIDO
 * www.iido.at <development@iido.at>
 *******************************************************************/
namespace IIDO\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('iido_core');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('previewMode')
                    ->defaultTrue()
                ->end()
                ->arrayNode('themeDesigner')
                    ->children()
                        ->booleanNode('disabled')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end() // themeDesigner
            ->end()
        ;

        return $treeBuilder;
    }
}
