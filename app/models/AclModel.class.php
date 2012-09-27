<?php

class AclModel extends MarketBaseModel
{
    public function initialize(AgaviContext $context, array $parameters = array()) 
    {
        parent::initialize($context, $parameters);
		$cfg = AgaviConfig::get('core.config_dir') . '/zend_acl.xml';

		if (is_readable($cfg)) {
			$this->config = include(AgaviConfigCache::checkConfig($cfg, $this->getContext()->getName()));
		} else {
            throw new Exception("zend_acl.xml file is not found");
        }
    }        

    public function getRoles() 
    {
        $roles = isset($this->config['roles']) ? $this->config['roles'] : array();;
        return $roles;
    }

    public function getResources() 
    {
        $resources = isset($this->config['resources']) ? $this->config['resources'] : array();
        return $resources;
    }
    
}
