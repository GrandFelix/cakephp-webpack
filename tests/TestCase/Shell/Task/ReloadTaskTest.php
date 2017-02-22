<?php
namespace Webpack\Test\TestCase\Shell\Task;

use Cake\TestSuite\TestCase;
use GrandFelix\Webpack\Shell\Task\ReloadTask;

/**
 * Webpack\Shell\Task\ReloadResourcesTask Test Case
 */
class ReloadTaskTest extends TestCase
{

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \GrandFelix\Webpack\Shell\Task\ReloadTask
     */
    public $ReloadResources;

    public $FileInstance;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->ReloadResources = $this->getMockBuilder('GrandFelix\Webpack\Shell\Task\ReloadTask')
            ->setConstructorArgs([$this->io])
            ->getMock();

        $this->FileInstance = $this->getMockBuilder('Cake\Filesystem\File')
            ->setConstructorArgs([TESTS . DS . 'TestCase' . DS . 'data'.DS.'test_config.php'])
            ->setMethods(null)
            ->getMock();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ReloadResources);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testSetMainFile()
    {
        $this->ReloadResources->assertEquals(
            TESTS . DS . 'TestCase' . DS . 'data'.DS.'test_config.php',
            $this->ReloadResources->mainFiles[TESTS . DS . 'TestCase' . DS . 'data'.DS.'test_config.php'])
            ->method('setMainFile')
            ->with('Test')
            ->with('testKey')
            ->with($this->FileInstance)
            ->with('.js');

        $this->ReloadResources->runCommand(['reload']);
    }
}
