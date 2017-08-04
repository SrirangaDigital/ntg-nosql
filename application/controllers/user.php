<?php

class user extends Controller {

	public function __construct() {

		parent::__construct();
	}

	public function index() {

		$this->login();
	}

	public function login($query = [], $errMsg = '') {

		$_SESSION['refererUrl'] = $this->model->getRefererUrl();
		($this->isLoggedIn()) ? $this->redirect('#home') : $this->view('user/login', array('errMsg' => $errMsg));
	}

	public function logout() {

		$this->model->destroyUser();
		$this->absoluteRedirect($_SERVER['HTTP_REFERER']);
	}

	public function verifyLogin() {

		$data = $this->model->getPostData();

		if ($data) {

			// Check if password recovery email has been entered. If not continue with authentication.
			if (!(isset($data['pr_email']))) {

				$isValid = $this->model->verifyCredentials($data);
				if($isValid) {

					$this->model->initiateUser($data);
					(isset($_SESSION['refererUrl'])) ? $this->absoluteRedirect($_SESSION['refererUrl']) : $this->redirect('#home');
				}
				else {

					$this->login([],VLDTY_INV_EM_PWD);
				}
			}
			else {
				
				$data = $this->requestPasswordReset($data);
			}
		}
		else {

			$this->login(VLDTY_EMPTY_DATA);
		}
	}

	public function registration($data = array('name' => '', 'email' => '', 'profession' => '', 'affiliation' => '', 'errMsg' => '')) {

		$this->view('user/registration', $data);
	}

	public function processRegistration() {

		$data = $this->model->getPostData();
		if(!$data['g-recaptcha-response']){
			$this->view('error/prompt', array('msg' => FB_CAPTCHA_MSG));
		}
		else
		{
			$data = $this->model->validateRegistrationData($data);

			//We should throw appropritae error message while redirecting back to form
			if ($data['isValid']) {
				
				$data = $this->model->completeRegistration($data);
				//Send email only on sucessful registration
				if ($data) {

					if (REQUIRE_EMAIL_VALIDATION) {
		
						$isSent = $this->model->sendLetterToPostman(SERVICE_NAME, SERVICE_EMAIL, $data['name'], $data['email'], REG_VERIFY_SUB, $this->model->bindVariablesToString(REG_VERIFY_MSG, $data), REG_VERIFY_SUCCESS_MSG, REG_VERIFY_ERROR_MSG);
						//If mail is not sent, redirect to blah
						if ($isSent) {

							$this->view('page/prompt', array('msg' => REG_VERIFY_SUCCESS_MSG));
						}
						else{

							$this->view('error/blah');
						}
					}
					else{

						($this->model->autoVerifyRegistration($data)) ? $this->view('page/prompt', array('msg' => REG_NO_VALIDATION_SUCCESS_MSG)) : $this->view('error/prompt', array('msg' => REG_VERIFY_ERROR_MSG));
					}
				}
				else {

					$this->view('error/prompt', array('msg' => REG_VERIFY_ERROR_MSG));
				}
			}
			else {

				$this->registration($data);
			}
		}
	}

	public function confirmRegistration($hash = '') {

		$result = $this->model->verifyRegistrationLink($hash);
		//Send email on sucessful confrmation
		if ($result) {
		
			$isSent = $this->model->sendLetterToPostman(SERVICE_NAME, SERVICE_EMAIL, $result['name'], $result['email'], REG_CONFIRM_SUB, $this->model->bindVariablesToString(REG_CONFIRM_MSG, $result), REG_CONFIRM_SUCCESS_MSG, REG_CONFIRM_ERROR_MSG);
			//If mail is not sent, redirect to blah
			if ($isSent) {
			
				$this->view('page/prompt', array('msg' => REG_CONFIRM_SUCCESS_MSG));
			}
			else {

				$this->view('error/blah');
			}
		}
		else {

			$this->view('error/prompt', array('msg' => REG_CONFIRM_ERROR_MSG));
		}
	}

	public function requestPasswordReset($data = '') {

		$data = $this->model->getPasswordResetLink($data);
		//Send email only on sucessful password request
		if ($data) {

			$isSent = $this->model->sendLetterToPostman(SERVICE_NAME, SERVICE_EMAIL, $data['name'], $data['pr_email'], PWD_RESET_SUB, $this->model->bindVariablesToString(PWD_RESET_MSG, $data), PWD_RESET_SUCCESS_MSG, PWD_RESET_ERROR_MSG);
			//If mail is not sent, redirect to blah
			if ($isSent) {

				$this->view('page/prompt', array('msg' => PWD_RESET_SUCCESS_MSG));
			}
			else{

				$this->view('error/blah');
			}
		}
		else {

			$this->view('error/prompt', array('msg' => PWD_RESET_ERROR_MSG));
		}
	}

	public function resetPassword($hash = '') {

		if ($this->model->verifyPasswordResetLink($hash)) {

			$this->view('user/getNewPassword', array('hash' => $hash, 'errMsg' => ''));
		}
		else{

			$this->view('error/prompt', array('msg' => PWD_RESET_LINK_EXPIRE_MSG));
		}
	}

	public function insertNewPassword() {

		$data = $this->model->getPostData();
		$data = $this->model->validatePasswordData($data);

		//We should throw appropritae error message while redirecting back to form
		if ($data['isValid']) {
			
			$data = $this->model->insertPassword($data);
			//Send email only on sucessful registration
			if ($data) {
	
				$isSent = $this->model->sendLetterToPostman(SERVICE_NAME, SERVICE_EMAIL, $data['name'], $data['email'], PWD_RESET_LINK_SUB, $this->model->bindVariablesToString(PWD_RESET_LINK_MSG, $data), PWD_RESET_LINK_SUCCESS_MSG, PWD_RESET_LINK_ERROR_MSG);
				//If mail is not sent, redirect to blah
				if ($isSent) {

					$this->view('page/prompt', array('msg' => PWD_RESET_LINK_SUCCESS_MSG));
				}
				else{

					$this->view('error/blah');
				}
			}
			else {

				$this->view('error/prompt', array('msg' => PWD_RESET_LINK_ERROR_MSG));
			}
		}
		else {

			$this->view('user/getNewPassword', $data);
		}
	}
}

?>
