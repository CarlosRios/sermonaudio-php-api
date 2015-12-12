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
	private $base_api_url = 'https://www.sermonaudio.com/api/%1$s?apikey=%2$s%3$s';

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
	 * Formats the string spaces into url readable characters
	 * 
	 * @param  string $string the string to format
	 * @return string the formatted string
	 */
	public function formatStringSpaces( $string )
	{
		$string = str_replace( ' ', '%20', $string );
		return $string;
	}

	/**
	 * Prepares the array variables for the API request
	 * by converting the array of variables into a string
	 * 
	 * @param  array  $variables an array of variables that will be submitted with the request
	 * @return string The prepared request variables
	 */
	public function prepareRequestVariables( $variables = array() )
	{
		$vars_string_array = array();
		$first_key = key( $variables );

		foreach( $variables as $key => $value ) {
			$format = '%1$s=%2$s';

			// Add an ampersand to the first element in the request variables
			if( $key == $first_key ) {
				$format = '&%1$s=%2$s';
			}

			// Store the keys and values in a string
			$vars_string_array[] = sprintf( $format, $key, $value );
		}

		// Return the request variables as a string
		return implode( '&', $vars_string_array );
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

			// Sets the item and replaces the spaces with url space code
			$vars['item'] = $this->formatStringSpaces( $speaker );
		}

		if( ! empty( $page ) ) {
			$vars['page'] = $page;
		}

		if( ! empty( $count ) && is_int( $count ) ) {
			$vars['pagesize'] = $count;
		}

		// Implode the variables into an 'ampersand' separated string
		$vars = $this->prepareRequestVariables( $vars );

		// Return the requested sermons
		return $this->getData( 'saweb_get_sermons.aspx', $vars  );
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

		// Stop if no sermons are returned from the API
		if( $api_sermons == false || empty( $api_sermons ) ) {
			return;
		}

		// Convert the array into sermon objects
		$sermons = array_map( array( $this, 'makeSermon' ), $api_sermons ); 

		// Split the sermons into array chunks
		if( $chunks ) {
			$sermons = array_chunk( $sermons, $chunks );
		}

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
		$sermon = new \SermonAudioAPI\Sermon;
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
	 * @param  string  $request_vars      Extra variables to add to the request
	 * @return false | array
	 */
	private function getData( $request_endpoint, $request_vars = '' )
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

		$api_route = sprintf( $this->base_api_url, $request_endpoint, $this->api_key, $request_vars );
		$contents = file_get_contents( $api_route );
		return json_decode( $contents );
	}

}