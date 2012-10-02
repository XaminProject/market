<?php

/**
 * Apliance tag action
 * 
 * PHP version 5.3
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 Authors
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Tag action
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 Authors
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Appliance_TagAction extends MarketApplianceBaseAction
{
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
     *
     * @param AgaviRequestDataHolder $rd request data
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */
	public function execute(AgaviRequestDataHolder $rd)
	{
        $appliance = $this->getContext()->getModel('Appliance', 'Appliance');
        $tag = $rd->getParameter('name');
        $this->setAttribute('appliances', $appliance->appliancesByTag($tag));
		return 'Success';
	}

    /**
     * Secure action or not?
     * 
     * @return boolean 
     * @access public 
     */
    public function isSecure()
    {
        return false;
    }
}
