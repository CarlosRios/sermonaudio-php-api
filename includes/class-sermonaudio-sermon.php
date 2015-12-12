<?php
/**
 * Helper object to tranform sermon data
 * 
 * @author  Carlos Rios
 * @package  SermonAudioAPI\Sermon
 * @version  0.1
 */

namespace SermonAudioAPI;

class Sermon {

	/**
	 * Title
	 * @var string
	 */
	private $title;

	/**
	 * The name of the speaker
	 * @var string
	 */
	private $speaker;

	/**
	 * The type of event
	 * @var string
	 */
	private $eventtype;

	/**
	 * Sets the data in the class
	 * 
	 * @param array $data An array of PHP objects
	 */
	public function set( $data )
	{
		$this->speaker = $data->speaker;
		$this->eventtype = $data->eventtype;
		$this->title = $data->title;
	}

	/**
	 * Return the name of the speaker
	 * 
	 * @return string
	 */
	public function getSpeaker()
	{
		return $this->speaker;
	}

}