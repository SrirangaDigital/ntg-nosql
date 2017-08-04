<?php

class userModel extends Model {

	public function __construct() {

		parent::__construct();
	}

	public function verifyCredentials($credentials) {

		$db = $this->db->useDB();
		$collection = $this->db->selectCollection($db, USER_COLLECTION);

		$result = $collection->findOne(
			[ 'email' => $credentials['lemail'], 'password'=> $credentials['lpassword'] ]
		);

		return ($result) ? True : False;
	}

	public function incrementVisitCount($credentials = array()) {

		$dbh = $this->db->connect(DB_NAME);

	    $sth = $dbh->prepare('UPDATE userdetails SET visitcount=visitcount+1 WHERE email=:email');
		$sth->execute(array('email' => $credentials['lemail']));
	}

	public function validateRegistrationData($data) {

        // Check if email is already registered
        $dbh = $this->db->connect(DB_NAME);
	    $sth = $dbh->prepare('SELECT COUNT(*) FROM userdetails WHERE email=:email');
		$sth->execute(array('email' => $data['email']));
		$result = $sth->fetch(PDO::FETCH_ASSOC);

		if ($result['COUNT(*)'] == 0) {

			$data = $this->validatePasswordData($data);
		}
		else {

			$data['isValid'] = False;
			$data['errMsg'] = VLDTY_EM_UREG;
		}
		return $data;
	}

	public function validatePasswordData($data) {

		// Check if passwords are in conformation
		if ($data['password'] == $data['cpassword']) {

			$data['isValid'] = True;
		}
		else {

			$data['isValid'] = False;
			$data['errMsg'] = VLDTY_PW_NEQ;
		}
		return $data;
	}

	public function completeRegistration($data) {

        $data['pwd'] = sha1(SALT.$data['password']);
        $data['tstamp'] = time();
        $data['hash'] = sha1($data['pwd'].$data['name'].$data['email'].$data['tstamp']);
        	
        $dbh = $this->db->connect(DB_NAME);
	    $sth = $dbh->prepare('INSERT INTO userdetails VALUES (:name, :email, :profession, :password, :affiliation, \'\', \'0\', \'1\', :hash, :tstamp, \'\')');
		$success = $sth->execute(array('name' => $data['name'], 'email' => $data['email'], 'profession' => $data['profession'], 'affiliation' => $data['affiliation'], 'password' => $data['pwd'], 'hash' => $data['hash'], 'tstamp' => $data['tstamp']));

		return ($success) ? $data : false;
	}

 	public function verifyRegistrationLink($hash) {

 		$dbh = $this->db->connect(DB_NAME);
	    $sth = $dbh->prepare('SELECT *,count(*) FROM userdetails WHERE hash=:verify');
		$sth->execute(array('verify' => $hash));
		$result = $sth->fetch(PDO::FETCH_ASSOC);

		if ($result['count(*)'] > 0) {

	        $tstamp=$result['timestamp'];
	        $cstamp = time();
	        if(floor(($cstamp - $tstamp) / 3600) > 24) {

	        	$sth = $dbh->prepare('DELETE FROM userdetails WHERE timestamp<=:tstamp');
				$sth->execute(array('tstamp' => $tstamp));
	            return False;
	        }
	        else {

	            $sth = $dbh->prepare('UPDATE userdetails SET isverified=\'1\' WHERE email=:email and name=:name and hash=:hash');
				$sth->execute(array('email' => $result['email'], 'name' => $result['name'], 'hash' => $hash));

	            return ($sth->rowCount() == 1) ? $result : False;
	        }
	    }
	    else {

	    	return False;
	    }
	}

	public function autoVerifyRegistration($data = array()) {

 		$dbh = $this->db->connect(DB_NAME);
        $sth = $dbh->prepare('UPDATE userdetails SET isverified=\'1\' WHERE email=:email and name=:name and hash=:hash');
		return $sth->execute(array('email' => $data['email'], 'name' => $data['name'], 'hash' => $data['hash']));
	}
	
	public function initiateUser($credentials) {

		// $this->incrementVisitCount($credentials);
		$_SESSION['email'] = $credentials['lemail'];
        $_SESSION['login'] = 1;
	}

	public function destroyUser() {

		session_destroy();
	}

	public function getRefererUrl() {

		$refererURL = $_SERVER['HTTP_REFERER'];
		$refererURL = preg_replace('/\/user.*/', '/#home', $refererURL);
		$refererURL = preg_replace('/\/error.*/', '/#home', $refererURL);
		return $refererURL;
	}

	public function getPasswordResetLink($data) {

        $dbh = $this->db->connect(DB_NAME);
	    $sth = $dbh->prepare('SELECT password,name,email FROM userdetails WHERE email=:pr_email');
		$sth->execute(array('pr_email' => $data['pr_email']));
		$pr_email = $data['pr_email'];
		
		if ($sth->rowCount() > 0) {
			
			$data = $sth->fetch(PDO::FETCH_ASSOC);
			$data['pr_email'] = $pr_email;
			$data['tstamp'] = time();
        	$data['hash'] = sha1($data['password'].$data['name'].$data['email'].$data['tstamp']);
        
            $sth = $dbh->prepare('INSERT INTO reset VALUES (:hash,:email,:name,:password,:tstamp,\'\')');
			$success = $sth->execute(array('hash' => $data['hash'], 'email' => $data['email'], 'name' => $data['name'], 'password' => $data['password'], 'tstamp' => $data['tstamp']));

			return ($success) ? $data : False;
		}
		else {

			return False;
		}
	}

	public function verifyPasswordResetLink($reset) {

 	    $dbh = $this->db->connect(DB_NAME);
	    $sth = $dbh->prepare('SELECT *,count(*) FROM reset WHERE hash=:reset');
		$sth->execute(array('reset' => $reset));
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		
		if ($data['count(*)'] == 0) {

	        return False;
	    }
	    else {

		    $tstamp=$data['timestamp'];
	        $cstamp = time();
	    	
	        if(floor(($cstamp - $tstamp) / 3600) > 24) {

	            $sth = $dbh->prepare('DELETE FROM reset WHERE timestamp<=:tstamp');
				$sth->execute(array('tstamp' => $tstamp));
		
	            return False;
	        }
	        else {

	            return True;
	        }
	    }
	}

	public function insertPassword($data) {

		//Create new hash
        $newPwd = sha1(SALT.$data['password']);
        
        $dbh = $this->db->connect(DB_NAME);
        $sth = $dbh->prepare('SELECT * FROM reset WHERE hash=:hash');
		$sth->execute(array('hash' => $data['hash']));
		$data = $sth->fetch(PDO::FETCH_ASSOC);
		
		if ($sth->rowCount() > 0) {
			//Update password in table
	        $sth = $dbh->prepare('UPDATE userdetails SET password=:newPwd WHERE email=:email and name=:name');
			$sth->execute(array('newPwd' => $newPwd, 'email' => $data['email'], 'name' => $data['name']));

			if(($sth->rowCount() == 1) || ($newPwd == $data['password'])) {
				//Clear reset table data
    	        $sth = $dbh->prepare('DELETE FROM reset WHERE hash=:hash and email=:email and name=:name');
				$sth->execute(array('hash' => $data['hash'], 'email' => $data['email'], 'name' => $data['name']));

				return $data;
			}
			else {

				return False;
			}
	    }
	    else {

		    return False;
	    }
	}
}

?>
