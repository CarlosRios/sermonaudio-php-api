<?php
/**
 * SermonAudioAPI connects to the sermonaudio.com json api via PHP
 *
 * @author  Carlos Rios
 * @package  SermonAudioAPI
 * @version  0.2.1
 */

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
	 * Sermon Audio request route url
	 *
	 * @access public
	 * @since  0.2
	 * 
	 * @var string
	 */
	public $request_route;

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
	public function setApiKey( $key )
	{
		$this->api_key = $key;
		return $this;
	}

	/**
	 * Sanitizes strings
	 *
	 * @access public
	 * @since  0.2
	 * 
	 * @param  string $data - a string to sanitize
	 * @return string
	 */
	public function sanitize_string( $data )
	{
		$esc_data = htmlentities( $data, ENT_QUOTES );
		return $esc_data;
	}

	/**
	 * Builds out the sermons api route and stores it in the class
	 *
	 * @access public
	 * @since  0.1
	 * 
	 * @param  array  $args - a list of allowed variables to query for.
	 * @return array | false
	 */
	public function sermonsApiRoute( $args = array() )
	{
		// Stores the request variables that will be added to the API request
		$vars = array();

		// Sets the speaker name and the category as speaker
		if( isset( $args['speaker'] ) ) {
			$vars['category'] = 'speaker';
			$vars['item'] = $this->sanitize_string( $args['speaker'] );
		}

		// Sets the event as the category
		if( isset( $args['event'] ) ) {
			$vars['category'] = 'eventtype';
			$vars['item'] = $this->sanitize_string( $args['event'] );
		}

		// Sets the series as the category
		if( isset( $args['series'] ) ) {
			$vars['category'] = 'series';
			$vars['item'] = $this->sanitize_string( $args['series'] );
		}

		// Sets the page number
		if( isset( $args['page'] ) ) {
			$vars['page'] = $this->sanitize_string( $args['page'] );
		}

		// The amount of sermons to get per page
		if( isset( $args['sermons_per_page'] ) ) {
			$vars['pagesize'] = abs( $args['sermons_per_page'] );
		}

		// Gets the sermons by the year
		if( isset( $args['year'] ) ) {
			$vars['year'] = abs( $args['year'] );
		}

		// Build the request and return the route
		return $this->buildRequestRoute( 'saweb_get_sermons.aspx', $vars  );
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
	public function getSermons( $args )
	{
		$this->sermonsApiRoute( $args );
		$data = $this->requestData();

		// Split the data into chunks
		if( isset( $args['chunks'] ) && !empty( $data ) && is_array( $data ) ) {
			$data = array_chunk( $data, abs( $args['chunks'] ) );
		}

		return $data;
	}

	/**
	 * Builds out the speakers api route and stores it in the class
	 *
	 * @access public
	 * @since  0.2
	 * 
	 * @return string
	 */
	public function speakersApiRoute()
	{
		return $this->buildRequestRoute( 'saweb_get_speakers.aspx' );
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
		$this->speakersApiRoute();
		$data = $this->requestData();
		return $data;
	}

	/**
	 * Builds out the events api route and stores it in the class
	 *
	 * @access public
	 * @since  0.2
	 * 
	 * @return string
	 */
	public function eventsApiRoute()
	{
		return $this->buildRequestRoute( 'saweb_get_eventtypes.aspx' );
	}

	/**
	 * Returns a list of objects for the events in this church
	 *
	 * @access public
	 * @since  0.1
	 * 
	 * @return array | false
	 */
	public function getEvents()
	{
		$this->eventsApiRoute();
		$data = $this->requestData();
		return $data;
	}

	/**
	 * Builds out the languages api route and stores it in the class
	 *
	 * @access public
	 * @since  0.2
	 * 
	 * @return array | false
	 */
	public function languagesApiRoute()
	{
		return $this->buildRequestRoute( 'saweb_get_languages.aspx' );
	}

	/**
	 * Returns a list of objects for the languages in this church
	 *
	 * @access public
	 * @since  0.1
	 * 
	 * @return array | false
	 */
	public function getLanguages()
	{
		$this->languagesApiRoute();
		$data = $this->requestData();
		return $data;
	}

	/**
	 * Builds out the total sermons api route and stores it in the class
	 *
	 * @access public
	 * @since  0.2.1
	 *
	 * @param  array $args - arguments accepted in sermonsApiRoute
	 * @see    sermonsApiRoute
	 * @return int | false
	 */
	public function totalSermonsApiRoute( $args = array() )
	{
		$args['page'] = 'total';
		return $this->sermonsApiRoute( $args );
	}

	/**
	 * Helper function that returns the total amount of sermons
	 * for a speaker, year, eventtype, or series
	 *
	 * @access public
	 * @since  0.2.1
	 * 
	 * @param  array $args - arguments accepted in sermonsApiRoute
	 * @see    sermonsApiRoute
	 * @return int | false
	 */
	public function getTotalSermons( $args = array() )
	{
		$this->totalSermonsApiRoute( $args );
		$sermons = $this->requestData();
		return !empty( $sermons->total ) ? abs( $sermons->total ) : false;
	}

	/**
	 * Builds the request route and stores it in the object
	 *
	 * @access private
	 * @since  0.2
	 *
	 * @param  string  $request_endpoint  The endpoint for the API request
	 * @param  array   $api_request_vars  Extra variables to add to the request
	 * @return string [description]
	 */
	private function buildRequestRoute( $request_endpoint, $api_request_vars = array() )
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
		return $this->request_route = sprintf( $this->base_api_url, $request_endpoint, $this->api_key, $api_request_vars );
	}

	/**
	 * Handy method that makes the request for data from the Sermon Audio API
	 *
	 * @access private
	 * @since  0.1
	 * 
	 * @return false | array
	 */
	private function requestData()
	{
		if( !empty( $this->request_route ) && is_string( $this->request_route ) ) {
			// Get the contents and convert them to PHP useable objects
			$contents = json_decode( file_get_contents( $this->request_route ) );

			// Return the contents of the request
			return $contents;
		}

		return false;
	}

}
