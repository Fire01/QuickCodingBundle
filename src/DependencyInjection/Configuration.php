<?php
namespace Fire01\QuickCodingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('quick_coding');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('app_name')->defaultValue('Quick Coding')->info('Quick Coding App Name')->end()
                ->scalarNode('app_logo')->defaultValue('')->info('Quick Coding App Logo')->end()
                ->scalarNode('app_home')->info('Quick Coding App Home')->end()
                //->arrayNode('layout_stylesheets')->scalarPrototype()->info('Quick Coding Layout Stylesheets')->end()
            ->end();
        
        return $treeBuilder;
    }
}