<?php

require_once('action.php');
require_once('backend/db.php');

class ViewAction extends Action {
	private $content;
	private $title;
	private $readPermission;

	public function __construct() {
		global $db;

		$this->title = $_REQUEST['page'];
		$row = $db->readNewestVersion($this->title);
		if ($row) {
			$this->content = $row['content'];
			$this->readPermission = $row['read_permission'];
		}
		if ($this->title === '') {
			$this->title = CONFIG_SITE_TITLE;
		}
	}

	public function mayRead() {
		global $session;

		if (strpos($this->readPermission, 'all') !== false) {
			return true;
		}

		return isset($session['accountId']);
	}

	public function writeHttpHeader() {
		if (!$this->mayRead()) {
			header('Location: https://'.$_SERVER['SERVER_NAME'].'/'.$_REQUEST['page'].'?action=login');
			exit();
		} else if ($this->content == null) {
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
		echo '<section>';
		echo $this->content;
		echo '</section>';
	}
}
