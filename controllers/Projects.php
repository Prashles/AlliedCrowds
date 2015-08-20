<?php

require 'libs/Database.php';

class Projects {

	/**
	 * @var PDO
	 */
	protected $db;

	public __construct()
	{
		$this->db = (new Database)->getConnection();
	}


}