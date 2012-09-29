<?php

class UsersModel extends MarketBaseModel
{
    const PREFIX  = 'User:';
  
	public static function computeSaltedHash($secret, $salt = null)
	{
        if (!$salt)
            $salt = '$2a$10$'.substr(str_replace('+', '.', base64_encode(sha1(microtime(true), true))), 0, 22);
		return crypt($secret, $salt);
	}


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
        return $dataArray;
    }

    
    public function register($user, $email, $password, $role = null)
    {
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
            'attributes'=> array()
            );
        $dataString = json_encode($data);
        
        return $this->redis->set($key, $dataString);
    }
}
