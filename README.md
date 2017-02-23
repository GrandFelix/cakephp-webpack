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

Load plugin
```
Plugin::load('GrandFelix/Webpack', ['bootstrap' => true, 'routes' => false]);
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

Install webpack globaly! yarn global add webpack or npm -g install webpack

## Usage

In each of your plugin conf folder (where you want to use it) create webpack.config.php with next config:

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
  'styles' => [ // alias key
    'aliasPath' => 'webroot/styles' // relative to plugin or main app path
    'resources' => [ // all paths here are relative to aliasPath
      'path/to/somefile.scc', // you can also use js... webpack will compile js files and scss files and move them where they should be after compilation
      '/', // this will take all files from aliasPath
    ]
  ]
];
```

You can add as many options as you want.

Run next shell command:

```
./bin/cake webpack reload
```

This command will create webpack.config.json in app root dir so webpack caa use it!

In root of app run next command (if you installed it globaly, which is prefered!)

```
webpack --watch
```

In your view files use HtmlHelper to include generated files as you need

### Alias key
#### Alias key at compile time
Is used to name file for compilation. In this ^^ example webpack will for js resource create concatenated file in main app webroot, like: APP/webroot/js/plugin-name-js.js and for styles will create APP/webroot/css/plugin-name-styles.scss

#### Alias key for importing/requiring files
Alias key can also be used for importing/requring files. Alias for importing is like pluginNameAliasKey so you can use in js like 

```js
import something from 'pluginNameAliasKey/path_to/some_file'
``` 

instead of using full paths which is painful. Paths are relative to aliasPath from resource config.

#### Alias key as starting point file when using mainJs or mainCss
useMainJs and useMainCss option is used to specify which file is starting point for one section in config. If it's true than will be named in webroot as pluginname-aliiaskey-main.js and pluginname-aliiaskey-style-main.css. If you specify your custom name then this cusotm name will be used. File will be removed from resources array and added as own entry point. So in this file you can initialize reactjs app etc. 


## TODO

- [ ] Add Component and Helper to automatically load generated files for plugin

