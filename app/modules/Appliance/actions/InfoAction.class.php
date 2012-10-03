<?php
/**
 * Action description
 * 
 * PHP version 5.2
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Appliance_InfoAction desciption
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Appliance_InfoAction extends MarketApplianceBaseAction
{
    
    
    /**
     * Returns the default view if the action does not serve the request
     * method used.
     *
     * @param AgaviRequestDataHolder $rd request data
     *
     * @return mixed <ul>
     *                <li>A string containing the view name associated
     *                   with this action; or</li>
     *                <li>An array with two indices: the parent module
     *                   of the view to be executed and the view to be
     *                   executed.</li>
     *               </ul>
     */
    public function execute(AgaviRequestDataHolder $rd)
    {
        $applianceModel = $this->getContext()->getModel('Appliance', 'Appliance');
        $appliance = $applianceModel->getAppliance($rd->getParameter('name'), $rd->getParameter('version'));
        if (!$appliance) {
            return 'Error';
        }
        $this->setAttribute('appliance', $appliance);
        $this->setAttribute('_title', $appliance['name']);
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
}
