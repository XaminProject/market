<?php

/**
 * The base action from which all project actions inherit.
 */
class MarketBaseAction extends AgaviAction
{
    
    /**
	 * Checks permissions to perform the operation. Called after validation.
	 *
	 * @return     bool Whether or not the operation is allowed.
	 *
	 * @author     fzerorubigd <fzerorubigd@gmail.com>
	 * @since      1.1.0
	 */
	public function getCredentials()
	{
        $actionName = substr(get_class($this), 0, -6);
        $actionName = strtolower(str_replace('_', ':', $actionName));
        
        $method = $this->getContext()->getRequest()->getMethod();
        
        return "$actionName.$method";
	}

    public function isSecure()
    {
        return true;
    }
}
