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
header('Content-Type: text/plain');

?>
Error.
<?php 
if (!ini_get('display_errors')) {
    throw $e;
}