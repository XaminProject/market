<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $
 * @link      http://xamin.ir
 * @see       References to other sections (if any)...
 */



/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 * @see       References to other sections (if any)...
 */
class Mailer_RegisterAction extends MarketMailerBaseAction
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
		return 'Success';
	}


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return mixed     Return description (if any) ...
     * @access protected
     */
    protected function getParameters() 
    {
        return array(
            'username'
            );
    }
}

