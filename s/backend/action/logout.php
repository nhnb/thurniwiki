<?php
require_once('action.php');

class LogoutAction extends Action {
	
	public function __construct() {
		global $session;

		unset($session);
		unset($_SESSION['session']);
	}

	public function writeHttpHeader() {
		header('Location: https://'.$_SERVER['SERVER_NAME'].'/');
		exit;
	}

}
