<?php

/**
 * Recover password action
 * 
 * Get the username and email and then send the recover link to user email 
 * 
 * PHP version 5
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Recover action
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Users_Recover_PasswordAction extends MarketUsersBaseAction
{

    /**
     * Recover form
     * @var Form_Form
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
        $this->setAttribute('form', $this->_getForm());
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
        $model = $this->getContext()->getModel('Users');
        try {
            $username = $rd->getParameter('username');
            $email = $rd->getParameter('email');
            $hash = $model->getRecoverHash(
                $username,
                $email
            );

            $tm = $this->getContext()->getTranslationManager();
            $this->sendMail(
                array($email => $username), 
                $tm->_("Recover password"), 
                'Recover', 
                array(
                    'username' => $username, 
                    'link' => $this->getContext()->getRouting()->gen(
                        'users.recover.confirm', 
                        array('hash' => $hash , 'user' => $username )
                    )
                    )
            );

        } catch (Exception $e) {
            $this->setAttribute('form', $this->_getForm());
            $this->setAttribute('error', $e->getMessage());
            return 'Error';
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

    /**
     * Register validator for current form
     * 
     * @return void  
     * @access public
     */
	public function registerWriteValidators()
    {
        Form_Validator::registerValidators(
            $this->_getForm(),
            $this->getContainer()->getValidationManager(),
            array() //?
        );
    }

    /**
     * Handle errors
     * 
	 * @param AgaviRequestDataHolder $rd the (validated) request data
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
     * isSecure action?
     * 
     * @return boolean 
     * @access public 
     */
    public function isSecure() 
    {
        return false;
    }


    /**
     * Get recover form
     * 
     * @return Form_Form
     * @access private
     */
    private function _getForm() 
    {
        if (!$this->_form) {
            $tm = $this->getContext()->getTranslationManager();
            $id = 0;
            $this->_form = new Form_Form(
                array (
                    'method' => 'post',
                    'submit' => $tm->_('Recover'),
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
                $this->_form
            );
            $this->_form->addChild($username);
            $email = new Form_Elements_TextField(
                array(
                    'name' => 'email',
                    'title' => $tm->_('Email address'),
                    'required' => true,
                    'email' => true,
                    'id' => $id++
                    ), 
                $this->_form
            );
            $this->_form->addChild($email);
        }

        return $this->_form;
    }
}
