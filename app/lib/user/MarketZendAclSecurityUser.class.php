<?php

/**
 * User object for market project
 * 
 * PHP version 5.3
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

/**
 * User object class
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 * @see       References to other sections (if any)...
 */
class MarketZendAclSecurityUser extends AgaviZendaclSecurityUser
{

	/**
	 * Initialize object.
	 *
	 * @param AgaviContext $context    The current application context.
	 * @param array		   $parameters An associative array of initialization parameters.
	 *
     * @return void
	 * @author	  fzerorubigd <fzerorubigd@gmail.com>
	 * @since	  1.0.8
	 */    
    public function initialize(AgaviContext $context, array $parameters = array()) 
    {
        parent::initialize($context, $parameters);
        $model = $this->getContext()->getModel('Acl');
        
        $this->_initResources($model->getResources());
        $this->_initRoles($model->getRoles());
    }

    /**
     * Initialize resources
     * 
     * @param array  $resources Resource array to initialize
     * @param string $parent    Parrent for resource array
     *
     * @return void   
     * @access private
     */
    private function _initResources($resources, $parent = null) 
    {
        $acl = $this->getZendAcl();
        
        while ($res = array_pop($resources)) {
            $acl->addResource($res['name'], $parent);
            $this->_initResources($res['childs'], $res['name']);
        }
    }
    
    /**
     * initialize roles
     * 
     * @param array  $roles  Roles array to add
     * @param string $parent Parent role
     *
     * @return void   
     * @access private
     */
    private function _initRoles($roles, $parent = null) 
    {
        $acl = $this->getZendAcl();
        
        while ($role = array_pop($roles)) {
            if (strtolower($role['name']) != 'null') {
                $acl->addRole($role['name'], $parent);  
            } else {
                $role['name'] = null;
            }
            if (isset($role['perms'])) {
                foreach ($role['perms'] as $perm) {
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
            $this->_initRoles($role['childs'], $role['name']);
        }
    }

    /**
     * Is allowed for current user to access to this resource
     * 
     * @param string $resource  Resource name or object (But always string in our case)
     * @param string $operation Operation, read write or any other action operation
     *
     * @return boolean
     * @access public 
     */
	public function isAllowed($resource, $operation = null)
	{
        //Prevent call parent isAllowed
        $aclRole = $this->getRoleId();
		return $this->getZendAcl()->isAllowed($aclRole, $resource, $operation);
	}

    /**
     *‌ Login user 
     * 
     * @param string  $user     username
     * @param string  $password password
     * @param boolean $hashed   is hashed?
     *
     * @return void                  
     * @access public                
     * @throws AgaviSecurityException if invalid login
     */
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

        foreach ($userArray['attributes'] as $attr => $value) {
            parent::setAttribute($attr, $value);
        }

        //This two attribute is important
        parent::setAttribute('username', $userArray['username']);
        
        if ($this->getZendAcl()->hasRole($userArray['acl_role'])) {
            parent::setAttribute('acl_role', $userArray['acl_role']);
        } else {
            parent::setAttribute('acl_role', AgaviConfig::get('authz.default_group', 'guest'));
        }
    }

    /**
     * Set attribute, and set it into redis
	 *
	 * If an attribute with the name already exists the value will be
	 * overridden.
	 *
	 * @param string $name  An attribute name.
	 * @param mixed  $value An attribute value.
	 * @param string $ns    An attribute namespace.     
     *
     * @return void
     */
    public function setAttribute($name, $value, $ns = null)
    {
        parent::setAttribute($name, $value, $ns);
        $users = $this->getContext()->getModel('Users');
        if ($ns == null) {
            //save this into redis.
            $users->storeAttribute($name);
        }
    }


    /**
     * Logout user
     * 
     * @return void  
     * @access public
     */
	public function logout()
	{
		$this->clearCredentials();
		$this->setAuthenticated(false);
		$this->clearAttributes();
	}


    /**
     * startup user system
     * 
     * @return void  
     * @access public
     */
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
