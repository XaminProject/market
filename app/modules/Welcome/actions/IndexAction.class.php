<?php

class Welcome_IndexAction extends MarketWelcomeBaseAction
{
	
	
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
        $form = new Form_Form(array(
            'submit' => 'test',
            'id' => 0
        ));
        $username = new Form_Elements_TextField(array(
            'name' => 'username',
            'title' => 'username',
            'required' => true,
            'id' => 1
        ), $form);
        $form->addChild($username);
        $password = new Form_Elements_PasswordField(array(
            'name' => 'password',
            'title' => 'password',
            'required' => true,
            'id' => 2
        ), $form);
        $form->addChild($password);
        $this->setAttribute('form', $form);
		return 'Success';
	}

    public function isSecure() 
    {
        return false;
    }
    
}

