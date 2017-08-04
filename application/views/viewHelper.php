<?php

class viewHelper extends View {

    public function __construct() {

    }

    public function displayDataInForm($json, $foreignKeys='') {

        $data = json_decode($json, true);
        
        // if ($auxJson) $data = array_merge($data, json_decode($auxJson, true));

        $count = 0;
        $formgroup = 0;

        foreach ($data as $key => $value) {
             //~ echo "Key: $key; Value: $value\n";
             if($key == 'albumID') {
				
				if (preg_match('/__/', $value)) {
				
					$id = preg_split('/__/', $value);
					$value = $id[1];
				}
			 }
    
            $disable = (($key == 'id') || ($key == 'albumID') || ($key == 'ForeignKeyId')|| ($key == 'ForeignKeyType'))? 'readonly' : '';
            
            $editForeignKey = "";

            if($foreignKeys){
                if(in_array($key, $foreignKeys)){
                    $editForeignKey = '<a  class="editDetails" href="' . BASE_URL . 'edit/foreignkey/'. urlencode($key) . '/'. urlencode($value) . '">Edit Event</a>';            
                }
            }
            echo '<div class="form-group" id="frmgroup' . $formgroup . '">' . "\n";
            echo '<input type="text" class="form-control edit key" name="id'. $count . '[]"  value="' . $key . '"' . $disable  . ' />';
            echo '<input type="text" class="form-control edit value" name="id'. $count . '[]"  value="' . $value . '"' . $disable . ' />';
    
            if($disable != "readonly")
                echo '<i class="fa fa-times" title="Remove field" onclick="removeUpdateDataElement(\'frmgroup'. $formgroup .'\')" value="Remove"></i>&nbsp;&nbsp;' . $editForeignKey . "\n";

            echo '</div>' . "\n";
            $count++;
            $formgroup++;
        }

        echo '<div id="keyvalues">' . "\n";
        echo '</div>' . "\n";
        echo '<i class="fa fa-plus" title="Add new field" id="keyvaluebtn" onclick="addnewfields(keyvaluebtn)"></i>' . "\n";
        echo '<input class="updateSubmit" type="submit" id="submit" value="Update Data" />' . "\n";
    }

    public function includeEditButton($albumID) {

        if(isset($_SESSION['login']))
        	echo '<ul class="list-unstyled"><li><a class="editDetails" href="' . BASE_URL . 'edit/archives/' . $albumID . '">Edit Details</a></li></ul>';
    }

    public function formatDisplayString($str){
		
		if(preg_match('/^\d{4}\-/', $str))
		$str = preg_replace('/\b(\d)\b/',"0$1",$str);

        if(preg_match('/^\d{4}\-\d{2}\-\d{2}/', $str)) {

            $str = $this->formatDate($str);
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

            return '<li><a href="' . DATA_URL . $id . '/index.pdf" target="_blank">Click here to view PDF</a></li>'; 
        }
    }
}

?>
