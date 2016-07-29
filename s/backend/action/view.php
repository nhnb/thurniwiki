<?php

require_once('action.php');
require_once('backend/db.php');
require_once('backend/util.php');

/**
 * Render the requested html page or offers the requested file for download
 *
 * @author hendrik
 */
class ViewAction extends Action {
	private $content;
	private $title;
	private $readPermission;
	private $mimeType;

	public function __construct() {
		global $db;

		$this->title = $_REQUEST['page'];
		if ($this->title === '') {
			$this->title = 'index.html';
		}
		$row = $db->readNewestVersion($this->title);
		if ($row) {
			$this->content = $row['content'];
			$this->readPermission = $row['read_permission'];
		}
		$this->mimeType = get_mime_type_from_filename($this->title);
		if ($this->title === '') {
			$this->title = CONFIG_SITE_TITLE;
			$this->mimeType = 'text/html';
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

		// Unless this is a html page, we will tricker a download
		// and exit the script before it can output the html
		// navigation frame.
		if ($this->mimeType !== 'text/html') {
			header('Content-Disposition: attachment');
			header('Content-Type: '.$this->mimeType);
			echo $this->content;
			exit;
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
