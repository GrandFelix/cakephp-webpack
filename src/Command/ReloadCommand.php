<?php
declare(strict_types=1);

namespace Webpack\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Utility\Inflector;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Reload command.
 */
class ReloadCommand extends Command
{
    /**
     * @var array collect main file handlers so we don't create for each file new instance
     */
    private array $mainFileHandlers = [];

    /**
     * @var array
     */
    private array $mainFiles = [];

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
     * @throws \Exception
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $files = $aliasPaths = [];

        // check for main webpack config
        $mainConfig = $this->findResourceConfig(ROOT);
        if (!empty($mainConfig)) {
            foreach ($mainConfig as $aliasKey => $resourceConfig) {
                $files[$this->getResourceKey(Configure::read('App.namespace'), $aliasKey)] =
                    $this->getResourceFiles('', $resourceConfig, $aliasKey);

                $aliasPaths[$this->getAliasKey(Configure::read('App.namespace'), $aliasKey)] =
                    $this->getAliasPath('', $resourceConfig) ?? [];
            }
        }

        // check for plugins webpack config files
        $loadedPlugins = Plugin::loaded();
        foreach ($loadedPlugins as $plugin) {
            $path = Plugin::path($plugin);

            $pluginConfig = $this->findResourceConfig($path);

            if ($pluginConfig !== null) {
                foreach ($pluginConfig as $aliasKey => $resourceConfig) {
                    $files[$this->getResourceKey($plugin, $aliasKey)] =
                        $this->getResourceFiles($plugin, $resourceConfig, $aliasKey);

                    $aliasPaths[$this->getAliasKey($plugin, $aliasKey)] =
                        $this->getAliasPath($plugin, $resourceConfig) ?? 0;
                }
            }
        }

        $files = array_merge($files, $this->mainFiles);

