<?php
/**
 * Action description
 *
 * PHP version 5.2
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
 * Comments_IndexAction desciption
 *
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Comments_IndexAction extends MarketCommentsBaseAction
{

    /**
     * @var Form_Form coment form
     */
    private $_form;

    /**
     * Whether or not this action is "simple", i.e. doesn't use validation etc.
     *
     * @return     bool true, if this action should act in simple mode, or false.
     *
     */
    public function isSimple()
    {
        return true;
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
        $user = $this->getContext()->getUser();

        //Get comments for last page
        //TODO : {fzerorubigd} Add pager support
        $scope = $this->getContainer()->getArguments()->getParameter('scope');
        $redirect = $this->getContainer()->getArguments()->getParameter('redirect');
        $model = $this->getContext()->getModel('Main', 'Comments');
        $this->setAttribute('comments', $model->getComments($scope));
        $form = '';
        if ($user->isAuthenticated()) {
            $form = $this->_getForm($scope, $redirect);
        }
        $this->setAttribute('form', $form);
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
     * Get comment form
     *
     * @param string $key      scope hash
     * @param string $redirect redirect url that would be used after saving comment
     *
     * @return Form_Form
     */
    private function _getForm($key, $redirect)
    {
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
                    'action' => $this->getContext()->getRouting()->gen(
                        'comments.save',
                        array(
                            'redirect' => $redirect
                        )
                    )
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
                    'name' => 'scope',
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
