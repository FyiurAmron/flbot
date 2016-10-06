<?php

$fsStyle = '<link rel="stylesheet" type="text/css" href="http://fallenlondon.storynexus.com/Content/style7.css">';
$fsScript = '<script>var g = 0;</script>';

function flPost( $url, $postDataArray, $outFile ) {
    global $fsStyle, $fsScript, $curl;
    $resp = curlPost( $curl, $url, $postDataArray );
    file_put_contents( $outFile, $fsStyle."\n".$fsScript.$resp );
    return $resp;
}

$jsonCfgFile = 'bot.json';
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
    global $flUrlLogin, $loginPostData;
    return flPost( $flUrlLogin, $loginPostData, 'output_login.html' );
}

function flStoryletBegin( $eventid ) {
    global $flUrlStoryletBegin, $storyletBeginPostData;
    $storyletBeginPostData['eventid'] = $eventid;
    return flPost( $flUrlStoryletBegin, $storyletBeginPostData, 'output_storyletBegin.html' );
}

function flBranchChoice( $branchId ) {
    global $flUrlBranchChoice, $storyletBeginPostData;
    $branchChoicePostData['branchId'] = $branchId;
    return flPost( $flUrlBranchChoice, $branchChoicePostData, 'output_branchChoice.html' );
}

function flGetMyself() {
    global $flUrlMyself;
    return flPost( $flUrlMyself, [], 'output_myself.html' );
}

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

/*
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
