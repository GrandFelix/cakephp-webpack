<?php

use \Cake\Core\Configure;

$isCli = PHP_SAPI === 'cli';

if ($isCli) {
    Configure::load('GrandFelix/Webpack.webpack_main');
    collection((array)Configure::read('Webpack.config'))->each(function ($file) {
        Configure::load($file);
    });
}
