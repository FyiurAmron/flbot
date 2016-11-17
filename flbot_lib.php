<?php

function flPost( $url, $postDataArray, $outFile ) {
    global $curl;
    $resp = curlPost( $curl, $url, $postDataArray );
    umask( 0002 );
    file_put_contents( $outFile, file_get_contents( 'head.html' ).$resp );
    return $resp;
}

$jsonCfgFile = 'bot.json';
$outputDir = 'output/';
$outputFileLogin = $outputDir.'login.html';
$outputFileStoryletBegin = $outputDir.'storyletBegin.html';
$outputFileBranchChoice = $outputDir.'branchChoice.html';
$outputFileMyself = $outputDir.'myself.html';

$cfg = json_decode( file_get_contents( $jsonCfgFile ), true );

$flUrl = $cfg['flUrl'];

$flUrlLogin = $flUrl.'/Auth/EmailLogin';
$flUrlStoryletBegin = $flUrl.'/Storylet/Begin';
$flUrlBranchChoice = $flUrl.'/Storylet/ChooseBranch';
$flUrlMyself = $flUrl.'/Gap/Load?content=/Me'; // not working as intended currently

$loginPostData = $cfg['loginPostData'];
$actionsLeftMin = $cfg['actionsLeftMin'];
$actionSequence = $cfg['actionSequence'];
$actionSequenceCount = count( $actionSequence );

//var_dump($cfg);

$storyletBeginPostData = [];
$branchChoicePostData = [];

function flLogin() {
    global $flUrlLogin, $loginPostData, $outputFileLogin;
    return flPost( $flUrlLogin, $loginPostData, $outputFileLogin );
}

function flStoryletBegin( $eventid ) {
    global $flUrlStoryletBegin, $storyletBeginPostData, $outputFileStoryletBegin;
    $storyletBeginPostData['eventid'] = $eventid;
    return flPost( $flUrlStoryletBegin, $storyletBeginPostData, $outputFileStoryletBegin );}

function flBranchChoice( $branchId ) {
    global $flUrlBranchChoice, $storyletBeginPostData, $outputFileBranchChoice;
    $branchChoicePostData['branchId'] = $branchId;
    return flPost( $flUrlBranchChoice, $branchChoicePostData, $outputFileBranchChoice );
}

function flGetMyself() {
    global $flUrlMyself, $outputFileMyself;
    return flPost( $flUrlMyself, [], $outputFileMyself );
}

/*
$getActionsLeftMatcherParts = [
    '<div class="actions">Actions<span class="actions_remaining"><span id="infoBarCurrentActions">',
    '</span>/',
    '</span></div>',
];

foreach( $getActionsLeftMatcherParts as &$text ) {
    $text = preg_quote( $text );
}

$getActionsLeftMatcher = $getActionsLeftMatcherParts[0].'(\d*)'
                        .$getActionsLeftMatcherParts[1].'(\d*)'
                        .$getActionsLeftMatcherParts[2];

function flGetActionsLeft( $respGap ) {
    preg_match( $getActionsLeftMatcher, $respGap, $matches );
    return ( count( $matches ) !== 0 ) ? $matches[1] : -1;
}
*/

function flGetActionsLeft( $resp ) {
    preg_match(
        '/setActionsLevel\((\d*),\s*20,\s*\'False\'\)\;/',
        $resp,
        $matches
    );
    return ( count( $matches ) !== 0 ) ? $matches[1] : -1;
}

function formatActions( $actionsLeft ) {
    //global $actionsLeftMin;
    //return $actionsLeft.' ('.$actionsLeftMin.' min)'."\n";
    return $actionsLeft." \n";
}
