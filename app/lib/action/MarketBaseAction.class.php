<?php

/**
 * The base action from which all project actions inherit.
 */
class MarketBaseAction extends AgaviAction
{
    
    /**
	 * Checks permissions to perform the operation. Called after validation.
	 *
	 *
	 * @param      AgaviUser              The AgaviUser object (for convenience).
	 * @param      AgaviRequestDataHolder The request data (for convenience).
	 *
	 * @return     bool Whether or not the operation is allowed.
	 *
	 * @author     fzerorubigd <fzerorubigd@gmail.com>
	 * @since      1.1.0
	 */
	public function checkPermissions(AgaviUser $user, AgaviRequestDataHolder $rd)
	{
		// get the cool stuff
		$context    = $this->getContext();
		$user       = $context->getUser();
        $container  = $this->getContainer();

		// get the current action instance
		$actionInstance = $container->getActionInstance();
        
        $actionName = trim(get_class($actionInstance), "\\");
        $actionName = strtolower(str_replace('_', '.', substr($actionName, 0, -6)));
        
        $method = $this->getContext()->getRequest()->getMethod();
        
        return $user->isAllowed($actionName, $method);
	}
}
