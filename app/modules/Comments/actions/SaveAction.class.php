<?php
/**
 * Action description
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
 * Comments_SaveAction desciption
 *
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Comments_SaveAction extends MarketCommentsBaseAction
{
    
    /**
     * @var Form_Form coment form
     */
    private $_form;

	/**
	 * Handles the Write request method.
	 *
	 * @param AgaviRequestDataHolder $rd the request data
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
        $model = $this->getContext()->getModel('Main', 'Comments');
        $tm = $this->getContext()->getTranslationManager();
        $scopeKey = $rd->getParameter('scopekey');
        $comment  = $rd->getParameter('comment');
        $user = $this->getContext()->getUser();
        $username = $user->getAttribute('username');
        
        //Now get the data for scopeKey
        $data = $model->getScopeData($scopeKey);
        if (!$data) {
            //Wrong key. 
            $this->setAttribute('class', 'error');
            $this->setAttribute('message', $tm->_('Invalid comment'));
        } else {
            $model->addComment($scopeKey, $username, $comment);
            $this->setAttribute('route', $data['route']);
            $this->setAttribute('parameters', $data['parameters']);
            $this->setAttribute('class', 'success');
            $this->setAttribute('message', $tm->_('Your comment is saved'));
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
     * Handle error
     * 
     * This action always serve success
     *
     * @param AgaviRequestDataHolder $rd Request data 
     *
     * @return string view name to serve
     */
    public function handleError(AgaviRequestDataHolder $rd) 
    {
        //This action is an exception. always server the success view
        parent::handleError($rd);
        $model = $this->getContext()->getModel('Main', 'Comments');
        $scopeKey = $rd->getParameter('scopekey');
        $data = $model->getScopeData($scopeKey);
        if (!$data) {
            $data = [
                'route' => 'index',
                'parameters' => []
                ];
        }

        $this->setAttribute('route', $data['route']);
        $this->setAttribute('parameters', $data['parameters']);
        $this->setAttribute('class', 'error');
        $this->setAttribute('message', join('<br />', $this->getAttribute('error')));
        return 'Success';
    }

    /**
     * secure action?
     *
     * @return boolean
     */
    public function isSecure()
    {
        return true;
    }

    /**
     * Register validator for current form
     * 
     * @return void  
     * @access public
     */
	public function registerWriteValidators()
    {
        $rd = $this->getContext()->getRequest()->getRequestData();
        Form_Validator::registerValidators(
            $this->_getForm($rd->getParameter('key')),
            $this->getContainer()->getValidationManager(),
            array() //?
        );
    }

    /**
     * Get comment form
     * 
     * @param string $key scope hash 
     *
     * @return Form_Form
     */
    private function _getForm($key) 
    {
        //
        if (!$this->_form) {
            $model = $this->getContext()->getModel('Main', 'Comments');
            $tm = $this->getContext()->getTranslationManager();
            $id = 0;
            $this->_form = new Form_Form(
                array (
                    'method' => 'post',
                    'submit' => $tm->_('Post comment'),
                    'id' => $id++,
                    'renderer' => $this->getContainer()->getOutputType()->getRenderer(),
                    'action' => $this->getContext()->getRouting()->gen('comments.save')
                    )
            );
            $comment = new Form_Elements_TextArea(
                array(
                    'name' => 'comment',
                    'title' => $tm->_('Your comment'),
                    'required' => true,
                    'id' => $id++
                    ), 
                $this->_form
            );
            $this->_form->addChild($comment);
            $scopeKey = new Form_Elements_HiddenField(
                array(
                    'name' => 'scopekey',
                    'required' => true,
                    'id' => $id++,
                    'value' => $key
                    ), 
                $this->_form
            );
            $this->_form->addChild($scopeKey);
        }
        return $this->_form;
    }

}
