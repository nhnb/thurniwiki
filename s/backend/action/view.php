<?php

require_once('action.php');
require_once('backend/db.php');


class ViewAction extends Action {
	private $content;
	private $title;
	
	public function __construct() {
		$this->title = $_REQUEST['page'];
		$this->readNewestVersion($this->title);
		if ($this->title === '') {
			$this->title = CONFIG_SITE_TITLE;
		}
	}

	/**
	 * gets the most recent version of the specified page
	 *
	 * @param string $title page title
	 */
	public function readNewestVersion($title) {
		$sql = "SELECT content, account_id, timedate"
			." FROM page_version WHERE id = ("
			." SELECT max(page_version.id) FROM page, page_version"
			." WHERE page.title = :title "
			." AND page_version.page_id = page.id)";
		$stmt = DB::connection()->prepare($sql);
		$stmt->execute(array(
			':title' => $title
		));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) {
			$this->content = $row['content'];
		}
		return null;
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
