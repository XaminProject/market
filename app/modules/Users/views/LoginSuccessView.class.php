<?php

class Users_LoginSuccessView extends MarketUsersBaseView
{
	

	/**
	 * Handles the Html output type.
	 *
	 * @parameter  AgaviRequestDataHolder the (validated) request data
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

	/**
	 * Handles the Mustache output type.
	 *
	 * @parameter  AgaviRequestDataHolder the (validated) request data
	 *
	 * @return     mixed <ul>
	 *                     <li>An AgaviExecutionContainer to forward the execution to or</li>
	 *                     <li>Any other type will be set as the response content.</li>
	 *                   </ul>
	 */
	public function executeMustache(AgaviRequestDataHolder $rd)
	{
		$this->setupMustache($rd);

		$this->setAttribute('_title', 'Login');
	}
}

?>