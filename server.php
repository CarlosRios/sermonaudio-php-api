<?php

require_once( 'SermonAudioAPI.php' );

$api = new SermonAudioAPI\SermonAudioAPI;
$api->setKey( 'EF1D0D28-DBF2-4DFF-AF01-FFC3C7D2BCE0' );

if( isset( $_GET['type'] ) && $_GET['type'] == 'get-speakers') {

	$response = $api->getSpeakers();

} elseif ( isset( $_GET['type'] ) && $_GET['type'] == 'get-sermons' ) {
	
	$speaker = $_GET[ 'speaker' ];
	$response = $api->getSermons( $speaker, 1, 12 );

} else {

	throw new Exception( "Error Processing Request", 400 );
	die;

}

// Echo the json
echo json_encode( $response );