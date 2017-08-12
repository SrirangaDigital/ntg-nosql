<?php

class Model {

	public function __construct() {

		$this->db = new Database();
		$this->dataShowFilter = (SHOW_ONLY_IF_DATA_EXISTS) ? '1' : ['$regex' => '0|1'];
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

	public function getArtefactFromJsonPath($path){

		$contentString = file_get_contents($path);
		$content = json_decode($contentString, true);

		return $content;
	}

	public function getPrecastKey($type, $key){

	    $structure = json_decode(file_get_contents(PHY_JSON_PRECAST_URL . 'archive-structure.json'), true);

		return (isset($structure{$type}['selectKey'])) ? $structure{$type}{$key} : '';
	}

	public function getRandomID($type, $filter, $count){

		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTEFACT_COLLECTION);

		$filter = $this->preProcessQueryFilter($filter);

		$match = ['DataExists' => $this->dataShowFilter, 'Type' => $type] + $filter;
		$result = $collection->findOne($match, ['projection' => ['id' => 1], 'skip' => rand(0, $count - 1)]);
		
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
		$content = $this->beforeDbUpdate($content);


	}

	public function replaceJsonDataInDB($collection, $data, $key, $value) {

		return $collection->replaceOne([ $key => $value ], $data);
	}

	public function beforeDbUpdate($data){

		if(isset($data['Date'])){

			if(preg_match('/^0000\-/', $data['Date'])) {

				unset($data['Date']);
			}
		}
		if(isset($data['AccessLevel'])) $data['AccessLevel'] = intval($data['AccessLevel']);

		return $data;
	}

	public function insertDataExistsFlag($data){

		$leaves = glob(PHY_DATA_URL . $data['id'] . '/thumbs/*' . PHOTO_FILE_EXT);
		$data['DataExists'] = (sizeof($leaves)) ? '1' : '0';

		return $data;
	}

	public function filterSpecialChars($string){

		$string = str_replace('/', '_', $string);
		$string = urlencode($string);

		return $string;
	}

	public function getForeignKeyTypes($db){

		$collection = $this->db->selectCollection($db, FOREIGN_KEY_COLLECTION);
		$result = $collection->distinct(FOREIGN_KEY_TYPE);
		return $result;
	}

	public function insertForeignKeyDetails($db, $artefactDetails , $foreignKeys){

		$collection = $this->db->selectCollection($db, FOREIGN_KEY_COLLECTION);

		$data = [];
		foreach($foreignKeys as $fkey){

			if(array_key_exists($fkey, $artefactDetails)){
				
				$result = $collection->findOne([$fkey => $artefactDetails[$fkey]]);
				$result = $this->unsetControlParams($result);

				$artefactDetails = array_merge((array) $artefactDetails, (array) $result);
			}
		}

		return $artefactDetails;
	}

	public function unsetControlParams($data){

		$controlParams = ['_id', 'AccessLevel','oid', 'DataExists', 'ForeignKeyId', 'ForeignKeyType', 'Aid', 'ColorType'];

		foreach ($controlParams as $param) {

			if(isset($data{$param})) unset($data{$param});
		}
		return $data;
	}

	public function preProcessQueryFilter($filter){

		foreach ($filter as $key => $value) {
			
			if($value == 'notExists')
				$filter{$key} = ['$exists' => false];
		}

		return $filter;
	}

	public function filterArrayToString($filter){

		$urlFilterArray = [];
		foreach ($filter as $key => $value) {
			
			array_push($urlFilterArray, $key . '=' . $value);
		}
		$urlFilter = implode('&', $urlFilterArray);

		return $urlFilter;
	}

	public function urlToActualID($id){

		$id = preg_replace('/(.*?)_(.*?)_(.*)/', "$1/$2/$3", $id);

		return $id;
	}
}

?>
