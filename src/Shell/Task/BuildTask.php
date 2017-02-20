<?php
namespace Webpack\Shell\Task;

use Cake\Console\Shell;

/**
 * BuildResources shell task.
 */
class BuildTask extends Shell
{

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * main() method.
     *
     * @param string|bool $watch turn on watch mode
     * @return bool|int Success or error code.
     */
    public function main($watch = false)
    {
        $commands[] = 'cd ' . ROOT;
        $commands[] = 'webpack ' . ($watch !== false ? '--watch' : '');

        passthru(implode(' && ', $commands));

        return true;
    }

    /**
     * Build JS files for production
     *
     * @return bool
     */
    public function production()
    {
        $commands[] = 'cd ' . ROOT;
        $commands[] = 'webpack -p';

        passthru((implode(' && ', $commands)));

        return true;
    }

    /**
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('dev', [
            'help' => 'Development mode'
        ]);

        return $parser;
    }
}
