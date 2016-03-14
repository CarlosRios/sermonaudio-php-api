<?php

require_once( '../../SermonAudioAPI.php' );

$api = new SermonAudioAPI;
$api->setApiKey( 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX' );

if( isset( $_GET['type'] ) && $_GET['type'] == 'get-speakers' ) {

	$response = $api->getSpeakers();

} elseif ( isset( $_GET['type'] ) && $_GET['type'] == 'get-sermons' ) {

	$args = array(
		'page'				=> 1,
		'sermons_per_page'	=> 25,
	);

	if( isset( $_GET['page'] ) ) {
		$args['page'] = $_GET['page'];
	}

	if( isset( $_GET['speaker'] ) ) {
		$args['speaker'] = $_GET['speaker'];
	}

	if( isset( $_GET['series'] ) ) {
		$args['series'] = $_GET['series'];
	}

	$response = $api->getSermons( $args );

} else {

	throw new Exception( "Error Processing Request", 400 );
	die;

}

// Echo the json
echo json_encode( $response );