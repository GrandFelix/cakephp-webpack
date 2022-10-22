<?php
declare(strict_types=1);

namespace Webpack\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * Build command.
 */
class BuildCommand extends Command
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
        $parser->addOption('watch', ['help' => 'Watch for changes']);
        $parser->addOption('production', ['help' => 'Production build']);

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
        $watch = $args->getOption('watch');
        $production = $args->getOption('production');

        if (!is_null($production)) {
            $this->production($io);
        } else {
            $commands[] = 'cd ' . ROOT;
            $commands[] = 'webpack ' . (!is_null($watch) ? '--watch' : '');

            $output = false;
            exec(implode(' && ', $commands), $output);

            $io->out($output);
        }
    }

    /**
     * Build JS files for production
     *
     * @return void
     */
    private function production(ConsoleIo $io)
    {
        if (Configure::read('Webpack.clean_before_build')) {
            $io->out('Cleaning folder before production build.');
            foreach (Configure::read('Webpack.clean_dirs') as $dir) {
                $files = glob($dir);
                if ($files) {
                    $io->out(sprintf('Cleaning %s', $dir));
                    foreach ($files as $file) {
                        $f = new \SplFileInfo($file);
                        if ($f->isFile()) {
                            unlink($file);
                        }
                    }
                }
            }
        }

        $io->out('Creating production build.');

        $commands[] = 'cd ' . ROOT;
        $commands[] = 'webpack --env=production';

        $output = false;
        exec(implode(' && ', $commands), $output);

        $io->out($output);
    }
}
