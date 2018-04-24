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

			// In need of better solution, this line is written. Currently nested arrays are sent for editing as json strings
			if(isset($data['Toc'])) $data['Toc'] = htmlspecialchars(json_encode($data['Toc'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

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
		
		$refererArtefact = (isset($query['refererArtefact'])) ? $query['refererArtefact'] : '';
		$foreignKeyId = $this->model->getForeignKeyId($key, $value);

		if($foreignKeyId){

			$data = $this->model->getArtefactFromJsonPath(PHY_FOREIGN_KEYS_URL . $key . '/' . $foreignKeyId . '.json');
			$data['refererArtefact'] = $refererArtefact;
			$this->view('edit/foreignKey', $data);
		}
		else {
			
			$this->view('error/index');
		}
	}

	public function updateArtefact() {
		
		// Get post data	
		$data = $this->model->getPostData();
		if(!$data){$this->view('error/index');return;}

		// Rearrange data in key value pairs
		$jsonData = [];
		foreach($data as $value){

			$jsonData[$value[0]] = $value[1];
		}

		// Here, obtained json string from nested array is put back as an associative array
		if(isset($jsonData['Toc'])) $jsonData['Toc'] = json_decode(htmlspecialchars_decode($jsonData['Toc'], ENT_QUOTES), True);

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
		$dbData = $this->model->insertDataExistsFlag($dbData);

		// Replace data in database
		if(!($this->model->replaceJsonDataInDB($collection, $dbData, 'id', $dbData['id']))){
			$this->view('error/prompt',["msg"=>"Problem in writing data to database"]); return;
		}

		$this->redirect('gitcvs/updateRepo/' . str_replace('/', '_', $jsonData['id']));
	}

	public function updateForeignKey() {
		
		// Get post data	
		$data = $this->model->getPostData();

		$data['refererArtefact'] = (isset($data['refererArtefact'])) ? $data['refererArtefact'] : '';
		$refererArtefact = $data['refererArtefact']; unset($data['refererArtefact']);

		if(!$data){$this->view('error/index');return;}

		// Rearrange data in key value pairs
		$jsonData = [];
		foreach($data as $value){

			$jsonData[$value[0]] = $value[1];
		}

		// Preprocess data before update
		$jsonData = $this->model->beforeDbUpdate($jsonData);

		// Write updated data to json file
		$path = PHY_FOREIGN_KEYS_URL . $jsonData['ForeignKeyType'] . '/' . $jsonData['ForeignKeyId'] . '.json';
		if(!($this->model->writeJsonToPath($jsonData, $path))){
			$this->view('error/prompt',["msg"=>"Problem in writing data to file"]); return;
		}

		// Replace data in database
		$dbData = $jsonData;
		$db = $this->model->db->useDB();
		$collection = $this->model->db->selectCollection($db, FOREIGN_KEY_COLLECTION);

		if(!($this->model->replaceJsonDataInDB($collection, $dbData, 'ForeignKeyId', $dbData['ForeignKeyId']))) {
			$this->view('error/prompt',["msg"=>"Problem in writing data to database"]); return;
		}

		$collection = $this->model->db->selectCollection($db, ARTEFACT_COLLECTION);
		
		if(!($this->model->resyncAffectedArtefacts($db, $dbData['ForeignKeyType'], $dbData[$dbData['ForeignKeyType']]))) {
			$this->view('error/prompt',["msg"=>"Problem in resyncing artefact details"]); return;			
		}

		$this->redirect('gitcvs/updateRepo/' . $refererArtefact);
	}

	public function bulkReplace($query) {
		
		$this->view('edit/bulkReplace', '');
	}
}
?>
