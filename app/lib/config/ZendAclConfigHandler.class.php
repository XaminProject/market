<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2005-2011 the Agavi Project.                                |
// | Based on the Mojavi3 MVC Framework, Copyright (c) 2003-2005 Sean Kerr.    |
// |                                                                           |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the    |
// | LICENSE file online at http://www.agavi.org/LICENSE.txt                   |
// |   vi: set noexpandtab:                                                    |
// |   Local Variables:                                                        |
// |   indent-tabs-mode: t                                                     |
// |   End:                                                                    |
// +---------------------------------------------------------------------------+

/**
 * ZendAclConfigHandler handles Zend Acl resources and roles settings
 */
class ZendAclConfigHandler extends AgaviXmlConfigHandler {

	const XML_NAMESPACE = 'http://xamin.ir/agavi/config/parts/zend_acl_definitions/1.1';

	/**
	 * Execute this configuration handler.
	 * @param      AgaviXmlConfigDomDocument The document to parse.
	 * @return     string Data to be written to a cache file.
	 * @throws     <b>AgaviUnreadableException</b> If a requested configuration
	 *                                             file does not exist or is not
	 *                                             readable.
	 * @throws     <b>AgaviParseException</b> If a requested configuration file is
	 *                                        improperly formatted.
	 */
	public function execute(AgaviXmlConfigDomDocument $document) {
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

	private function parseResources($resources, &$data) {
		$data['resources'] = array();
		foreach( $resources as $resources) {
			$data['resources'][] = $this->parseResource($resources);
		} 
	}

	private function parseResource($resource) {
		$result = array();
		$result['name'] = $resource->getAttribute('name');
		$result['childs'] = array();
		foreach($resource as $res) {
			$result['childs'][] = $this->parseResource($res);
		}
		return $result;
	}

	private function parseRoles($roles, &$data) {
		if (!isset($data['roles'])) {
			$data['roles'] = array();
		}
		foreach($roles as $role) {
			$tmp = $this->parseRole($role) ;
			if ($tmp) {
				$data['roles'][] = $tmp;
			}
		}
	} 

	private function parseRole($role) {
		$result = array();
		$result['name'] = $role->getAttribute('name');
		if ($role->has('perms')) {
			$result['perms'] = array();
			foreach ($role->get('perms') as $perm) {
				$result['perms'][$perm->getValue()] = array($perm->getValue() , $perm->getAttribute('type'));
				$result['perms'][$perm->getValue()][] = $perm->hasAttribute('assert') ? $perm->getAttribute('assert') : null;
				$result['perms'][$perm->getValue()][] = $perm->hasAttribute('privileges') ? explode(',' , $perm->getAttribute('privileges')) : null;
			}
		}
		$result['childs'] = array();
		foreach($role as $r) {
            if ($r->tagName == 'role') {
                $tmp = $this->parseRole($r) ;
                if ($tmp) {
                    $result['childs'][] = $tmp;
                }
            }
		}		
		return $result;
	}
}
