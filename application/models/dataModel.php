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
		//~ $text = $this->praja2Unicode($text);
		return $text;
	}

	public function xml2Json() {

		$xml = simplexml_load_file(PHY_METADATA_URL . PRASADA . '/prasada.xml');

		foreach ($xml->issue as $issue) {
			
			$completeIssue = [];
	
			foreach ($issue->entry as $entry) {

				$completeIssue['volume'] = (string)$issue['vnum'];
				$completeIssue['issue'] = (string)$issue['inum'];
				$completeIssue['year'] = (string)$issue['year'];
				$completeIssue['month'] = (string)$issue['month'];
				$completeIssue['mname'] = (string)$issue['mname'];
				$completeIssue['id'] = PRASADA . '/' . $completeIssue['year'] . '/' . $completeIssue['month'];
				
				$array = [];
				$array['title'] = $entry->title->__toString();
				$array['page'] = $entry->page->__toString();
				$jsonFilePath = PHY_METADATA_URL . PRASADA . '/' . $completeIssue['year'] . '/' . $completeIssue['month'] . '/';
				
				if(preg_match('/0.*\-0.*/', $array['page'], $matches)){

					$splitPage = explode('-', $array['page']);
					$files = glob($jsonFilePath . "text/*.txt");
					$articleStartOffset = array_search($jsonFilePath . 'text/' . $splitPage[0] . '.txt', $files);
					$articleEndOffset = array_search($jsonFilePath . 'text/' . $splitPage[1] . '.txt', $files) + 1;
					$textFiles = array_slice($files, $articleStartOffset, $articleEndOffset - $articleStartOffset);
					$array['relativePageNumber'] = (array_search($jsonFilePath . 'text/' . $splitPage[0] . '.txt', $files)) ? array_search($jsonFilePath . 'text/' . $splitPage[0] . '.txt', $files)+1 : 1;
					$array['relativePageRange'] = $array['relativePageNumber'] . '-' . (array_search($jsonFilePath . 'text/' . $splitPage[1] . '.txt', $files)+1);
						
					$textArray = [];
					$array['fullText'] = [];
					foreach ($textFiles as $textFile) {

						preg_match('/(.*)\/text\/(.*)\.txt/', $textFile, $matches);
						$textArray['page'] = $matches[2];
						$textArray['text'] = trim(file_get_contents($textFile));
						array_push($array['fullText'], $textArray);
					}
				}

				if($entry->author != ''){
					foreach ($entry->author as $author) {

						$arrayArthor = [];
						$arrayArthor['name'] = $author->__toString();
						$array['author'][] = $arrayArthor;
					}
				}

				$completeIssue['toc'][] = $array;
			}
			
			exec("mkdir -p " . $jsonFilePath);
			$json = json_encode($completeIssue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
			file_put_contents($jsonFilePath . 'issue.json' , $json);
		}
	}

	public function insertEntries($collection) {

		$titleAlphabet = [];
		$authorAlphabet = [];
		$jsonFiles = $this->getFilesIteratively(PHY_METADATA_URL , $pattern = '/issue.json$/i');

		foreach ($jsonFiles as $jsonFile) {

			$contentString = file_get_contents($jsonFile);
			$content = json_decode($contentString, true);

			foreach ($content['toc'] as $article) {

				$data = $content;
				$data['Type'] = 'Journal';
				if(isset($data['toc']))	unset($data['toc']);
				$data = $data + $article;
				$data['id'] = $data['id'];
				$data = array_filter($data);
				$result = $collection->insertOne($data);

				// fetching initial letter from author
				if(isset($article['author'])) {

					foreach ($article['author'] as $author) 
					array_push($authorAlphabet, preg_replace('/(^.).*/u', '$1', $author['name']));
				}

				// fetching initial letter from title
				array_push($titleAlphabet, preg_replace('/(^.).*/u', '$1', $article['title']));
			}
		}

		sort($titleAlphabet); sort($authorAlphabet);
		$this->insertAlphabet(array_unique($titleAlphabet), array_unique($authorAlphabet));
	}

	public function insertAlphabet($titleAlphabet, $authorAlphabet) {

		$data = [];
		$db = $this->db->useDB();
		$collection = $this->db->createCollection($db, ALPHABET_COLLECTION);
		$data['title'] = array_values($titleAlphabet);
		$data['author'] = array_values($authorAlphabet);

		$result = $collection->insertOne($data);
	}

	public function praja2Unicode ($text) {

		// Initial parse
		$text = str_replace('"j', 'j"', $text);

		// ya group
		$text = str_replace('O"', 'ಯ', $text);
		$text = str_replace('Ò"', 'ಯೆ', $text);
		$text = str_replace('h"', 'ಯಿ', $text);
		$text = str_replace('O{}', 'ಯ್', $text);

		// ma group
		$text = str_replace('Aj"', 'ಮ', $text);
		$text = str_replace('Au"', 'ಮೆ', $text);
		$text = str_replace('a"', 'ಮಿ', $text);
		$text = str_replace('Aj{}', 'ಮ್', $text);

		// swara

		// Vyanjana
		$text = str_replace('Ûk', 'ಢ್', $text);
		$text = str_replace(';k', 'ಧ್', $text);
		$text = str_replace('=k', 'ಫ್', $text);
		$text = str_replace('_k', 'ಫಿ', $text);
		$text = str_replace(']k', 'ಧಿ', $text);

		$text = str_replace('Š‡', '್ಧ', $text);
		$text = str_replace('Úm', 'ಠ್', $text);
		$text = str_replace(';nk', 'ಥ್', $text);
		$text = str_replace('æl', 'ಭ್', $text);
		$text = str_replace('ül', 'ಛ್', $text);
		$text = str_replace('vl', 'ಭಿ', $text);

		// Lookup
		$text = str_replace('!', 'ಅ', $text);
		$text = str_replace('"', 'ು', $text);
		$text = str_replace('#', 'ಇ', $text);
		$text = str_replace('$', 'ಈ', $text);
		$text = str_replace('%', 'ಉ', $text);
		$text = str_replace('&', 'ಊ', $text);
		$text = str_replace("'", 'ಪ್', $text);
		$text = str_replace('(', 'ಎ', $text);
		$text = str_replace(')', 'ಏ', $text);
		$text = str_replace('*', 'ಐ', $text);
		$text = str_replace('+', 'ಒ', $text);
		$text = str_replace(',', '್ಫ', $text);
		//~ $text = str_replace('-', '', $text);
		// $text = str_replace('.', '್ಝ', $text);
		$text = str_replace('/', 'ಃ', $text);
		// $text = str_replace('0', '೦', $text);
		// $text = str_replace('1', '೧', $text);
		// $text = str_replace('2', '೨', $text);
		// $text = str_replace('3', '೩', $text);
		// $text = str_replace('4', '೪', $text);
		// $text = str_replace('5', '೫', $text);
		// $text = str_replace('6', '೬', $text);
		// $text = str_replace('7', '೭', $text);
		// $text = str_replace('8', '೮', $text);
		// $text = str_replace('9', '೯', $text);
		$text = str_replace(':', 'ತ್', $text);
		$text = str_replace(';', 'ದ್', $text);
		$text = str_replace('<', 'ನ್', $text);
		$text = str_replace('=', 'ಪ್', $text);
		// $text = str_replace('>', 'zz', $text); //?
		$text = str_replace('?', 'ಲ್', $text);
		$text = str_replace('@', 'ಣಿ', $text);
		$text = str_replace('A', 'ವ್', $text);
		$text = str_replace('B', 'ಶ್', $text);
		$text = str_replace('C', 'ಷ್', $text);
		$text = str_replace('D', 'ಸ್', $text);
		$text = str_replace('E', 'ಹ್', $text);
		$text = str_replace('F', 'ಜ್ಞ್', $text);
		$text = str_replace('G', 'ಖ', $text);
		$text = str_replace('H', 'ಙ', $text);
		$text = str_replace('I', 'ಜ', $text);
		$text = str_replace('J', 'ಞ', $text);
		$text = str_replace('K', 'ಟ', $text);
		$text = str_replace('L', 'ಣ', $text);
		$text = str_replace('M', 'ಋ', $text);// ?
		$text = str_replace('N', 'ಬ', $text);
		$text = str_replace('O', 'ಯ್', $text);
		$text = str_replace('P', 'ಲ', $text);
		$text = str_replace('Q', 'ಜ್ಞ', $text);
		$text = str_replace('R', 'ಕಿ', $text);
		$text = str_replace('S', 'ಖಿ', $text);
		$text = str_replace('T', 'ಗಿ', $text);
		$text = str_replace('U', 'ಚಿ', $text);
		$text = str_replace('V', 'ಛಿ', $text); //?
		$text = str_replace('W', 'ಜಿ', $text);
		$text = str_replace('X', 'ಟಿ', $text);
		$text = str_replace('Y', 'ರಿ', $text);
		$text = str_replace('Z', 'ಡಿ', $text);
		//~ $text = str_replace('[', '', $text);
		$text = str_replace("\\", 'ತಿ', $text);
		$text = str_replace(']', 'ದಿ', $text);
		$text = str_replace('^', 'ನಿ', $text);
		$text = str_replace('_', 'ಪಿ', $text);
		$text = str_replace('`', 'ೀ', $text);
		$text = str_replace('a', 'ವಿ', $text);
		$text = str_replace('b', 'ಲಿ', $text);
		$text = str_replace('c', 'ಷಿ', $text);
		$text = str_replace('d', 'ಸಿ', $text);
		$text = str_replace('e', 'ಹಿ', $text);
		$text = str_replace('f', 'ಳಿ', $text);
		$text = str_replace('g', 'ಜ್ಞಿ', $text);
		$text = str_replace('h', 'ಯಿ', $text); //?
		$text = str_replace('i', 'ಶಿ', $text);
		$text = str_replace('j', 'ಅ', $text);
		$text = str_replace('k', 'k', $text); //?
		//~ $text = str_replace('l', '', $text); //?
		//~ $text = str_replace('m', '', $text); //?
		//~ $text = str_replace('n', '', $text); //?
		$text = str_replace('o', 'ಾ', $text);
		$text = str_replace('p', 'ಿ', $text);
		$text = str_replace('q', 'ಆ', $text);
		$text = str_replace('r', 'ೂ', $text);
		//~ $text = str_replace('s', '', $text); //?
		$text = str_replace('t', 't', $text);
		$text = str_replace('u', 'ೆ', $text);
		$text = str_replace('v', 'ಬಿ', $text);
		$text = str_replace('w', 'ೆ', $text);
		$text = str_replace('x', '್ಕ', $text);
		$text = str_replace('y', 'ೄ', $text);
		$text = str_replace('z', 'ೌ', $text);
		//~ $text = str_replace('{', '', $text); //?
		//~ $text = str_replace('|', '', $text); //?
		$text = str_replace('}', '್', $text);
		$text = str_replace('~', 'R', $text);
		$text = str_replace('¡', '್ಖ', $text);
		$text = str_replace('¢', '್ಗ', $text);
		$text = str_replace('£', '್ಘ', $text);
		$text = str_replace('¤', '್ಙ', $text);
		$text = str_replace('¥', '್ಚ', $text);
		//~ $text = str_replace('¦', '', $text);
		$text = str_replace('§', '್ಜ', $text);
		$text = str_replace('©', '್ಞ', $text);
		$text = str_replace('ª', '್ಟ', $text);
		$text = str_replace('«', '್ಠ', $text);
		$text = str_replace('®', '್ಣ', $text);
		$text = str_replace('°', '್ಥ', $text);
		$text = str_replace('±', '್ಱ', $text);
		$text = str_replace('²', '್ೞ', $text);
		$text = str_replace('³', 'ೞ', $text);
		$text = str_replace('´', 'ಱ', $text);
		//~ $text = str_replace('µ', '', $text);
		$text = str_replace('¶', '್ಮ', $text);
		//~ $text = str_replace('·', '', $text);
		$text = str_replace('¸', '್ತ್ರ', $text);
		//~ $text = str_replace('¹', '', $text);
		$text = str_replace('º', '್ವ', $text);
		
		$text = str_replace('»', '್ಶ', $text);
		$text = str_replace('¿', '್ಳ', $text);
		$text = str_replace('À', '್ಹ', $text);
		$text = str_replace('Á', 'ॐ', $text);
		$text = str_replace('Â', '್ಕೃ', $text);
		$text = str_replace('Ã', '್ಬೈ', $text);
		$text = str_replace('Ä', '್ಟ್ರ', $text);
		$text = str_replace('Å', '್ತೃ', $text);
		$text = str_replace('Æ', '್ತೈ', $text);
		$text = str_replace('Ç', '್ಯ', $text);
		$text = str_replace('È', '್ರ', $text);
		$text = str_replace('É', '್ಪ್ರ', $text);
		$text = str_replace('Ê', '್ರೈ', $text);
		$text = str_replace('Ë', '್ಸ್ರ', $text);
		$text = str_replace('Ì', '್ಕ್ಷ', $text);
		$text = str_replace('Í', '್ಕ್ರ', $text);
		$text = str_replace('Î', 'ೆ', $text);
		//~ $text = str_replace('Ï', '', $text); //?
		$text = str_replace('Ñ', 'ೂ', $text);
		$text = str_replace('Ò', 'ಯೆ', $text); //?
		$text = str_replace('Ó', 'ಕ್', $text);
		$text = str_replace('Ô', 'ಗ್', $text);
		$text = str_replace('Õ', 'ಘ್', $text);
		$text = str_replace('Ö', 'ಚ್', $text);
		$text = str_replace('Ø', 'ಜ್', $text);
		$text = str_replace('Ù', 'ಟ್', $text);
		$text = str_replace('Ú', 'ರ್', $text);
		$text = str_replace('Û', 'ಡ್', $text);
		$text = str_replace('Ü', 'ಣ್', $text);
		$text = str_replace('ß', '', $text);
		$text = str_replace('à', 'ಂ', $text);
		$text = str_replace('á', 'ಶ್ರೀ', $text);
		$text = str_replace('â', 'ೃ', $text);
		$text = str_replace('ã', 'ೈ', $text);
		$text = str_replace('ä', ',', $text);
		$text = str_replace('å', '.', $text);
		$text = str_replace('æ', 'ಬ್', $text);
		$text = str_replace('ç', 'ನ್', $text);
		$text = str_replace('è', 'ಳ್', $text);
		$text = str_replace('é', '್ತ್ರ', $text);
		$text = str_replace('ê', '್ತ್ಯ', $text);
		$text = str_replace('ë', '್ಷ', $text);
		//~ $text = str_replace('ì', '', $text); //?
		$text = str_replace('í', 'ಫ್', $text);
		$text = str_replace('î', 'ಖ್', $text);
		$text = str_replace('ò', 'ಔ', $text);
		$text = str_replace('ô', '', $text);
		$text = str_replace('ö', 'ಘಿ', $text);
		$text = str_replace('ø', 'ಓ', $text);
		$text = str_replace('ù', 'ಕ', $text);
		$text = str_replace('ú', 'ಕೆ', $text);
		$text = str_replace('û', 'ು', $text);
		$text = str_replace('ü', 'ೞ್', $text);
		$text = str_replace('ÿ', 'ಌ', $text);
		$text = str_replace('Œ', '್ಪ', $text);
		$text = str_replace('œ', '್ಸ', $text);
		$text = str_replace('Š', '್ದ', $text);
		$text = str_replace('š', '್ಲ', $text);
		$text = str_replace('–', 'ೞ', $text);
		$text = str_replace('—', 'ಱ', $text);
		$text = str_replace('‘', '್ಫ', $text);
		$text = str_replace('’', '್ಬ', $text);
		$text = str_replace('“', '್ಱ', $text);
		$text = str_replace('”', '್ೞ', $text);
		$text = str_replace('†', '್ಡ', $text);
		//~ $text = str_replace('‡', '', $text); //?
		$text = str_replace('‰', '್ತ', $text);
		$text = str_replace('‹', '್ನ', $text);
		$text = str_replace('›', '್ಷ', $text);
		$text = str_replace('™', '', $text);
		$text = str_replace('•', '್ತ್ಯ', $text);

		// Special cases

		// Swara
		$text = preg_replace('/್[ಅ]/u', '', $text);
		$text = preg_replace('/್([ಾಿೀುೂೃೄೆೇೈೊೋೌ್])/u', "$1", $text);
		
		// vyanjana
		$text = str_replace('ವt', 'ಮಾ', $text);
		$text = str_replace('ಯ್t', 'ಯಾ', $text);
		$text = str_replace('ಫì', 'ಘ', $text);

		$swara = "ಅ|ಆ|ಇ|ಈ|ಉ|ಊ|ಋ|ಎ|ಏ|ಐ|ಒ|ಓ|ಔ";
		$vyanjana = "ಕ|ಖ|ಗ|ಘ|ಙ|ಚ|ಛ|ಜ|ಝ|ಞ|ಟ|ಠ|ಡ|ಢ|ಣ|ತ|ಥ|ದ|ಧ|ನ|ಪ|ಫ|ಬ|ಭ|ಮ|ಯ|ರ|ಱ|ಲ|ವ|ಶ|ಷ|ಸ|ಹ|ಳ|ೞ";
		$swaraJoin = "ಾ|ಿ|ೀ|ು|ೂ|ೃ|ೄ|ೆ|ೇ|ೊ|ೋ|ೌ|ಂ|ಃ|್";

		$syllable = "($vyanjana)($swaraJoin)|($vyanjana)($swaraJoin)|($vyanjana)|($swara)";

		$text = preg_replace("/($swaraJoin)್($vyanjana)/u", "್$2$1", $text);

		$text = str_replace('ೊ', 'ೊ', $text);
		$text = str_replace('ೆೈ', 'ೈ', $text);

		$text = str_replace('ಿ|', 'ೀ', $text);
		$text = str_replace('ೆ|', 'ೇ', $text);
		$text = str_replace('ೊ|', 'ೋ', $text);

		$text = preg_replace("/($swaraJoin)್($vyanjana)/u", "್$2$1", $text);

		$text = preg_replace("/($syllable)/u", "$1zzz", $text);
		$text = preg_replace("/್zzz/u", "್", $text);
		$text = preg_replace("/zzz([^z]*?)zzzR/u", "zzzರ್zzz" . "$1", $text);

		$text = str_replace("zzz", "", $text);

		return $text;
	}
}

?>
