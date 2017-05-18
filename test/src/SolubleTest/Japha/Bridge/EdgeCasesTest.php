<?php

/*
 * Soluble Japha
 *
 * @link      https://github.com/belgattitude/soluble-japha
 * @copyright Copyright (c) 2013-2017 Vanvelthem Sébastien
 * @license   MIT License https://github.com/belgattitude/soluble-japha/blob/master/LICENSE.md
 */

namespace SolubleTest\Japha\Bridge;

use Soluble\Japha\Bridge\Adapter;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-04 at 16:47:42.
 */
class EdgeCasesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $servlet_address;

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
        \SolubleTestFactories::startJavaBridgeServer();

        $this->servlet_address = \SolubleTestFactories::getJavaBridgeServerAddress();

        $this->adapter = new Adapter([
            'driver' => 'Pjb62',
            'servlet_address' => $this->servlet_address,
        ]);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testGetDriver()
    {
        $driver = $this->adapter->getDriver();
        $this->assertInstanceOf('Soluble\Japha\Bridge\Driver\AbstractDriver', $driver);
    }

    public function testJavaBigMemory()
    {
        $ba = $this->adapter;
        $save_mem = ini_get('memory_limit');
        if (defined('HHVM_VERSION')) {
            ini_set('memory_limit', '800M');
        } else {
            ini_set('memory_limit', '512M');
        }

        // Very big string
        $initial_mem = memory_get_usage();
        $s = str_repeat('1', 31554432);
        $str = $ba->java('java.lang.String', $s);
        $this->assertEquals(31554432, $str->length());
        $full_mem = memory_get_usage();

        // releasing
        unset($s);
        unset($str);
        gc_collect_cycles();
        $released_mem = memory_get_usage();

        echo "\n";
        echo "Debug for java big memory test\n";
        echo "Released memory must be approx equal to initial memory\n";
        echo '- Initial memory   : ' . number_format($initial_mem, 0, '.', ',') . "\n";
        echo '- Max memory       : ' . number_format($full_mem, 0, '.', ',') . "\n";
        echo '- After release    : ' . number_format($released_mem, 0, '.', ',') . "\n";
        echo "\n";

        if (defined('HHVM_VERSION')) {
            // because hhvm does not really return memory consumption
            $this->assertLessThanOrEqual($full_mem, $released_mem);
        } else {
            $this->assertLessThan($full_mem, $released_mem);
        }
        // restore memory limit
        ini_set('memory_limit', $save_mem);
    }
}
