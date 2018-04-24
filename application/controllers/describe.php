<?php

class describe extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function artefact($query = [], $id = '') {

		$id = preg_replace('/(.*?)_(.*?)_(.*)/', "$1/$2/$3", $id);
		$artefact['details'] = $this->model->getArtefactDetails($id);

		if($artefact['details']) {
		
			$artefact['images'] = $this->model->getArtefactImages($id);
			$artefact['neighbours'] = $this->model->getNeighbourhood($artefact['details'], $query);
			$artefact['filter'] = $this->model->filterArrayToString($query);
			$artefact = $this->model->includeExternalResources($artefact);

			$artefact['details'] = $this->model->unsetControlParams($artefact['details']);
		}

		($artefact['details']) ? $this->view('describe/artefact', $artefact) : $this->view('error/index');
	}

	public function transcription($query = [], $id = '') {

		$id = preg_replace('/(.*?)_(.*?)_(.*)/', "$1/$2/$3", $id);
		$artefact['details'] = $this->model->getArtefactDetails($id);

		if($artefact['details']) {
		
			$artefact['images'] = $this->model->getArtefactImages($id);
			$artefact['details'] = $this->model->unsetControlParams($artefact['details']);
		}

		($artefact['details']) ? $this->view('describe/sideBySide', $artefact) : $this->view('error/index');
	}
}

?>
