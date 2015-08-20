<?php

class Database {

	/**
	 * @var  PDO
	 */

	private $connection;

	public function __construct()
	{
		try {
		    $db = new PDO('mysql:host=localhost;dbname=ac;charset=utf8', 'root', 'root', [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

		    $this->connection = $db;

		} catch (PDOException $e) {
		    throw new exception('Could not connect to database');
		}
	}

	/**
	 * Retrieve the PDO instance
	 *
	 * @return PDO
	 */
	public fuction getConnection()
	{
		return $this->connection;
	}

}