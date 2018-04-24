<?php

class listing extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function categories($query = [], $type = DEFAULT_TYPE) {

		if($type == 'Miscellaneous') $this->redirect('listing/artefacts/Miscellaneous/' . MISCELLANEOUS_NAME);

		$query = $this->model->preProcessURLQuery($query);

		$query['select'] = (isset($query['select'])) ? $query['select'] : ''; $selectKey = $query['select']; unset($query['select']);
		$query['page'] = (isset($query['page'])) ? $query['page'] : "1"; $page = $query['page']; unset($query['page']);

		$precastSelectKeys = $this->model->getPrecastKey($type, 'selectKey');
		if(array_search($selectKey, $precastSelectKeys) === false) {$this->view('error/index');return;}

		$categories = $this->model->getCategories($type, $selectKey, $page, $query);

		if($page == '1')
			($categories != 'noData') ? $this->view('listing/categories', $categories) : $this->view('error/index');
		else
			echo json_encode($categories);
	}

	public function artefacts($query = [], $type = DEFAULT_TYPE) {

		$query = $this->model->preProcessURLQuery($query);

		$query['page'] = (isset($query['page'])) ? $query['page'] : "1"; $page = $query['page']; unset($query['page']);
		$sortKeys = $this->model->getPrecastKey($type, 'sortKey');

		$artefacts = $this->model->getArtefacts($type, $sortKeys, $page, $query);

		if($page == '1')
			($artefacts != 'noData') ? $this->view('listing/artefacts', $artefacts) : $this->view('error/index');
		else
			echo json_encode($artefacts);
	}
	
	public function structure($query = [], $type = DEFAULT_TYPE) {

		// Get structural params from json-precast
		// listing/structure

		$query = $this->model->preProcessURLQuery($query);
		
		$query['select'] = (isset($query['select'])) ? $query['select'] : ''; $selectKey = $query['select']; unset($query['select']);

		$precastSelectKeys = $this->model->getPrecastKey($type, 'selectKey');

		if(array_search($selectKey, $precastSelectKeys) === false) {$this->view('error/index');return;}
		$categories['values'] = $this->model->getJournalCategories($type, $selectKey, $query);
		
		($categories) ? $this->view('listing/structure', json_encode($categories)) : $this->view('error/index');
	}
	
	public function authors($query = [], $letter = DEFAULT_LETTER) {

		// Albhabetic list of authors displayed letter wise
		// listing/authors/A
		$url = BASE_URL . 'api/distinct/author.name?author.name=@^' . $letter;
		$result = json_decode($this->model->getDataFromApi($url), true);
		$result['pageTitle'] = ARCHIVE . ' > ' . AUTHORS;
		$result['subTitle'] = AUTHORS;
		$result['nextUrl'] = BASE_URL . 'articles/author/';

		// getting alphabet list
		$url = BASE_URL . 'api/alphabet/';
		$result['alphabet'] = json_decode($this->model->getDataFromApi($url), true)['author'];

		($result) ? $this->view('listing/items', json_encode($result)) : $this->view('error/index');
	}
}

?>
