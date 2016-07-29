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
	}
	
	public function extractDirectoryEntry($entry, $index) {
		$entry = trim(substr($entry, $index), '/');
		$pos = strpos($entry, '/');
		if ($pos > 0) {
			$entry = substr($entry, 0, $pos + 1);
		}
		return $entry;
	}

	public function writeContent() {
		global $session, $db;

		$this->title = trim($_REQUEST['page'], '/');
		$lookup = $this->title;
		// unless we are in the root folder, require a /
		if (strlen($lookup) > 0) {
			$lookup = $lookup . '/';
		}
		$rows = $db->getListOfPages($lookup);

		echo '<h1>Verzeichnis: '.htmlspecialchars($this->title).'</h1>';

		echo '<ul class="directorylisting">';
		$lastEntry = '';
		foreach ($rows as $row) {
			$entry = $this->extractDirectoryEntry($row['title'], strlen($this->title));
			if ($entry === $lastEntry) {
				continue;
			}
			$lastEntry = $entry;
			$icon = substr($entry, strrpos($entry, '.') + 1);
			if (strpos($entry, '/') > 0) {
				$suffix = "?action=index";
				$icon = "folder";
				$entry = trim($entry, '/');
			} else {
				$suffix = "";
			}
			echo '<li><a href="/'.htmlspecialchars($lookup.$entry).$suffix
				.'"><img class="fileicon" src="/s/frontend/free-file-icons/32px/'.htmlspecialchars($icon).'.png"> '
				.htmlspecialchars($entry).'</a>';
		}
		echo '</ul>';

		?>
		<form enctype="multipart/form-data" action="?action=upload" method="POST">
			<input type="file" name="file">
			<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($session['csrf']); ?>">
			<input type="hidden" name="readpermission" value="users">
			<input type="submit" value="Speichern"><br><br>
		</form>
<?php
	}
}
