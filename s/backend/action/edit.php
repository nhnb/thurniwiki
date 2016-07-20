<?php

require_once('action.php');
require_once('backend/db.php');


class EditAction extends Action {
	private $content;
	private $title;

	public function __construct() {
		$this->title = $_REQUEST['page'];
		if (isset($_POST['content-editor'])) {
			$this->content = $this->filterHtml($_POST['content-editor']);
		} else {
			global $db;
			$row = $db->readNewestVersion($this->title);
			if ($row) {
				$this->content = $row['content'];
			}
		}
		if ($this->title === '') {
			$this->title = CONFIG_SITE_TITLE;
		}
	}

	public function writeHtmlHeader() {
		parent::writeHtmlHeader($this->title);
	}

	function writeContent() {
		global $session;

?>
<form method="POST">
<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($session['csrf']); ?>">
<br><input type="submit" value="Speichern"><br><br>
<textarea id="content-editor" name="content-editor">
	<?php echo $this->content; ?>
</textarea>
</form>
<script src="/s/frontend/ckeditor/ckeditor.js"></script>
<script src="/s/frontend/thurniwiki.js"></script>
<?php
	}

	function filterHtml($html) {
		require_once 'backend/htmlpurifier/library/HTMLPurifier.auto.php';
		$purifier = new HTMLPurifier();
		return $purifier->purify($html);
	}
	
}
