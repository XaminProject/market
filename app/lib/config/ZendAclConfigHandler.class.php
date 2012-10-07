<?php

/**
 * ZendAclConfigHandler handles Zend Acl resources and roles settings
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
 */

/**
 * Config handler
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
class ZendAclConfigHandler extends AgaviXmlConfigHandler
{

    /**
     * XML namespace
     */
	const XML_NAMESPACE = 'http://xamin.ir/agavi/config/parts/zend_acl_definitions/1.1';

	/**
	 * Execute this configuration handler.
     *
	 * @param AgaviXmlConfigDomDocument $document The document to parse.
     *
	 * @return     string Data to be written to a cache file.
	 * @throws     <b>AgaviUnreadableException</b> If a requested configuration
	 *                                             file does not exist or is not
	 *                                             readable.
	 * @throws     <b>AgaviParseException</b> If a requested configuration file is
	 *                                        improperly formatted.
	 */
	public function execute(AgaviXmlConfigDomDocument $document)
    {
		$this->document = $document;
		$this->document->setDefaultNamespace(self::XML_NAMESPACE, 'zend_acl_definitions');
		
		$data = array();
		
		foreach ($this->document->getConfigurationElements() as $cfg) {
			if ($cfg->has('resources')) {
				$this->parseResources($cfg->get('resources'), $data);
			}

			if ($cfg->has('roles')) {
				$this->parseRoles($cfg->get('roles'), $data);
			}
		}
		
		return "<?php return " . var_export($data, true) . ";";
	}

    /**
     * parse resources tag
     * 
     * @param array $resources Resource to parse
     * @param array &$data     Data to return
     *
     * @return void   
     * @access protected
     */
	protected function parseResources($resources, &$data)
    {
		$data['resources'] = array();
		foreach ($resources as $resources) {
			$data['resources'][] = $this->parseResource($resources);
		} 
	}

    /**
     * Parse resource tag
     * 
     * @param mixed $resource Resource to parse
     *
     * @return array
     * @access protected
     */
	protected function parseResource($resource)
    {
		$result = array();
		$result['name'] = $resource->getAttribute('name');
		$result['childs'] = array();
		foreach ($resource as $res) {
			$result['childs'][] = $this->parseResource($res);
		}
		return $result;
	}

    /**
     * Parse roles tag
     * 
     * @param array $roles Role to parse
     * @param array &$data Data to return
     *
     * @return void   
     * @access protected
     */
	protected function parseRoles($roles, &$data)
    {
		if (!isset($data['roles'])) {
			$data['roles'] = array();
		}
		foreach ($roles as $role) {
			$tmp = $this->parseRole($role);
			if ($tmp) {
				$data['roles'][] = $tmp;
			}
		}
	} 

    /**
     * Parse role tag
     * 
     * @param mixed $role tag 
     *
     * @return array
     * @access protected
     */
	protected function parseRole($role)
    {
		$result = array();
		$result['name'] = $role->getAttribute('name');
		if ($role->has('perms')) {
			$result['perms'] = array();
			foreach ($role->get('perms') as $perm) {
				$result['perms'][$perm->getValue()] = array($perm->getValue() , $perm->getAttribute('type'));
				$result['perms'][$perm->getValue()][] = $perm->hasAttribute('assert') ? $perm->getAttribute('assert') : null;
				$result['perms'][$perm->getValue()][] = $perm->hasAttribute('privileges') ? explode(',', $perm->getAttribute('privileges')) : null;
			}
		}
		$result['childs'] = array();
		foreach ($role as $r) {
            if ($r->tagName == 'role') {
                $tmp = $this->parseRole($r);
                if ($tmp) {
                    $result['childs'][] = $tmp;
                }
            }
		}		
		return $result;
	}
}
