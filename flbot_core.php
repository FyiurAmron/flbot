<?php
/*
function handleHeaderLine( $curl, $header ) {
    static $returnedHeaders = [];
    if ( $curl === null ) {
        return $returnedHeaders;
    }
    // echo $header; // DEBUG
    array_push( $returnedHeaders, $header );
    return strlen( $header );
}
*/
parse_str( implode( '&', array_slice( $argv, 1 ) ), $_GET );

function curlSetup( $curl, $cookieFile ) {
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
    //curl_setopt( $curl, CURLOPT_HEADERFUNCTION, 'handleHeaderLine' );
    //curl_setopt( $curl, CURLOPT_VERBOSE, true );
    //curl_setopt( $curl, CURLOPT_HEADER, true );
    //curl_setopt( $curl, CURLINFO_HEADER_OUT, true );
    curl_setopt( $curl, CURLOPT_COOKIEJAR, $cookieFile );
    curl_setopt( $curl, CURLOPT_COOKIEFILE, $cookieFile );
    return $curl;
}

$curl = curlSetup( curl_init(), 'cookies.txt' );

function curlPost( $curl, $url, $postDataArray ) {
    curl_setopt( $curl, CURLOPT_POST, true );
    curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $postDataArray ) );
    curl_setopt( $curl, CURLOPT_URL, $url );
    return curl_exec( $curl );
}

$fsStyle = '<link rel="stylesheet" type="text/css" href="http://fallenlondon.storynexus.com/Content/style7.css">';
$fsScript = '<script>var g = 0;</script>';

function flPost( $url, $postDataArray, $outFile ) {
    global $fsStyle, $fsScript, $curl;
    $resp = curlPost( $curl, $url, $postDataArray );
    file_put_contents( $outFile, $fsStyle."\n".$fsScript.$resp );
    return $resp;
}

$cfg = json_decode( file_get_contents( $jsonCfgFile ), true );

$flUrl = $cfg['flUrl'];

$flUrlLogin = $flUrl.'/Auth/EmailLogin';
$flUrlStoryletBegin = $flUrl.'/Storylet/Begin';
$flUrlBranchChoice = $flUrl.'/Storylet/ChooseBranch';
$flUrlMyself = $flUrl.'/Gap/Load?content=/Me';

$loginPostData = $cfg['loginPostData'];
$actionSequence = $cfg['actionSequence'];
$actionsLeftMin = $cfg['actionsLeftMin'];
$actionSequenceCount = count( $actionSequence );

$storyletBeginPostData = [
];
$branchChoicePostData = [
];

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
    return flPost( $flUrlMyself, null, 'output_myself.html' ) {
}

$getActionsLeftMatcherParts = [
    '<div class="actions">Actions<span class="actions_remaining"><span id="infoBarCurrentActions">',
    '</span>/',
    '</span></div>',
]

foreach( $getActionsLeftMatcherParts as &$text ) {
    $text = preg_quote( $text );
}

$getActionsLeftMatcher = $getActionsLeftMatcherParts[0].'(\d*)'
                        .$getActionsLeftMatcherParts[1].'(\d*)'
                        .$getActionsLeftMatcherParts[2];

function flGetActionsLeft( $respMyself ) {
    preg_match( $getActionsLeftMatcher, $respMyself, $matches );
    return ( count( $matches ) !== 0 ) ? $matches[1] : -1;
}

function flGetActionsLeftFromResp( $resp ) {
    preg_match(
        '/setActionsLevel\((\d*),20, \'False\'\)\;/',
        $resp,
        $matches
    );
    return ( count( $matches ) !== 0 ) ? $matches[1] : -1;
}

// BOT BEGIN

flLogin( $curl );
$myself = flGetMyself();
$actionsLeft = flGetActionsLeft( $myself );

while( $actionsLeft > $actionsLeftMin ) {
    for( $i = 1; $i <= $actionSequenceCount; i++ ) {
        $resp = 
    }
    flStoryletBegin( '11000' );
    //$resp = flBranchChoice( '4880' );
    $resp = flBranchChoice( '4612' );

    $actionsLeft = flGetActionsLeftFromResp( $resp );
    echo $actionsLeft, "\n";
}

//file_put_contents( 'headers.txt', implode( handleHeaderLine( null, null ) ) );

// BOT END

curl_close( $curl );
