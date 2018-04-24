<?php

class articles extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function all($query = [], $letter = DEFAULT_LETTER) {

		// Albhabetic list of article displayed letter wise
		// articles/all/A

		// Get data from api
		// get('api/articles?title=@^' . $letter)

		$url = BASE_URL . 'api/articles?title=@^' . $letter . '&sort=title';
		$result = json_decode($this->model->getDataFromApi($url), true);
		$result['pageTitle'] = ARCHIVE . ' > ' . ARTICLES;
		$url = BASE_URL . 'api/alphabet/';
		$result['alphabet'] = json_decode($this->model->getDataFromApi($url), true)['title'];
		($result) ? $this->view('articles/articles', json_encode($result)) : $this->view('error/index');
	}

	public function toc($query = []) {

		// Table of contents. This accepts one or more structural parameters
		// articles/toc?volume=001&part=01
		// articles/toc?number=123

		// Get data from api
		// get('api/articles?volume=001&part=01)

		require_once 'application/views/viewHelper.php';
		$viewHelper = new viewHelper();

		$filter = $this->model->filterArrayToString($query);
		$url = BASE_URL . 'api/articles?' . $filter;
		$result = json_decode($this->model->getDataFromApi($url), true);
		$result['pageTitle'] = ARCHIVE . ' > ' . TOC . ' > ' . ARCHIVE_YEAR . ' ' . $viewHelper->roman2Kannada($viewHelper->rlZero($query['year'])) . ', ' . ARCHIVE_MONTH . ' '. $viewHelper->kannadaMonth($query['month']);
		($result) ? $this->view('articles/articles', json_encode($result)) : $this->view('error/index');
	}

	public function author($query = [], $author = DEFAULT_STRING) {

		// Chronological list of article written by an author
		// articles/author/author1

		// Get data from api
		// get('api/articles?author=author1

		$url = BASE_URL . 'api/articles?author.name=' . $this->model->filterSpecialChars($author);
		$result = json_decode($this->model->getDataFromApi($url), true);
		$result['pageTitle'] = ARCHIVE . ' > ' . AUTHORS . ' > ' . $author;
		($result) ? $this->view('articles/articles', json_encode($result)) : $this->view('error/index');
	}

	public function category($query = [], $category = DEFAULT_STRING, $categoryValue = DEFAULT_STRING) {

		// Chronological list of article in a given category
		// articles/category/feature/Editorial

		// Get data from api
		// get('api/articles?feature=Editorial

		$url = BASE_URL . 'api/articles?' . $category . '=' . $this->model->filterSpecialChars($categoryValue);
		$result = json_decode($this->model->getDataFromApi($url), true);
		$result['pageTitle'] = ARCHIVE . ' > ' . constant(strtoupper($category)) . ' > ' . $categoryValue;
		($result) ? $this->view('articles/articles', json_encode($result)) : $this->view('error/index');
	}

	public function search($query = []) {

		// Chronological list of article served as search results
		// articles/search?title='value1'&author.name='value1'
		// Here while doing the api call, care is to be taken to invoke regular expression for each search term

		$query = array_filter($query); unset($query['submit']);

		foreach ($query as $key => $value)
			$query[$key] = '@' . $value;

		$filterString = $this->model->filterArrayToString($query);
		$url = BASE_URL . 'api/articles?' . $filterString;
		$result = json_decode($this->model->getDataFromApi($url), true);
		$result['pageTitle'] = ARCHIVE . ' > ' . SEARCH_RESULTS;
		($result) ? $this->view('articles/articles', json_encode($result)) : $this->view('error/index');
	}
}

?>
