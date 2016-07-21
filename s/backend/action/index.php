<?php

require_once('action.php');
require_once('backend/db.php');

class IndexAction extends Action {
	
	public function writeContent() {
		global $db;

		$rows = $db->getListOfPages($_REQUEST['page']);
		
		echo '<ul>';
		foreach ($rows as $row) {
			echo '<li><a href="/'.htmlspecialchars($row['title']).'">'.htmlspecialchars($row['title']).'</a>';
		}
		echo '</ul>';
	}
}