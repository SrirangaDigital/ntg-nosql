<?php

class Controller {
	
	public function __construct() {
		
		if(!isset($_SESSION)) session_start();
	}

	public function loadModel($model) {

		$path = 'application/models/' . $model . '.php';

		if(file_exists($path)) {

			require_once $path;
			return new $model();
		}
	}
	
	public function view($path, $data = array()) {

		$view = new View();
		$model = new Model();
		
		// Get Navigation array in nested form	
		$navigation = $view->getNavigation(PHY_FLAT_URL);
		// Get folder list in flat form
		$folderList = $view->getFolderList($navigation);
		// Get actual path void of sorting numbers
		$actualPath = $view->getActualPath($path, $folderList);
		// Actual path is given path for dynamic pages
		if(!($actualPath)) $actualPath = $path;
		// Show Page

		(preg_match('/flat|error|prompt/', $path)) ? $view->showFlatPage($data, $path, $actualPath, $navigation) : $view->showDynamicPage($data, $path, $actualPath, $navigation);
	}

	public function isLoggedIn() {

		if(isset($_SESSION['login'])) {

			return ($_SESSION['login'] == 1) ? True : False;
		}
		else {

			return False;
		}
	}

	public function redirect($path) {

		@header('Location: ' . BASE_URL . $path);
	}

	public function absoluteRedirect($path) {

		@header('Location: ' . $path);
	}

}

?>
