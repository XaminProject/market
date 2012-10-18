<?php

/**
 * Error for json
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Xamin
 * @package   Market
 * @author    Authors <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 * @see       References to other sections (if any)...
 */
if (!ini_get('display_errors')) {
	throw $e;
}
$response = array(
	'success' => false,
	'trace' => $e->getTraceAsString(),
	'error' => $e->getMessage()
);
echo json_encode($response);

