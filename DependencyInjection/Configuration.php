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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class for the bundle
 */
class Configuration implements ConfigurationInterface {
    /**
     * Build the configuration tree for this bundle
     * @see \Symfony\Component\Config\Definition\ConfigurationInterface::getConfigTreeBuilder()
     */
    public function getConfigTreeBuilder () {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('alyx_gray_oath_token');

        // Token drivers can be local or remote
        $driver_types = array ('local', 'remote');

        // Local token drivers (store serets)
        $localDrivers = array ('csv', 'sqlite', 'doctrine');
        $remoteDrivers = array ('hsm', 'rabbitmq', 'cmdline');
        $validDrivers = array_merge ($localDrivers, $remoteDrivers, array ('custom'));

        // Driver Settings
        $root->children()
            ->scalarNode('driver')
                ->cannotBeEmpty()
                ->cannotBeOverwritten()
                ->isRequired()
                ->validate()
                    ->ifNotInArray($validDrivers)
                    ->thenInvalid('Drivers must be one of '.json_encode($validDrivers))
                ->end()
            ->end()
            ->scalarNode('driver_type')
                ->cannotBeEmpty()
                ->cannotBeOverwritten()
                ->isRequired()
                ->validate()
                    ->ifNotInArray($driver_types)
                    ->thenInvalid('Driver type must be one of '.json_encode($driver_types))
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
