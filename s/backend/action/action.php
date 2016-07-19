<?php
class Action {
	
	public function writeHttpHeader() {
	}

	public function writeHtmlHeader($title) {
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset='UTF-8'">
	<link rel="stylesheet" href="/s/frontend/thurniwiki-00001.css">
	<title><?php echo htmlspecialchars($title);?></title>
</head>
<body>
<div class="frame">
<header>
	<img class="logo" src="/s/frontend/logo.jpg">
	<!-- <div>Thurnithistraße 20</div> -->
<h1><?php echo htmlspecialchars($title);?></h1>
	<a class="navbutton" href="/">Startseite</a> <a class="navbutton" href="#">Login</a>
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
		
}