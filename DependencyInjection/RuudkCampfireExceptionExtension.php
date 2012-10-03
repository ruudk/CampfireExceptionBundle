<?php

/*
 * This file is part of the RuudkCampfireExceptionBundle package.
 *
 * (c) Ruud Kamphuis <ruudk@mphuis.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ruudk\CampfireExceptionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class RuudkCampfireExceptionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (isset($config['subdomain'])) {
            $container->setParameter('ruudk_campfire_exception.subdomain', $config['subdomain']);
        }

        if (isset($config['token'])) {
            $container->setParameter('ruudk_campfire_exception.token', $config['token']);
        }

        if (isset($config['room'])) {
            $container->setParameter('ruudk_campfire_exception.room', $config['room']);
        }
    }
}
