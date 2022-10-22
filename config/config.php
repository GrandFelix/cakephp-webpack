<?php

return [
    'Webpack' => [
        'resources' => [
            'fileExtensionsToSearch' => ['js', 'scss']
        ],
        'clean_before_build' => true,
        'clean_dirs' => [
            WWW_ROOT . 'js/*.map',
            WWW_ROOT . 'css/*.map',
        ]
    ]
];
