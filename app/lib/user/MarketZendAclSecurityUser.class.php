<?php


class MarketZendAclSecurityUser extends AgaviZendaclSecurityUser {

    
    public function initialize(AgaviContext $context, array $parameters = array()) 
    {
        parent::initialize($context, $parameters);
        $model = $this->getContext()->getModel('Acl');
        
        $this->initResources($model->getResources());
        $this->initRoles($model->getRoles());
    }

    private function initResources($resources, $parent = null) 
    {
        $acl = $this->getZendAcl();
        
        while($res = array_pop($resources)) {
            $acl->addResource($res['name'], $parent);
            $this->initResources($res['childs'], $res['name']);
        }
    }
    
    private function initRoles($roles, $parent = null) 
    {
        $acl = $this->getZendAcl();
        
        while($role = array_pop($roles)) {
            if (strtolower($role['name']) !== 'null') {
                $acl->addRole($role['name'], $parent);  
            }
            if (isset($role['perms'])) {
                foreach($role['perms'] as $perm) {
                    list($res, $type, $assert, $privs) = $perm;
                    if (strtolower($type) == 'allow') {
                        if ($assert !== null) {
                            if (class_exists($assert)) {
                                //XXX : Assert is risky. 
                                $assert = new $assert();
                                if (!$assert instanceof Zend_Acl_Assert_Interface) {
                                    $assert = null;
                                }
                            } else {
                                $assert = null;
                            }
                        }
                        $acl->allow($role['name'], $res, $privs, $assert);
                    }
                }
            }
            $this->initRoles($role['childs'], $role['name']);
        }
    }

    public function login($user, $password, $hashed = false) 
    {
        $users = $this->getContext()->getModel('Users');
        try {
            $userArray = $users->login($user, $password, $hashed);
        } catch (Exception $e) {
            throw AgaviSecurityException($e->getMessage());
        }

        $this->setAuthenticated(true);
        $this->clearCredentials();

        foreach( $userArray['attributes'] as $attr => $value) {
            $this->setAttribute($attr, $value);
        }

        //This two attribute is important
        $this->setAttribute('username', $userArray['username']);
        
        if ($this->getZendAcl()->hasRole($userArray['acl_role'])) {
            $this->setAttribute('acl_role', $userArray['acl_role']);
        } else {
            $this->setAttribute('acl_role', AgaviConfig::get('authz.default_group', 'guest'));
        }
    }

	public function logout()
	{
		$this->clearCredentials();
		$this->setAuthenticated(false);
		$this->clearAttributes();
	}


	public function startup()
	{
		parent::startup();
		//XXX: I'm not ok with autologin cookie :D must check this later
		/* 
           $reqData = $this->getContext()->getRequest()->getRequestData();
		
		if(!$this->isAuthenticated() && $reqData->hasCookie('autologon')) {
			$login = $reqData->getCookie('autologon');
			try {
				$this->login($login['username'], $login['password'], true);
			} catch(AgaviSecurityException $e) {
				$response = $this->getContext()->getController()->getGlobalResponse();
				// login didn't work. that cookie sucks, delete it.
				$response->setCookie('autologon[username]', false);
				$response->setCookie('autologon[password]', false);
			}
		}
        */
	}

}
