<?php
// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.								   |
// | Copyright (c) 2012 Parspooyesh co.								           |
// |																		   |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the	   |
// | LICENSE file online at http://www.agavi.org/LICENSE.txt				   |
// |   vi: set noexpandtab:													   |
// |   Local Variables:														   |
// |   indent-tabs-mode: t													   |
// |   End:																	   |
// +---------------------------------------------------------------------------+

/**
 * Provides Redis connectivity through phpredis extension
 *
 * @package	agavi
 * @subpackage Acl
 *
 * @author	  fzerorubigd
 * @copyright Authors
 * @copyright The Agavi Project
 *
 * @since	  1.0.8
 *
 * @version	$Id$
 */
class MarketZendAclSecurityUser extends AgaviZendaclSecurityUser {

	/**
	 * Initialize object.
	 *
	 * @param	  AgaviContext The current application context.
	 * @param	  array		An associative array of initialization parameters.
	 *
	 * @author	  fzerorubigd <fzerorubigd@gmail.com>
	 * @since	  1.0.8
	 */    
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
            if (strtolower($role['name']) != 'null') {
                $acl->addRole($role['name'], $parent);  
            } else {
                $role['name'] = null;
            }
            if (isset($role['perms'])) {
                foreach($role['perms'] as $perm) {
                    list($res, $type, $assert, $privs) = $perm;
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
                    if (strtolower($type) == 'allow') {
                        $acl->allow($role['name'], $res, $privs, $assert);
                    } else {
                        $acl->deny($role['name'], $res, $privs, $assert);
                    }
                }
            }
            $this->initRoles($role['childs'], $role['name']);
        }
    }

	public function isAllowed($resource, $operation = null)
	{
        //Prevent call parent isAllowed
        $aclRole = $this->getRoleId();
		return $this->getZendAcl()->isAllowed($aclRole, $resource, $operation);
	}

    public function login($user, $password, $hashed = false) 
    {
        $users = $this->getContext()->getModel('Users');
        try {
            $userArray = $users->login($user, $password, $hashed);
        } catch (Exception $e) {
            throw new AgaviSecurityException($e->getMessage());
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
