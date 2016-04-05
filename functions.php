<?php

// helps rewrite fetch url to csv
function key_from_url($url) {
    preg_match( "/\/d\/(.*)\//i", $url, $r );
    if( !is_array($r) ) return( false );
    if( empty($r) ) return( false );
    if( !isset($r[1]) ) return( false );

    return( $r[1] );
}

// read csv file, return as associative array
// with some specialized keys and hints
function get_csv_file($filename) {
    $ret = array();
    $fp = fopen($filename, "rt");
    $header = fgetcsv($fp);

    while( $r = fgetcsv($fp) ) {
        $row = $r;

        // consistent number hint
        $row["__count"] = count($r);

        // associative helper
        if( count($header) === count($r) ) {

            $t = array_combine($header, $r);
            foreach( $t as $k => $v ) {
                $row[$k] = $v;
            }
        }

        $ret[] = $row;
    }
    return( $ret );
}
