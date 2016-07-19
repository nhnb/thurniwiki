<?php

require_once('action.php');

class UnknownAction extends Action {

	public function writeHttpHeader() {
		header('HTTP/1.1 404 Not Found');
	}

	public function writeContent() {
		echo '<h1>Error</h1><section>Unknown action</section>';
	}
}
