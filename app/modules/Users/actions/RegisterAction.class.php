<?php

class Users_RegisterAction extends MarketUsersBaseAction
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
        $model = $this->getContext()->getModel('Users');
        $username = $rd->getParameter('username');
        $password = $rd->getParameter('password');
        $email = $rd->getParameter('email');

        try {
            $model->register($username, $email, $password);
        } catch (Exception $e) {
            $this->setAttribute('form', $this->createForm());
            $this->setAttribute('error', array($e->getMessage()));
            return "Error";
        }
        
		return 'Success';
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

	public function registerWriteValidators()
    {
        Form_Validator::registerValidators(
            $this->createForm(),
            $this->getContainer()->getValidationManager(),
            array() //?
            );
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
        $id = 0;
        $this->form = new Form_Form(
            array (
                'method' => 'post',
                'submit' => $tm->_('Register'),
                'id' => $id++,
                'renderer' => $this->getContainer()->getOutputType()->getRenderer()
                )
            );
        $username = new Form_Elements_TextField(
            array(
                'name' => 'username',
                'title' => $tm->_('User name'),
                'required' => true,
                'id' => $id++
                ), 
            $this->form
	        );
        $this->form->addChild($username);
        $email = new Form_Elements_TextField(
            array(
                'name' => 'email',
                'title' => $tm->_('Email address'),
                'required' => true,
                'email' => true,
                'id' => $id++
                ), 
            $this->form
	        );
        $this->form->addChild($email);

        $password = new Form_Elements_PasswordField(
            array(
                'name' => 'password',
                'title' => $tm->_('Password'),
                'required' => true,
                'min' => 6,
                'id' => $id++
                ), 
            $this->form
            );
        $this->form->addChild($password);
        $confirm = new Form_Elements_PasswordField(
            array(
                'name' => 'confirm',
                'title' => $tm->_('Confirm'),
                'required' => true,
                'equal' => 'password', //Name of other field 
                'id' => $id++
                ), 
            $this->form
            );

        $this->form->addChild($confirm);
        return $this->form;
    }

}

?>