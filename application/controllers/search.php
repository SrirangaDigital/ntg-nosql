<?php

class search extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function field($query = []) {
		
		if(!(isset($query['term']))) {

			$this->view('error/index');
			return;
		}

		$data['term'] = $query['term'];
		$page = (isset($query['page'])) ? $query['page'] : "1";

		$result = $this->model->getSearchResults($data, $page);

		if($page == '1')
			($result != 'noData') ? $this->view('search/result', $result) : $this->view('error/noResults', 'search/index/');
		else
			echo json_encode($result);
	}
}

?>
