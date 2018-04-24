<?php

class articleModel extends Model {

	public function __construct() {

		parent::__construct();
	}

	public function generatePDF($root, $year, $month, $pageRange) {

		$pdfFile = PHY_DATA_URL . $root. '/' . $year . '/' . $month . '/index.pdf';
		$articlePdf = $root . '_' . $year . '_' . $month . '_' . $pageRange;
		$cmd = 'pdftk ' . $pdfFile . ' cat ' . $pageRange . ' output ' . PHY_DOWNLOAD_URL . $articlePdf  . '.pdf';
		
		exec('find ' . PHY_DOWNLOAD_URL . ' -mmin +10 -type f -name "*.pdf" -exec rm {} \;');
		exec($cmd, $output, $return);

		return $return;
	}
}
?>
