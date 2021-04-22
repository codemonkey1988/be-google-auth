<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Backend Google Authorization',
    'description' => 'Google oAuth2 sign in for backend users.',
    'category' => 'services',
    'author' => 'Tim Schreiner',
    'author_email' => 'schreiner.tim@gmail.com',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '0.3.3',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
