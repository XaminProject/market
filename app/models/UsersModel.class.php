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
 * @copyright 2012 (c) ParsPooyesh co
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
 * @copyright 2012 (c) ParsPooyesh co
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
     * eMail prefix
     */
    const EMAIL_KEY = 'Email:';
  
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
        $data = $this->getRedis()->get(self::PREFIX . $user);
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
        //may be create a new name space for emails in redis?
        $tm = $this->getContext()->getTranslationManager();
        $key = self::PREFIX . strtolower($user);
        $data = $this->getRedis()->get($key);
        if ($data) {
            throw new Exception($tm->_('Username already exist'));
        }
        $emailKey = self::PREFIX . self::EMAIL_KEY . strtolower(md5($email));
        $data = $this->getRedis()->get($emailKey);
        if ($data) {
            throw new Exception($tm->_('eMail is already in use'));
        }
        $data = array (
            'username' => $user,
            'password' => $this->computeSaltedHash($password),
            'acl_role' => $role ? $role : AgaviConfig::get('authz.default_group', 'guest'),
            'email'    => $email,
            'attributes' => array()
            );
        $dataString = json_encode($data);
        
        $done = $this->getRedis()->multi()
            ->set($emailKey, $username)
            ->set($key, $dataString)
            ->exec();
        return $done[0] && $done[1];
    }

    /**
     * Change password
     * 
     * Change password
     * 
     * @param string $username user name
     * @param string $newPass  new password
     * @param string $oldPass  old password, set false to force
     *
     * @return boolean
     * @access public 
     * @throws Exception if user dose not exist or oldPass is wrong
     */
    public function changePassword($username, $newPass, $oldPass) 
    {
        $tm = $this->getContext()->getTranslationManager();
        //First check if user exist, all user are in lower case
        $user = strtolower($username);
        $data = $this->getRedis()->get(self::PREFIX . $user);
        if ($data === false) {
            throw new Exception($tm->_('User name is wrong.'));
        }
        
        $dataArray = json_decode($data, true);
        if ($oldPass !== false) {
            $password = self::computeSaltedHash($oldPass, $dataArray['password']);
            if ($dataArray['password'] != $password) {
                throw new Exception($tm->_('Old password is wrong.'));
            }
        }

        $dataArray['password'] = self::computeSaltedHash($newPass);
        $dataString = json_encode($dataArray);
        
        return $this->getRedis()->set(self::PREFIX . $user, $dataString);

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

        $user = $this->getRedis()->get($key);
        if (!$user) {
            throw new Exception($tm->_("User dose not exist."));
        }
        $user = json_decode($user, true);
        if ($email !== null && strcasecmp($email, $user['email']) != 0) {
            throw new Exception($tm->_("Email is not match."));
        }
        
        $hash = $this->getRedis()->get($recoverKey);
        if (!$hash) {
            $timeOut = AgaviConfig::get('authz.recover_hash_expire', 2);
            $timeOut = $timeOut * 24 * 60 * 60 * 60; //In secound
            $hash = strtolower(sha1(substr(base64_encode(sha1(microtime(true), true)), 0, 22)));
            $this->getRedis()->setex($recoverKey, $timeOut, $hash);
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
        return $this->getRedis()->del($recoverKey);
    }

    /**
     * Generates a random password drawn from the defined set of characters.
     *
     * @param int  $length            The length of password to generate
     * @param bool $specialChars      Whether to include standard special characters. Default true.
     * @param bool $extraSpecialChars Whether to include other special characters. Used when
     *   generating secret keys and salts. Default false.
     *
     * @return string The random password
     **/
    private function _generatePassword($length = 6, $specialChars = true, $extraSpecialChars = false ) 
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($specialChars) {
            $chars .= '!@#$%^&*()';
        }
        if ($extraSpecialChars) {
            $chars .= '-_ []{}<>~`+=,.;:/?|';
        }
        $password = '';
        $randFunc = 'rand';
        if (function_exists('mt_rand')) {
            $randFunc = 'mt_rand';
        }
        for ( $i = 0; $i < $length; $i++ ) {
            $password .= substr($chars, $randFunc(0, strlen($chars) - 1), 1);
        }
        return $password;
    }

    /**
     * Set a random password after check Recover hash.
     * 
     * @param string $username user name
     * @param string $hash     hash from user input
     *
     * @return boolean
     * @access public 
     * @throws Exception if user dose not exist
     */
    public function createRandomPassword($username, $hash) 
    {
        if ($this->checkRecoverHash($username, $hash)) {
            $this->dropRecoverHash($username);
            $randomPassword = $this->_generatePassword();
            if ($this->changePassword($username, $randomPassword, false)) {
                return $randomPassword;
            }
        }
        return false;
    }

    /**
     * Get email address for a user (null for current user)
     *
     * @param string $username User name or null for current user
     *
     * @return string user email or null on no user or any
     */
    public function getEmail($username = null) 
    {
        if ($username == null) {
            $user = $this->getContext()->getUser();
            $username = $user->isAuthenticated() ? $user->getAttribute('username', null) : null;
            if ($username == null) {
                return null;
            }
        }
        $key = self::PREFIX . strtolower($username);
        $user = $this->getRedis()->get($key);
        if (!$user) {
            return null;
        }
        $user = json_decode($user, true);
        return $user['email'];        
    }
}
