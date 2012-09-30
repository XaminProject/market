<?php

/**
 * Secure success view
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
 * View class
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class System_SecureSuccessView extends MarketSystemBaseView
{

    /**
     * execute html 
     * 
     * @param AgaviRequestDataHolder $rd request data 
     *
     * @return void   
     * @access public 
     */
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
        $tm = $this->getContext()->getTranslationManager();
		$this->setAttribute('_title', $tm->_('Access Denied'));
		
		$this->setupHtml($rd);
		
		$this->getResponse()->setHttpStatusCode('403');
	}
}

?>
