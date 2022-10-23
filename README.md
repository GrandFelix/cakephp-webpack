# Webpack plugin for CakePHP

Collect all .js, .scss (you can configure extensions) files and collect them in json file. Webpack consumes json from this file and compile it, minifiy it....

## Requirements

* PHP >= 7.4
* CakePHP >= 4.0
* Webpack >= 4.0 (you can use older webpack, but you must configure (webpack.config.js) it on your own).

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require grandfelix/cakephp-webpack
```

**Load plugin**

Add next line to Application.php
```
$this->addPlugin('GrandFelix/Webpack', ['bootstrap' => true, 'routes' => false]);
```

**Install webpack config files**
```
./bin/cake webpack install
```

To install webpack go to app root folder and run next command:

**yarn**
```
yarn
```

**npm**
```
npm install
```

**Install webpack globally!** `yarn global add webpack-cli` or `npm -g install webpack-cli`

## Usage

In each of your plugin conf folder (where you want to use it) create webpack.config.php with next config:

```php
return [
  'js' => [ // this is alias key!
    'aliasPath' => 'webroot/js', // relative to plugin or main app path
    'resources' => [ // all paths here are relative to aliasPath
      'path/to/somefile.js',
      '/', // this will take all files from aliasPath
    ]
  ],
  'styles' => [ // alias key
    'aliasPath' => 'webroot/styles', // relative to plugin or main app path
    'resources' => [ // all paths here are relative to aliasPath
      'path/to/somefile.scss', // you can also use js... webpack will compile js files and scss files and move them where they should be after compilation
      '/', // this will take all files from aliasPath
    ]
  ]
];
```

You can add as many entry points as you want.

Run next shell command:

```
./bin/cake webpack reload
```

This command will create webpack.config.json in app root dir so webpack caa use it!

In root of app run next command for development build

**--watch** option is optional

```
./bin/cake webpack build --watch
```

**Production build**
```
./bin/cake webpack build --production
```

In your view files use HtmlHelper to include generated files as you need

### Clean source map files

In production builds we clean up *.map files. You can disable this behavior with `Webpack.clean_before_build => false` config option in your App config (we don't recommend it!).

## Config
````php
return [
    'Webpack' => [
        'resources' => [
            'fileExtensionsToSearch' => ['js', 'scss'] // search for file extensions
        ],
        'clean_before_build' => true, // clean *.map files in production build
        'clean_dirs' => [ // folders to clean up in production build 
            WWW_ROOT . 'js/*.map',
            WWW_ROOT . 'css/*.map',
        ]
    ]
];
````

You can override those config options in your main App config.

### Alias key
#### Alias key at compile time
Is used to name file for compilation. In this ^^ example webpack will for js resource create concatenated file in main app webroot, like: APP/webroot/js/plugin-name-js.js and for styles will create APP/webroot/css/plugin-name-styles.scss

#### Alias key for importing/requiring files
Alias key can also be used for importing/requring files. Alias for importing is like pluginNameAliasKey so you can use in js like

```js
import something from 'pluginNameAliasKey/path_to/some_file'
```

instead of using full paths which is painful. Paths are relative to aliasPath from resource config.



