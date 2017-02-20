<?php
namespace Webpack\Test\TestCase\Shell\Task;

use Cake\TestSuite\TestCase;
use Webpack\Shell\Task\ReloadResourcesTask;

/**
 * Webpack\Shell\Task\ReloadResourcesTask Test Case
 */
class ReloadResourcesTaskTest extends TestCase
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
     * @var \Webpack\Shell\Task\ReloadResourcesTask
     */
    public $ReloadResources;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->ReloadResources = $this->getMockBuilder('Webpack\Shell\Task\ReloadResourcesTask')
            ->setConstructorArgs([$this->io])
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
}
