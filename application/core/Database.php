<?php

class Database extends PDO {

	public function __construct() {
	
	}

	public function useDB() {

		// Establish connection
		$connection = new MongoDB\Client("mongodb://" . DB_USER . ":" . DB_PASSWORD . "@" . DB_HOST . ":" . DB_PORT . "/" . DB_NAME);

		// Select db
		$db = $connection->{DB_NAME};
		return $db;
	}

	public function createCollection($db, $collectionName) {

		// Drop collection if exists
		$db->dropCollection($collectionName);

		// Create Collection
		$db->createCollection($collectionName);

		// Select collection
		$collection = $this->selectCollection($db, $collectionName);

		//Create fulltext index on every field
		$collection->createIndex(['$**' => 'text'], [ 'language_override' => "mylanguage" ]);

		return $collection;
	}

	public function selectCollection($db, $collectionName) {

		// Select collection
		$collection = $db->selectCollection($collectionName);

		return $collection;
	}
}

?>
