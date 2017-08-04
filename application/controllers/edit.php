<?php


class edit extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function artefact($query, $id='') {
	
		$data = $this->model->editArtefact($id);

		if($data){
			
			$foreignKeys = $this->model->getForeignKeyTypes(FOREIGN_KEY_TYPE);
			$data->foreignKeys =  (array)$foreignKeys;
			$this->view('edit/artefact', $data); 
		} else {
			
			$this->view('error/index');
		}
	}	

	public function foreignkey($query, $key, $value) {
	
		$data = $this->model->editForeignKey($key,$value);

		if($data){

			$this->view('edit/foreignkey', $data); 
		} else {
			
			$this->view('error/index');
		}
	}




	public function updateArtefactJson() {
		
		$data = $this->model->getPostData();
		if(!$data){$this->view('error/index');return;}

		$fileContents = array();

		foreach($data as $value){

			$fileContents[$value[0]] = $value[1];
		}

		if(isset($fileContents['id'])){

			$path = PHY_METADATA_URL . $fileContents['id'] . "/index.json";
			$collectionName = ARTEFACT_COLLECTION;
			$id = $fileContents['id'];
			$idKey = 'id';
			$url =  BASE_URL . 'describe/artefact/' . str_replace('/', '_', $id);
		}
		else{

			$path = PHY_FOREIGN_KEYS_URL . $fileContents['ForeignKeyType'] . "/" . $fileContents['ForeignKeyId'] . ".json";
			$collectionName = FOREIGN_KEY_COLLECTION;
			$id = $fileContents['ForeignKeyId'];
			$idKey = 'ForeignKeyId';
			$url =  BASE_URL;

		}

		// var_dump($path);
		// echo "<br /><br />";
		// var_dump($collectionName);
		// echo "<br /><br />";

		$fileContentsJson = json_encode($fileContents,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

		if(file_put_contents($path, $fileContentsJson))
		{
			$this->model->syncArtefactJsonToDB($idKey, $id, $collectionName, $path);
			
			if(REQUIRE_GIT_TRACKING)
			{
				$this->redirect('gitcvs/updateRepo/' . str_replace('/', '_', $id));
			}
			else
			{				
				$this->absoluteRedirect($url);
			}
			
		}
		else
		{
			$this->view('error/prompt',["msg"=>"Problem in writing data to a file"]);
		}
	}

}

?>
