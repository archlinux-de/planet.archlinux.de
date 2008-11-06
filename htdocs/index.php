<?php

header('HTTP/1.1 200 OK');

#if (strpos($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false)
#	{
#	header('Content-Type: application/xhtml+xml; charset=UTF-8');
#	}
#else
#	{
	header('Content-Type: text/html; charset=UTF-8');
#	}

$html = file_get_contents('index.html');

if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)
	{
	header('Content-Encoding: gzip');
	header('Vary: Accept-Encoding');
	$html = gzencode($html, 3);
	}

header('Content-Length: '.strlen($html));
echo $html;

?>
