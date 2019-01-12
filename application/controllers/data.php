<?php

class data extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function buildDBFromJson() {

		$this->insertForeignKeys();

		$jsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/index.json$/i');
		
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, ARTEFACT_COLLECTION);

		$foreignKeys = $this->model->getForeignKeyTypes($db);
		ini_set('max_execution_time', 300);
		
		foreach ($jsonFiles as $jsonFile) {

			$content = $this->model->getArtefactFromJsonPath($jsonFile);
			$content = $this->model->insertForeignKeyDetails($db, $content, $foreignKeys);
			$content = $this->model->insertDataExistsFlag($content);
			$content = $this->model->beforeDbUpdate($content);

			$result = $collection->insertOne($content);
		}

		// Insert fulltext
		$this->insertFulltext();
		//~ $this->buildDBFromXml();
	}
	
	private function insertForeignKeys() {

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

	public function insertFulltext() {

		ini_set('max_execution_time', 300);
		
		$txtFiles = $this->model->getFilesIteratively(PHY_METADATA_URL, $pattern = '/\/text\/\d+\.txt$/i');

		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, FULLTEXT_COLLECTION);

		foreach ($txtFiles as $txtFile) {

			$content['text'] = file_get_contents($txtFile);
			$content['text'] = $this->model->processFulltext($content['text']);
			
			$txtFile = str_replace(PHY_METADATA_URL, '', $txtFile);
			preg_match('/^(.*)\/text\/(.*)\.txt/', $txtFile, $matches);

			$content['id'] = $matches[1];
			$content['page'] = $matches[2];

			$content = $this->model->beforeDbUpdate($content);
			$result = $collection->insertOne($content);
		}
	}

	public function bulkReplaceAction() {
		
		// Get post data	
		$data = $this->model->getPostData();

		$metaDataJsonFiles = $this->model->getFilesIteratively(PHY_METADATA_URL  , $pattern = '/index.json$/i');
		$foreignKeyJsonFiles = $this->model->getFilesIteratively(PHY_FOREIGN_KEYS_URL , $pattern = '/json$/i');
		
		$jsonFiles = array_merge($metaDataJsonFiles, $foreignKeyJsonFiles);

		$resultBoolean = True;
		$affectedFiles = [];
		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);
			
			if(isset($content[$data['key']])) {

				if($content[$data['key']] == $data['oldValue']) { 

					$content[$data['key']] = $data['newValue'];
					
					if(!(@$this->model->writeJsonToPath($content, $jsonFile))){

						$resultBoolean = False;
						break;
					}
					array_push($affectedFiles, $jsonFile);
				}
			}
		}

		if($resultBoolean){

			$this->buildDBFromJson();
			$this->redirect('gitcvs/updateRepo');
		}
		else{

			require_once 'application/controllers/gitcvs.php';

			$gitcvs = new gitcvs;
			$gitcvs->checkoutFiles($affectedFiles);
			$this->view('error/prompt',["msg"=>"Problem in writing data to file"]); return;
		}
	}

	public function buildDBFromXml() {

		$this->model->xml2Json();
		$db = $this->model->db->useDB();
		$collection = $this->model->db->createCollection($db, ARTICLES_COLLECTION);
		$this->model->insertEntries($collection);
	}

	// Use this method for global changes in json files
	public function modify($query = '', $param) {

		// $db = $this->model->db->useDB();
		// $collection = $this->model->db->selectCollection($db, ARTEFACT_COLLECTION);

		// $iterator = $collection->distinct("State", ["Type" => "Brochure"]);

		// $data = [];
		// foreach ($iterator as $state) {
			
		// 	$Places = $collection->distinct("Place", ["State" => $state]);
		// 	$data[$state][] = $Places;
		// }
		// file_put_contents("StatePlaces.txt", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

		// echo '<!DOCTYPE html>';
		// echo '<html lang="en">';
		// echo '<head>';
		// echo '<meta charset="utf-8">';
		// echo '<title>JSS Mahavidyapeetha</title>';
		// echo '<body>';

		$db = $this->model->db->useDB();
		$collection = $this->model->db->selectCollection($db, ARTEFACT_COLLECTION);

		$iterator = $collection->find([$param => ['$exists' => true]]);


		foreach ($iterator as $row) {

			 if(isset($row['AccessionCards']))
			 	echo 'ID : ' . $row['id'] . ' --> AccessionCard : ' . $row['AccessionCards'] . '<br />';
		}
		// To generate ForeignKey id
		// $folders1 = glob(PHY_METADATA_URL . '001/*', GLOB_ONLYDIR);

		// foreach ($folders1 as $folder) {

		// 	$data = [];
		// 	$folders2 = glob($folder . '/*', GLOB_ONLYDIR);
		// 	$jsonFile = $folders2[0] . '/index.json';
		// 	$contentString = file_get_contents($jsonFile);
		// 	$content = json_decode($contentString, true);
		// 	$fileID = preg_replace('/.*\/(.*)\/.*/', "$1", $content['id']);
		// 	$foreignKeyData['ForeignKeyId'] = $fileID;
		// 	$foreignKeyData['ForeignKeyType'] = 'FileID';
		// 	$foreignKeyData['BoxTitle'] = (preg_match('/(.*)\/(.*)/', $content['Correspondence'], $matches)) ? trim($matches[1]) : $content['Correspondence'];
		// 	$foreignKeyData['FileTitle'] = (preg_match('/(.*)\/(.*)/', $content['Correspondence'], $matches)) ? trim($matches[2]) : '';
		// 	$foreignKeyData['Box'] = $content['Box'];
		// 	$foreignKeyData['File'] = $content['File'];
		// 	$foreignKeyData['FileID'] = $fileID;

		// 	file_put_contents(PHY_FOREIGN_KEYS_URL . 'FileID/' . $fileID . '.json', json_encode($foreignKeyData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		// }
	}

	public function getKeys() {

		$keys = [];
		$jsonFiles = ['001/001.03/01978', '001/001.04/02931', '001/001.11/03674', '001/001.12/02645', '001/001.13/01297', '001/001.14/01994', '001/001.15/01984', '001/001.15/04545', '001/001.16/01985', '001/001.17/01986', '001/001.18/01987', '001/001.19/01988', '001/001.19/01989', '001/001.19/01990', '001/001.19/01991', '001/001.19/01992', '001/001.20/01993', '001/002.01/01999', '001/002.02/01998', '001/002.03/01996', '001/003.01/04961', '001/003.03/02066', '001/003.03/02069', '001/003.03/02071', '001/003.03/02072', '001/003.04/02074', '001/003.04/02075', '001/003.04/02076', '001/003.04/02078', '001/003.04/02079', '001/003.04/02080', '001/003.04/02082', '001/003.04/02083', '001/003.04/02084', '001/003.04/02085', '001/003.04/02086', '001/003.04/02087', '001/003.04/02088', '001/003.04/02089', '001/003.04/02090', '001/003.09/00244', '001/003.09/03329', '001/003.09/05063', '001/003.10/00994', '001/003.11/02571', '001/003.12/00885', '001/003.13/02508', '001/003.15/02997', '001/003.16/02037', '001/003.16/02038', '001/004.02/04992', '001/004.09/02556', '001/004.10/02661', '001/004.10/02843', '001/004.10/03696', '001/004.18/01337', '001/004.18/02666', '001/004.18/02667', '001/004.18/02826', '001/004.18/02847', '001/004.18/03040', '001/004.18/03044', '001/004.19/03048', '001/004.21/00942', '001/004.22/01621', '001/004.24/04990', '001/004.25/01598', '001/004.25/01879', '001/004.26/01343', '001/005.01/02592', '001/005.02/01622', '001/005.02/01623', '001/005.02/02202', '001/005.02/02203', '001/005.02/02204', '001/005.02/02206', '001/005.02/02207', '001/005.02/02208', '001/005.02/02210', '001/005.03/02227', '001/005.03/02228', '001/005.03/02229', '001/005.04/01881', '001/005.05/02180', '001/005.06/02185', '001/005.07/02892', '001/005.08/02226', '001/005.09/02240', '001/005.10/02239', '001/005.11/02196', '001/005.11/02197', '001/005.11/02198', '001/005.11/02199', '001/005.11/02200', '001/005.11/02201', '001/006.01/02188', '001/006.01/02189', '001/006.01/02190', '001/006.01/02231', '001/006.02/02170', '001/006.02/02171', '001/006.02/02172', '001/006.02/02174', '001/006.02/02175', '001/006.03/02233', '001/006.03/02234', '001/006.04/04862', '001/006.04/05044', '001/006.05/01153', '001/006.05/02192', '001/006.05/02193', '001/006.05/03197', '001/006.05/03198', '001/006.06/02214', '001/006.06/02218', '001/006.06/02219', '001/006.06/02222', '001/006.06/04563', '001/006.07/02230', '001/006.08/02224', '001/006.09/01604', '001/006.11/02128', '001/006.11/02131', '001/006.11/02132', '001/006.11/02133', '001/006.11/02134', '001/006.11/02135', '001/006.11/02136', '001/006.12/02130', '001/006.12/02168', '001/006.12/02169', '001/006.12/02176', '001/006.12/02213', '001/006.12/02215', '001/006.12/02217', '001/006.12/02221', '001/006.12/02223', '001/006.14/02195', '001/006.15/02178', '001/006.16/02191', '001/008.03/02014', '001/008.09/03746', '001/008.12/03745', '001/009.12/03003', '001/010.17/02430', '001/011.03/02460', '001/011.08/01338', '001/011.17/02422', '001/011.18/01351', '001/012.01/02998', '001/012.01/02999', '001/012.01/03000', '001/012.01/03001', '001/012.01/03002', '001/012.02/02004', '001/012.16/02267', '001/013.15/02941', '001/013.15/02942', '001/016.04/01352', '001/016.07/05028', '001/016.08/04818', '001/016.09/04819', '001/016.09/04821', '001/016.10/02976', '001/016.10/02977', '001/016.14/01664', '001/016.14/01665', '001/016.14/01670', '001/016.15/01230', '001/016.15/02905', '001/016.16/02904', '001/016.17/02865', '001/016.19/02980', '001/018.03/01995', '001/019.05/01743', '001/019.05/01744', '001/019.05/01745', '001/028.07/00597', '001/029.11/03078', '001/029.19/02432', '001/031.01/05020', '001/033.18/00578', '001/034.09/00575', '001/034.09/00576', '001/036.03/04829', '001/040.06/04250', '001/042.01/04719', '001/042.01/04749', '001/045.01/01848', '001/045.06/01601', '001/045.06/01877', '001/045.06/01882', '001/047.06/03135', '001/048.01/01125', '001/052.02/04739', '001/052.02/04879', '001/052.03/04249', '001/057.01/01771', '001/057.01/01772', '001/057.01/03403', '001/057.02/04953', '001/057.03/01775', '001/057.03/01790', '001/057.03/01791', '001/057.04/01822', '001/057.05/03903', '001/057.05/05075', '001/057.07/00965', '001/057.08/01785', '001/057.10/04812', '001/057.10/04813', '001/057.11/02899', '001/058.02/01843', '001/058.02/01844', '001/058.02/01846', '001/058.02/01847', '001/058.02/04890', '001/058.04/04375', '001/058.05/01814', '001/058.05/01815', '001/058.05/01819', '001/058.05/01820', '001/058.05/01823', '001/058.05/01824', '001/058.05/04377', '001/058.06/04803', '001/058.07/01798', '001/058.07/01807', '001/058.07/01808', '001/058.07/01811', '001/059.01/04376', '001/059.01/04378', '001/061.03/00632', '001/061.23/01553', '001/065.06/05416', '001/067.07/04780', '001/068.14/04996', '001/069.05/05027', '001/069.06/04798', '001/074.05/00844', '001/074.06/03685', '001/076.05/04754', '001/076.05/04781', '001/076.08/04931', '001/076.18/01076', '001/077.02/01075', '001/082.04/04936', '001/083.11/02649', '001/087.01/04788'];
		$data = "id\tDate\tState\tPlace\tTitle\tDirection\tTroupe\tVenue\tWriter\tEvent\tDesc\tPages\tSize\tAccessionCards\tAccessionNumber\tAdaptation\tArticle\tArtist\tAssistance\tBasiclesson\tChoreography\tCodirection\tConcept\tConceptdirection\tCostume\tDance\tDescription\tDramatization\tEquipment\tGimmickry\tHost\tIdea\tLanguage\tLight\tLightconcept\tMake-up\tManagement\tManager\tMaterial\tMusic\tMusic Direction\tMusicconcept\tOrganization\tPlaces\tProducer\tProduction\tProjection\tSong\tSound\tSponsor\tStage\tStageconcept\tStates\tStory\tTime\tTranslation\tVersion\n";
		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents(METADATA_URL . $jsonFile . '/index.json');
			$content = json_decode($contentString, true);
			// $keys = array_merge($keys, array_keys($content));
			$keys = ['Date', 'State', 'Place', 'Title', 'Direction', 'Troupe', 'Venue', 'Writer', 'Event', 'Desc', 'Pages', 'Size', 'AccessionCards', 'AccessionNumber', 'Adaptation', 'Article', 'Artist', 'Assistance', 'Basiclesson', 'Choreography', 'Codirection', 'Concept', 'Conceptdirection', 'Costume', 'Dance', 'Description', 'Dramatization', 'Equipment', 'Gimmickry', 'Host', 'Idea', 'Language', 'Light', 'Lightconcept', 'Make-up', 'Management', 'Manager', 'Material', 'Music', 'Music Direction', 'Musicconcept', 'Organization', 'Places', 'Producer', 'Production', 'Projection', 'Song', 'Sound', 'Sponsor', 'Stage', 'Stageconcept', 'States', 'Story', 'Time', 'Translation', 'Version'];
			$string = $jsonFile . "\t";
			foreach ($keys as $key) {
				
				if(isset($content[$key]))
					$string .=  $content[$key] . "\t";
				else
					$string .=  "\t";
			}
			$data .= $string . "\n";
		}
		file_put_contents(PHY_BASE_URL . 'sumne.txt', $data);
	}
}

?>
