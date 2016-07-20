<?php

require_once('action.php');
require_once('backend/db.php');


class EditAction extends Action {
	private $content;
	private $title;
	
	public function __construct() {
		$this->title = $_REQUEST['page'];
		if (isset($_POST['content-editor'])) {
			$this->content = $this->filterHtml($_POST['content-editor']);
		} else {
			$this->readNewestVersion($this->title);
		}
		if ($this->title === '') {
			$this->title = CONFIG_SITE_TITLE;
		}
	}

	/**
	 * gets the most recent version of the specified page
	 *
	 * @param string $title page title
	 */
	public function readNewestVersion($title) {
		$sql = "SELECT content, account_id, timedate"
			." FROM page_version WHERE id = ("
			." SELECT max(page_version.id) FROM page, page_version"
			." WHERE page.title = :title "
			." AND page_version.page_id = page.id)";
		$stmt = DB::connection()->prepare($sql);
		$stmt->execute(array(
			':title' => $title
		));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) {
			$this->content = $row['content'];
		}
		return null;
	}

	public function writeHtmlHeader() {
		parent::writeHtmlHeader($this->title);
	}

	function writeContent() {
		global $session;

		if ($this->content == null) {
			echo '<section>Page not found</section>';
			return;
		}
?>
<form method="POST">
<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($session['csrf']); ?>">
<br><input type="submit" value="Speichern"><br><br>
<textarea id="content-editor" name="content-editor">
	<?php echo $this->content; ?>
</textarea>
</form>
<script src="/s/frontend/ckeditor/ckeditor.js"></script>
<script src="/s/frontend/thurniwiki.js"></script>
<?php
	}

	function filterHtml($html) {
		require_once 'backend/htmlpurifier/library/HTMLPurifier.auto.php';
		$purifier = new HTMLPurifier();
		return $purifier->purify($html);
	}
	
}
