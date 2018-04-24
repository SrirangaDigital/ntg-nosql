<?php

class listingModel extends Model {


	public function __construct() {

		parent::__construct();
	}

	public function getCategories($type, $selectKey, $page, $filter = ''){
		
		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTEFACT_COLLECTION);

		$skip = ($page - 1) * PER_PAGE;
		$limit = PER_PAGE;

		$matchFilter = $this->preProcessQueryFilter($filter);
		$match = [ 'DataExists' => $this->dataShowFilter, 'Type' => $type ] + $matchFilter;

		$iterator = $collection->aggregate(
				 [
					[ '$match' => $match ],
					[ '$group' => [ '_id' => [ 'Category' => '$' . $selectKey, 'Type' => '$Type' ], 'count' => [ '$sum' => 1 ]]],
					[ '$sort' => [ '_id' => 1 ] ],
					[ '$skip' => $skip ],
					[ '$limit' => $limit ]
				]
			);

		$data = [];

		$precastSelectKeys = $this->getPrecastKey($type, 'selectKey');
		$selectKeyIndex = array_search($selectKey, $precastSelectKeys);
		$nextSelectKey = (isset($precastSelectKeys[$selectKeyIndex + 1])) ? $precastSelectKeys[$selectKeyIndex + 1] : false;

		$urlFilter = $this->filterArrayToString($filter);
		$urlFilter = ($urlFilter) ? '&' . $urlFilter : '';

		$auxiliary = ['parentType' => $type, 'selectKey' => $selectKey, 'filter' => $filter];

		foreach ($iterator as $row) {
			
			$category['name'] = (isset($row['_id']['Category'])) ? $row['_id']['Category'] : MISCELLANEOUS_NAME;
			$filter[$selectKey] = (isset($row['_id']['Category'])) ? $category['name'] : 'notExists';

			$category['nameURL'] = $this->filterSpecialChars($category['name']);
		
			$category['parentType'] = $row['_id']['Type'];
			$category['leafCount'] = $row['count'];
			$category['thumbnailPath'] = $this->getThumbnailPath($this->getRandomID($type, $filter, $category['leafCount']));

            if(!(isset($row['_id']['Category'])))
            	$category['nameURL'] = 'notExists';
			
            if($nextSelectKey)
    			$category['nextURL'] = BASE_URL . 'listing/categories/' . $category['parentType'] . '/?select=' . $nextSelectKey . '&' . $selectKey . '=' . $category['nameURL'] . $urlFilter;
            else
                $category['nextURL'] = BASE_URL . 'listing/artefacts/' . $category['parentType'] . '?' . $selectKey . '=' . $category['nameURL'] . $urlFilter;
            
			array_push($data, $category);
		}
		
		// This marks the end of sifting through results
		if($data){

			$data['auxiliary'] = $auxiliary;
		}
		else
			$data = 'noData';

		return $data;
	}

	public function getArtefacts($type, $sortKeys, $page, $filter){
		
		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTEFACT_COLLECTION);

		$skip = ($page - 1) * PER_PAGE;
		$limit = PER_PAGE;

		// Form match filter array
		$matchFilter = $this->preProcessQueryFilter($filter);
		$match = ['DataExists' => $this->dataShowFilter, 'Type' => $type] + $matchFilter;

		// Form Projection array
		$projectArray['Type'] = 1;
		foreach ($sortKeys as $key => $value) {
			
			if(!isset($firstKey)) $firstKey = $key;
			$projectArray[$key] = 1;
		}
		$projectArray['id'] = 1;
		$projectArray['sortKeyExists'] = [ '$cond' => [ '$' . $firstKey, '1', '0' ]];

		// Form sort array
		$sortArray['sortKeyExists'] = -1;
		foreach ($sortKeys as $key => $value) {
			
			$sortArray[$key] = $value;
		}
		$sortArray['id'] = 1;

		$iterator = $collection->aggregate(
				 [
					[ '$match' => $match ],
					[ '$project' => $projectArray ],
					[ '$sort' => $sortArray	],
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
			// $artefact['cardName'] = (isset($artefact{$sortKeys[0]})) ? $artefact{$sortKeys[0]} : '';
			
			$artefact['cardName'] = [];
			foreach ($sortKeys as $key => $value) {
					
				if(isset($artefact{$key})) $artefact['cardName'][] =  $viewHelper->formatDisplayString($artefact{$key});
			}
			$artefact['cardName'] = '<span>' . implode('</span><br/><span>', $artefact['cardName']) . '</span>';
			
			array_push($data, $artefact);
		}

		if($data){
			$auxiliary = ['filterString' => $this->filterArrayToString($filter), 'filter' => $filter, 'sortKey' => $sortKeys];
			$data['auxiliary'] = $auxiliary;
		}
		else
			$data = 'noData';

		return $data;
	}
	
	public function getJournalCategories($type, $selectKey, $filter = ''){

		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTICLES_COLLECTION);

		$skip = 0;
		$limit = NO_LIMIT;

		$matchFilter = $this->preProcessQueryFilter($filter);
		$match = [ 'Type' => $type ] + $matchFilter;

		$iterator = $collection->aggregate(
				 [
					[ '$match' => $match ],
					[ '$group' => [ '_id' => [ 'Category' => '$' . $selectKey, 'Type' => '$Type' ], 'count' => [ '$sum' => 1 ]]],
					[ '$sort' => [ '_id' => 1 ] ],
					[ '$skip' => $skip ],
					[ '$limit' => $limit ]
				]
			);

		$data = [];

		$precastSelectKeys = $this->getPrecastKey($type, 'selectKey');
		$selectKeyIndex = array_search($selectKey, $precastSelectKeys);
		$nextSelectKey = (isset($precastSelectKeys[$selectKeyIndex + 1])) ? $precastSelectKeys[$selectKeyIndex + 1] : false;

		$urlFilter = $this->filterArrayToString($filter);
		$urlFilter = ($urlFilter) ? '&' . $urlFilter : '';

		$auxiliary = ['parentType' => $type, 'selectKey' => $selectKey, 'filter' => $filter];

		foreach ($iterator as $row) {
			
			$category['name'] = (isset($row['_id']['Category'])) ? $row['_id']['Category'] : MISCELLANEOUS_NAME;
			$filter[$selectKey] = (isset($row['_id']['Category'])) ? $category['name'] : 'notExists';

			$category['nameURL'] = $this->filterSpecialChars($category['name']);
			$category['parentType'] = $row['_id']['Type'];
			// $category['leafCount'] = $row['count'];
			
            if(!(isset($row['_id']['Category'])))
            	$category['nameURL'] = 'notExists';
			
            if($nextSelectKey)
    			$category['nextURL'] = BASE_URL . 'listing/structure/' . $category['parentType'] . '/?select=' . $nextSelectKey . '&' . $selectKey . '=' . $category['nameURL'] . $urlFilter;
            else
                $category['nextURL'] = BASE_URL . 'articles/toc?' . $selectKey . '=' . $category['nameURL'] . $urlFilter;
            array_push($data, $category);
		}
		if($data){

			$data['auxiliary'] = $auxiliary;
		}	

		return $data;
	}
}

?>
