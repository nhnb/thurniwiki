<?php

/**
 * filters HTML code for security reasons
 *
 * @param string $html
 * @return filtered HTML
 */
function filter_html($html) {
	require_once 'backend/htmlpurifier/library/HTMLPurifier.auto.php';
	$purifier = new HTMLPurifier();
	return $purifier->purify($html);
}
