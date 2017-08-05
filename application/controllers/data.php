<?php

class data extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function syncJsonToDB() {

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/index.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, ARTEFACT_COLLECTION);

		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);
			$content = $this->model->insertDataExistsFlag($content);
			$content = $this->model->beforeDbUpadte($content);

			$result = $collection->insertOne($content);
		}
		
		if(file_exists(PHY_FOREIGN_KEYS_URL)){

			$this->insertForeignKeys();
		}
	}
	
	private function insertForeignKeys()
	{
		$jsonFiles = $this->model->getFilesIteratively(PHY_FOREIGN_KEYS_URL, $pattern = '/.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, FOREIGN_KEY_COLLECTION);

		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);
			$content = $this->model->beforeDbUpadte($content);

			$result = $collection->insertOne($content);
		}
	}

	// Use this method for global changes in json files
	public function modify() {

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL . '003/', $pattern = '/index.json$/i');
		
		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);

			if(isset($content['State'])) {

				$content['State'] = str_replace("हरियाणा, पंजाब", 'HR', $content['State']);

				$content['State'] = str_replace("AP", "आंध्र प्रदेश", $content['State']);
				$content['State'] = str_replace("AR", "अरुणाचल प्रदेश", $content['State']);
				$content['State'] = str_replace("AS", "असम", $content['State']);
				$content['State'] = str_replace("BR", "बिहार", $content['State']);
				$content['State'] = str_replace("CG", "छत्तीसगढ़", $content['State']);
				$content['State'] = str_replace("CH", "चंडीगढ़†", $content['State']);
				$content['State'] = str_replace("DL", "दिल्ली", $content['State']);
				$content['State'] = str_replace("GA", "गोआ", $content['State']);
				$content['State'] = str_replace("GJ", "गुजरात", $content['State']);
				$content['State'] = str_replace("HP", "हिमाचल प्रदेश", $content['State']);
				$content['State'] = str_replace("HR", "हरियाणा", $content['State']);
				$content['State'] = str_replace("JH", "झारखंड", $content['State']);
				$content['State'] = str_replace("JK", "जम्मू और कश्मीर", $content['State']);
				$content['State'] = str_replace("KA", "कर्नाटक", $content['State']);
				$content['State'] = str_replace("KL", "केरल", $content['State']);
				$content['State'] = str_replace("MH", "महाराष्ट्र", $content['State']);
				$content['State'] = str_replace("ML", "मेघालय", $content['State']);
				$content['State'] = str_replace("MN", "मणिपुर", $content['State']);
				$content['State'] = str_replace("MP", "मध्य प्रदेश", $content['State']);
				$content['State'] = str_replace("NL", "नागालैंड", $content['State']);
				$content['State'] = str_replace("OR", "ओड़िशा", $content['State']);
				$content['State'] = str_replace("PB", "पंजाब", $content['State']);
				$content['State'] = str_replace("RJ", "राजस्थान", $content['State']);
				$content['State'] = str_replace("TN", "तमिलनाडु", $content['State']);
				$content['State'] = str_replace("UK", "उत्तराखण्ड", $content['State']);
				$content['State'] = str_replace("UP", "उत्तर प्रदेश", $content['State']);
				$content['State'] = str_replace("WB", "पश्चिम बंगाल", $content['State']);
			}

			$json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

			file_put_contents($jsonFile, $json);
		}
	}
}

?>
