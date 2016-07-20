<?php

require_once('action.php');
require_once('backend/db.php');

class ViewAction extends Action {
	private $content;
	private $title;
	
	public function __construct() {
		global $db;
		
		$this->title = $_REQUEST['page'];
		$row = $db->readNewestVersion($this->title);
		if ($row) {
			$this->content = $row['content'];
		}
		if ($this->title === '') {
			$this->title = CONFIG_SITE_TITLE;
		}
	}

	public function writeHttpHeader() {
		if ($this->content == null) {
			header('HTTP/1.1 404 Not Found');
		}
	}

	public function writeHtmlHeader() {
		parent::writeHtmlHeader($this->title);
	}

	function writeContent() {
		if ($this->content == null) {
			echo '<section>Page not found</section>';
			return;
		}
		echo '<section><h1>'.$this->title.'</h1>';
		echo $this->content;
		echo '</section>';
	}
}
