<?php

ini_set('default_charset', 'utf-8');
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');

require_once('config.php');

global $session;

$session = array();
session_start();
if (isset($_SESSION['session'])) {
	$session = $_SESSION['session'];
}

/**
 * creates the Action instance
 */
function createAction() {
	global $session;
	
	$action = "view";
	if (isset($_REQUEST['action'])) {
		$action = $_REQUEST['action'];
	}
	if (!preg_match('/^[a-z]+$/', $action)) {
		$action = "unknown";
	}
	if (!file_exists('backend/action/'.$action.'.php')) {
		$action = "unknown";
	}

	// Force change of initial password
	if ($action !== 'logout' && isset($session['status']) && $session['status'] === 'I') {
		$action = "password";
	}
	
	require_once('backend/action/'.$action.'.php');
	$action = ucfirst($action)."Action";
	return new $action();
}


$action = createAction();
$action->writeHttpHeader();
$action->writeHtmlHeader(CONFIG_SITE_TITLE);
$action->writeContent();
$action->writeHtmlFooter();