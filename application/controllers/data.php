<?php

class data extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function buildDBFromJson() {

		if(file_exists(PHY_FOREIGN_KEYS_URL)){

			$this->insertForeignKeys();
		}

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/index.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, ARTEFACT_COLLECTION);

		$foreignKeys = $this->model->getForeignKeyTypes($db);

		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);

			$content = $this->model->insertForeignKeyDetails($db, $content, $foreignKeys);
			$content = $this->model->insertDataExistsFlag($content);
			$content = $this->model->beforeDbUpdate($content);

			$result = $collection->insertOne($content);
		}
	}
	
	private function insertForeignKeys()
	{
		$jsonFiles = $this->model->getFilesIteratively(PHY_FOREIGN_KEYS_URL, $pattern = '/.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, FOREIGN_KEY_COLLECTION);

		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);
			$content = $this->model->beforeDbUpdate($content);

			$result = $collection->insertOne($content);
		}
	}

	// Use this method for global changes in json files
	public function modify() {

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/index.json$/i');
		
		foreach ($jsonFiles as $jsonFile) {

			if(preg_match('/IMFG/', $jsonFile)){

				var_dump($jsonFile);
			}
			// $contentString = file_get_contents($jsonFile);
			// $content = json_decode($contentString, true);

			// $content = array_filter($content);
			// $json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	
			// file_put_contents($jsonFile, $json);
		}
	}
}

?>
