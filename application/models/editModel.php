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

	public function resyncAffectedArtefacts($db, $key, $value) {

		$collection = $this->db->selectCollection($db, ARTEFACT_COLLECTION);
		$foreignKeys = $this->getForeignKeyTypes($db);

		$result = $collection->find([$key => $value], ['projection' => ['id' => 1]]);

		$isResult = True;
		foreach ($result as $row) {
			
			$id = $row['id'];
			$artefactData = $this->getArtefactFromJsonPath(PHY_METADATA_URL . $id . '/index.json');
			$artefactData = $this->insertForeignKeyDetails($db, $artefactData , $foreignKeys);
			$artefactData = $this->insertDataExistsFlag($artefactData);

			$isResult = $isResult and $this->replaceJsonDataInDB($collection, $artefactData, 'id', $artefactData['id']);
		}

		return $isResult;
	}
}

?>
