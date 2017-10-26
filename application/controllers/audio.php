<?php

class audio extends Controller {

	public function __construct() {
		
		parent::__construct();
	}

	public function player($query = [], $id = '') {

		$data['id'] = preg_replace('/(.*?)_(.*?)_(.*)/', "$1/$2/$3", $id);

		$leaves = glob(PHY_DATA_URL . $data['id'] . '/*' . PHOTO_FILE_EXT);
		$firstLeaf = array_shift($leaves);

		$data['cover'] = ($firstLeaf) ? str_replace(PHY_DATA_URL, DATA_URL, $firstLeaf) : STOCK_IMAGE_URL . 'default-image.png';

	    require_once 'application/views/audio/player.php';
	}
}

?>
