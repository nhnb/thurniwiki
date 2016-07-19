<?php

class DB {
	private static $db;

	public static function connection() {
		if (!isset(DB::$db)) {
			try {
				DB::$db = new PDO(CONFIG_DB_CONNECTION, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD);
				DB::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				DB::$db->exec('set character set utf8');
			} catch(PDOException $e) {
				error_log('ERROR connecting to database: ' . $e->getMessage());
				die(DB::databaseConnectionErrorMessage());
			}
		}
		return DB::$db;
	}

	private static function databaseConnectionErrorMessage($message) {
		@header('HTTP/1.0 500 Maintenance', true, 500);
?>
<html>
	<head>
		<title>Error</title><meta name="robots" content="noindex">
	</head>
	<body>
		<div style='border:5px solid red; font-size:200%; padding:1em; margin:2em'>
			<p><b>We are currently doing <?php echo htmlspecialchars($message)?> maintenance.</b></p>
			<p>We apologize for the inconvenience.</p>
		</div>
	</body>
</html>
<?php
	}
	
	private function createDatabaseStructure() {
		$sql = "
		create table page (
				id int auto_increment not null,
				title VARCHAR(255),
				primary key(id)
				);
		
		create table page_version (
				id int auto_increment not null,
				page_id int not null,
				account_id int not null,
				content VARBINARY(100000000),
				commitcomment VARCHAR(255),
				timedate timestamp default CURRENT_TIMESTAMP,
				primary key(id)
				);
		";
	}
}
