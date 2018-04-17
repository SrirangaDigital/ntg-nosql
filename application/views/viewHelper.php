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
}

?>
