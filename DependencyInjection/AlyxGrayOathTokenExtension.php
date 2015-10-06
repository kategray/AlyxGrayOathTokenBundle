<?php
/**
 * This file is part of the Alyx Gray OATH token bundle.
 *
 * (c) Alyx Gray <opensource@alyxgray.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlyxGray\OathTokenBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This extension is responsible for loading the service configuration automatically
 */
class AlyxGrayOathTokenExtension extends Extension
{
    /**
     * Load the configuration extension
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder interface
     * @see \Symfony\Component\DependencyInjection\Extension\ExtensionInterface::load()
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // Parse the configuration
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        // Load the driver-specific configuration
        if ('custom' !== $config['driver']) {
            $loader->load(sprintf('%s.xml', $config['driver']));
        } else {
            throw new InvalidConfigurationException ('Custom drivers are not currently supported.');
        }
    }

    /**
     * Provides the path for XSD validation
     * @see \Symfony\Component\DependencyInjection\Extension\Extension::getXsdValidationBasePath()
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/';
    }

    /*
     * Provides the namespace for XSD validation
     * @see \Symfony\Component\DependencyInjection\Extension\Extension::getNamespace()
     */
    public function getNamespace()
    {
        return 'https://github.com/alyxgray/AlyxGrayOathTokenBundle/schema/';
    }
}
