<?php
/**
 * Action description
 * 
 * PHP version 5.2
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Cli_RoutingAction desciption
 *
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Cli_RoutingAction extends MarketCliBaseAction
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
    public function executeRead(AgaviRequestDataHolder $rd)
    {
        $emberCode = $this->getContext()->getModel('EmberCode', 'Cli');
        try {
            $routes = $emberCode->genRoutes('web');
            $this->setAttribute('routes', $routes);
        } catch (Exception $e) {
            die($e->getMessage());
        }
        return 'Success';
    }

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

}
