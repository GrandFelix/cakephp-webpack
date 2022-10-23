<?php

namespace GrandFelix\Webpack;

use Cake\Core\BasePlugin;
use Cake\Core\ContainerInterface;
use Cake\Core\PluginApplicationInterface;
use Cake\Console\CommandCollection;
use Cake\Http\MiddlewareQueue;
use GrandFelix\Webpack\Command\BuildCommand;
use GrandFelix\Webpack\Command\InstallCommand;
use GrandFelix\Webpack\Command\ReloadCommand;

class WebpackPlugin extends BasePlugin
{
    public function middleware(MiddlewareQueue $middleware): MiddlewareQueue
    {
        // Add middleware here.
        $middleware = parent::middleware($middleware);

        return $middleware;
    }

    public function console(CommandCollection $commands): CommandCollection
    {
        // Add console commands here.
        $commands = parent::console($commands);

        // Add entry command to handle entry point and backwards compat.
        $commands->add('webpack install', InstallCommand::class);
        $commands->add('webpack reload', ReloadCommand::class);
        $commands->add('webpack build', BuildCommand::class);

        return $commands;
    }

    public function bootstrap(PluginApplicationInterface $app): void
    {
        // Add constants, load configuration defaults.
        // By default will load `config/bootstrap.php` in the plugin.
        parent::bootstrap($app);
    }

    public function routes($routes): void
    {
        // Add routes.
        // By default will load `config/routes.php` in the plugin.
        parent::routes($routes);
    }

    /**
     * Register application container services.
     *
     * @param \Cake\Core\ContainerInterface $container The Container to update.
     * @return void
     * @link https://book.cakephp.org/4/en/development/dependency-injection.html#dependency-injection
     */
    public function services(ContainerInterface $container): void
    {
        // Add your services here
    }
}
