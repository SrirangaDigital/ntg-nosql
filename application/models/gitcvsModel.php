<?php

class gitcvsModel extends Model {

	public function __construct() {

		parent::__construct();
	}

	public function getChangesFromGit($repo) {

		// Get status in porcelain mode
		$status = (string) $repo->status();
		

		// Replace '??' with A which means untracked files which are to be added
		$status = str_replace('??', 'A', $status);
		$status = preg_replace('/\h+/m', ' ', $status);
		$status = preg_replace('/^\h/m', '', $status);

		$lines = preg_split("/\n/", $status);
		
		$files['A'] = $files['M'] = $files['D'] = array();
		
		$metadaDataPath = str_replace(BASE_URL, '', PUBLIC_URL);
		$metadaDataPath = str_replace('/', '\/', $metadaDataPath);

		foreach ($lines as $file) {

			// Extract files into three bins - A->Added, M->Modified and D->Deleted. 
			if(
				(preg_match('/^([AMD])\s(.*)/', $file, $matches)) && 
				(preg_match('/' . $metadaDataPath . '.*\/.+\.json/', $file))
			  ) {

				array_push($files[$matches[1]], $matches[2]);
			}
		}

		return $files;
	}

	public function gitProcess($repo, $files, $operation, $message, $user) {

		if(($operation == 'addAll')&&(is_array($files))) {

			$path = preg_replace('/(.*)\/.*/' , "$1", $files[0]);
			$repo->run('add --all ' . $path);
		}
		else{

			foreach ($files as $file) {
				
				$repo->{$operation}($file);
			}
		}

		// $message = str_replace(':journal', $journal, $message);
		$repo->run('-c "user.name=' . $user['name'] . '" -c "user.email=' . $user['email'] . '" commit -m "' . escapeshellarg($message) . '"');
	}


}

?>
