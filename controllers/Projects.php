<?php

require 'libs/Database.php';

class Projects {

	/**
	 * @var PDO
	 */
	protected $db;

	public __construct()
	{
		$db = new Database;

		$this->db = $db->getConnection();
	}


}