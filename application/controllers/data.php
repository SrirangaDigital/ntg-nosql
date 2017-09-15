<?php

class data extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function buildDBFromJson() {

		$this->insertForeignKeys();

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/index.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, ARTEFACT_COLLECTION);

		$foreignKeys = $this->model->getForeignKeyTypes($db);

		foreach ($jsonFiles as $jsonFile) {

			$content = $this->model->getArtefactFromJsonPath($jsonFile);
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

		// $db = $this->model->db->useDB();
		// $collection = $this->model->db->selectCollection($db, ARTEFACT_COLLECTION);

		// $iterator = $collection->distinct("State", ["Type" => "Brochure"]);

		// $data = [];
		// foreach ($iterator as $state) {
			
		// 	$Places = $collection->distinct("Place", ["State" => $state]);
		// 	$data[$state][] = $Places;
		// }
		// file_put_contents("StatePlaces.txt", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


		$jsonFiles = $this->model->getFilesIteratively(PHY_FOREIGN_KEYS_URL , $pattern = '/json$/i');
		
		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);
			
			if(isset($content['Asstdirector'])) {

				$value = $content['Asstdirector'];
				$content['Asst-director'] = $value;
				unset($content['Asstdirection']);
				$json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
				// var_dump($json);
		
				file_put_contents($jsonFile, $json);
			}
		}
	}
}

?>
