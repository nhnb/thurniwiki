<?php

require_once('action.php');
require_once('backend/db.php');
require_once('backend/util.php');

class CreateaccountAction extends Action {
	private $error = '';

	public function __construct() {
		global $db, $session;
		
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return;
		}

		if (!$this->mayAccess('admin')) {
			$this->error = 'Fehlende Rechte';
			return;
		}
		
		$account = $db->getAccount($_POST['email']);
		if ($account) {
			$this->error = 'Account existiert schon';
			return;
		}

		if (strlen($_REQUEST['new']) < 6) {
			$this->error .= "Das Passwort muss mindestens 6 Zeichen lang sein.\r\n";
		}
		if ($_REQUEST['new'] !== $_REQUEST['repeat']) {
			$this->error .= "Das neue Password und die Wiederholung müssen identisch sein.\r\n";
		}

		echo $this->error;
		// create account
		if ($this->error === '') {
			$db->insertAccount($_POST['email'], hashPassword($_REQUEST['new']), $_POST['groups']);
		}
	}

	public function writeHttpHeader() {
		global $session;
	
		if (!isset($session['accountId'])) {
			header('Location: https://'.$_SERVER['SERVER_NAME'].'/'.$_REQUEST['page'].'?action=login');
			exit();
		}
	}

	public function writeContent() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->error === '') {
			echo '<p>Account erfolgreich erstellt.</p>';
			return;
		}
?>
<p>Account erstellen</p>
<form method="POST" action="?action=createaccount">
	<label for="email">E-Mail:</label><input type="email" name="email" id="email" required minlength="3"><br>
	<label for="new">Neues Passwort:</label><input type="password" name="new" id="new" required minlength="6"><br>
	<label for="repeat">Wiederholung:</label><input type="password" name="repeat" id="repeat" required minlength="6"><br>
	<label for="groups">Gruppen:</label><input type="text" name="groups" id="groups" required><br>
	<label for="info">Existierende Gruppen:</label> public,bewohner,bewohner_kontaktliste,eigentümer
	<br>
	<?php if ($this->error !== '') { echo '<div class="error">'.str_replace("\r\n", "<br>", htmlspecialchars($this->error)).'</div>';}?>
	<input type="submit" value="Speichern">
</form>
<?php
	}
	
}