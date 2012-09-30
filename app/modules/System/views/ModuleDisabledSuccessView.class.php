<?php

/**
 * Module disabled view
 * 
 * PHP version 5
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $
 * @link      http://xamin.ir
 */


/**
 * Module disabled view class
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class System_ModuleDisabledSuccessView extends MarketSystemBaseView
{

    /**
     * execute html
     * 
     * @param AgaviRequestDataHolder $rd Request data
     *
     * @return void   
     * @access public 
     */
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
        $tm = $this->getContext()->getTranslationManager();
		$this->setAttribute('_title', $tm->_('Module Disabled'));
		
		$this->setupHtml($rd);
		
		$this->getResponse()->setHttpStatusCode('503');
	}
}

?>
