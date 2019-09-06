<?php
namespace Fire01\QuickCodingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class QuickCodingExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
    
    public function prepend(ContainerBuilder $container)
    {
        $twigConfig = [];
        $twigConfig['paths'][__DIR__.'/../Resources/views'] = "quick_coding.view";
        $twigConfig['globals']['quick_coding']['public'] = "bundles/quickcoding/";
        $container->prependExtensionConfig('twig', $twigConfig);
    }
    
}