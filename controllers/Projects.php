<?php

require __DIR__ . '/../libs/Database.php';

class Projects {

	/**
	 * @var PDO
	 */
	protected $db;

	public function __construct()
	{
		$this->db = (new Database)->getConnection();
	}

	/**
	 * @param bool|false $active
	 * @return object
	 */
	public function display($active = false)
	{
		if ($active === true) {
			$get = $this->db->query('SELECT * FROM projects WHERE active = 1 LIMIT 9');
		}
		else {
			$get = $this->db->query('SELECT * FROM projects LIMIT 9');
		}


		return $get->fetchAll(PDO::FETCH_OBJ);
	}


}