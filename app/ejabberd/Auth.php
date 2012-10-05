<?php

/**
 * Ejabberd auth class for redis
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
 * Ejabbered auth class, authenticate from redis
 * 
 * @category  Xamin
 * @package   Ejabberd
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Xamin_Auth implements Ejabberd_Auth_AuthInterface
{
    /**
     * User scope prefix in redis
     */
    const USER_PREFIX = 'User:';

    /**
     * @var Redis connection
     */
    private $_redis;

    /**
     * @var array redis config
     */
    private $_config;
    /**
     * Construct object
     * 
     * @param array $configFile redis connection parameter from agavi db config
     *
     * @return void
     */
    public function __construct($configFile)
    {
        $this->_configs = $this->_extractParameters($configFile);
        $this->_initRedis();
    }

    /**
     * extract parameters from agavi config file
     *
     * @param string $configFile agavi config file address
     * 
     * @return array config array
     */
    private function _extractParameters($configFile)
    {
        //TODO : {fzerorubigd} Get only production value, not development value
        $config = new DOMDocument();
        $config->load($configFile);
        $databases = $config->getElementsByTagName('database');
        $configParams = array('host'       => 'localhost',
                               'port'       => '6379',
                               'timeout'    => '1',
                               'persistent' => 'false');

        foreach ($databases as $db) {
            if (strcasecmp($dbName = $db->getAttribute('name'), 'redis') == 0) {
                $children = $db->childNodes;
                foreach ($children as $item) {
                    if ($item->nodeName == 'ae:parameter') {
                        $value = $item->nodeValue;
                        if (in_array($item->getAttribute('name'), array('host', 'port', 'timeout', 'persistent'))) {
                            $configParams[$item->getAttribute('name')] = $value;
                        }
                    }
                }
                break;
            }
        }
        return $configParams;
    }

    /**
     * Initialize redis
     * 
     * @return void
     * @throw Exception on failed to connect
     */
    private function _initRedis()
    {
        if (!$this->_redis) {
            $this->_redis = new Redis();
            extract($this->_configs);
            if ($persistent) {
                $result = $this->_redis->pconnect($host, $port, $timeout);
            } else {
                $result = $this->_redis->connect($host, $port, $timeout);
            }
            if (!$result) {
                throw new Exception("Redis connection failed.");
            }
        }        
    }

    /**
     * Create hashed passsword
     * 
     * @param string $secret Password to create hash 
     * @param string $salt   Salt for create hash 
     *                        
     * @return string hashed password 
     * @access public
     * @static
     */
    public static function computeSaltedHash($secret, $salt = null)
    {
        if (!$salt) {
            $salt = '$2a$10$' . substr(str_replace('+', '.', base64_encode(sha1(microtime(true), true))), 0, 22);
        }
        return crypt($secret, $salt);
    }


    /**
     * isuser operation ejabberd
     *
     * @param string $user   username to check
     * @param string $server server name 
     *
     * @return boolean true if exist
     */
    public function isuserOper($user, $server)
    {
        $data = $this->_redis->get(self::USER_PREFIX . strtolower($user));
        if ($data) {
            return true;
        }
        return false;
    }

    /**
     * auth operation 
     *
     * @param string $user     user name to authenticate
     * @param string $server   server name 
     * @param string $password password
     *
     * @return boolean true on success
     */
    public function authOper($user, $server, $password)
    {
        $data = $this->_redis->get(self::USER_PREFIX . strtolower($user));
        if (!$data) {
            return false;
        }
        $dataArray = json_decode($data, true);
        $password = self::computeSaltedHash($password, $dataArray['password']);
        
        if ($dataArray['password'] != $password) {
            return false;
        }
        return true;
    }

    /**
     * set password operation 
     *
     * @param string $user     user name to authenticate
     * @param string $server   server name 
     * @param string $password password
     *
     * @return boolean true on success
     */
    public function setpassOper($user, $server, $password)
    {
        $data = $this->_redis->get(self::USER_PREFIX . strtolower($user));
        if (!$data) {
            return false;
        }
        $dataArray = json_decode($data, true);
        $password = self::computeSaltedHash($password);

        $dataArray['password'] = $password;
        $dataString = json_encode($dataArray);
        return $this->_redis->set(self::USER_PREFIX . strtolower($user), $dataString);
    }

    /**
     * register new user
     *
     * @param string $user     user name to authenticate
     * @param string $server   server name 
     * @param string $password password
     *
     * @return boolean true on success
     */
    public function tryregisterOper($user, $server, $password)
    {
        if ($this->isuserOper($user, $server)) {
            return false;
        }
        
        $data = array(
            'username' => $user,
            'password' => self::computeSaltedHash($password),
            );
        $dataString = json_encode($data);
        return $this->_redis->set(self::USER_PREFIX . strtolower($user), $dataString);
    }

    /**
     * remove user
     *
     * @param string $user   user name to authenticate
     * @param string $server server name 
     *
     * @return boolean true on success
     */
    public function removeuserOper($user, $server)
    {
        if (!$this->isuserOper($user, $server)) {
            return false;
        }
        return $this->_redis->del(self::USER_PREFIX . strtolower($user)) > 0;
    }

    /**
     * safe remove user
     *
     * @param string $user     user name to authenticate
     * @param string $server   server name 
     * @param string $password password
     *
     * @return boolean true on success
     */
    public function removeuser3Oper($user, $server, $password)
    {
        if (!$this->authOper($user, $server, $password)) {
            return false;
        }
        return $this->removeuserOper($user, $server);
    }

}
