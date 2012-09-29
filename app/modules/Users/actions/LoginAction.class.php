<?php

class Users_LoginAction extends MarketUsersBaseAction
{
	
	private $form;
	/**
	 * Handles the Read request method.
	 *
	 * @parameter  AgaviRequestDataHolder the (validated) request data
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>^
	 */
	public function executeRead(AgaviRequestDataHolder $rd)
	{
        $this->setAttribute('form', $this->createForm());
		return 'Input';
	}

	/**
	 * Handles the Write request method.
	 *
	 * @parameter  AgaviRequestDataHolder the (validated) request data
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>^
	 */
	public function executeWrite(AgaviRequestDataHolder $rd)
	{
		$username = $rd->getParameter('username');
		$password = $rd->getParameter('password');
		
		$user = $this->getContext()->getUser();
		try {
			$user->login($username, $password, false);
		} catch (AgaviSecurityException $e) {
			$this->setAttribute('form', $this->createForm());
			$this->setAttribute('error', array($e->getMessage()));
			return 'Error';
		}
		return 'Success';
	}

	public function registerWriteValidators()
    {
	    xdebug_break();
        Form_Validator::registerValidators(
            $this->createForm(),
            $this->getContainer()->getValidationManager(),
            array() //?
            );
    }
	
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */
	public function getDefaultViewName()
	{
		return 'Input';
	}

	public function handleError(AgaviRequestDataHolder $rd)
    {
        $this->executeRead($rd);
        return parent::handleError($rd);
    }

    public function isSecure()
    {
        return false;
    }

    private function createForm() 
    {
        if ($this->form) {
            return $this->form;
        }
        $tm = $this->getContext()->getTranslationManager();
        $this->form = new Form_Form(
            array (
	            'method' => 'post',
                'submit' => $tm->_('Login'),
                'id' => 0,
                'renderer' => $this->getContainer()->getOutputType()->getRenderer()
                )
            );
        $username = new Form_Elements_TextField(
            array(
                'name' => 'username',
                'title' => $tm->_('User name'),
                'required' => true,
                'id' => 1
                ), 
            $this->form
            );
        $this->form->addChild($username);
        $password = new Form_Elements_PasswordField(
            array(
                'name' => 'password',
                'title' => $tm->_('Password'),
                'required' => true,
                'id' => 2
                ), 
            $this->form
            );
        $this->form->addChild($password);
        return $this->form;
    }
}

?>