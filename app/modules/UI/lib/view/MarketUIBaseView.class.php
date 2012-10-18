<?php

/**
 * The base view from which all UI module views inherit.
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
 * base model for this module
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class MarketUIBaseView extends MarketBaseView
{
    const SLOT_JS_NAME = 'js';
    /**
     * Prepares the HTML output type.
     *
     * @param AgaviRequestDataHolder $rd         The request data associated with this execution.
     * @param string                 $layoutName The layout to load.
     *
     * @return void
     */
    public function setupHtml(AgaviRequestDataHolder $rd, $layoutName = null)
    {
        if ($layoutName === null && !$this->getContainer()->getParameter('is_slot', false)) {
            $layoutName = self::SLOT_JS_NAME;
        }

        parent::setupHtml($rd, $layoutName);
    }

}
