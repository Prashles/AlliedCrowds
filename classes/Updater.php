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
		while (time() < $this->startTime + 10) {
			$that = $this;
			$this->rate(function() use ($that)
			{
				$id = (!$that->lastRequest || !$that->lastRequest->has_next) ? 0 : $that->lastRequest->next_project_id;

				$projects = $that->api->getProjects($id);

				foreach ($projects->projects->project as $project) {

					$insert = $that->db->prepare('INSERT INTO projects_test (api_id) VALUES (?) ON DUPLICATE KEY UPDATE updated = 1');

					$insert->execute([$project->id]);
				}
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

















