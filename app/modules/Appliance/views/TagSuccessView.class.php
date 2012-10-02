<?php

/**
 * Apiance tag success view
 * 
 * PHP version 5.3
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
 * @copyright 2012 (c) ParsPooyesh co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Appliance_TagSuccessView extends MarketApplianceBaseView
{
	

	/**
	 * Handles the Html output type.
	 *
	 * @param AgaviRequestDataHolder $rd the (validated) request data
	 *
	 * @return     mixed <ul>
	 *                     <li>An AgaviExecutionContainer to forward the execution to or</li>
	 *                     <li>Any other type will be set as the response content.</li>
	 *                   </ul>
	 */
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);
        $appliances = [];
        foreach ($this->getAttribute('appliances', array()) as $name => $version) {
            $appliances[] = array(
                "name" => $name,
                "version" => $version
            );
        }
        $this->setAttribute('appliances', $appliances);
	}
}
