<?php

class api extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function index() {

	}

	public function distinct($query = [], $param = DEFAULT_PARAM) {

		$data = $this->model->getDistinct($param, $query);
		echo $data;
	}
		
	public function articles($query = []) {

		if (!isset($query['sort'])) $query['sort'] = '';
		$sort = $query['sort'];	unset($query['sort']);
		$data = $this->model->getArticles($query, $sort);
		echo $data;
	}

	public function alphabet() {

		echo $this->model->getAlphabet();
	}
}

?>
