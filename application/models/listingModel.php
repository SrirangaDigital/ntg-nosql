<?php

class listingModel extends Model {

	public function __construct() {

		parent::__construct();
	}

	public function getCategories($type, $selectKey, $page){
		
		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTEFACT_COLLECTION);

		$skip = ($page - 1) * PER_PAGE;
		$limit = PER_PAGE;

		$iterator = $collection->aggregate(
				 [
					[ '$match' => [ 'Type' => $type ] ],
					[ '$group' => [ '_id' => [ 'Category' => '$' . $selectKey, 'Type' => '$Type' ], 'count' => [ '$sum' => 1 ]]],
					[ '$sort' => [ '_id' => 1 ] ],
					[ '$skip' => $skip ],
					[ '$limit' => $limit ]
				]
			);

		$data = [];
		foreach ($iterator as $row) {
			
			$category['name'] = (isset($row['_id']['Category'])) ? $row['_id']['Category'] : MISCELLANEOUS_NAME;

			$category['nameURL'] = $this->filterSpecialChars($category['name']);
		
			$category['parentType'] = $row['_id']['Type'];
			$category['leafCount'] = $row['count'];
			$category['thumbnailPath'] = $this->getThumbnailPath($this->getRandomID($type, $selectKey, $category['name'], $category['leafCount']));

			array_push($data, $category);
		}

		// This marks the end of sifting through results
		if($data){
			$auxiliary = ['parentType' => $type];
			$data['auxiliary'] = $auxiliary;
		}
		else
			$data = 'noData';

		return $data;
	}

	public function getArtefacts($type, $category, $selectKey, $sortKey, $page){
		
		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTEFACT_COLLECTION);

		$skip = ($page - 1) * PER_PAGE;
		$limit = PER_PAGE;

		$match = ($category == MISCELLANEOUS_NAME) ? ['Type' => $type, $selectKey => ['$exists' => false] ] : [ 'Type' => $type, $selectKey => $category ];
		$iterator = $collection->aggregate(
				 [
					[ '$match' => $match ],
					[ 
						'$project' => [
							'Type' => 1,
							$selectKey => 1,
							$sortKey => 1,
							'id' => 1,
							'sortKeyExists' => [ '$cond' => [ '$' . $sortKey, '1', '0' ]]
						]
					],
					[
						'$sort' => [
							'sortKeyExists' => -1,
							$sortKey => 1,
							'id' => 1
						]
					],
					[ '$skip' => $skip ],
					[ '$limit' => $limit ]
				]
			);

		$data = [];

		$viewHelper = new viewHelper();
	
		foreach ($iterator as $row) {
	
			$artefact = $row;
			$artefact = $this->unsetControlParams($artefact);
			$artefact['thumbnailPath'] = $this->getThumbnailPath($artefact['id']);
			$artefact['idURL'] = str_replace('/', '_', $artefact['id']);
			$artefact['cardName'] = (isset($artefact{$sortKey})) ? $artefact{$sortKey} : '';

			$artefact['cardName'] = $viewHelper->formatDisplayString($artefact['cardName']);

			array_push($data, $artefact);
		}

		if($data){
			$auxiliary = ['category' => $this->filterSpecialChars($category), 'selectKey' => $selectKey, 'sortKey' => $sortKey];
			$data['auxiliary'] = $auxiliary;
		}
		else
			$data = 'noData';

		return $data;
	}
}

?>
