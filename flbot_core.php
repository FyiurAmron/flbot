<?php

parse_str( implode( '&', array_slice( $argv, 1 ) ), $_GET );

include 'curl.php';
$curl = curlSetup( curl_init(), 'cookies.txt' );

include 'flbot_lib.php';

flLogin( $curl );

$myself = flGetMyself();

$actionsLeft = flGetActionsLeft( $myself );
echo formatActions( $actionsLeft );

while( $actionsLeft > $actionsLeftMin ) {
    for( $i = 1; $i <= $actionSequenceCount; $i++ ) {
        $action = $actionSequence[$i];
        switch( $action['type'] ) {
            case 'storyletBegin':
                $resp = flStoryletBegin( $action['id'] );
                break;
            case 'branchChoice':
                $resp = flBranchChoice( $action['id'] );
                break;
        }
    }

    $actionsLeft = flGetActionsLeft( $resp );
    echo formatActions( $actionsLeft );
}

curl_close( $curl );
