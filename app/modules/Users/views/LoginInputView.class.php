<?php

class Users_LoginInputView extends MarketUsersBaseView
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

		if ($this->getContainer()->hasAttributeNamespace('org.agavi.controller.forwards.login')) {
			// we were redirected to the login form by the controller because the requested action required security
			// so store the input URL in the session for a redirect after login
			$user->setAttribute('redirect', $this->getContext()->getRequest()->getUrl(), self::LASTPAGE_NAMESPACE);
		} else {
			// clear the redirect URL just to be sure
			// but only if request method is "read", i.e. if the login form is served via GET!
			$user->removeAttribute('redirect', self::LASTPAGE_NAMESPACE);
		}		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Login');
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