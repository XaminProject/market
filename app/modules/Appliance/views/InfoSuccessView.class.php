<?php

/**
 * View class
 * 
 * PHP versions 5.2
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
 * View class
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Appliance_InfoSuccessView extends MarketApplianceBaseView
{
    

    /**
     * Handles the Html output type.
     *
     * @param AgaviRequestDataHolder $rd request data
     *
     * @return     mixed <ul>
     *                     <li>An AgaviExecutionContainer to forward the execution to or</li>
     *                     <li>Any other type will be set as the response content.</li>
     *                   </ul>
     */
    public function executeHtml(AgaviRequestDataHolder $rd)
    {
        $this->setupHtml($rd);
        $this->registerCommentSlot($rd->getParameter('name'), null, $rd->getParameter('p', 1));
    }
}

