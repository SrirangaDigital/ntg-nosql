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
}

?>
