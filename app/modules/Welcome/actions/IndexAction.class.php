<?php

class Welcome_IndexAction extends MarketWelcomeBaseAction
{
	
	
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */
	public function getDefaultViewName()
	{
		$cfg = AgaviConfig::get('core.config_dir') . '/zend_acl.xml';

		if (is_readable($cfg)) {
			$this->config = include(AgaviConfigCache::checkConfig($cfg, $this->getContext()->getName()));
		}
		return 'Success';
	}
}

?>