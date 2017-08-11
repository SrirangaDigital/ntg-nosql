<?php

class data extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function buildDBFromJson() {

		if(file_exists(PHY_FOREIGN_KEYS_URL)){

			$this->insertForeignKeys();
		}

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/index.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, ARTEFACT_COLLECTION);

		$foreignKeys = $this->model->getForeignKeyTypes($db);

		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);

			$content = $this->model->insertForeignKeyDetails($db, $content, $foreignKeys);
			$content = $this->model->insertDataExistsFlag($content);
			$content = $this->model->beforeDbUpdate($content);

			$result = $collection->insertOne($content);
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
			$content = $this->model->beforeDbUpdate($content);

			$result = $collection->insertOne($content);
		}
	}

	// Use this method for global changes in json files
	public function modify() {

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL . '001/', $pattern = '/index.json$/i');
		
		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);

			if(isset($content['Place'])) {

				$content['Place'] = preg_replace('/^Agra$/', 'Agra', $content['Place']);
				$content['Place'] = preg_replace('/^Ahamedbad$/', 'Ahmedabad', $content['Place']);
				$content['Place'] = preg_replace('/^Ahmedabad$/', 'Ahmedabad', $content['Place']);
				$content['Place'] = preg_replace('/^Ajmer$/', 'Ajmer', $content['Place']);
				$content['Place'] = preg_replace('/^Allahabad$/', 'Allahabad', $content['Place']);
				$content['Place'] = preg_replace('/^Allahabad, Varanasi$/', 'Allahabad', $content['Place']);
				$content['Place'] = preg_replace('/^Almora$/', 'Almora', $content['Place']);
				$content['Place'] = preg_replace('/^Alwar$/', 'Alwar', $content['Place']);
				$content['Place'] = preg_replace('/^Ambala$/', 'Ambala', $content['Place']);
				$content['Place'] = preg_replace('/^Ambikapur$/', 'Ambikapur', $content['Place']);
				$content['Place'] = preg_replace('/^Amritsar$/', 'Amritsar', $content['Place']);
				$content['Place'] = preg_replace('/^Andaman and Nicobar Island$/', 'Andaman and Nicobar Island', $content['Place']);
				$content['Place'] = preg_replace('/^Attingal$/', 'Attingal', $content['Place']);
				$content['Place'] = preg_replace('/^Aurangabad$/', 'Aurangabad', $content['Place']);
				$content['Place'] = preg_replace('/^Australia$/', 'Australia', $content['Place']);
				$content['Place'] = preg_replace('/^Ayodhya$/', 'Ayodhya', $content['Place']);
				$content['Place'] = preg_replace('/^Azamgarh$/', 'Azamgarh', $content['Place']);
				$content['Place'] = preg_replace('/^azamgarh$/', 'Azamgarh', $content['Place']);
				$content['Place'] = preg_replace('/^Badhai$/', 'Badhai', $content['Place']);
				$content['Place'] = preg_replace('/^Baharampur$/', 'Baharampur', $content['Place']);
				$content['Place'] = preg_replace('/^Ballabgarh$/', 'Ballabhgarh', $content['Place']);
				$content['Place'] = preg_replace('/^Ballabhgarh$/', 'Ballabhgarh', $content['Place']);
				$content['Place'] = preg_replace('/^Bandra$/', 'Bandra', $content['Place']);
				$content['Place'] = preg_replace('/^Bangalore$/', 'Bangalore', $content['Place']);
				$content['Place'] = preg_replace('/^Bangladesh$/', 'Bangladesh', $content['Place']);
				$content['Place'] = preg_replace('/^Banswara$/', 'Banswara', $content['Place']);
				$content['Place'] = preg_replace('/^Barmer$/', 'Barmer', $content['Place']);
				$content['Place'] = preg_replace('/^Bastar$/', 'Bastar', $content['Place']);
				$content['Place'] = preg_replace('/^Begusarai$/', 'Begusarai', $content['Place']);
				$content['Place'] = preg_replace('/^Bhopal$/', 'Bhopal', $content['Place']);
				$content['Place'] = preg_replace('/^Bhubaneswar$/', 'Bhubaneswar', $content['Place']);
				$content['Place'] = preg_replace('/^Bikaner$/', 'Bikaner', $content['Place']);
				$content['Place'] = preg_replace('/^Bomai$/', 'Bomai', $content['Place']);
				$content['Place'] = preg_replace('/^Bombay$/', 'Bombay', $content['Place']);
				$content['Place'] = preg_replace('/^Budaun$/', 'Budaun', $content['Place']);
				$content['Place'] = preg_replace('/^Calcutta$/', 'Kolkata', $content['Place']);
				$content['Place'] = preg_replace('/^Chandigarh$/', 'Chandigarh', $content['Place']);
				$content['Place'] = preg_replace('/^Chekhov$/', 'Chekhov', $content['Place']);
				$content['Place'] = preg_replace('/^Chota Nagpur$/', 'Chota Nagpur', $content['Place']);
				$content['Place'] = preg_replace('/^Cuttack$/', 'Cuttack', $content['Place']);
				$content['Place'] = preg_replace('/^Dehradun$/', 'Dehradun', $content['Place']);
				$content['Place'] = preg_replace('/^Delhi$/', 'Delhi', $content['Place']);
				$content['Place'] = preg_replace('/^Dibrugarh$/', 'Dibrugarh', $content['Place']);
				$content['Place'] = preg_replace('/^Durg$/', 'Durg', $content['Place']);
				$content['Place'] = preg_replace('/^England$/', 'England', $content['Place']);
				$content['Place'] = preg_replace('/^Ernakulam$/', 'Ernakulam', $content['Place']);
				$content['Place'] = preg_replace('/^Faridabad$/', 'Faridabad', $content['Place']);
				$content['Place'] = preg_replace('/^France$/', 'France', $content['Place']);
				$content['Place'] = preg_replace('/^Gandhigram$/', 'Gandhigram', $content['Place']);
				$content['Place'] = preg_replace('/^Ghaziabad$/', 'Ghaziabad', $content['Place']);
				$content['Place'] = preg_replace('/^GhaZiabad, Sahibabad$/', 'Ghaziabad', $content['Place']);
				$content['Place'] = preg_replace('/^Ghazipur$/', 'Ghazipur', $content['Place']);
				$content['Place'] = preg_replace('/^Ghazipur$/', 'Ghazipur', $content['Place']);
				$content['Place'] = preg_replace('/^Goa$/', 'Goa', $content['Place']);
				$content['Place'] = preg_replace('/^Goalpara$/', 'Goalpara', $content['Place']);
				$content['Place'] = preg_replace('/^Gorakhpur$/', 'Gorakhpur', $content['Place']);
				$content['Place'] = preg_replace('/^Gorkhpur$/', 'Gorakhpur', $content['Place']);
				$content['Place'] = preg_replace('/^Guna$/', 'Guna', $content['Place']);
				$content['Place'] = preg_replace('/^Guntur$/', 'Guntur', $content['Place']);
				$content['Place'] = preg_replace('/^Gurgaon$/', 'Gurgaon', $content['Place']);
				$content['Place'] = preg_replace('/^Guwahati$/', 'Guwahati', $content['Place']);
				$content['Place'] = preg_replace('/^Gwalior$/', 'Gwalior', $content['Place']);
				$content['Place'] = preg_replace('/^Haldwani$/', 'Haldwani', $content['Place']);
				$content['Place'] = preg_replace('/^Heggodu$/', 'Heggodu', $content['Place']);
				$content['Place'] = preg_replace('/^Hisar$/', 'Hisar', $content['Place']);
				$content['Place'] = preg_replace('/^Howrah$/', 'Howrah', $content['Place']);
				$content['Place'] = preg_replace('/^Hyderabad$/', 'Hyderabad', $content['Place']);
				$content['Place'] = preg_replace('/^Imphal$/', 'Imphal', $content['Place']);
				$content['Place'] = preg_replace('/^Indore$/', 'Indore', $content['Place']);
				$content['Place'] = preg_replace('/^Jabalpur$/', 'Jabalpur', $content['Place']);
				$content['Place'] = preg_replace('/^Jaipur$/', 'Jaipur', $content['Place']);
				$content['Place'] = preg_replace('/^Jammu$/', 'Jammu', $content['Place']);
				$content['Place'] = preg_replace('/^Jatani$/', 'Jatani', $content['Place']);
				$content['Place'] = preg_replace('/^Jodhpur$/', 'Jodhpur', $content['Place']);
				$content['Place'] = preg_replace('/^Kanpur$/', 'Kanpur', $content['Place']);
				$content['Place'] = preg_replace('/^Kashi$/', 'Kashi', $content['Place']);
				$content['Place'] = preg_replace('/^Katihar$/', 'Katihar', $content['Place']);
				$content['Place'] = preg_replace('/^Khanpur$/', 'Kanpur', $content['Place']);
				$content['Place'] = preg_replace('/^Kochi$/', 'Kochi', $content['Place']);
				$content['Place'] = preg_replace('/^Kolkata$/', 'Kolkata', $content['Place']);
				$content['Place'] = preg_replace('/^Korba$/', 'Korba', $content['Place']);
				$content['Place'] = preg_replace('/^Kota$/', 'Kota', $content['Place']);
				$content['Place'] = preg_replace('/^Kumhari$/', 'Kumhari', $content['Place']);
				$content['Place'] = preg_replace('/^Kurukshetra$/', 'Kurukshetra', $content['Place']);
				$content['Place'] = preg_replace('/^Lajpat Nagar$/', 'Delhi', $content['Place']);
				$content['Place'] = preg_replace('/^Lakhimpur$/', 'Lakhimpur', $content['Place']);
				$content['Place'] = preg_replace('/^Lok Kala Manch$/', 'Delhi', $content['Place']);
				$content['Place'] = preg_replace('/^Lucknow$/', 'Lucknow', $content['Place']);
				$content['Place'] = preg_replace('/^Lucknow,  Charbagh$/', 'Lucknow', $content['Place']);
				$content['Place'] = preg_replace('/^Luknow$/', 'Lucknow', $content['Place']);
				$content['Place'] = preg_replace('/^Madhubani$/', 'Madhubani', $content['Place']);
				$content['Place'] = preg_replace('/^Madras$/', 'Chennai', $content['Place']);
				$content['Place'] = preg_replace('/^Madurai$/', 'Madurai', $content['Place']);
				$content['Place'] = preg_replace('/^Mandi$/', 'Mandi', $content['Place']);
				$content['Place'] = preg_replace('/^Mathura$/', 'Mathura', $content['Place']);
				$content['Place'] = preg_replace('/^Meerut$/', 'Meerut', $content['Place']);
				$content['Place'] = preg_replace('/^Meghdoot$/', 'Meghdoot', $content['Place']);
				$content['Place'] = preg_replace('/^Mehsana$/', 'Mehsana', $content['Place']);
				$content['Place'] = preg_replace('/^Merta$/', 'Merta', $content['Place']);
				$content['Place'] = preg_replace('/^mochemad$/', 'Mochemad', $content['Place']);
				$content['Place'] = preg_replace('/^Moradabad$/', 'Moradabad', $content['Place']);
				$content['Place'] = preg_replace('/^Mumbai$/', 'Mumbai', $content['Place']);
				$content['Place'] = preg_replace('/^Murshidabad$/', 'Murshidabad', $content['Place']);
				$content['Place'] = preg_replace('/^Mysore$/', 'Mysore', $content['Place']);
				$content['Place'] = preg_replace('/^Nagpur$/', 'Nagpur', $content['Place']);
				$content['Place'] = preg_replace('/^Nainital$/', 'Nainital', $content['Place']);
				$content['Place'] = preg_replace('/^New Delhi$/', 'Delhi', $content['Place']);
				$content['Place'] = preg_replace('/^Panaji$/', 'Panaji', $content['Place']);
				$content['Place'] = preg_replace('/^Patna$/', 'Patna', $content['Place']);
				$content['Place'] = preg_replace('/^Pondicherr$/', 'Pondicherry', $content['Place']);
				$content['Place'] = preg_replace('/^Pune$/', 'Pune', $content['Place']);
				$content['Place'] = preg_replace('/^Purnia$/', 'Purnia', $content['Place']);
				$content['Place'] = preg_replace('/^Raigad$/', 'Raigad', $content['Place']);
				$content['Place'] = preg_replace('/^Raipur$/', 'Raipur', $content['Place']);
				$content['Place'] = preg_replace('/^Rajkot$/', 'Rajkot', $content['Place']);
				$content['Place'] = preg_replace('/^Ramanagar$/', 'Ramanagar', $content['Place']);
				$content['Place'] = preg_replace('/^Rampur$/', 'Rampur', $content['Place']);
				$content['Place'] = preg_replace('/^Ranchi$/', 'Ranchi', $content['Place']);
				$content['Place'] = preg_replace('/^Rohtak$/', 'Rohtak', $content['Place']);
				$content['Place'] = preg_replace('/^Roorkee$/', 'Roorkee', $content['Place']);
				$content['Place'] = preg_replace('/^Sahibabad$/', 'Sahibabad', $content['Place']);
				$content['Place'] = preg_replace('/^Sasaram$/', 'Sasaram', $content['Place']);
				$content['Place'] = preg_replace('/^Shahjahanpur$/', 'Shahjahanpur', $content['Place']);
				$content['Place'] = preg_replace('/^Shimla$/', 'Shimla', $content['Place']);
				$content['Place'] = preg_replace('/^Sirsa$/', 'Sirsa', $content['Place']);
				$content['Place'] = preg_replace('/^Sitapur$/', 'Sitapur', $content['Place']);
				$content['Place'] = preg_replace('/^Srinagar$/', 'Srinagar', $content['Place']);
				$content['Place'] = preg_replace('/^Srinagar-Garhwal$/', 'Srinagar', $content['Place']);
				$content['Place'] = preg_replace('/^Surat$/', 'Surat', $content['Place']);
				$content['Place'] = preg_replace('/^Tagore Nagar$/', 'Delhi', $content['Place']);
				$content['Place'] = preg_replace('/^Tehri$/', 'Tehri', $content['Place']);
				$content['Place'] = preg_replace('/^Thiruvananthapuram$/', 'Thiruvananthapuram', $content['Place']);
				$content['Place'] = preg_replace('/^Thrissur$/', 'Thrissur', $content['Place']);
				$content['Place'] = preg_replace('/^Trichur$/', 'Thrissur', $content['Place']);
				$content['Place'] = preg_replace('/^Trivandrum$/', 'Thiruvananthapuram', $content['Place']);
				$content['Place'] = preg_replace('/^Uaipur$/', 'Udaipur', $content['Place']);
				$content['Place'] = preg_replace('/^Udaipur$/', 'Udaipur', $content['Place']);
				$content['Place'] = preg_replace('/^Udaypur$/', 'Udaipur', $content['Place']);
				$content['Place'] = preg_replace('/^Ujjain$/', 'Ujjain', $content['Place']);
				$content['Place'] = preg_replace('/^Unnao$/', 'Unnao', $content['Place']);
				$content['Place'] = preg_replace('/^Vadodara$/', 'Vadodara', $content['Place']);
				$content['Place'] = preg_replace('/^Varanasi$/', 'Varanasi', $content['Place']);
				$content['Place'] = preg_replace('/^Vidisha$/', 'Vidisha', $content['Place']);
				$content['Place'] = preg_replace('/^Visakhapatnam$/', 'Visakhapatnam', $content['Place']);
				$content['Place'] = preg_replace('/^जम्मू$/', 'जम्मू', $content['Place']);
				$content['Place'] = preg_replace('/^जम्मू$/', 'जम्मू', $content['Place']);
	
				$content = array_filter($content);
				$content['AccessLevel'] = 0;

				$json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		
				file_put_contents($jsonFile, $json);
			}

		}
	}
}

?>
