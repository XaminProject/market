<?php

/**
 * Ejabberd auth log class
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


/**
 * Ejabbered auth class
 * 
 * @category  Xamin
 * @package   Ejabberd
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Xamin_Log implements Ejabberd_Auth_LogInterface
{
    /**
     * @var file to log
     */
    private $_file;

    /**
     * @var int lower level to debug
     */
    private $_level;

    const DEBUG = 1;

    const INFO = 2;

    const NOTICE = 3;

    const WARNING = 4;

    const ERROR = 5;

    const CRITICAL = 6;

    const NONE = 7;
    
    /**
     * Create new logger
     * 
     * @param string $address file address
     * @param int    $level   Lowest level to log
     *
     * @return void
     */
    public function __construct($address, $level = self::NOTICE) 
    {
		if (!file_exists($address)) {
            @touch($address);
		}
        if (!is_writable($address)) {
            throw new RuntimeException($address . ' is not writable');
        }
        $this->_level = $level;
        $this->_file = fopen($address, 'a');
    }

    /**
     * Actual log function
     * 
     * @param string $message message to log
     *
     * @return void
     */
    private function _log($message)
    {
        $message = sprintf("[%17s] : %s %s", date('y/m/d H:i:s'), $message, PHP_EOL);
        fwrite($this->_file, $message);
    }
    
    /**
     * Debug log
     *
     * Detailed debug information.
     * 
     * @param string $message message to log in this level
     *
     * @return void
     */
    public function debug($message)
    {
        if ($this->_level <= self::DEBUG) {
            $this->_log(sprintf('[%8s] %s', 'DEBUG', $message));
        }
    }

    /**
     * Info log
     *
     * Interesting events. Examples: User logs in, SQL logs.
     * 
     * @param string $message message to log in this level
     *
     * @return void
     */
    public function info($message)
    {
        if ($this->_level <= self::INFO) {
            $this->_log(sprintf('[%8s] %s', 'INFO', $message));
        }
    }

    /**
     * Notice log
     * 
     * Normal but significant events.
     *
     * @param string $message message to log in this level
     *
     * @return void
     */
    public function notice($message)
    {
        if ($this->_level <= self::NOTICE) {
            $this->_log(sprintf('[%8s] %s', 'NOTICE', $message));
        }
    }

    /**
     * warning log
     *
     * Exceptional occurrences that are not errors. Examples: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
     *
     * @param string $message message to log in this level
     *
     * @return void
     */
    public function warning($message)
    {
        if ($this->_level <= self::WARNING) {
            $this->_log(sprintf('[%8s] %s', 'WARNING', $message));
        }
    }

    /**
     * error log
     * 
     * Runtime errors that do not require immediate action but should typically be logged and monitored.
     *
     * @param string $message message to log in this level
     *
     * @return void
     */
    public function error($message)
    {
        if ($this->_level <= self::ERROR) {
            $this->_log(sprintf('[%8s] %s', 'ERROR', $message));
        }
    }

    /**
     * critical log
     * 
     * Critical conditions. Example: Application component unavailable, unexpected exception.
     *
     * @param string $message message to log in this level
     *
     * @return void
     */
    public function critical($message)
    {
        if ($this->_level <= self::CRITICAL) {
            $this->_log(sprintf('[%8s] %s', 'CRITICAL', $message));
        }
    }

}
