<?php

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

function curlPost( $curl, $url, $postDataArray ) {
    curl_setopt( $curl, CURLOPT_POST, true );
    curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $postDataArray ) );
    curl_setopt( $curl, CURLOPT_URL, $url );
    return curl_exec( $curl );
}
