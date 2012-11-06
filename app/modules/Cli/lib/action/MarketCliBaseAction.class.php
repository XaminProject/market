<?php

/**
 * The base action from which all Cli module actions inherit.
 * 
 * PHP versions 5.2
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
 * Base action for module
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class MarketCliBaseAction extends MarketBaseAction
{

    /**
     * Check if this is a secure action (wich is not normally in cli module)
     *
     * @return boolean
     */ 
    public function isSecure()
    {
        return false;
    }
}
