<?php

/**
 * Aplianc index action
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
 * @see       References to other sections (if any)...
 */


/**
 * Index action class
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 * @see       References to other sections (if any)...
 */
class Appliance_IndexSuccessView extends MarketApplianceBaseView
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
        $this->setAttribute('_title', 'Index');

        $this->getLayer('content')->setSlot(
            'tags',
            $this->createSlotContainer(
                'Appliance', // name of module to use
                'Tags', // name of action to execute
                array(), // parameters to pass to the slot
                'html', // output type to use
                'read' // request method to use
            )
        );
        $this->getLayer('content')->setSlot(
            'search',
            $this->createSlotContainer(
                'Appliance', // name of module to use
                'Search', // name of action to execute
                array(), // parameters to pass to the slot
                'html', // output type to use
                'read' // request method to use
            )
        );
    }

    /**
     * Handles the json output type.
     *
     * @param AgaviRequestDataHolder $rd the (validated) request data
     *
     * @return     mixed <ul>
     *                     <li>An AgaviExecutionContainer to forward the execution to or</li>
     *                     <li>Any other type will be set as the response content.</li>
     *                   </ul>
     */
   
    public function executeJson(AgaviRequestDataHolder $rd)
    {
        $this->loadLayout();
        $this->getLayer('content')->setSlot(
            'tags',
            $this->createSlotContainer(
                'Appliance', // name of module to use
                'Tags', // name of action to execute
                array(), // parameters to pass to the slot
                'html', // output type to use
                'read' // request method to use
            )
        );
        $this->getLayer('content')->setSlot(
            'search',
            $this->createSlotContainer(
                'Appliance', // name of module to use
                'Search', // name of action to execute
                array(), // parameters to pass to the slot
                'html', // output type to use
                'read' // request method to use
            )
        );
        return parent::executeJson($rd);
    }
}
