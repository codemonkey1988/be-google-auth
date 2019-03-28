<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Backend Google Authorization',
    'description' => 'Google oAuth2 sign in for backend users.',
    'category' => 'services',
    'author' => 'Tim Schreiner',
    'author_email' => 'schreiner.tim@gmail.com',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '0.2.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
