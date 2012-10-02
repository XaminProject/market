<?php
/**
 * Action description
 * 
 * PHP version 5.2
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 Authors
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */


/**
 * Users_Recover_ConfirmAction desciption
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 Authors
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Users_Recover_ConfirmAction extends MarketUsersBaseAction
{
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
        $tm = $this->getContext()->getTranslationManager();
        $model = $this->getContext()->getModel('Users');
        $username = $rd->getParameter('user', '');
        $hash = $rd->getParameter('hash', '');
        try {
            $password = $model->createRandomPassword($username, $hash);
           
            $email = $model->getEmail($username);
            if (!$email) {
                //TODO : {fzerorubigd} Assertaion procedure
                $this->setAttribute('error', array('Bug, user has no email.'));
                return "Error";
            }
            $this->sendMail(
                array( $email => $username), 
                $tm->_("New password"), 
                'Confirm', 
                array(
                    'username' => $username, 
                    'password' => $password
                    )
            );

        } catch (Exception $e) {
            $this->setAttribute('error', array($e->getMessage()));
            return "Error";
        }
		return 'Success';
	}

	
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return mixed <ul>
	 *                <li>A string containing the view name associated
	 *                   with this action; or</li>
	 *                <li>An array with two indices: the parent module
	 *                   of the view to be executed and the view to be
	 *                   executed.</li>
	 *               </ul>
	 */
	public function getDefaultViewName()
	{
		return 'Success';
	}
	
	/**
	 * secure action?
	 * 
	 * @return boolean
	 */
	public function isSecure()
	{
		return false;
	}

}
