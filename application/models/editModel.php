<?php


class editModel extends Model {

	public function __construct() {

		parent::__construct();
	}

	public function getForeignKeyId($key,$value) {
		
		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, FOREIGN_KEY_COLLECTION);

		$result = $collection->findOne([$key => $value], ['projection' => ['ForeignKeyId' => 1]]);

		return ($result) ? $result['ForeignKeyId'] : '';
	}

	public function writeJsonToPath($data, $path) {

		$jsonString = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		return (file_put_contents($path, $jsonString)) ? True : False;
	}

	public function resyncAffectedArtefacts($db, $key, $value) {

		$collection = $this->db->selectCollection($db, ARTEFACT_COLLECTION);
		$foreignKeys = $this->getForeignKeyTypes($db);

		$result = $collection->find([$key => $value], ['projection' => ['id' => 1]]);

		$isResult = True;
		foreach ($result as $row) {
			
			$id = $row['id'];
			$artefactData = $this->getArtefactFromJsonPath(PHY_METADATA_URL . $id . '/index.json');
			$artefactData = $this->insertForeignKeyDetails($db, $artefactData , $foreignKeys);
			
			$isResult = $isResult and $this->replaceJsonDataInDB($collection, $artefactData, 'id', $artefactData['id']);
		}

		return $isResult;
	}
}

?>
