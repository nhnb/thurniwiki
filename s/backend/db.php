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

	/**
	 * gets the most recent version of the specified page
	 *
	 * @param string $title page title
	 */
	public function readNewestVersion($title) {
		$sql = "SELECT content, account_id, read_permission, timedate"
				." FROM page_version WHERE id = ("
				." SELECT max(page_version.id) FROM page, page_version"
				." WHERE page.title = :title "
				." AND page_version.page_id = page.id)";
		$stmt = DB::connection()->prepare($sql);
		$stmt->execute(array(
			':title' => $title
		));
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * saves a page to the database
	 * @param string $title
	 * @param binary $content
	 * @param int $accountId
	 * @param string $readPermission
	 */
	public function savePageVersion($title, $content, $accountId, $readPermission) {
		$pageId = $this->getPageIdCreateIfNecessary($title);
		$sql = "INSERT INTO page_version (page_id, content, account_id, read_permission) "
				. " VALUES (:page_id, :content, :account_id, :read_permission)";
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute(array(
			':page_id' => $pageId,
			':content' => $content,
			':account_id' => $accountId,
			':read_permission' => $readPermission
		));
	}
	
	
	/**
	 * gets the id of specified page, creating the page if it does not exists
	 *
	 * @param string $title page title
	 * @return id of page
	 */
	public function getPageIdCreateIfNecessary($title) {
		$id = $this->getPageId($title);
		if (isset($id)) {
			return $id;
		}
		$sql = "INSERT INTO page (title) VALUES (:title)";
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute(array(
			':title' => $title
		));
		return $this->getPageId($title);
	}

	/**
	 * gets the id of specified page
	 *
	 * @param string $title page title
	 * @return id of page or <code>null</code>.
	 */
	public function getPageId($title) {
		$sql = "SELECT id FROM page WHERE page.title = :title";
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute(array(
			':title' => $title
		));
			
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) {
			return $row['id'];
		}
		return null;
	}

	public function getAccount($email) {
		$sql = 'SELECT id, email, password FROM account WHERE email=:email';
		$stmt = $this->connection()->prepare($sql);
		$stmt->execute(array(
			':email' => $email
		));
		return $stmt->fetch(PDO::FETCH_ASSOC);
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
create table account (
  id int auto_increment not null,
  email VARCHAR(255),
  password VARCHAR(255),
  timedate timestamp default CURRENT_TIMESTAMP,
  status char(1),
  primary key(id),
  unique index(email)
);

create table page (
  id int auto_increment not null,
  title VARCHAR(255),
  primary key(id),
  unique index(title)
);

create table page_version (
  id int auto_increment not null,
  page_id int not null,
  account_id int not null,
  content VARBINARY(100000000),
  commitcomment VARCHAR(255),
  read_permission VARCHAR(255),
  timedate timestamp default CURRENT_TIMESTAMP,
  primary key(id)
);
		";
	}
}

global $db;
$db = new DB();
