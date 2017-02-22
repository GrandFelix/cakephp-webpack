<?php
namespace GrandFelix\Webpack\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;

/**
 * ReloadResources shell task
 */
class ReloadTask extends Shell
{
    /**
     * @var array collect main file handlers so we don't create for each file new instance
     */
    private $mainFileHandlers = [];

    /**
     * @var array
     */
    private $mainFiles = [];

    /**
     * main() method.
     *
     * @return void
     */
    public function main()
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
        if ($this->createFile(ROOT . DS . 'webpack.config.json', json_encode([
            'paths' => array_filter($files),
            'aliases' => array_filter($aliasPaths)
        ]))
        ) {
            $this->out("Webpack CakePHP configuration file successfully created!");
        }
    }

    /**
     * Find resource config file
     *
     * @param string $path path to check
     * @return array|null
     * @throws \Exception
     */
    protected function findResourceConfig($path)
    {
        $file = new File($path . DS . 'config' . DS . 'webpack.config.php');

        if ($file->exists()) {
            $config = include $file->path;

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
    protected function getResourceFiles($plugin, array $resourceConfig, $aliasKey)
    {
        $return = [];
        $startPath = $this->getStartPath($plugin);

        if (isset($resourceConfig['resources'])) {
            $pathToResources = (string)$resourceConfig['aliasPath'] ?: 'webroot';
            $mainJs = isset($resourceConfig['mainJs']) ? $resourceConfig['mainJs'] : false;
            $mainCss = isset($resourceConfig['mainCss']) ? $resourceConfig['mainCss'] : false;

            foreach ($resourceConfig['resources'] as $resource) {
                $folderPath = $startPath . DS . $pathToResources . DS . $resource;
                $file = new File($folderPath);

                // check if is file!
                if ($file->exists()) {
                    // check if isset mainJs or mainCss and skip it on true and add it to main files
                    $jsFileToExclude = $this->checkIfIsMainFile($mainJs, $plugin, $resourceConfig, $aliasKey, '.js');
                    if ($jsFileToExclude instanceof File) {
                        if ($this->compareFilePaths($jsFileToExclude->path, $file)) {
                            $this->setMainFile($plugin, $aliasKey, $jsFileToExclude);
                            continue;
                        }
                    }

                    $cssFileToExclude = $this->checkIfIsMainFile($mainCss, $plugin, $resourceConfig, $aliasKey, '.scss');
                    if ($cssFileToExclude instanceof File) {
                        if ($this->compareFilePaths($cssFileToExclude->path, $file)) {
                            $this->setMainFile($plugin, $aliasKey, $cssFileToExclude);
                            continue;
                        }
                    }

                    $return[] = $this->removeMultipleSlashesFromPath($file->path);
                } else {
                    // it looks like this is folder! Get all files from this folder, recursive!
                    $dir = new Folder($folderPath);
                    if ($dir->path !== null) {
                        $filesInPath =
                            $dir->findRecursive('.*\.(' . implode('|', Configure::read('Webpack.resources.fileExtensionsToSearch')) . ')');
                        if (isset($filesInPath)) {
                            foreach ($filesInPath as $file) {
                                // check if isset mainJs or mainCss and skip it on true and add it to main files
                                $jsFileToExclude = $this->checkIfIsMainFile($mainJs, $plugin, $resourceConfig, $aliasKey, '.js');
                                if ($jsFileToExclude instanceof File) {
                                    if ($this->compareFilePaths($jsFileToExclude->path, $file)) {
                                        $this->setMainFile($plugin, $aliasKey, $jsFileToExclude);
                                        continue;
                                    }
                                }

                                $cssFileToExclude = $this->checkIfIsMainFile($mainCss, $plugin, $resourceConfig, $aliasKey, '.scss');
                                if ($cssFileToExclude instanceof File) {
                                    if ($this->compareFilePaths($cssFileToExclude->path, $file)) {
                                        $this->setMainFile($plugin, $aliasKey, $cssFileToExclude);
                                        continue;
                                    }
                                }

                                $return[] = $this->removeMultipleSlashesFromPath($file);
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
    private function getAliasPath($plugin, array $resourceConfig)
    {
        $startPath = $this->getStartPath($plugin);

        if (isset($resourceConfig['aliasPath'])) {
            $dir = new Folder($startPath . $resourceConfig['aliasPath']);

            // Only add to alias path if dir exists!
            if ($dir->path !== null) {
                $return = $dir->path;
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
    private function checkIfIsMainFile($mainOption, $plugin, array $resourceConfig, $aliasKey, $extension = '.js')
    {
        $key = $plugin . $aliasKey.$extension;
        if (isset($this->mainFileHandlers[$key])) {
            return $this->mainFileHandlers[$key];
        }

        if ($mainOption === true || $mainOption != '') {
            if ($mainOption === true) {
                // use pluginName.js to compare
                $file = $this->getResourceKey($plugin, $aliasKey . 'Main') . $extension;
            } elseif ($mainOption != '') {
                // user have set custom name
                $file = $mainOption;
            }

            $mainFile = new File($this->getStartPath($plugin) . $resourceConfig['aliasPath'] . DS . $file);

            if ($mainFile->exists()) {
                $this->mainFileHandlers[$key] = $mainFile;

                return $mainFile;
            }
        }
    }

    /**
     * Get start path for resource.
     *
     * @param string $plugin if empty than main app path is used elese plugin path
     * @return string Return start path
     */
    private function getStartPath($plugin)
    {
        if (!empty($plugin)) {
            $startPath = $pluginPath = Plugin::path($plugin) . DS;
        } else {
            // it's main app
            $startPath = ROOT . DS . APP_DIR . DS;
        }

        return $startPath;
    }

    /**
     * Set resource file as main file for resource config option
     *
     * @param string $plugin plugin name
     * @param string $aliasKey alias key from resource config
     * @param \Cake\Filesystem\File $file file resource
     * @return void
     */
    private function setMainFile($plugin, $aliasKey, File $file)
    {
        $key = $this->getResourceKey($plugin, $aliasKey) . '-main';

        $this->mainFiles[$key] = $file->path;
    }

    /**
     * Remove duplicated slashes from path
     *
     * @param string $path path to sanitize
     * @return string sanitized path
     */
    private function removeMultipleSlashesFromPath($path)
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
    private function compareFilePaths($file1, $file2)
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
    private function getResourceKey($plugin, $aliasKey)
    {
        if($plugin == '') {
            $plugin = Configure::read('App.namespace');
        }

        return strtolower($plugin) . '-' . Inflector::dasherize($aliasKey);
    }

    /**
     * Get alias key
     *
     * @param string $plugin plugin name
     * @param string $aliasKey alias key from resource config
     * @return string
     */
    private function getAliasKey($plugin, $aliasKey)
    {
        if($plugin == '') {
            $plugin = Configure::read('App.namespace');
        }

        return strtolower($plugin) . Inflector::camelize($aliasKey);
    }
}
