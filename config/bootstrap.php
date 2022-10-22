<?php

use \Cake\Core\Configure;

$isCli = PHP_SAPI === 'cli';

if ($isCli) {
    Configure::load('Webpack.config');
//    Configure::load('GrandFelix/Webpack.webpack_main');
}
