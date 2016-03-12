<?php
/**
 * Gets all the sermons for this church
 *
 * @author     Carlos Rios
 * @package    SermonAudioAPI
 * @subpackage Examples / All Sermons
 */

// Include the api
include_once( '../SermonAudioAPI.php' );

// Connect to the api
$sa_api = new SermonAudioAPI;
$sa_api->setApiKey( 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX' );

// Get the total number of sermons and create sermons array
$total_sermons = $sa_api->getTotalSermons();
$sermons = array();

// If there are more than 100 sermons we will need to
// make multiple requests because currently 100 is the most
// sermons allowed per request by Sermon Audio
if( $total_sermons > 100 ){

	// Divide the total by 100
	$total_pages = ( $total_sermons / 100 );

	// Get the data for all the sermons, requires multiple requests
	// depending on how many sermons you have stored in sermon audio.
	// Each request gets 100 sermons.
	for ( $i=0; $i <= $total_pages + 1; $i++ ) { 
		$args = array(
			'page'				=> $i,
			'sermons_per_page'	=> 100,
		);
		$sermons[] = $sa_api->getSermons( $args );
	}

} else {

	// Get the sermons, will only grab up to 100
	$args = array(
		'sermons_per_page'	=> 100,
	);
	$sermons[] = $sa_api->getSermons( $args );

}

// Merge the sermons into one array and return them as json.
if( !empty( $sermons ) ) {

	$all_sermons = array();

	foreach( (array) $sermons as $sermon_set ) {
		$all_sermons = array_merge( array_reverse( $sermon_set ), $all_sermons );
	}

	echo json_encode( $all_sermons );
}

// Exit the script
exit;