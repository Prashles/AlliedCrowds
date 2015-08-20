<?php

class Api {

	/**
	 * Rate limit for API (requests per minute)
	 * 0 = unlimited
	 */
	const RATE_LIMIT = 5;

	/**
	 * @var string
	 */
	private $key = '5251d282-3d26-4afa-ab17-c89c3a9a6ba7';

	/**
	 * @var string
	 */
	protected $baseURL = 'https://api.globalgiving.org/api';

	/**
	 * Get projects
	 * 
	 * @param  int $nextID
	 * @return string
	 */
	public function getProjects($nextID = 0)
	{
		$operation = '/public/projectservice/all/projects/summary'; // NOTE: only retrieving summary for the sample application

		$url = "{$this->baseURL}{$operation}?api_key=$this->key";

		if ($nextID !== 0) {
			$url .= "&nextProjectId={$nextID}";
		}

		return $this->request($url);
	}

	/**
	 * Make a request to the API
	 *
	 * @param  string $url
	 * @return object
	 * @throws Exception
	 */
	public function request($url)
	{	
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

		$output = json_decode(curl_exec($ch));

		// Invalid HTTP code
		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
			throw new Exception('Could not retrieve data from API');
		} 

		// json_decode returns null for JSON that is encoded incorrectly
		if ($output === null) {
			throw new Exception('Invalid response from API');
		}

		// Successful data transfer
		return $output;

	}



}