<?php

/**
 * Suuccess view for login action
 * 
 * PHP version 5.3
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 * @see       References to other sections (if any)...
 */


/**
 * View class
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 * @see       References to other sections (if any)...
 */
class Users_LoginSuccessView extends MarketUsersBaseView
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
		$tm = $this->getContext()->getTranslationManager();
		$user = $this->getContext()->getUser();

		if ($user->hasAttribute('redirect', self::LASTPAGE_NAMESPACE)) {
			$this->getResponse()->setRedirect($user->removeAttribute('redirect', self::LASTPAGE_NAMESPACE));
		} else {
			$this->getResponse()->setRedirect($this->getContext()->getRouting()->gen('index'));
		}
		//Do nothing
	}
}

