<?php

require_once('action.php');
require_once('backend/db.php');

class PasswordAction extends Action {
	private $error = '';

	public function __construct() {
		global $db, $session;
		
		if (!isset($session['accountId']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
			return;
		}
		
		$account = $db->getAccount($session['email']);
		$cryptpass = crypt($_REQUEST['password'], $account['password']);
		if ($cryptpass !== $account['password']) {
			$this->error = "Altes Passwort ist falsch\r\n";
		}

		if (strlen($_REQUEST['new']) < 6) {
			$this->error .= "Das Passwort muss mindestens 6 Zeichen lang sein.\r\n";
		}
		if ($_REQUEST['new'] !== $_REQUEST['repeat']) {
			$this->error .= "Das neue Password und die Wiederholung müssen identisch sein.\r\n";
		}
		if ($_REQUEST['old'] === $_REQUEST['new']) {
			$this->error .= "Das neue Password darf nicht gleich das alte Passwort sein.\r\n";
		}

		// update password in database
		if ($this->error === '') {
			$db->updatePassword($session['accountId'], $this->hashPassword($_REQUEST['new']));
		}
	}

	/**
	 * Creates a secure hash of the password
	 *
	 * @param string $password
	 * @return sha512crypt hash
	 */
	private function hashPassword($password) {
		$alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
		$salt = '$6$';
		for($i = 0; $i < 16; $i++) {
			$salt .= $alphabet[rand(0, 63)];
		}
		return crypt($password, $salt);
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
			echo '<p>Passwort erfolgreich geändert.</p>';
			return;
		}
?>
<p>Bitte ändern Sie Ihr Passwort hier. Mindestlänge 6 Zeichen.</p>
<form method="POST" action="?action=password">
	<label for="password">Altes Passwort:</label><input type="password" name="password" id="password" required minlength="6"><br>
	<label for="new">Neues Passwort:</label><input type="password" name="new" id="new" required minlength="6"><br>
	<label for="repeat">Wiederholung:</label><input type="password" name="repeat" id="repeat" required minlength="6"><br>
	<br>
	<?php if ($this->error !== '') { echo '<div class="error">'.str_replace("\r\n", "<br>", htmlspecialchars($this->error)).'</div>';}?>
	<input type="submit" value="Speichern">
</form>
<?php
	}
	
}