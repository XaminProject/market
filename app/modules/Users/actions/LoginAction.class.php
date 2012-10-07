<?php

/**
 * Login action
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
 */


/**
 * Action class
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
class Users_LoginAction extends MarketUsersBaseAction
{
	
    /**
     * Description for private
     * @var object 
     * @access private
     */
	private $_form;
    
	/**
	 * Handles the Read request method.
	 *
	 * @param AgaviRequestDataHolder $rd the (validated) request data
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
        $this->setAttribute('form', $this->_createForm());
		return 'Input';
	}

	/**
	 * Handles the Write request method.
	 *
	 * @param AgaviRequestDataHolder $rd the (validated) request data
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
			$this->setAttribute('form', $this->_createForm());
			$this->setAttribute('error', array($e->getMessage()));
			return 'Error';
		}
		return 'Success';
	}

    /**
     * Register validator for this action
     * 
     * @return void  
     * @access public
     */
	public function registerWriteValidators()
    {
        Form_Validator::registerValidators(
            $this->_createForm(),
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

    /**
     * Handle error for this action
     * 
     * @param AgaviRequestDataHolder $rd Request data
     *
     * @return string 
     * @access public 
     */
	public function handleError(AgaviRequestDataHolder $rd)
    {
        $this->executeRead($rd);
        return parent::handleError($rd);
    }

    /**
     * Is this a secure action?
     * 
     * @return boolean 
     * @access public 
     */
    public function isSecure()
    {
        return false;
    }

    /**
     * Create new form
     * 
     * @return Form_Form created form 
     * @access private
     */
    private function _createForm() 
    {
        if ($this->_form) {
            return $this->_form;
        }
        $tm = $this->getContext()->getTranslationManager();
        $this->_form = new Form_Form(
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
            $this->_form
        );
        $this->_form->addChild($username);
        $password = new Form_Elements_PasswordField(
            array(
                'name' => 'password',
                'title' => $tm->_('Password'),
                'required' => true,
                'id' => 2
                ), 
            $this->_form
        );
        $this->_form->addChild($password);
        return $this->_form;
    }
}
