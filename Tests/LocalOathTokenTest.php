<?php
/**
 * This file is part of the Alyx Gray OATH token bundle.
 *
 * (c) Alyx Gray <opensource@alyxgray.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AlyxGray\OathTokenBundle\Tests;

use AlyxGray\OathTokenBundle\Model\OathToken;
use AlyxGray\OathTokenBundle\Model\LocalOathToken;

/**
 * Tests whether or not the token manager service properly loads
 */
class LocalOathTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocalOathToken Token to test with
     */
    private $token;

    /**
     * Configure the environment for testing
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->token = new LocalOathToken();
    }

    /**
     * Ensure that local tokens inherit from Remote and Generic tokens
     */
    public function testTokenClassHierarchy () {
        $this->assertInstanceOf('AlyxGray\OathTokenBundle\Model\OathToken', $this->token);
        $this->assertInstanceOf('AlyxGray\OathTokenBundle\Model\RemoteOathToken', $this->token);
        $this->assertInstanceOf('AlyxGray\OathTokenBundle\Model\LocalOathToken', $this->token);
    }
}
