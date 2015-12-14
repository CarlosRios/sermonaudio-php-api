<?php
/**
 * SermonAudio API for PHP
 *
 * @author  Carlos Rios
 * @package  SermonAudioAPI
 * @version  0.1
 */

namespace SermonAudioAPI;

class SermonAudioAPI {

	/**
	 * API key provided by Sermon Audio
	 * 
	 * @var string
	 */
	private $api_key;

	/**
	 * Sermon Audio's API URL
	 * 
	 * @var string
	 */
	private $base_api_url = 'https://www.sermonaudio.com/api/%1$s?apikey=%2$s&%3$s';

	/**
	 * Call the construct for safety
	 */
	public function __construct()
	{
		include_once( __DIR__ . '/includes/class-sermonaudio-sermon.php' );
	}

	/**
	 * Sets the api key in the class
	 *
	 * @param  string $key API key provided by Sermon Audio
	 * @return object
	 */
	public function setKey( $key )
	{
		$this->api_key = $key;

		return $this;
	}

	/**
	 * Returns the sermons allowed for this API Key
	 *
	 * @param  string  $speaker Specific speaker for this API Key
	 * @param  integer $page The page number we're getting the sermons from
	 * @param  integer $count Number of sermons to request, max allowed is 100
	 * @return array | false
	 */
	public function getSermons( $speaker = null, $page = 1, $count = 100 )
	{
		// Stores the request variables that will be added to the API request
		$vars = array();

		if( ! is_null( $speaker ) && ! empty( $speaker ) ) {
			// Sets the speaker name and the category of speaker
			$vars['category'] = 'speaker';

			// Sets the item as the speaker name
			$vars['item'] = $speaker;
		}

		if( ! empty( $page ) ) {
			$vars['page'] = $page;
		}

		if( ! empty( $count ) && is_int( $count ) ) {
			$vars['pagesize'] = $count;
		}

		// Make the request and store the data
		$data = $this->getData( 'saweb_get_sermons.aspx', $vars  );

		// Return the data from the request
		return $data;
	}

	/**
	 * Returns a list of sermons with the Sermon
	 * 
	 * @param  string  $speaker name of the speaker
	 * @param  integer $page    page number to get
	 * @param  integer $count   number of sermons to get
	 * @param  integer $chunks  number o
	 * @return array | false    Returns an array of sermons
	 */
	public function getSermonsWithHelper( $speaker = null, $page = 1, $count = 100, $chunks = false )
	{
		// Get the sermons from the API
		$api_sermons = $this->getSermons( $speaker, $page, $count );

		// Convert the array into sermon objects
		$sermons = array_map( array( $this, 'makeSermon' ), $api_sermons ); 

		// Split the sermons into array chunks
		if( $chunks ) {
			$sermons = array_chunk( $sermons, $chunks );
		}

		// Return the sermons
		return $sermons;
	}

	/**
	 * Attaches the sermon data to the Sermon class
	 * 
	 * @param  array $data - the data for the sermon provided by the API
	 * @return object SermonAudioAPI\Sermon
	 */
	private function makeSermon( $data )
	{
		$sermon = new Sermon;
		$sermon->set( $data );
		return $sermon;
	}

	/**
	 * Returns a list objects of the speakers for this church
	 * 
	 * @return array | false
	 */
	public function getSpeakers()
	{
		return $this->getData( 'saweb_get_speakers.aspx' );
	}

	/**
	 * Returns the total number of sermons for the speaker
	 * 
	 * @param  string $speaker the name of the speaker
	 * @return int | false
	 */
	public function getTotal( $speaker )
	{
		$sermons = $this->getSermons( $speaker, 'total' );
		return abs( $sermons->total );
	}

	/**
	 * Returns the data from the Sermon Audio API
	 *
	 * @param  string  $request_endpoint  The endpoint for the API request
	 * @param  array   $api_request_vars  Extra variables to add to the request
	 * @return false | array
	 */
	private function getData( $request_endpoint, $api_request_vars = array() )
	{
		// Make sure api key is set first
		if( ! isset( $this->api_key ) || empty( $this->api_key ) ) {
			throw new Exception( 'API key is required. No API key has been set.', 1);
			return false;
		}

		// Request endpoint must be a string
		if( ! is_string( $request_endpoint ) ){
			return false;
		}

		// Setup the variables for the request
		$api_request_vars = http_build_query( $api_request_vars );

		// Setup the entire request url
		$api_route_request = sprintf( $this->base_api_url, $request_endpoint, $this->api_key, $api_request_vars );

		// Get the contents and convert them to PHP useable objects
		$contents = json_decode( file_get_contents( $api_route_request ) );

		// Return the contents of the request
		return $contents;
	}

}