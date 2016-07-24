<?php

require_once('action.php');
require_once('backend/db.php');

class IndexAction extends Action {

	public function writeHttpHeader() {
		global $session;
	
		if (!isset($session['accountId'])) {
			header('Location: https://'.$_SERVER['SERVER_NAME'].'/'.$_REQUEST['page'].'?action=login');
			exit();
		}
	}
	
	public function writeContent() {
		global $session, $db;

		$rows = $db->getListOfPages($_REQUEST['page']);
		
		echo '<ul>';
		foreach ($rows as $row) {
			echo '<li><a href="/'.htmlspecialchars($row['title']).'">'.htmlspecialchars($row['title']).'</a>';
		}
		echo '</ul>';

		?>
		<form enctype="multipart/form-data" action="<?php echo $_REQUEST['page'] ?>?action=upload" method="POST">
			<input type="file" name="file">
			<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($session['csrf']); ?>">
			<input type="hidden" name="readpermission" value="users">
			<input type="submit" value="Speichern"><br><br>
		</form>
<?php
	}
}