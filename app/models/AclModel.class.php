<?php

/**
 * Acl model
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
 * Model class
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
class AclModel extends MarketBaseModel
{

    /**
     * Initialize model
     * 
     * @param AgaviContext $context    Agavi context
     * @param array        $parameters parameters
     *
     * @return void     
     * @access public   
     * @throws Exception Exception description (if any) ...
     */
    public function initialize(AgaviContext $context, array $parameters = array()) 
    {
        parent::initialize($context, $parameters);
		$cfg = AgaviConfig::get('core.config_dir') . '/zend_acl.xml';

		if (is_readable($cfg)) {
			$this->config = include AgaviConfigCache::checkConfig($cfg, $this->getContext()->getName());
		} else {
            throw new Exception("zend_acl.xml file is not found");
        }
    }        

    /**
     * get roles
     * 
     * @return array
     * @access public 
     */
    public function getRoles() 
    {
        $roles = isset($this->config['roles']) ? $this->config['roles'] : array();;
        return $roles;
    }

    /**
     * Get resources
     * 
     * @return array
     * @access public 
     */
    public function getResources() 
    {
        $resources = isset($this->config['resources']) ? $this->config['resources'] : array();
        return $resources;
    }
    
}
