<?php

require_once('action.php');
require_once('backend/db.php');
require_once('backend/util.php');


class EditAction extends Action {
	private $content;
	private $title;
	private $readpermission;
	private $error;

	public function __construct() {
		header_remove('Content-Security-Policy');

		global $db, $session;
		$this->title = $_REQUEST['page'];
		if ($this->title === '') {
			$this->title = 'index.html';
		}
		$row = $db->readNewestVersion($this->title);
		if (isset($_POST['content-editor'])) {
			$this->content = filter_html($_POST['content-editor']);
			$this->readpermission = $_POST['readpermission'];
			if (!isset($session['accountId'])) {
				$this->error = 'Die Sitzung ist abgelaufen.';
			} else {
				if ($_POST['csrf'] != $session['csrf']) {
					$this->error = 'Sitzungsinformationen verloren. Bitte speichern Sie erneut.';
				} else {
					if (!$row || $this->mayAccess($row['read_permission'])) {
						$db->savePageVersion($this->title, $this->content, $session['accountId'], $_POST['readpermission']);
					} else {
						$this->error = 'Fehlende Berechtigung';
					}
				}
			}
		} else {
			if ($row && !$this->mayAccess($row['read_permission'])) {
				$this->error = 'Fehlende Berechtigung';
				return;
			}

			if ($row) {
				$this->content = $row['content'];
				$this->readpermission = $row['read_permission'];
			} else {
				$this->content = '';
				if (isset($_REQUEST['permission'])) {
					$this->readpermission = $_REQUEST['permission'];
				} else {
					$this->readpermission = 'public';
				}
			}
		}
	}


	public function writeHttpHeader() {
		global $session;

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
Artikel-Berechtigungen: <input type="text" id="readpermission" name="readpermission" value="<?php echo htmlspecialchars($this->readpermission); ?>">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Speichern"><br>
Meine Berechtigungen: <?php echo htmlspecialchars($session['groups']);?> <br><br>
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

}
