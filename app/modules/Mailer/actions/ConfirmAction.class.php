<?php
/**
 * Action description
 * 
 * PHP version 5.2
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 Authors
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Mailer_ConfirmAction desciption
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 Authors
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Mailer_ConfirmAction extends MarketMailerBaseAction
{
	
	
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return mixed <ul>
	 *                <li>A string containing the view name associated
	 *                   with this action; or</li>
	 *                <li>An array with two indices: the parent module
	 *                   of the view to be executed and the view to be
	 *                   executed.</li>
	 *               </ul>
	 */
	public function getDefaultViewName()
	{
		return 'Success';
	}
	
	/**
	 * secure action?
	 * 
	 * @return boolean
	 */
	public function isSecure()
	{
		return false;
	}

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters() 
    {
        return array ('username', 'password');
    }
}
