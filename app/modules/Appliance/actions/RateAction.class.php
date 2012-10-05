<?php
/**
 * Action description
 * 
 * PHP version 5.2
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
 * Appliance_RateAction desciption
 *
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Appliance_RateAction extends MarketApplianceBaseAction
{
    

    /**
     * Handles the Read request method.
     *
     * @param AgaviRequestDataHolder $rd the request data
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
        $name = $rd->getParameter('name');
        $rate = $rd->getParameter('rate');
        $applianceModel = $this->getContext()->getModel('Appliance', 'Appliance');
        $appliance = $applianceModel->getAppliance($name);
        if (!$appliance) {
            return 'Error';
        }
        $current = $appliance['rate'];
        // increase / decrease would be happened if user changes the rating
        $difference = pow($rate, 2) - pow($current, 2);
        $applianceModel->increaseRatingBy($name, $difference);
        $applianceModel->setUserRateOfAppliance($name, $rate);
        exit;
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
