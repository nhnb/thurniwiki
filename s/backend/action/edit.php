<?php

require_once('action.php');
require_once('backend/db.php');


class EditAction extends Action {
	private $content;
	private $title;
	private $readpermission;
	private $error;

	public function __construct() {
		global $db, $session;

		$this->title = $_REQUEST['page'];
		if (isset($_POST['content-editor'])) {
			$this->content = $this->filterHtml($_POST['content-editor']);
			$this->readpermission = $_POST['readpermission'];
			if (!isset($session['accountId'])) {
				$this->error = 'Die Sitzung ist abgelaufen.';
			} else {
				if ($_POST['csrf'] != $session['csrf']) {
					$this->error = 'Sitzungsinformationen verloren. Bitte speichern Sie erneut.';
				} else {
					$db->savePageVersion($this->title, $this->content, $session['accountId'], $_POST['readpermission']);
				}
			}
		} else {
			$row = $db->readNewestVersion($this->title);
			if ($row) {
				$this->content = $row['content'];
				$this->readpermission = $row['read_permission'];
			} else {
				$this->content = '';
				$this->readpermission = 'all, users';
			}
		}
		if ($this->title === '') {
			$this->title = CONFIG_SITE_TITLE;
		}
	}


	public function writeHttpHeader() {
		if (!isset($session['accountId'])) {
			header('Location: https://'.$_SERVER['SERVER_NAME'].'/'.$_REQUEST['page'].'?action=login');
			exit();
		}
	}


	public function writeHtmlHeader() {
		parent::writeHtmlHeader($this->title);
	}


	public function writeContent() {
		global $session, $db;

?>
<form method="POST">
<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($session['csrf']); ?>">
<br>
Leseberechtigung: <input type="text" id="readpermission" name="readpermission" value="<?php echo htmlspecialchars($this->readpermission); ?>">
<input type="submit" value="Speichern"><br><br>
<?php 
if ($this->error) {
	echo '<div class="error">'.htmlspecialchars($this->error).'</div>';
}
?>
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
