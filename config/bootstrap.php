<?php

use \Cake\Core\Configure;

$isCli = PHP_SAPI === 'cli';

if ($isCli) {
    Configure::load('GrandFelix/Webpack.config');
}
