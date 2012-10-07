<?php

/**
 * AgaviBasicSecurityFilter checks security by calling the getCredentials() 
 * method of the action. Once the credential has been acquired, 
 * AgaviBasicSecurityFilter verifies the user has the same credential 
 * by calling the hasCredentials() method of SecurityUser.
 * 
 * PHP version 5.3
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Security filter
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class MarketSecurityFilter extends AgaviFilter implements AgaviIActionFilter, AgaviISecurityFilter
{
	/**
	 * Execute this filter.
	 *
	 * @param AgaviFilterChain        $filterChain A FilterChain instance.
	 * @param AgaviExecutionContainer $container   The current execution container.
	 *
     * @return void
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
            $authorized = $actionInstance->checkAcl($us, $container->getRequestData());
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

		if (!$authorized) {
			if ($user->isAuthenticated()) {
				// the user doesn't have access
				$container->setNext($container->createSystemActionForwardContainer('secure'));
			} else {
				// the user is not authenticated
				$container->setNext($container->createSystemActionForwardContainer('login'));
			}
		}
	}
}
