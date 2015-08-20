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

	public function display()
	{
		$get = $this->db->query('SELECT * FROM projects LIMIT 9');

		return $get->fetchAll(PDO::FETCH_OBJ);
	}


}