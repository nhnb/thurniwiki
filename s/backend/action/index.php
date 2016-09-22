<?php

require_once('action.php');
require_once('backend/db.php');

class IndexAction extends Action {
	private $title;

	public function writeHttpHeader() {
		global $session;

		if (!isset($session['accountId'])) {
			header('Location: https://'.$_SERVER['SERVER_NAME'].'/'.$_REQUEST['page'].'?action=login');
			exit();
		}

		if ($_POST['newtype'] === 'folder') {
			$this->createFolder();
		} else if ($_POST['newtype'] === 'page') {
			$this->redirectToEdit();
		}
	}

	private function createFolder() {
		global $db, $session;
		$dir = $db->readNewestVersion($lookup);
		if ($dir) {
			if (!$this->mayAccess($dir['read_permission'])) {
				echo 'Fehlende Berechtigung';
				exit();
			}
		}

		$pagename = trim($_REQUEST['page'].'/'.$_REQUEST['pagename'], '/');
		if ($_POST['csrf'] !== $session['csrf']) {
			echo 'Sessioninformationen verloren';
			exit();
		}

		if (!isset($session['accountId'])) {
			echo 'Session abgelaufen';
			exit();
		}
		$db->savePageVersion($pagename.'/', '', $session['accountId'], $_POST['permission']);
		
		header('Location: https://'.$_SERVER['SERVER_NAME'].'/'
				.$pagename
				.'?action=index');
		exit();
	}

	private function redirectToEdit() {
		$pagename = $_REQUEST['pagename'];
		if (strpos(str_replace('/', '_', $pagename), '.html') === false) {
			$pagename = $pagename . '.html';
		}
		header('Location: https://'.$_SERVER['SERVER_NAME'].'/'
				.trim($_REQUEST['page'].'/'.$pagename, '/')
				.'?action=edit&permission='.$_REQUEST['permission']);
		exit();
	}

	public function extractDirectoryEntry($entry, $index) {
		$entry = trim(substr($entry, $index), '/');
		$pos = strpos($entry, '/');
		if ($pos > 0) {
			$entry = substr($entry, 0, $pos + 1);
		}
		return $entry;
	}

	private function writeNewDialog($dir) {
		global $session;
		?>
		<button id="newfolder">Neues Verzeichnis erstellen...</button>
		<button id="newfile">Neue Datei hochladen...</button>
		<button id="newpage">Neue Seite erstellen...</button>
		<fieldset class="hidden" id="newfieldset"><legend id="newlegend">Neue erstellen</legend>
		<form id="newform" enctype="multipart/form-data" action="?action=upload" method="POST">
			<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($session['csrf']); ?>">
			<input type="hidden" name="newtype" id="newtype" value="">
			<label for="permission">Berechtiungen:</label> <input type="text" name="permission" value="<?php echo htmlspecialchars($dir['read_permission'])?>">
			<div id="pagenamelabel"><label for="pagename">Name:</label> <input type="text" id="pagename" name="pagename"></div>
			<br><input type="file" id="file" name="file">
			<br><input type="submit" value="Speichern"><br><br>
		</form>
		</fieldset>
		<?php
	}
	
	public function writeContent() {
		global $session, $db;

		$this->title = trim($_REQUEST['page'], '/');
		$lookup = $this->title;
		// unless we are in the root folder, require a /
		if (strlen($lookup) > 0) {
			$lookup = $lookup . '/';
		}

		$dir = $db->readNewestVersion($lookup);
		if ($dir) {
			if (!$this->mayAccess($dir['read_permission'])) {
				echo 'Fehlende Berechtigung';
				return;
			}
		}

		$rows = $db->getListOfPages($lookup);

		echo '<h1>Verzeichnis: '.htmlspecialchars($this->title).'</h1>';
		$this->writeNewDialog($dir);

		echo '<ul class="directorylisting">';
		$lastEntry = '';
		foreach ($rows as $row) {
			$entry = $this->extractDirectoryEntry($row['title'], strlen($this->title));
			if ($entry === $lastEntry) {
				continue;
			}
			$lastEntry = $entry;
			$icon = substr($entry, strrpos($entry, '.') + 1);
			if (strpos($entry, '/') > 0 || $entry.'/' === $row['title']) {
				$suffix = "?action=index";
				$icon = "folder";
				$entry = trim($entry, '/');
			} else {
				$suffix = "";
			}
			echo '<li><a href="/'.htmlspecialchars($lookup.$entry).$suffix
				.'"><img class="fileicon" src="/s/frontend/free-file-icons/32px/'.htmlspecialchars($icon).'.png"></a>'
				.' <a href="/'.htmlspecialchars($lookup.$entry).$suffix.'">'.htmlspecialchars($entry).'</a>';
		}
		echo '</ul>';
?>
		<script src="/s/frontend/thurniwiki.js"></script>
<?php
	}
}
