<?php

class apiModel extends Model {

	public function __construct() {

		parent::__construct();
	}

	public function getDistinct($param, $filter) {

		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTICLES_COLLECTION);
		$filter = $this->reformFilter($filter);
		
		$match = ['$match' => $filter];
		$aggregatePipeline = [
				[ '$group' => [ '_id' => [ 'Param' => '$' . $param ], 'count' => [ '$sum' => 1 ]]],
				[ '$sort' => [ '_id' => 1 ] ],
				[ '$skip' => NO_SKIP ],
				[ '$limit' => NO_LIMIT ]
			];

		// Add match to aggregate pipeline only if filter is not null
		if ($filter) array_unshift($aggregatePipeline, $match);

		$iterator = $collection->aggregate($aggregatePipeline);
	
		$values = [];
		foreach ($iterator as $row) {
			
			$array = (array) $row['_id']['Param'];
			$value['item'] = array_pop($array);
			$value['count'] = $row['count'];
			array_push($values, $value);
		}

		$data = ['param' => $param, 'values' => $values, 'filter' => $filter];

		return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}

	public function getArticles($filter, $sort = '') {
		
		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ARTICLES_COLLECTION);
		
		$filter = $this->reformFilter($filter);

		$projection = [ 'projection' => ['_id' => 0] ];
		if($sort) $projection['sort'] = $this->reformSort($sort);

		$iterator = $collection->find($filter, $projection);
	
		$articles = [];
		foreach ($iterator as $row) {
			
			$articles[] = $row;
		}
		
		$data = ['articles' => $articles, 'filter' => $filter, 'sort' => $sort];

		return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}

	public function getAlphabet() {

		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, ALPHABET_COLLECTION);
		$result = $collection->findOne();
		unset($result['_id']);
		
		return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}

	public function reformSort($sort) {

		$values = explode(',', $sort);
		$reformedSort = [];
		foreach ($values as $value) {

			$key = preg_replace('/^\!/', '', $value);
			$value = (preg_match('/^\!/', $value)) ? -1 : 1;
			$reformedSort[$key] = $value;
		}

		return $reformedSort;
	}

	public function reformFilter($filter) {

		$reformedFilter = [];
		foreach ($filter as $key => $value) {
			
			// Values beginning with @ are treated as regular expressions
			if(preg_match('/^@/', $value)) {

				$value = ['$regex' => preg_replace('/^@/', '', $value)];
			}

			// Here _ in key is replaced with dot. PHP had initially done this change
			$reformedFilter{str_replace('_', '.', $key)} = $value;
		}

		return $reformedFilter;
	}
}

?>
