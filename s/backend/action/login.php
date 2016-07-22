<?php
require_once('action.php');
require_once('backend/db.php');

class LoginAction extends Action {
	private $error;
	
	public function __construct() {
		global $db, $session;

		if (isset($_POST['email'])) {
			$this->tryLogin();
		}
	}

	public function writeHttpHeader() {
		if ($this->handleRedirectIfAlreadyLoggedIn()) {
			exit();
		}
	}

	private function handleRedirectIfAlreadyLoggedIn() {
		global $session;

		if (isset($session['accountId'])) {
			if (isset($_REQUEST['url'])) {
				header('Location: https://'.$_SERVER['SERVER_NAME'].'/'.$_REQUEST['url']);
			} else {
				header('Location: https://'.$_SERVER['SERVER_NAME'].'/');
			}
			return true;
		}
		return false;
	}

	private function tryLogin() {
		global $db, $session;
		
		$account = $db->getAccount($_REQUEST['email']);
		if (!isset($account)) {
			$this->error = 'Benutzername oder Passwort ungültig';
			return;
		}

		$cryptpass = crypt($_REQUEST['password'], $account['password']);
		if ($cryptpass !== $account['password']) {
			$this->error = 'Benutzername oder Passwort ungültig';
			return;
		}

		$session['accountId'] = $account['id'];
		$session['email'] = $account['email'];
		$session['status'] = $account['status'];
		$session['csrf'] = $this->createRandomString(); 
		$_SESSION['session'] = $session;
	}
	
	/**
	 * Creates a secure hash of the password
	 *
	 * @param string $password
	 * @return sha512crypt hash
	 */
	public function hashPassword($password) {
		$alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
		$salt = '$6$';
		for($i = 0; $i < 16; $i++) {
			$salt .= $alphabet[rand(0, 63)];
		}
		return crypt($password, $salt);
	}

	/**
	 * creates a random string
	 */
	private function createRandomString() {
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$res = '';
		for ($i = 0; $i < 20; $i++) {
			$res .= $characters[mt_rand(0, strlen($characters) - 1)];
		}
		return $res;
	}

	public function writeContent() {
?>
<p>Bitte melden Sie sich mit Ihrer E-Mail-Adresse und Passwort an</p>
<form method="POST">
	<label for="email">E-Mail: </label><input type="email" name="email" id="email" required><br>
	<label for="password">Passwort:</label><input type="password" name="password" id="password" required><br>
	<br>
	<?php if (isset($this->error)) { echo '<div class="error">'.htmlspecialchars($this->error).'</div>';}?>
	<input type="submit" value="Login">
</form>
<?php
	}
}
