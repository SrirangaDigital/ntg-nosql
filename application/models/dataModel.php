<?php

class dataModel extends Model {

	public function __construct() {

		parent::__construct();
	}

	public function getFilesIteratively($dir, $pattern = '/*/'){

		$files = [];
	    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(rtrim($dir, "/")));
		$regex = new RegexIterator($iterator, $pattern, RecursiveRegexIterator::GET_MATCH);

	    foreach($regex as $file => $object) {
	        
			array_push($files, $file);
	    }

	    sort($files);
	    return ($files);
	}

	public function getIdFromPath($path){

		$id = str_replace(PHY_METADATA_URL, '', $path);
		$id = str_replace('/index.json', '', $id);
		// $id = str_replace('/', '_', $id);
		return $id;
	}

	public function processFulltext($text){

		$text = preg_replace('/\s+/', ' ', $text);
		return $text;
	}

}

?>
