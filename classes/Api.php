<?php

class Api {

	/**
	 * @var string
	 */
	private $key = '5251d282-3d26-4afa-ab17-c89c3a9a6ba7';

	/**
	 * @var string
	 */
	protected $baseURL = 'https://api.globalgiving.org/api/';

	/**
	 * Rate limit for the API
	 * 
	 * @var integer
	 */
	private $rateLimit = 0;


	public function getProjects($nextID = false)
	{
		if ($nextID !== false) {

		}

		$op = 'public/projectservice/all/projects';

		$url = $this->baseURL . $op . '?api_key=' . $this->key;

		return $this->request($url);
	}

	/**
	 * Make a request to the API
	 *
	 * @var  string  $operation
	 * @var  string  $url
	 * 
	 * @return array
	 */
	public function request($url)
	{	
		/* TAKEN FROM API DOCS, RECODE IN CURL */

		/*$opts = array(
		  'http'=>array(
		    'method'=>"GET",
		    'header'=>"Accept: application/json\r\n"
		  )
		);
		
		$output = file_get_contents($url, false, $context);
		return  $output;*/

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);

		$output = json_decode(curl_exec($ch));

		if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
			throw new Exception('Could not retrieve data from API');
		} 

		if ($output === null) {
			throw new Exception('Invalid response from API');
		}

		// Successful data transfer
		return $output;

	}



}