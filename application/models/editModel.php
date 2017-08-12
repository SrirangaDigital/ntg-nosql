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
}

?>
