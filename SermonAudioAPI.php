<?php
/**
 * SermonAudio API for PHP
 *
 * @author  Carlos Rios
 * @package  SermonAudioAPI
 * @version  0.2
 */

namespace SermonAudioAPI;

class SermonAudioAPI {

	/**
	 * API key provided by Sermon Audio
	 *
	 * @access private
	 * @since  0.1
	 * 
	 * @var string
	 */
	private $api_key;

	/**
	 * Sermon Audio's API URL
	 *
	 * @access private
	 * @since  0.1
	 * 
	 * @var string
	 */
	private $base_api_url = 'https://www.sermonaudio.com/api/%1$s?apikey=%2$s&%3$s';

	/**
	 * Call the construct for safety
	 *
	 * @access public
	 * @since  0.1
	 */
	public function __construct()
	{
	}

	/**
	 * Sets the api key in the class
	 *
	 * @access public
	 * @since  0.1
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
	 * @access public
	 * @since  0.1
	 * 
	 * @param  array  $args - a list of allowed variables to query for.
	 * @return array | false
	 */
	public function getSermons( $args = array() )
	{
		// Stores the request variables that will be added to the API request
		$vars = array();

		// Sets the speaker name and the category as speaker
		if( isset( $args['speaker'] ) ) {
			$vars['category'] = 'speaker';
			$vars['item'] = $args['speaker'];
		}

		// Sets the event as the category
		if( isset( $args['event'] ) ) {
			$vars['category'] = 'eventtype';
			$vars['item'] = $args['event'];
		}

		// Sets the series as the category
		if( isset( $args['series'] ) ) {
			$vars['category'] = 'series';
			$vars['item'] = $args['series'];
		}

		// Sets the page number
		if( isset( $args['page'] ) ) {
			$vars['page'] = $args['page'];
		}

		// The amount of sermons to get per page
		if( isset( $args['sermons_per_page'] ) ) {
			$vars['pagesize'] = $args['sermons_per_page'];
		}

		// Make the request and store the data
		$data = $this->getData( 'saweb_get_sermons.aspx', $vars  );

		// Split the data into chunks
		if( isset( $args['chunks'] ) && !empty( $data ) && $data !== false ) {
			$data = array_chunk( $data, abs( $args['chunks'] ) );
		}

		// Return the data from the request
		return $data;
	}

	/**
	 * Returns a list objects of the speakers for this church
	 *
	 * @access public
	 * @since  0.1
	 * 
	 * @return array | false
	 */
	public function getSpeakers()
	{
		return $this->getData( 'saweb_get_speakers.aspx' );
	}

	/**
	 * Returns a list of objects for the events in this church
	 *
	 * @access public
	 * @since  0.2
	 * 
	 * @return array | false
	 */
	public function getEvents()
	{
		return $this->getData( 'saweb_get_eventtypes.aspx' );
	}

	/**
	 * Returns a list of objects for the languages in this church
	 *
	 * @access public
	 * @since  0.2
	 * 
	 * @return array | false
	 */
	public function getLanguages()
	{
		return $this->getData( 'saweb_get_languages.aspx' );
	}

	/**
	 * Returns the total number of sermons for the speaker
	 *
	 * @access public
	 * @since  0.1
	 * 
	 * @param  string $speaker the name of the speaker
	 * @return int | false
	 */
	public function getTotal( $speaker )
	{
		$args = array(
			'speaker'	=> $speaker,
			'page'		=> 'total',
		);

		$sermons = $this->getSermons( $args );

		return !empty( $sermons->total ) ? abs( $sermons->total ) : false;
	}

	/**
	 * Returns the data from the Sermon Audio API
	 *
	 * @access private
	 * @since  0.1
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