#!/usr/bin/php
<?php
/**
 * Ejabberd Authenticate
 * 
 * PHP version 5.3
 * 
 * @category  Xamin
 * @package   Ejabberd
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */

set_include_path(
    realpath(__DIR__ . '/../../libs/') . PATH_SEPARATOR .  get_include_path()
);

$logFile = "/var/log/ejabberd/exauth.log";
$configFile = realpath(__DIR__ . '/../config/databases.xml');

require_once "autoload.php";
require_once __DIR__ . "/Auth.php";
require_once __DIR__ . "/Log.php";
$log = null;
try {
    $log = new Xamin_Log($logFile, Xamin_Log::DEBUG);
    $auth = new Xamin_Auth($configFile);

    $authenticator = new Ejabberd_Auth($auth, $log);

    $authenticator->serve();
} catch (Exception $e) {
    if ($log) {
        $log->critical($e->getMessage());
    } else {
        file_put_contents('php://stderr', $e->getMessage());
    }
    exit(1); //Exit with code 1
}