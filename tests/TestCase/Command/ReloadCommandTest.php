<?php
declare(strict_types=1);

namespace Webpack\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Webpack\Command\ReloadCommand;

/**
 * Webpack\Command\ReloadCommand Test Case
 *
 * @uses \Webpack\Command\ReloadCommand
 */
class ReloadCommandTest extends TestCase
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
