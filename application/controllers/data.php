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

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL . '001/', $pattern = '/index.json$/i');
		
		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);

			$state = $content['State'];
			$state = str_replace('Assam', 'असम', $state);
			$state = str_replace('Delhi', 'दिल्ली', $state);
			$state = str_replace('Manipur', 'मणिपुर', $state);
			$state = str_replace('Uttarakhand', 'उत्तराखण्ड', $state);
			$state = str_replace('Odisha', 'ओड़िशा', $state);
			$state = str_replace('Andhra Pradesh', 'आंध्र प्रदेश', $state);
			$state = str_replace('Uttar Pradesh', 'उत्तर प्रदेश', $state);
			$state = str_replace('Kerala', 'केरल', $state);
			$state = str_replace('Karnataka', 'कर्नाटक', $state);
			$state = str_replace('Goa', 'गोआ', $state);
			$state = str_replace('Gujarat', 'गुजरात', $state);
			$state = str_replace('Puducherry', 'पॉण्डिचेरी', $state);
			$state = str_replace('Chhattisgarh', 'छत्तीसगढ़', $state);
			$state = str_replace('Chandigarh', 'चंडीगढ़†', $state);
			$state = str_replace('Punjab', 'पंजाब', $state);
			$state = str_replace('Jammu and Kashmir', 'जम्मू और कश्मीर', $state);
			$state = str_replace('Maharashtra', 'महाराष्ट्र', $state);
			$state = str_replace('West Bengal', 'पश्चिम बंगाल', $state);
			$state = str_replace('Tamil Nadu', 'तमिलनाडु', $state);
			$state = str_replace('Bihar', 'बिहार', $state);
			$state = str_replace('Rajasthan', 'राजस्थान', $state);
			$state = str_replace('Madhya Pradesh', 'मध्य प्रदेश', $state);
			$state = str_replace('Haryana', 'हरियाणा', $state);
			$state = str_replace('Himachal Pradesh', 'हिमाचल प्रदेश', $state);
			$state = str_replace('Andaman and Nicobar Islands', 'अंडमान व निकोबार द्वीपसमूह', $state);
			$state = str_replace('Arunachal Pradesh', 'अरुणाचल प्रदेश', $state);
			$state = str_replace('Jharkhand', 'झारखंड', $state);
			$state = str_replace('Meghalaya', 'मेघालय', $state);
			$state = str_replace('Mizoram', 'मिज़ोरम', $state);
			$state = str_replace('Sikkim', 'सिक्किम', $state);
			$state = str_replace('Tripura', 'त्रिपुरा', $state);

			if(preg_match('/.*,.*/', $state)) {

				$content['States'] = $state;
				unset($content['State']);
			}
			elseif($state == ''){

				unset($content['State']);
			}
			else{

				$content['State'] = $state;
			}

			if(preg_match('/.*,.*/', $content['Place'])) {

				$content['Places'] = $content['Place'];
				unset($content['Place']);
			}
			elseif($content['Place'] == ''){

				unset($content['Place']);
			}

			if($content['Date'] == '00-00-0000'){

				unset($content['Date']);
			}

			// $content['Type'] = 'Photograph';

			// if(isset($content['albumID'])) unset($content['albumID']);

			// $id = $this->model->getIdFromPath($jsonFile);
			// $content['id'] = $id;

			// // Remove null elements
			// $content = array_filter($content);
			$json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			var_dump($jsonFile);
			// file_put_contents($jsonFile, $json);
		}
	}
}

?>
