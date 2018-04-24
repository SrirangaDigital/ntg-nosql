<?php

class gitcvs extends Controller {

	public function __construct() {
		
		parent::__construct();

		$_SESSION['email'] = GIT_EMAIL;
		$_SESSION['name'] = GIT_USER_NAME;
	}

	public function updateRepo($query, $idURL = ''){

		$url = ($idURL) ? BASE_URL . 'describe/artefact/' . $idURL : BASE_URL;
		if(!(REQUIRE_GIT_TRACKING)) {

			$this->absoluteRedirect($url);
			return;
		}

		$statusMsg = array();

		$repo = Git::open(PHY_BASE_URL . '.git');

		// Before all operations, a git pull is done to sync local and remote repos.
	
		if(REQUIRE_GITHUB_SYNC) {

			$repo->run('pull ' . GIT_REMOTE . ' master');
			array_push($statusMsg, 'Repo synced with remote');
		}

		$files = $this->model->getChangesFromGit($repo);
		array_push($statusMsg, 'Files to be updated listed');

		$user['email'] = $_SESSION['email'];
		//$user['password'] = $_SESSION['password'];
		// $split = explode('@', $_SESSION['email']);
		$user['name'] = $_SESSION['name'];

		if($files['A']){ 
				$this->model->gitProcess($repo, $files['A'], 'add', GIT_ADD_MSG, $user);
				array_push($statusMsg, ' Addition of JSON for Albums and Archives are completed');
		}	
		if($files['M']){ 
				$this->model->gitProcess($repo, $files['M'], 'add', GIT_MOD_MSG, $user);
				array_push($statusMsg, ' Modification of JSON for Albums and Archives are completed');
		}		
		if($files['D']){ 
				$this->model->gitProcess($repo, $files['D'], 'rm', GIT_DEL_MSG, $user);
				array_push($statusMsg, ' Deleted of JSON for Albums / Archives are completed');
		}	
		
		if(REQUIRE_GITHUB_SYNC) {
			
			$repo->run('push ' . GIT_REMOTE . ' master');
			array_push($statusMsg, 'Local changes pushed to remote');
		}
		
		$this->absoluteRedirect($url);
	}

	public function checkoutFiles($files){

		$repo = Git::open(PHY_BASE_URL . '.git');

		foreach ($files as $file) {
		
			$repo->run('checkout ' . $file);
		}
		return;
	}
}	
?>
