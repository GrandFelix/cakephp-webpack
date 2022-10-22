<?php
declare(strict_types=1);

namespace Webpack\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Webpack\Command\WebpackCommand;

/**
 * Webpack\Command\WebpackCommand Test Case
 *
 * @uses \Webpack\Command\WebpackCommand
 */
class WebpackCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }
}
