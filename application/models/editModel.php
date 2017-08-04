<?php


class editModel extends Model {

	public function __construct() {

		parent::__construct();
	}

	public function editArtefact($id) {
		
		$file = PHY_METADATA_URL . str_replace('_','/',$id) . '/index.json';
		$artefactDetails = file_get_contents($file);
		$data = json_decode($artefactDetails);
		$data->thumbnailPath = $this->getThumbnailPath($data->id);
		$data->idURL = $id;
		return ($data);
	}	

	public function editForeignKey($key,$value) {
		
		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, FOREIGN_KEY_COLLECTION);

		$result = $collection->findOne([$key => $value]);
		
		if($result) 
			$result = $this->unsetControlParams($result);
		
		return $result;
	}	
}

?>
