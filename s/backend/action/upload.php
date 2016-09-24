<?php

require_once('action.php');
require_once('backend/db.php');
require_once('backend/util.php');

class UploadAction extends Action {
	private $error;

	public function writeContent() {
		global $session, $db;

		$content = file_get_contents($_FILES['file']['tmp_name']);
		$filename = $_FILES['file']['name'];
		$title = trim($_REQUEST['page'].'/'.$filename, " \t\n\r\0\x0B/");
		
		if (!isset($session['accountId'])) {
			echo '<div class="error">Die Sitzung ist abgelaufen.</div>';
			return;
		}

		if ($_POST['csrf'] != $session['csrf']) {
			echo '<div class="error">Sitzungsinformationen verloren. Bitte speichern Sie erneut.</div>';
			return;
		}

		chmod($_FILES['file']['tmp_name'], 0644);
		exec('/usr/bin/clamdscan '.$_FILES['file']['tmp_name'], $ignored, $res);
		if ($res !== 0) {
			echo '<div class="error">Virus-Scan ist fehlgeschlagen.</div>';
			return;
		}

		$mimeType = get_mime_type_from_filename($title);
		if ($mimeType === "text/html") {
			$content = filter_html($content);
		}

		$db->savePageVersion($title, $content, $session['accountId'], $_POST['permission']);
		echo 'File uploaded successfully.';
	}
}