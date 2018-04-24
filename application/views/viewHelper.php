<?php

class viewHelper extends View {

    public function __construct() {

    }

    public function includeEditButton($albumID) {

        if(isset($_SESSION['login']))
        	echo '<ul class="list-unstyled"><li><a class="editDetails" href="' . BASE_URL . 'edit/archives/' . $albumID . '">Edit Details</a></li></ul>';
    }

    public function formatDisplayString($str){
		
		if(preg_match('/^\d{4}\-/', $str))
			$str = preg_replace('/\b(\d)\b/',"0$1",$str);

        if(preg_match('/^\d{4}\-\d{2}\-\d{2}/', $str)) {

            $dates = array_filter(array_map('trim', preg_split('/,|;/', $str)));
            $data = [];

            foreach ($dates as $date)
                array_push($data, $this->formatDate($date));

            $str = implode(', ', $data);
        }

        return $str;
    }

    public function formatDate($dateString = '') {

        date_default_timezone_set('Asia/Kolkata');

        $dateStringVars = explode('-', $dateString);

        // Date formatting should include cases like 2105-10-00 and 2015-00-00

        $realDateString = $dateString;
        $realDateString = preg_replace('/\-00/', '-01', $realDateString);
        $timestamp = strtotime($realDateString);

        $dateFormatted = '';

        $dateFormatted = (intval($dateStringVars[2])) ? $dateFormatted . date('j', $timestamp) . '<sup>' . date('S', $timestamp) . '</sup>' : $dateFormatted;
        $dateFormatted = (intval($dateStringVars[1])) ? $dateFormatted . ' ' . date('F', $timestamp) : $dateFormatted;
        $dateFormatted = (intval($dateStringVars[0])) ? $dateFormatted . ' ' . date('Y', $timestamp) : $dateFormatted;

        return $dateFormatted;
    }

    public function linkPDFIfExists($id){

        if(file_exists(PHY_DATA_URL . $id . '/index.pdf')) {

            echo '<li><a href="' . BASE_URL . 'artefact/pdf/' . str_replace('/', '_', $id) . '" target="_blank">Click here to view PDF</a></li>'; 
        }

        if(file_exists(PHY_DATA_URL . $id . '/transcription.pdf')) {

            echo '<li><a href="' . BASE_URL . 'describe/transcription/' . str_replace('/', '_', $id) . '" target="_blank">Transcript (Side-by-side View)</a></li>';
        }

        return;
    }

    public function includeAccessionCards($accessionCards){

        if(!$accessionCards) return '';

        $accessionCardsHtml  = '<div id="viewCardImages">';
        foreach (explode(',', $accessionCards) as $card) {
            
            $card = trim($card);
            $cardThumbPath = PUBLIC_URL . 'accessionCards/' . preg_replace('/(\d+)\.(.*)/', "$1/thumbs/$1.$2.jpg", $card);
            $cardPath = str_replace('thumbs', '', $cardThumbPath);
            
            if(file_exists(str_replace(PUBLIC_URL, PHY_PUBLIC_URL, $cardThumbPath)))
                $accessionCardsHtml .= '<img class="img-responsive" data-original="' . $cardPath . '" src="' . $cardThumbPath . '">';
        }
        $accessionCardsHtml .= '</div>';

        return $accessionCardsHtml;
    }

    public function displayToc($toc){

        $tocHtml = '
            <div id="toc"><p><strong>Table of Contents:</strong></p><ul class="toc">';

        foreach ($toc as $row) {

            $page = explode(',', $row['Page'])[0];

            $tocHtml .= '<li><a data-href="image_' . $page . '">' . $row['Title'] . '</a><br />';
            if(isset($row['Author'])) $tocHtml .= '<span class="author">' . $row['Author'] . '</span>';
            $tocHtml .= '</li>';
        }

        $tocHtml .= '</ul></div>';
        return $tocHtml;
    }

    public function getStructurePageTitle($filter){

        $pageTitle = ARCHIVE . ' > ' . NAV_ARCHIVE_VOLUME;

        foreach ($filter as $key => $value) {
                
            $pageTitle .= ' > ' . constant('ARCHIVE_' . strtoupper($key)) . ' ' . $this->roman2Kannada($this->rlZero($value));
        }

        return $pageTitle;
    }

    public function getDisplayName($filter){

        $displayString = '';

        foreach ($filter as $key => $value) {
                
            $displayString .= constant('ARCHIVE_' . strtoupper($key)) . ' ' . $this->roman2Kannada($this->rlZero($value));
        }

        return $displayString;
    }

    public function getCoverPage($filter){

        $coverURL = PHY_DATA_URL . PRASADA .'/'; 
        $coverURL .= (isset($filter['year'])) ? $filter['year'] . '/' : '';

        if (!(isset($filter['month']))) {
            
            $months = glob($coverURL . '*' ,GLOB_ONLYDIR);
            $coverURL .= str_replace($coverURL, '', $months[0]) . '/';
        }
        else{
            $coverURL .= $filter['month'] . '/';
        }

        $coverURL .= 'cover.jpg';
        return (file_exists($coverURL)) ? str_replace(PHY_DATA_URL, DATA_URL, $coverURL) : STOCK_IMAGE_URL . 'generic-cover.jpg'; 
    }

    public function roman2Kannada($str){

        $str = str_replace('0', '೦', $str);
        $str = str_replace('1', '೧', $str);
        $str = str_replace('2', '೨', $str);
        $str = str_replace('3', '೩', $str);
        $str = str_replace('4', '೪', $str);
        $str = str_replace('5', '೫', $str);
        $str = str_replace('6', '೬', $str);
        $str = str_replace('7', '೭', $str);
        $str = str_replace('8', '೮', $str);
        $str = str_replace('9', '೯', $str);
        return $str;
    }

    public function rlZero($term) {

        $term = preg_replace('/^0+/', '', $term);
        $term = preg_replace('/\-0+/', '-', $term);
        return $term;
    }

    public function kannadaMonth($month) {
        # code...

        $month = preg_replace('/01/', 'ಜನವರಿ', $month);
        $month = preg_replace('/02/', 'ಫೆಬ್ರವರಿ', $month);
        $month = preg_replace('/03/', 'ಮಾರ್ಚ್', $month);
        $month = preg_replace('/04/', 'ಏಪ್ರಿಲ್', $month);
        $month = preg_replace('/05/', 'ಮೇ', $month);
        $month = preg_replace('/06/', 'ಜೂನ್', $month);
        $month = preg_replace('/07/', 'ಜುಲೈ', $month);
        $month = preg_replace('/08/', 'ಆಗಸ್ಟ್', $month);
        $month = preg_replace('/09/', 'ಸೆಪ್ಟೆಂಬರ್', $month);
        $month = preg_replace('/10/', 'ಅಕ್ಟೋಬರ್', $month);
        $month = preg_replace('/11/', 'ನವೆಂಬರ್', $month);
        $month = preg_replace('/12/', 'ಡಿಸೆಂಬರ್', $month);
    
        return $month;
    }
}

?>
