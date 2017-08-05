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
			$parentJsonFile = PHY_METADATA_URL . '003/' . $parentId . '.json';
			$parentJsonFileOut = PHY_METADATA_URL . 'foreign/' . $parentId . '.json';

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);

			$parentContentString = file_get_contents($parentJsonFile);
			$parentContent = json_decode($parentContentString, true);

			$content['id'] = '003/' . $parentId . '/' . $content['id'];

			$content['ColorType'] = $content['type'];
			unset($content['type']);

			$content = array_filter($content);

			$content['Type'] = 'Photograph';
			$content['EventID'] = $parentId;

			foreach (array_keys($content) as $key) {
				
				if($key != 'id') {
					
					$value = $content{$key};
					unset($content{$key});

					$content{ucwords($key)} = $value;
				}
			}

			if(isset($parentContent['state'])){

				$content['State'] = $parentContent['state'];
				unset($parentContent['state']);
			}

			if(isset($parentContent['State'])){

				$content['State'] = $parentContent['State'];
				unset($parentContent['State']);
			}

			if(isset($parentContent['troup'])){

				$content['Troupe'] = $parentContent['troup'];
				unset($parentContent['troup']);
			}
			
			if(isset($parentContent['troupe'])){

				$content['Troupe'] = $parentContent['troupe'];
				unset($parentContent['troupe']);
			}

			if(isset($parentContent['date'])){

				$content['Date'] = $parentContent['date'];
				unset($parentContent['date']);
			}

			if(isset($parentContent['Date'])){

				$content['Date'] = $parentContent['Date'];
				unset($parentContent['Date']);
			}

			$parentContent['ForeignKeyId'] = $parentContent['albumID'];
			unset($parentContent['albumID']);

			$parentContent['ForeignKeyType'] = 'EventID';
			
			$content['AccessionCards'] = $parentContent['cardList'];
			unset($parentContent['cardList']);

			foreach (array_keys($parentContent) as $key) {
				
				$value = $parentContent{$key};
				unset($parentContent{$key});

				$parentContent{ucwords($key)} = $value;
			}

			$json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			$parentJson = json_encode($parentContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

			file_put_contents($jsonFile, $json);
			file_put_contents($parentJsonFileOut, $parentJson);

			// $content['Type'] = 'Photograph';

			// if(isset($content['albumID'])) unset($content['albumID']);

			// $id = $this->model->getIdFromPath($jsonFile);
			// $content['id'] = $id;

			// // Remove null elements
			// $content = array_filter($content);
			// var_dump($jsonFile);
		}
	}
}

?>
