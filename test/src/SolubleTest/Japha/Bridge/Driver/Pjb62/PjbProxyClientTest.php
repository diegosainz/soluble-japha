<?php

namespace SolubleTest\Japha\Bridge\Driver\Pjb62;

use Soluble\Japha\Bridge\Driver\Pjb62\PjbProxyClient;
use Soluble\Japha\Bridge\Adapter;
use Soluble\Japha\Bridge\Driver\Pjb62\Java;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-11-13 at 10:21:03.
 */
class PjbProxyClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $servlet_address;

    /**
     * @var string
     */
    protected $options;

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->servlet_address = \SolubleTestFactories::getJavaBridgeServerAddress();
        $this->options = [
            'servlet_address' => $this->servlet_address,
            'java_prefer_values' => true,
        ];
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testGetInstance()
    {
        $pjbProxyClient = PjbProxyClient::getInstance($this->options);

        $this->assertInstanceOf('Soluble\Japha\Bridge\Driver\Pjb62\PjbProxyClient', $pjbProxyClient);
        $this->assertTrue(PjbProxyClient::isInitialized());
        $this->assertInstanceOf('Soluble\Japha\Bridge\Driver\Pjb62\Client', $pjbProxyClient->getClient());

        $pjbProxyClient->unregisterInstance();
        $this->assertFalse(PjbProxyClient::isInitialized());
        $this->assertInstanceOf('Soluble\Japha\Bridge\Driver\Pjb62\PjbProxyClient', $pjbProxyClient);
    }

    public function testGetJavaClass()
    {
        $pjbProxyClient = PjbProxyClient::getInstance($this->options);
        $cls = $pjbProxyClient->getJavaClass('java.lang.Class');
        $this->assertInstanceOf('Soluble\Japha\Interfaces\JavaClass', $cls);
    }

    public function testInvokeMethod()
    {
        $pjbProxyClient = PjbProxyClient::getInstance($this->options);
        $bigint1 = new Java('java.math.BigInteger', 10);
        $value = $pjbProxyClient->invokeMethod($bigint1, 'intValue');
        $this->assertEquals(10, $value);

        $bigint2 = new Java('java.math.BigInteger', 20);
        $bigint3 = $pjbProxyClient->invokeMethod($bigint1, 'add', [$bigint2]);
        $this->assertEquals(30, $bigint3->intValue());
    }
}
