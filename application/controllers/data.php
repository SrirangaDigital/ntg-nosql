<?php

class data extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function syncJsonToDB() {

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/index.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, ARTEFACT_COLLECTION);

		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);
			$content = $this->model->insertDataExistsFlag($content);
			$content = $this->model->beforeDbUpadte($content);

			$result = $collection->insertOne($content);
		}
		
		if(file_exists(PHY_FOREIGN_KEYS_URL)){

			$this->insertForeignKeys();
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
			$content = $this->model->beforeDbUpadte($content);

			$result = $collection->insertOne($content);
		}
	}

	// Use this method for global changes in json files
	public function modify() {

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL . '003/', $pattern = '/index.json$/i');
		
		foreach ($jsonFiles as $jsonFile) {

			$parentId = preg_replace('/.*003\/(.*)\/\d{5}\/.*/', "$1", $jsonFile);
			
			$parentJsonFile = PHY_FOREIGN_KEYS_URL . 'EventID/' . $parentId . '.json';
			$parentJsonFileOut = PHY_METADATA_URL . 'foreign/' . $parentId . '.json';
			
			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);

			$parentContentString = file_get_contents($parentJsonFile);
			$parentContent = json_decode($parentContentString, true);

			if((!isset($content['State'])) && (isset($parentContent['Place']))) {

				if(($parentContent['Place'] == 'पांडिचेरी')) {

					$content['State'] = $parentContent['Place'];
					unset($parentContent['Place']);
		
					$json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
					$parentJson = json_encode($parentContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

					file_put_contents($jsonFile, $json);
					file_put_contents($parentJsonFileOut, $parentJson);
				}
			}
		}
	}
}

?>
