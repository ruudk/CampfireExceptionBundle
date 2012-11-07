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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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

        if (isset($config['subdomain'])) {
            $exceptionListener = new Definition($container->getParameter('ruudk_campfire_exception.exception_listener.class'), array(
                new Reference('ruudk_campfire_exception.campfire')
            ));

            $exceptionListener->addTag('kernel.event_listener', array(
                'event'  => 'kernel.exception',
                'method' => 'onKernelException'
            ));

            $container->setDefinition('ruudk_campfire_exception.exception_listener', $exceptionListener);
        }
    }
}
