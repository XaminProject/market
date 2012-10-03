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
 * Appliance_InstallAction desciption
 *
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Appliance_InstallAction extends MarketApplianceBaseAction
{
    

    /**
     * Handles the Read request method.
     *
     * @param AgaviRequestDataHolder $rd the request data
     *
     * @return     mixed <ul>
     *                     <li>A string containing the view name associated
     *                     with this action; or</li>
     *                     <li>An array with two indices: the parent module
     *                     of the view to be executed and the view to be
     *                     executed.</li>
     *                   </ul>^
     */
    public function execute(AgaviRequestDataHolder $rd)
    {
        $name = $rd->getParameter('name');
        $version = $rd->getParameter('version');
        $applianceModel = $this->getContext()->getModel('Appliance', 'Appliance');
        $appliance = $applianceModel->getAppliance($name, $version);
        if (!$appliance) {
            return 'Error';
        }
        $this->setAttribute('appliance', $appliance);
        $applianceModel->install(
            $this->getContext()->getUser()->getAttribute('jid'),
            $appliance['name'],
            $appliance['version']
        );
        return 'Success';
    }
}
