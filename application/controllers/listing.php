<?php

class listing extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function categories($query = [], $type = DEFAULT_TYPE) {

		if($type == 'Miscellaneous') $this->redirect('listing/artefacts/Miscellaneous/' . MISCELLANEOUS_NAME);

		$page = (isset($query['page'])) ? $query['page'] : "1";

		$selectKey = $this->model->getPrecastKey($type, 'selectKey');

		if(!($selectKey)) {$this->view('error/index');return;}

		$categories = $this->model->getCategories($type, $selectKey, $page);

		if($page == '1')
			($categories != 'noData') ? $this->view('listing/categories', $categories) : $this->view('error/index');
		else
			echo json_encode($categories);
	}

	public function artefacts($query = [], $type = DEFAULT_TYPE, $category = '') {

		$category = str_replace('_', '/', $category);
		$category = htmlspecialchars_decode($category, ENT_QUOTES);

		$page = (isset($query['page'])) ? $query['page'] : "1";

		$selectKey = $this->model->getPrecastKey($type, 'selectKey');
		$sortKey = $this->model->getPrecastKey($type, 'sortKey');

		if(!($selectKey)) {$this->view('error/index');return;}

		$artefacts = $this->model->getArtefacts($type, $category, $selectKey, $sortKey, $page);

		if($page == '1')
			($artefacts != 'noData') ? $this->view('listing/artefacts', $artefacts) : $this->view('error/index');
		else
			echo json_encode($artefacts);
	}
}

?>