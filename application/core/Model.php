<?php

class Model {

	public function __construct() {

		$this->db = new Database();
	}
	
	public function getPostData() {

		if (isset($_POST['submit'])) {

			unset($_POST['submit']);	
		}

		if(!array_filter($_POST)) {
		
			return false;
		}
		else {

			return array_filter(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS));
		}
	}

	public function getPrecastKey($type, $key){

	    $structure = json_decode(file_get_contents(PHY_JSON_PRECAST_URL . 'archive-structure.json'), true);

		return (isset($structure{$type}['selectKey'])) ? $structure{$type}{$key} : '';
	}

	public function getRandomID($type, $selectKey, $category, $count){

		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTEFACT_COLLECTION);

		$result = $collection->findOne(['Type' => $type, $selectKey => $category], ['projection' => ['id' => 1], 'skip' => rand(0, $count - 1)]);

		if(!$result)
			$result = $collection->findOne(['Type' => $type, $selectKey => ['$exists' => false]], ['projection' => ['id' => 1], 'skip' => rand(0, $count - 1)]);
		
		return $result['id'];
	}

	public function getThumbnailPath($id){

		$artefactPath = PHY_DATA_URL . $id;

		$leaves = glob(PHY_DATA_URL . $id . '/thumbs/*' . PHOTO_FILE_EXT);

		$firstLeaf = array_shift($leaves);

		return ($firstLeaf) ? str_replace(PHY_DATA_URL, DATA_URL, $firstLeaf) : STOCK_IMAGE_URL . 'default-image.png';
	}

	public function syncArtefactJsonToDB($idKey, $id, $collectionName, $path){

		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, $collectionName);

		// $jsonFile = PHY_METADATA_URL . $id . '/index.json';
		$jsonFile = $path;

		$contentString = file_get_contents($jsonFile);
		$content = json_decode($contentString, true);
		$content = $this->beforeDbUpadte($content);

		$result = $collection->replaceOne(
			[ $idKey => $id ],	
			$content
		);

	}

	public function beforeDbUpadte($data){

		if(isset($data['Date'])){

			if(preg_match('/^0000\-/', $data['Date'])) {

				unset($data['Date']);
			}
		}
		return $data;
	}

	public function filterSpecialChars($string){

		$string = str_replace('/', '_', $string);
		$string = urlencode($string);

		return $string;
	}

	public function getForeignKeyTypes($foreignKeyType){

		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, FOREIGN_KEY_COLLECTION);
		$result = $collection->distinct($foreignKeyType);
		return $result;
	}

	public function unsetControlParams($data){

		$controlParams = ['_id', 'AccessLevel','oid'];

		foreach ($controlParams as $param) {

			if(isset($data{$param})) unset($data{$param});
		}
		return $data;
	}
}

?>
