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
 * AgaviBasicSecurityFilter checks security by calling the getCredentials() 
 * method of the action. Once the credential has been acquired, 
 * AgaviBasicSecurityFilter verifies the user has the same credential 
 * by calling the hasCredentials() method of SecurityUser.
 *
 * @package    agavi
 * @subpackage filter
 *
 * @author     Sean Kerr <skerr@mojavi.org>
 * @author     David Zülke <dz@bitxtender.com>
 * @copyright  Authors
 * @copyright  The Agavi Project
 *
 * @since      0.9.0
 *
 * @version    $Id: AgaviSecurityFilter.class.php 4901 2011-12-17 08:05:00Z david $
 */
class MarketSecurityFilter extends AgaviFilter implements AgaviIActionFilter, AgaviISecurityFilter
{
	/**
	 * Execute this filter.
	 *
	 * @param      AgaviFilterChain        A FilterChain instance.
	 * @param      AgaviExecutionContainer The current execution container.
	 *
	 * @author     David Zülke <dz@bitxtender.com>
	 * @author     Sean Kerr <skerr@mojavi.org>
	 * @since      0.9.0
	 */
	public function execute(AgaviFilterChain $filterChain, AgaviExecutionContainer $container)
	{
		// get the cool stuff
		$context    = $this->getContext();
		$user       = $context->getUser();

		// get the current action instance
		$actionInstance = $container->getActionInstance();
        
        $actionName = trim(get_class($actionInstance), "\\");
        $actionName = strtolower(str_replace('_', '.', substr($actionName, 0, -6)));
        
        $method = $this->getContext()->getRequest()->getMethod();
        
        $us = $this->getContext()->getUser();
        $authorized = false;
        if (method_exists($actionInstance, 'checkAcl')) {
            $authorized = $actionInstance->checkAcl($us , $container->getRequestData());
        } else {
            $authorized = $us->isAllowed($actionName, $method);
        }

        if ($authorized) {
			try {
				$filterChain->execute($container);
			} catch(AgaviSecurityException $e) {
				$authorized = false;
			}
        }

		if(!$authorized) {
			if($user->isAuthenticated()) {
				// the user doesn't have access
				$container->setNext($container->createSystemActionForwardContainer('secure'));
			} else {
				// the user is not authenticated
				$container->setNext($container->createSystemActionForwardContainer('login'));
			}
		}
	}
}

?>
