#!/usr/bin/env php
<?php

$jsonCfgFile = 'bot.json';

//$cfg = json_decode( file_get_contents( $jsonCfgFile ) );

$flUrl = 'fallenlondon.storynexus.com';

$loginPostData = [
    'emailAddress' => 'example@example.com',
    'password' => 'foobar',
    'rememberMe' => 'true',
];

$actionSequence = [
    '1' => [
        'type' => 'storyletBegin',
        'id'   => '11000'
    ],
    '2' => [
        'type' => 'branchChoice',
        'id'   => '4612'
    ]
];

$actionsLeftMin = 18;

$cfg = [];
$cfg['flUrl'] = $flUrl;
$cfg['loginPostData'] = $loginPostData;
$cfg['actionsLeftMin'] = $actionsLeftMin;
$cfg['actionSequence'] = $actionSequence;

umask( 0002 );
file_put_contents( $jsonCfgFile, json_encode( $cfg, JSON_PRETTY_PRINT ) );

//$cfg = json_decode( file_get_contents( $jsonCfgFile ), true );
//var_dump( $cfg );