        $this->params['force'] = true; // set force to true so we can override files!
        if ($io->createFile(ROOT . DS . 'webpack.config.json', json_encode([
            'paths' => array_filter($files),
            'aliases' => array_filter($aliasPaths)
        ]))
        ) {
            $io->out("Webpack CakePHP configuration file successfully created!");
        }
    }

    /**
     * Find resource config file
     *
     * @param string $path path to check
     * @return array|null
     * @throws \Exception
     */
    protected function findResourceConfig(string $path): ?array
    {
        $file = new \SplFileInfo($path . DS . 'config' . DS . 'webpack.config.php');

        if ($file->getRealPath()) {
            $config = include $file->getRealPath();

            if (is_array($config)) {
                return $config;
            } else {
                throw new \Exception("Webpack config file must return array!");
            }
        }

        return null;
    }

    /**
     * Get all resource files for each config option
     *
     * @param string $plugin plugin name
     * @param array $resourceConfig resource config
     * @param string $aliasKey alias key from resource config
     * @return array
     * @throws \Exception if resource option is not set in resource config
     */
    protected function getResourceFiles(string $plugin, array $resourceConfig, string $aliasKey): array
    {
        $return = [];
        $startPath = $this->getStartPath($plugin);

        if (isset($resourceConfig['resources'])) {
            $pathToResources = (string)$resourceConfig['aliasPath'] ?: 'webroot';
            $mainJs = $resourceConfig['mainJs'] ?? false;
            $mainCss = $resourceConfig['mainCss'] ?? false;

            foreach ($resourceConfig['resources'] as $resource) {
                $folderPath = $startPath . DS . $pathToResources . DS . $resource;
                $file = new \SplFileInfo($folderPath);

                // check if is file!
                if ($file->isFile()) {
                    // check if isset mainJs or mainCss and skip it on true and add it to main files
                    $jsFileToExclude = $this->checkIfIsMainFile($mainJs, $plugin, $resourceConfig, $aliasKey, '.js');
                    if ($jsFileToExclude instanceof \SplFileInfo) {
                        if ($this->compareFilePaths($jsFileToExclude->getRealPath(), $file->getRealPath())) {
                            $this->setMainFile($plugin, $aliasKey, $jsFileToExclude);
                            continue;
                        }
                    }

                    $cssFileToExclude = $this->checkIfIsMainFile($mainCss, $plugin, $resourceConfig, $aliasKey, '.scss');
                    if ($cssFileToExclude instanceof \SplFileInfo) {
                        if ($this->compareFilePaths($cssFileToExclude->getRealPath(), $file->getRealPath())) {
                            $this->setMainFile($plugin, $aliasKey, $cssFileToExclude);
                            continue;
                        }
                    }

                    $return[] = $this->removeMultipleSlashesFromPath($file->getRealPath());
                } else {
                    // it looks like this is folder! Get all files from this folder, recursive!
                    /** @var \SplFileInfo[] $filesInPath */
                    $filesInPath = [];
                    $dir = new \SplFileInfo($folderPath);

                    if ($dir->getRealPath()) {
                        $it = new RecursiveDirectoryIterator($folderPath);

                        foreach (new RecursiveIteratorIterator($it) as $file) {
                            if (in_array($file->getExtension(), Configure::read('Webpack.resources.fileExtensionsToSearch'))) {
                                $filesInPath[] = $file;
                            }
                        }

                        if (isset($filesInPath)) {
                            foreach ($filesInPath as $file) {
                                // check if isset mainJs or mainCss and skip it on true and add it to main files
                                $jsFileToExclude = $this->checkIfIsMainFile($mainJs, $plugin, $resourceConfig, $aliasKey, '.js');
                                if ($jsFileToExclude instanceof \SplFileInfo) {
                                    if ($this->compareFilePaths($jsFileToExclude->getRealPath(), $file->getRealPath())) {
                                        $this->setMainFile($plugin, $aliasKey, $jsFileToExclude);
                                        continue;
                                    }
                                }

                                $cssFileToExclude = $this->checkIfIsMainFile($mainCss, $plugin, $resourceConfig, $aliasKey, '.scss');
                                if ($cssFileToExclude instanceof \SplFileInfo) {
                                    if ($this->compareFilePaths($cssFileToExclude->getPath(), $file->getPath())) {
                                        $this->setMainFile($plugin, $aliasKey, $cssFileToExclude);
                                        continue;
                                    }
                                }

                                $return[] = $this->removeMultipleSlashesFromPath($file->getRealPath());
                            }
                        }
                    }
                }
            }
        } else {
            throw new \Exception('For plugin ' . $plugin . ' resources option is not set! Pleas set it.');
        }

        return $return;
    }

    /**
     * Get alias path. Used for webpack resolve->alias
     *
     * @param string $plugin plugin name
     * @param array $resourceConfig resource config
     * @return string
     * @throws \Exception if folder does not exists or if aliasPath is not set in resource config
     */
    private function getAliasPath(string $plugin, array $resourceConfig): string
    {
        $startPath = $this->getStartPath($plugin);

        if (isset($resourceConfig['aliasPath'])) {
            $dir = new \SplFileInfo($startPath . $resourceConfig['aliasPath']);

            // Only add to alias path if dir exists!
            if ($dir->getPath() !== null) {
                $return = $dir->getPath();
            } else {
                throw new \Exception("Folder does not exists!");
            }
        } else {
            throw new \Exception("aliasPath is not set!");
        }

        return $return;
    }

    /**
     * Check if file from resource is named as main file so exclude it and then add it as separate option in webpack
     * entry option. Main files are used as entry point for any plugin or main app.
     *
     * @param string|bool $mainOption Should be boolean or string. If boolean then link to default main file
     * @param string $plugin plugin or '' for main app
     * @param array $resourceConfig plugin or app resource config array
     * @param string $aliasKey resource config alias key
     * @param string $extension extension to use. .js and .scss
     * @return File|mixed
     */
    private function checkIfIsMainFile($mainOption, string $plugin, array $resourceConfig, string $aliasKey, string $extension = '.js')
    {
        $key = $plugin . $aliasKey;
        if (isset($this->mainFileHandlers[$key])) {
            return $this->mainFileHandlers[$key];
        }

        if ($mainOption) {
            if ($mainOption === true) {
                // use pluginName.js to compare
                $file = Inflector::dasherize($aliasKey . 'Main') . $extension;
            } elseif ($mainOption != '') {
                // user have set custom name
                $file = $mainOption;
            }

            $mainFile = new \SplFileInfo($this->getStartPath($plugin) . $resourceConfig['aliasPath'] . DS . $file . $extension);
            if ($mainFile->getRealPath()) {
                $this->mainFileHandlers[$key] = $mainFile;

                return $mainFile;
            }
        }
    }

    /**
     * Get start path for resource.
     *
     * @param string $plugin if empty then main app path is used else plugin path
     * @return string Return start path
     */
    private function getStartPath(string $plugin): string
    {
        if (!empty($plugin)) {
            $startPath = Plugin::path($plugin) . DS;
        } else {
            // it's main app
            $startPath = APP;
        }

        return $startPath;
    }

    /**
     * Set resource file as main file for resource config option
     *
     * @param string $plugin plugin name
     * @param string $aliasKey alias key from resource config
     * @param \SplFileInfo $file file resource
     * @return void
     */
    private function setMainFile(string $plugin, string $aliasKey, \SplFileInfo $file): void
    {
        $key = $this->getResourceKey($plugin, $aliasKey) . '-main';
        $this->mainFiles[$key] = $file->getRealPath();
    }

    /**
     * Remove duplicated slashes from path
     *
     * @param string $path path to sanitize
     * @return string sanitized path
     */
    private function removeMultipleSlashesFromPath(string $path): string
    {
        return preg_replace('~/+~', DS, $path);
    }

    /**
     * Compare file paths
     *
     * @param string $file1 file path
     * @param string $file2 file path
     * @return bool
     */
    private function compareFilePaths(string $file1, string $file2): bool
    {
        return $this->removeMultipleSlashesFromPath($file1) == $this->removeMultipleSlashesFromPath($file2);
    }

    /**
     * Get resource key
     *
     * @param string $plugin plugin name
     * @param string $aliasKey alias key from resource config
     * @return string
     */
    private function getResourceKey(string $plugin, string $aliasKey): string
    {
        return strtolower($plugin) . '-' . Inflector::dasherize($aliasKey);
    }

    /**
     * Get alias key
     *
     * @param string $plugin plugin name
     * @param string $aliasKey alias key from resource config
     * @return string
     */
    private function getAliasKey(string $plugin, string $aliasKey): string
    {
        return strtolower($plugin) . Inflector::camelize($aliasKey);
    }
}
