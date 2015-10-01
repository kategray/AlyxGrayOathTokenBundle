<?php
/**
 * This file is part of the Alyx Gray OATH token bundle.
 *
 * (c) Alyx Gray <opensource@alyxgray.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlyxGray\OathTokenBundle\Tests\DependencyInjection;

use AlyxGray\OathTokenBundle\DependencyInjection\AlyxGrayOathTokenExtension;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests whether or not the token manager service properly loads
 */
class AlyxGrayOathTokenExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Ensure that invalid driver types throw an error
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidDriverType() {
        $extension = new AlyxGrayOathTokenExtension();
        $config = $this->getConfig ('invalid_driver_type');
        $extension->load (array ($config), new ContainerBuilder());
    }

    /**
     * Ensure that invalid drivers throw an error
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidDriver () {
        $extension = new AlyxGrayOathTokenExtension();
        $config = $this->getConfig ('invalid_driver');
        $extension->load (array ($config), new ContainerBuilder());
    }

    /**
     * Test configuration parsing for the SQLite driver
     */
    public function testSQLiteDriver () {
        $extension = new AlyxGrayOathTokenExtension();
        $config = $this->getConfig ('sqlite');
        $extension->load (array ($config), new ContainerBuilder());
    }

    /**
     * Build a configuration for testing purposes
     * @param string $which
     */
    protected function getConfig($which) {
        $yaml = "";

        switch ($which) {
            case 'invalid_driver':
                $yaml .= "driver_type: local\n";
                $yaml .= "driver:      lmnop\n";
                break;

            case 'invalid_driver_type':
                $yaml .= "driver_type: lmnop\n";
                $yaml .= "driver:      sqlite\n";
                break;

            case 'sqlite':
                $yaml .= "driver_type: local\n";
                $yaml .= "driver:      sqlite\n";
                break;
        }

        // Parse the config
        $parser = new Parser();
        return $parser->parse ($yaml, true);
    }
}