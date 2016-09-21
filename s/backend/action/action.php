<?php
class Action {
	
	public function writeHttpHeader() {
	}

	public function writeHtmlHeader($title) {
		global $session;

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset='UTF-8'">
	<link rel="stylesheet" href="/s/frontend/thurniwiki-00001.css">
	<title><?php echo htmlspecialchars($title).' &ndash; '.CONFIG_SITE_TITLE;?></title>
</head>
<body>
<div class="frame">
<header>
	<a href="/" class="home"><img class="logo" src="/s/frontend/logo.jpg"></a>

<?php 
	echo '<h1>'.htmlspecialchars(CONFIG_SITE_TITLE).'</h1>';
	if (isset($session['accountId'])) {
		echo '<a class="navbutton" href="/?action=index">Index</a>';
		echo '<a class="navbutton" href="?action=edit">Seite bearbeiten</a>';
		echo '<a class="navbutton" href="?action=logout">Logout</a>';
	} else {
		echo '<a class="navbutton right" href="?action=login">Login</a>';
	}
?>
</header>
<?php
		}
	
	
	public function writeHtmlFooter() {
?>
	</div>
	</body>
</html>
<?php
	}

	public function mayAccess($permission) {
		global $session;
		$required = explode(',', $permission);
		$available = ['public'];
		if (isset($session) && isset($session['groups'])) {
			$available = explode(',', $session['groups']);
		}
		return count(array_intersect($required, $available)) > 0;
	}
}