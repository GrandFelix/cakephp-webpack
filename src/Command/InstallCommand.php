<?php
declare(strict_types=1);

namespace Webpack\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Webpack\WebpackPlugin;

/**
 * Install command.
 */
class InstallCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|void|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $plugin = new WebpackPlugin();
        $io->info('Install required webpack and npm configuration files to desired place');

        $js = file_get_contents($plugin->getConfigPath() . 'webpack.config.default.js');
        if ($io->createFile(ROOT . DS . 'webpack.config.js', $js)) {
            $io->out("Webpack configuration file successfully created!");
        }

        $json = file_get_contents($plugin->getConfigPath() . 'webpack.packages.json');
        if ($io->createFile(ROOT . DS . 'package.json', $json)) {
            $io->out("Package file successfully created!");
        }
    }
}
