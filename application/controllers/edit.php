<?php


class edit extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function artefact($query, $idURL = '') {
		
		$id = $this->model->urlToActualID($idURL);
		$data = $this->model->getArtefactFromJsonPath(PHY_METADATA_URL . $id . '/index.json');

		if($data) {
			
			$db = $this->model->db->useDB();
			$data['auxiliary']['thumbnailPath'] = $this->model->getThumbnailPath($id);
			$data['auxiliary']['idURL'] = $idURL;
			$data['auxiliary']['foreignKeys'] = $this->model->getForeignKeyTypes($db);

			$this->view('edit/artefact', $data);
		}
		else {
			
			$this->view('error/index');
		}
	}	

	public function foreignKey($query, $key, $value) {
		
		$foreignKeyId = $this->model->getForeignKeyId($key, $value);

		if($foreignKeyId){

			$data = $this->model->getArtefactFromJsonPath(PHY_FOREIGN_KEYS_URL . $key . '/' . $foreignKeyId . '.json');
			$this->view('edit/foreignKey', $data);
		}
		else {
			
			$this->view('error/index');
		}
	}

	public function updateArtefactJson() {
		
		// Get post data	
		$data = $this->model->getPostData();
		if(!$data){$this->view('error/index');return;}

		// Rearrange data in key value pairs
		$jsonData = [];
		foreach($data as $value){

			$jsonData[$value[0]] = $value[1];
		}

		// Preprocess data before update
		$jsonData = $this->model->beforeDbUpdate($jsonData);

		// Write updated data to json file
		$path = PHY_METADATA_URL . $jsonData['id'] . "/index.json";
		if(!($this->model->writeJsonToPath($jsonData, $path))){
			$this->view('error/prompt',["msg"=>"Problem in writing data to file"]); return;
		}

		// Insert foreignKey details to artefact details
		$db = $this->model->db->useDB();
		$collection = $this->model->db->selectCollection($db, ARTEFACT_COLLECTION);
		$foreignKeys = $this->model->getForeignKeyTypes($db);
		$dbData = $this->model->insertForeignKeyDetails($db, $jsonData , $foreignKeys);

		// Replace data in database
		if(!($this->model->replaceJsonDataInDB($collection, $dbData, 'id', $dbData['id']))){
			$this->view('error/prompt',["msg"=>"Problem in writing data to database"]); return;
		}

		$this->redirect('gitcvs/updateRepo/' . str_replace('/', '_', $jsonData['id']));
	}

}

?>
