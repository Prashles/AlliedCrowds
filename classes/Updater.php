<?php

require __DIR__ . '/../libs/Database.php';

class Updater {

	/**
	 * @var PDO
	 */
	protected $db;

	/**
	 * @var object
	 */
	protected $lastRequest;

	/**
	 * @var Api
	 */
	protected $api;

	/**
	 * @var  integer
	 */
	protected $actions;

	/**
	 * @var  object
	 */
	protected $currentRequest;

	/**
	 * @var integer
	 */
	protected $startTime;


	/**
	 * @param Api $api
	 */
	public function __construct(Api $api)
	{
		$this->db          = (new Database)->getConnection();
		$this->lastRequest = $this->db->query('SELECT * FROM requests ORDER BY id DESC LIMIT 1')->fetchObject();
		$this->api         = new $api;
		$this->actions     = 0;
		$this->startTime   = time();
	}

	/**
	 * Start loop to retrieve/update projects
	 * 
	 * @return void
	 */
	public function run()
	{
		// Max run time for the script is X seconds, to ensure it doesn't run over another instance
		while (time() < $this->startTime + 10) {

			// Way to access $this inside of anonymous function
			$that = $this;
			$this->rate(function() use ($that)
			{
				// Set id to start from, 0 if no more 
				$id = (!$that->lastRequest || !$that->lastRequest->has_next) ? 0 : $that->lastRequest->next_project_id;

				// Get projects from API
				$projects = $that->api->getProjects($id);

				// Loop through all projects
				foreach ($projects->projects->project as $project) {
					$insert = $that->db->prepare('INSERT INTO projects
						(api_id, title, summary, organisation_name, country, url, image_url,
						 active, last_updated )
						VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
						ON DUPLICATE KEY UPDATE
						title = ?, summary = ?, organisation_name = ?, country = ?, url = ?, image_url = ?,
						active = ?, last_updated = NOW()');

					$insert->execute([$project->id, $project->title, $project->summary,
						$project->organization->name, $project->country, $project->organization->url,
						$project->imageLink, (int) $project->active, $project->title,
						$project->summary, $project->organization->name, $project->country,
						$project->organization->url, $project->imageLink, (int) $project->active]);
				}
				// Update the last request
				$that->lastRequest->has_next = $projects->projects->hasNext;
				$that->lastRequest->next_project_id = $projects->projects->nextProjectId;

				return $projects->projects;
			}, Api::RATE_LIMIT);
		}

		$this->saveRequest();
	}

	/**
	 * Ensure the rate limit from the API isn't met
	 * 
	 * @param  Closure $method
	 * @param  integer $rateLimit
	 * @return void
	 */
	protected function rate(Closure $method, $rateLimit)
	{
		// Check if rate limited
		if ($rateLimit === 0 || $this->actions < $rateLimit) {
			$this->currentRequest = $method();
			$this->actions++;
		}
		else {
			$this->saveRequest();

			exit;
		}

	}

	/**
	 * Save request 
	 * 
	 * @return void
	 */
	protected function saveRequest()
	{
		if ($this->db->query('SELECT id FROM requests')->rowCount() >= 100) {
			$this->db->query('DELETE FROM requests');
		}
			
		$insert = $this->db->prepare('INSERT INTO requests (next_project_id, has_next) VALUES (?, ?)');
		$insert->execute([$this->currentRequest->nextProjectId, ($this->currentRequest->hasNext == false) ? 0 : 1]);
	}

}