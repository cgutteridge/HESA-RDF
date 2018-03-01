<?php
$base_dir = __DIR__;

$source_dir = "$base_dir/source_data";
require_once( "$base_dir/arc/ARC2.php" );
require_once( "$base_dir/Graphite/Graphite.php" );

# this function gets either Turtle or RDFXML from the cmd line args
# or else complains
function getFormatFromArgs($args) {
	$format = $args[1];
	if( $format != "Turtle" && $format != "RDFXML" ) {
		die( "Please specify Turtle or RDFXML after the command name\n" );
	}
	return $format;
}

# read a csv file with optional assigned column headings
# get otherwise use first row for headings
function readCSV( $filename, $fields=null ) {
	return readRows( $filename, $fields, "str_getcsv" );
}

function _splitOnTab( $string ) {
	return preg_split( "/\t/", $string );
}

# read a tsv file with optional assigned column headings
# get otherwise use first row for headings
function readTSV( $filename, $fields=null ) {
	return readRows( $filename, $fields, "_splitOnTab" );
}

function readRows( $filename, $fields, $splitFn ) {
	$lines = file($filename);
	if( $fields === null ) {
		$fields = $splitFn( chop( array_shift( $lines ) ));
	}

	$records = array();
	foreach( $lines as $line ) {
		$cells = $splitFn( chop( $line ) );
		$record = array();
		for( $i=0; $i<count($fields); ++$i ) {
			$value = trim( $cells[$i] );
			if( $value == "#N/A" ) { $value = null; }
			$record[$fields[$i]] = $value;
		}
		$records []= $record;
	}
	return $records;
}

##################################################################
# Data field functions
##################################################################

# This function tries to deal with sloppy URLS missing the protocol.
function cleanURL( $url ) {

	if( !preg_match( "/^[a-z]+:/", $url ) ) { 
		$url = "http://$url";
	}
	return $url;
}

function stripWhitespace( $text ) {
	return preg_replace( "/\s+/","", $text );
}

