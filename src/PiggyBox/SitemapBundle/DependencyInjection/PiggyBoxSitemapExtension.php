<?php

namespace PiggyBox\SitemapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PiggyBoxSitemapExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('twig.xml');

        if (!isset($config['base_url'])) {
            throw new \InvalidArgumentException(
                'The "base_url" option must be set'
            );
        }

        $container->setParameter(
            'piggy_box_sitemap.base_url',
            $config['base_url']
        );
    }
}
