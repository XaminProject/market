<?php
/**
 * Exception template
 * 
 * PHP version 5
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 * @see       References to other sections (if any)...
 */

header('HTTP/1.0 500 Internal Server Error');
header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Error</title>
		<meta http-equiv="Content-Language" content="en" />
		<meta name="robots" content="none" />
	</head>
	<body>
		<h1>Internal Server Error</h1>
		<p>An error occurred.</p>
	</body>
</html>
<?php 
if (!ini_get('display_errors')) {
    throw $e; 
}