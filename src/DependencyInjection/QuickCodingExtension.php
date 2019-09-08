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
        
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
    }
    
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        
        $twigConfig = [];
        
        $twigConfig['paths'][__DIR__.'/../Resources/views'] = "quick_coding.view";
        $twigConfig['globals']['quick_coding']['public'] = "bundles/quickcoding/";
        $twigConfig['globals']['quick_coding']['app_name'] = $config['app_name'];
        $twigConfig['globals']['quick_coding']['app_logo'] = $config['app_logo'];
        
        $container->prependExtensionConfig('twig', $twigConfig);
    }
    
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new QuickCodingExtension();
        }
        return $this->extension;
    }
    
    public function getAlias()
    {
        return 'quick_coding';
    }
}