<?php

require_once('action.php');
require_once('backend/db.php');

class RecentChangesAction extends Action {

	public function writeContent() {
		global $session, $db;

		$rows = $db->getRecentChanges();
		
		echo '<h1>Letzte Änderungen</h1>';

		echo '<table>';
		$lastEntry = '';
		foreach ($rows as $row) {
			if (!$this->mayAccess($row['read_permission'])) {
				continue;
			}
			$entry = $row['title'];
			if ($entry[strlen($entry) - 1] == '/') {
				continue;
			}
			$icon = substr($entry, strrpos($entry, '.') + 1);
			echo '<tr><td>'.htmlspecialchars($row['timedate'])
				.'</td><td><a href="/'.htmlspecialchars($entry)
				.'"><img class="fileicon" src="/s/frontend/free-file-icons/32px/'.htmlspecialchars($icon).'.png"></a>'
				.' <a href="/'.htmlspecialchars($entry).'">'.htmlspecialchars($entry)
				.'</a></td><td>'.htmlspecialchars($row['realname']).'</td></tr>';
		}
		echo '</table>';
	}
}
