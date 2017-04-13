<?php
require( "config.php" );
require( "functions.php" );

if( !isset( $_GET['project']) ) die( "Error: missing project" );
$project = $_GET['project'];

if( !isset( $projects[$project]) ) die( "Error: can't find project in config" );

// PROGRAM_FOLDER requires trailing slash
$filename = PROGRAM_FOLDER . "projects/" . md5($project) . ".csv";

// ==================================
// MODE 1: synchronize data from gdoc
// ==================================
if( isset($_GET['sync']) ) {
    $fetch_url = $projects[$project]["url"];
    $key = key_from_url($fetch_url);
    if( $key === false ) die( "Error: invalid gdoc key in config");

    $actual_fetch_url = sprintf(
        "https://docs.google.com/spreadsheets/d/%s/pub?output=csv",
        $key
    );

    // needs to match setting in gdoc
    if( $_GET['sync'] !== $projects[$project]["key"] ) die( "Invalid key" );

    // everything good, fetch file, save as CSV
    $data = file_get_contents( $actual_fetch_url );

    if( !@file_put_contents( $filename, $data ) ) {
        echo "Error: unable to write to file {$filename}";
        die;
    }

    $bytes = filesize($filename);
    echo "Bytes: " . $bytes;
    die;
}

// ==================================
// MODE 2: read from disk
// ==================================
if( !isset( $_GET['callback']) ) die( "Error: missing callback" );

if( file_exists($filename) ) {
    try {
        $data = get_csv_file($filename);
    } catch( Exception $e ) {
        $data = array();
    }
} else {
    $data = array();
}

if( $_GET['callback'] === "raw_json" ) {
    echo json_encode($data);
} else {
    printf( "%s(%s);", $_GET['callback'], json_encode($data) );
}
