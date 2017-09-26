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

		$db = $this->model->db->useDB();
		$collection = $this->model->db->selectCollection($db, ARTEFACT_COLLECTION);

		$xmlObject = simplexml_load_file(PHY_BASE_URL . 'ntgc-brochures.xml');

		// $iterator = $collection->distinct("State", ["Type" => "Brochure"]);
		// $iterator = $collection->distinct("Place", ["Type" => "Brochure"]);
		$data = [];
		foreach ($xmlObject as $brochure) {
			
			$cid = str_replace(';', ', ', $brochure['cid']);
			$indexFile = '';

			$iterator = $collection->find(['AccessionCards' => ['$regex' => $cid]], ['projection' => [ 'id' => '1', '_id' => '0']]);
			
			$jsonFile = '';

			foreach ($iterator as $key => $value) {
				
				$jsonFile = (array)$value;
			}
			$jsonFileName = PHY_METADATA_URL . $jsonFile['id'] . '/index.json';
			$contentString = file_get_contents(PHY_METADATA_URL . $jsonFile['id'] . '/index.json');
			$content = json_decode($contentString, true);

			foreach ($brochure->children() as $key => $value) {
				
				if($key == 'state') {

					if(isset($content['States'])) {
						if(!preg_match('/' . (string)$value . '/', $content['States']))
						$content['States'] .= ' &&& ' . (string)$value;
					}
					elseif(isset($content['State']) && $content['State'] != (string)$value)	{

						$content['States'] = $content['State'] . ' &&& ' . (string)$value;
						unset($content['State']);
					}
					elseif(!isset($content['State']))
						$content['State'] = (string)$value;
				}

				elseif($key == 'place'){

					if(isset($content['Places'])) {
						if(!preg_match('/' . (string)$value . '/', $content['Places']))
						$content['Places'] .= ' &&& ' . (string)$value;
					}
					elseif(isset($content['Place']) && $content['Place'] != (string)$value)	{

						$content['Places'] = $content['Place'] . ' &&& ' . (string)$value;
						unset($content['Place']);
					}
					elseif(!isset($content['Place']))
						$content['Place'] = (string)$value;
				}
					
				elseif(isset($content[ucwords($key)]) && $content[ucwords($key)] != (string)$value)
				$content[ucwords($key)] .=  ' &&& ' . (string)$value;

				elseif(!isset($content[ucwords($key)]))
				$content[ucwords($key)] = (string)$value;
			}
			
			$json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			// file_put_contents($jsonFileName, $json);
		}

		// file_put_contents("StatePlaces.txt", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


		// $jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL .'001/' , $pattern = '/json$/i');
		
		// foreach ($jsonFiles as $jsonFile) {

		// 	$contentString = file_get_contents($jsonFile);
		// 	$content = json_decode($contentString, true);

		// 	if(isset($content['State']) && $content['State'] == 'दिल्ली' && isset($content['Place']) && $content['Place'] == 'दिल्ली' ){

		// 		unset($content['Place']);
		// 	}
		// }
	}

	public function getData() {

		$db = $this->model->db->useDB();
		$collection = $this->model->db->selectCollection($db, ARTEFACT_COLLECTION);

		$iterator = $collection->find(['Type' => 'Brochure'] , ['projection' => ['id' => 1, 'AccessionNumber' => 1 ]], ['sort' => ['id' => '1']]);

		foreach ($iterator as $value) {
			# code...
			if(!preg_match('/999\.99/', $value['id']))
			echo $value['AccessionNumber'] . '-->' . $value['id'] . "<br/>\n";
		}
	}
}

?>
