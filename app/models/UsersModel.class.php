<?php

/**
 * User model
 * 
 * User model for application. 
 * 
 * PHP version 5
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $
 * @link      http://xamin.ir
 */


/**
 * User model class
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class UsersModel extends MarketBaseModel
{

    /**
     * Prefix for users in redis database
     */
    const PREFIX  = 'User:';

    /**
     * Recover hash prefix
     */
    const RECOVER_HASH = 'RecoverHash:';
  
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
     * Try to login a user
     * 
     * Authenticate user and set attributes 
     * 
     * @param string  $user     User name
     * @param string  $password Password
     * @param boolean $hashed   Indicate the $password is hashed or not
     *                             
     * @return array     user data array from redis database
     * @access public   
     * @throws Exception Wrong user and/or password
     */
    public function login($user, $password, $hashed = false) 
    {
        $tm = $this->getContext()->getTranslationManager();
        //First check if user exist, all user are in lower case
        $user = strtolower($user);
        $data = $this->redis->get(self::PREFIX . $user);
        if ($data === false) {
            throw new Exception($tm->_('User name and/or password is wrong.'));
        }
        
        $dataArray = json_decode($data, true);
        if (!$hashed) {
            $password = self::computeSaltedHash($password, $dataArray['password']);
        } 
        if ($dataArray['password'] != $password) {
            throw new Exception($tm->_('User name and/or password is wrong.'));
        }
        //Login ok, return the user
        unset($dataArray['password']);
        //Just in case, drop any requested recoverHash
        $this->dropRecoverHash($user);
        return $dataArray;
    }

    
    /**
     * Try to register new user
     * 
     * @param string $user     User name
     * @param string $email    User email
     * @param string $password password
     * @param string $role     ACL role (default from setting)
     *                             
     * @return boolean  
     * @access public   
     * @throws Exception if user already exists
     */
    public function register($user, $email, $password, $role = null)
    {
        //TODO : {fzerorubigd} Check for duplicate email, 
        //may be create a new name space for emails in redis?
        $tm = $this->getContext()->getTranslationManager();
        $key = self::PREFIX . strtolower($user);
        $data = $this->redis->get($key);
        if ($data) {
            throw new Exception($tm->_('Username already exist'));
        }
        $data = array (
            'username' => $user,
            'password' => $this->computeSaltedHash($password),
            'acl_role' => $role ? $role : AgaviConfig::get('authz.default_group', 'guest'),
            'email'    => $email,
            'attributes' => array()
            );
        $dataString = json_encode($data);
        
        return $this->redis->set($key, $dataString);
    }

    /**
     * get Recover hash.
     * 
     * check db for User:RecoverHash:username this hash is time-based hash 
     * time is get from setting
     * 
     * @param string $username user name
     * @param string $email    email. check this only if not null
     *
     * @return string
     * @access public 
     * @throws Exception if user dose not exist or email dose not match
     */
    public function getRecoverHash($username, $email)
    {
        $tm = $this->getContext()->getTranslationManager();
        $key = self::PREFIX . strtolower($username);
        $recoverKey = self::PREFIX . self::RECOVER_HASH . strtolower($username);

        $user = $this->redis->get($key);
        if (!$user) {
            throw new Exception($tm->_("User dose not exist."));
        }
        $user = json_decode($user, true);
        if ($email !== null && strcasecmp($email, $user['email']) != 0) {
            throw new Exception($tm->_("Email is not match."));
        }
        
        $hash = $this->redis->get($recoverKey);
        if (!$hash) {
            //TODO : {fzerorubigd} Handle time base redis key
            $hash = strtolower(sha1(substr(base64_encode(sha1(microtime(true), true)), 0, 22)));
            $this->redis->set($recoverKey, $hash);
        }
        return $hash;
    }

    /**
     * check Recover hash.
     * 
     * @param string $username user name
     * @param string $hash     hash from user input
     *
     * @return boolean
     * @access public 
     * @throws Exception if user dose not exist
     */
    public function checkRecoverHash($username, $hash)
    {
        $rHash = $this->getRecoverHash($username, null);
        return strcasecmp($hash, $rHash) == 0;
    }

    /**
     * drop recover hash if exists
     * 
     * @param string $username user name
     *
     * @return boolean
     * @access public 
     */
    public function dropRecoverHash($username) 
    {
        $recoverKey = self::PREFIX . self::RECOVER_HASH . strtolower($username);
        return $this->redis->del($recoverKey);
    }
}
