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

In each your plugin conf folder add webpack.config.php with next options:

```php
return [
  'js' => [ // this is alias key!
    'aliasPath' => 'webroot/js' // relative to plugin or main app path
    'resources' => [ // all paths here are relative to aliasPath
      'path/to/somefile.js',
      '/', // this will take all files from aliasPath
    ]
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

alias key is used to name file for compilation. In this ^^ example webpack will for js resource create concatenated file in main app webroot, like: APP/webroot/js/plugin-name-js.js and for styles will create APP/webroot/css/plugin-name-styles.scss


More instructions will come..
