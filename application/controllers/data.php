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
	
			if(array_key_exists(FOREIGN_KEY_TYPE, $content))
				$content = $this->model->insertForeignKeyDetails($db, $content, $foreignKeys);
	
			$content = $this->model->insertDataExistsFlag($content);
			$content = $this->model->beforeDbUpdate($content);

			$result = $collection->insertOne($content);
		}

		// Insert fulltext
		$this->insertFulltext();
	}
	
	private function insertForeignKeys() {

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

	public function insertFulltext() {

		ini_set('max_execution_time', 300);

		$txtFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/\/text\/\d+\.txt$/i');

		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, FULLTEXT_COLLECTION);

		foreach ($txtFiles as $txtFile) {

			$content['text'] = file_get_contents($txtFile);
			$content['text'] = $this->model->processFulltext($content['text']);
			
			$txtFile = str_replace(PHY_METADATA_URL, '', $txtFile);
			preg_match('/^(.*)\/text\/(.*)\.txt/', $txtFile, $matches);

			$content['id'] = $matches[1];
			$content['page'] = $matches[2];

			$content = $this->model->beforeDbUpdate($content);
			$result = $collection->insertOne($content);
		}
	}

	public function bulkReplaceAction() {
		
		// Get post data	
		$data = $this->model->getPostData();

		$metaDataJsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL  , $pattern = '/index.json$/i');
		$foreignKeyJsonFiles = $this->model->getFilesIteratively(PHY_FOREIGN_KEYS_URL , $pattern = '/json$/i');
		
		$jsonFiles = array_merge($metaDataJsonFiles, $foreignKeyJsonFiles);

		$resultBoolean = True;
		$affectedFiles = [];
		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);
			
			if(isset($content[$data['key']])) {

				if($content[$data['key']] == $data['oldValue']) { 

					$content[$data['key']] = $data['newValue'];
					
					if(!(@$this->model->writeJsonToPath($content, $jsonFile))){

						$resultBoolean = False;
						break;
					}
					array_push($affectedFiles, $jsonFile);
				}
			}
		}

		if($resultBoolean){

			$this->buildDBFromJson();
			$this->redirect('gitcvs/updateRepo');
		}
		else{

			require_once 'application/controllers/gitcvs.php';

			$gitcvs = new gitcvs;
			$gitcvs->checkoutFiles($affectedFiles);
			$this->view('error/prompt',["msg"=>"Problem in writing data to file"]); return;
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
