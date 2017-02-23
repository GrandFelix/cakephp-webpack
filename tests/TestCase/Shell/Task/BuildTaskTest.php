<?php
namespace Webpack\Test\TestCase\Shell\Task;

use Cake\TestSuite\TestCase;
use GrandFelix\Webpack\Shell\Task\BuildResourcesTask;

/**
 * Webpack\Shell\Task\BuildResourcesTask Test Case
 */
class BuildTaskTest extends TestCase
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
     * @var \GrandFelix\Webpack\Shell\Task\BuildResourcesTask
     */
    public $BuildResources;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->BuildResources = $this->getMockBuilder('GrandFelix\Webpack\Shell\Task\BuildResourcesTask')
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
        unset($this->BuildResources);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        //$this->markTestIncomplete('Not implemented yet.');
    }
}
