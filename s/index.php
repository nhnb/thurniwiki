<?php 

require_once('config.php');
require_once('backend/main.php');

echo 'Hallo, '.htmlspecialchars($_REQUEST['page']).'!';