<?php
require_once('config.php');
require_once('action.php');
require_once('backend/db.php');
require_once('backend/util.php');

if (count($argv) != 5) {
    echo "Parameters: accountId permissions prefix folder";
    exit;
}

$accountId = $argv[1];
$permission = $argv[2];
$prefix = $argv[3];
$root = $argv[4];

function handleFolder($prefix, $base) {
    global $accountId, $permission, $db;

    echo $prefix . '   ' . $base . "\r\n";
    $files = scandir($base);
    foreach ($files as $file) {
        if ($file === "." || $file === "..") {
            continue;
        }

        $fullfilename = $base . '/' . $file;
        $fullpagename = $prefix . '/' . $file;
        if (is_dir($fullfilename)) {
            echo '    Creating folder ' . $file . "\r\n";
            $db->savePageVersion($fullpagename.'/', '', $accountId, $permission);
            handleFolder($fullpagename, $fullfilename);
        } else {
            echo '    Uploading file ' . $file . "\r\n";
            $content = file_get_contents($fullfilename);
            $db->savePageVersion($fullpagename, $content, $accountId, $permission);
        }
    }
}

handleFolder($prefix, $root);

// 
// $db->savePageVersion($pagename.'/', '', $session['accountId'], $_POST['permission']);

