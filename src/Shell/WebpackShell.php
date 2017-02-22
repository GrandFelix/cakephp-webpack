<?php
namespace GrandFelix\Webpack\Shell;

use Cake\Console\Shell;

/**
 * Webpack shell command.
 */
class WebpackShell extends Shell
{

    /**
     * @var array
     */
    public $tasks = [
        'GrandFelix/Webpack.Reload',
        'GrandFelix/Webpack.Build',
    ];

    /**
     * Disable welcome text
     * @return void
     */
    public function startup()
    {
    }

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('reload', [
            'help' => 'Reload all resources from defined paths and write to config json',
            'parser' => $this->Reload->getOptionParser(),
        ]);

        $parser->addSubcommand('build', [
            'help' => 'Build all resources from defined paths',
            'parser' => $this->Build->getOptionParser(),
        ]);

        return $parser;
    }

    /**
     * main() method.
     *
     * @return void
     */
    public function main()
    {
        $this->out($this->OptionParser->help());
    }
}
