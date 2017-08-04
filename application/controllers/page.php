<?php

class page extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function index() {
		
		$this->view('flat/Home/index');
	}

	public function flat() {

		$params = (func_get_args());
		// Remove url query from params
		unset($params[0]);

		$path = 'flat/' . implode('/', $params);
		$this->view($path);
	}
}

?>