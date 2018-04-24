<?php

class Model {

	public function __construct() {

		$this->db = new Database();
		$this->dataShowFilter = (SHOW_ONLY_IF_DATA_EXISTS) ? ['$regex' => '1|External'] : ['$regex' => '0|1|External'];
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

			// handle mm-dd-yyyy format
	        $data['Date'] = preg_replace('/(\d{2})\-(\d{2})\-(\d{4})/', "$3-$2-$1", $data['Date']);

			if(preg_match('/^0000\-/', $data['Date'])) {

				unset($data['Date']);
			}
		}
		if(isset($data['AccessLevel'])) $data['AccessLevel'] = intval($data['AccessLevel']);

		return $data;
	}

	public function insertDataExistsFlag($data){

		$leaves = glob(PHY_DATA_URL . $data['id'] . '/thumbs/*' . PHOTO_FILE_EXT);

		if(!isset($data['DataExists'])){

			$data['DataExists'] = (sizeof($leaves)) ? '1' : '0';
		}

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

	public function preProcessURLQuery($filter){

		foreach ($filter as $key => $value) {
			
			$filter{$key} = str_replace('_', '/', $filter{$key});
		}

		return $filter;
	}

	public function getTypeByID($id){

		$fileName = PHY_METADATA_URL . $id . '/index.json';
		$contentString = file_get_contents($fileName);
		$content = json_decode($contentString, true);
		
		return (isset($content['Type'])) ? $content['Type'] : '';
	}

	public function includeExternalResources($artefact){

        if($artefact['details']['DataExists'] == 'External'){


            $fileName = str_replace(BASE_URL, '', DATA_URL) . $artefact['details']['id'] . '/'. EXTERNAL_RESOURCE;
            $artefact['external']['fileName'] = (file_exists($fileName)) ? $fileName : EXTERNAL_RESOURCE_NOT_EXISTS;
        }
		return $artefact;
	}

	public function writeJsonToPath($data, $path) {

		$jsonString = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		return (file_put_contents($path, $jsonString)) ? True : False;
	}

	public function getUniqueKeys(){

		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTEFACT_KEYS_COLLECTION);

		$result = array_values(array_filter($collection->distinct('_id')));
		return $result;
	}
	
	public function getDataFromApi($url){

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);

		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}
}

?>
