<?php

class UsersModel extends MarketBaseModel
{
    const PREFIX  = 'User:';
  
	public static function computeSaltedHash($secret, $salt)
	{
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
}
