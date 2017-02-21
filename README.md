# Webpack plugin for CakePHP

Collect all .js, .scss (you can configure extensions) files and collect them in json file. Webpack consumes json from this from this file and compile it, minifiy it....

## Requirements

* PHP >= 5.6
* CakePHP >= 3.2.9
* Webpack >= 2 (you can use webpack 1 but you must configure it on your own).

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require grandfelix/cakephp-webpack
```

For now, copy webpack.config.default.js and webpack.packages.json from Webpack plugin to your main App folder (not ./src) and rename it to webpack.config.js and packages.json. If you already have packages.json file than add webpack dependencies to it...

To install webpack go to app root folder and run next command:
With yarn (better, faster option)
```
yarn
```

With npm
```
npm install
```

## Usage

In each your plugin conf folder (where you want to use this) create webpack.config.php with next options:

```php
return [
  'js' => [ // this is alias key!
    'aliasPath' => 'webroot/js' // relative to plugin or main app path
    'resources' => [ // all paths here are relative to aliasPath
      'path/to/somefile.js',
      '/', // this will take all files from aliasPath
    ],
    'useMainJs' => true, // you can specify own filename
    'useMainCss' => true // you can specify own filename
  ],
  'styles' => [
    'aliasPath' => 'webroot/styles' // relative to plugin or main app path
    'resources' => [ // all paths here are relative to aliasPath
      'path/to/somefile.scc', // you can also use js... webpack will compile js files and scss files and move them where they should be after compilation
      '/', // this will take all files from aliasPath
    ]
  ]
];
```
### Alias key
#### compile time
Is used to name file for compilation. In this ^^ example webpack will for js resource create concatenated file in main app webroot, like: APP/webroot/js/plugin-name-js.js and for styles will create APP/webroot/css/plugin-name-styles.scss

#### Importing requring files
Alias key can also be used for importing/requring files. Alias for importing is like pluginNameAliasKey so you can use in js like impor something from 'pluginNameAliasKey/path_to/some_file' instead of using full paths which is painful

#### Starting point files mainJs mainCss
useMainJs and useMainCss option is used to specify which file is starting point for one section in config. If it's true than will be named in webroot as pluginname-aliiaskey-main.extension. If you specify your custom name then this cusotm name will e used. File will be removed from resources array and added as own entry point. It's like index.js...

Run next shell command:

```
./bin/cake webpack reload
```

This command will cerate webpack.config.json in app root dir so webpack caa use it!

In your view files use HtmlHelper to include generate files as you need


More instructions will come..

## TODO

- [ ] Add Component and Helper to automatically load generated files for plugin
- [ ] Write tests

