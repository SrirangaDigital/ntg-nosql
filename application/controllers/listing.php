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
		$sortKey = $this->model->getPrecastKey($type, 'sortKey');

		$artefacts = $this->model->getArtefacts($type, $sortKey, $page, $query);

		if($page == '1')
			($artefacts != 'noData') ? $this->view('listing/artefacts', $artefacts) : $this->view('error/index');
		else
			echo json_encode($artefacts);
	}
}

?>