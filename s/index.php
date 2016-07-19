<?php 

require_once('config.php');
require_once('backend/action/action.php');
require_once('backend/main.php');

/**
 * creates the Action instance
 */
function createAction() {
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
	require_once('backend/action/'.$action.'.php');
	$action = ucfirst($action)."Action";
	return new $action();
}


$action = createAction();
$action->writeHttpHeader();
$action->writeHtmlHeader('Titel der Seite');
$action->writeContent();
$action->writeHtmlFooter();