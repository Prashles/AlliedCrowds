<?php

require __DIR__ . '../classes/Api.php';
require __DIR__ . '../libs/Database.php';


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
	protected $currentRequest


	/**
	 * @param Api $api
	 */
	public function __construct(Api $api)
	{
		$this->db          = (new Database)->getConnection();
		$this->lastRequest = $this->db->query('SELECT * FROM requests ORDER BY id DESC LIMIT 1')->fetchObject();
		$this->api         = new $api;
		$this->actions     = 0;
	}


	public function run()
	{
		while (true) {
			$this->rate(function()
			{
				$id = (!$this->lastRequest || $this->last->hasNext) ? $this->lastRequest->nextProjectId : 0;

				$projects = $this->api->getProjects($id);

				foreach ($projects->projects as $project) {
					$insert = $this->db->prepare('INSERT INTO projects (api_id) VALUES (?) ON DUPLICATE KEY UPDATE updated = 1');
					$insert->execute([$project->id]);
				}

				return $projects;
			}, Api::RATE_LIMIT);
		}
	}

	/**
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
			if ($this->db->query('SELECT id FROM requests')->rowCount() >= 100) {
				$this->db->query('DELETE FROM requests');
			}
		}

		$insert = $this->db->prepare('INSERT INTO requests (next_project_id, has_next) VALUES (?, ?)');
		$insert->execute([$this->currentRequest->nextProjectId, ($this->currentRequest->hasNext == false) ? 0 : 1]);

		exit;
	}

}

















