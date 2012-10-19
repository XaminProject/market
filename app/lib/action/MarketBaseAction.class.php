<?php
/**
 * The base action from which all project actions inherit.
 *
 * PHP version 5.2
 *
 * @category Xamin
 * @package  Market
 * @author   fzerorubigd <fzerorubigd@gmail.com>
 * @license  Custom <http://xamin.ir>
 * @link     http://xamin.ir
 */

/**
 * The base action from which all project actions inherit.
 * 
 * @category Xamin
 * @package  Market
 * @author   fzerorubigd <fzerorubigd@gmail.com>
 * @license  Custom <http://xamin.ir>
 * @link     http://xamin.ir
 */
class MarketBaseAction extends AgaviAction
{
    /**
     * Get attribute
     *
     * @param AgaviExecutionContainer $container container
     *
     * @return nothing
     *
     * @author     fzerorubigd <fzerorubigd@gmail.com>
     * @since      1.1.0
     */    
    public function initialize(AgaviExecutionContainer $container) 
    {
        parent::initialize($container);
    
        //TODO Add multi language support by changing this
        $tm = $this->getContext()->getTranslationManager();
        $tm->setDefaultDomain('default.messages');

        // Call initialize renderer
        $this->initializeRenderer($this->getContainer()->getOutputType()->getRenderer());
    }
    
    /**
     * Checks permissions to perform the operation. Called after validation.
     *
     * @return     bool Whether or not the operation is allowed.
     *
     * @author     fzerorubigd <fzerorubigd@gmail.com>
     * @since      1.1.0
     */
    public function getCredentials()
    {
        $actionName = substr(get_class($this), 0, -6);
        $actionName = strtolower(str_replace('_', ':', $actionName));
        
        $method = $this->getContext()->getRequest()->getMethod();
        
        return "$actionName.$method";
    }
    
    /**
     * Get attribute
     *
     * @return bool
     *
     * @author     fzerorubigd <fzerorubigd@gmail.com>
     * @since      1.1.0
     */    
    public function isSecure()
    {
        return true;
    }

    /**
     * Handle errors
     *
     * @param AgaviRequestDataHolder $rd Request data
     *
     * @return     string 
     *
     * @author     fzerorubigd <fzerorubigd@gmail.com>
     * @since      1.1.0
     */
    public function handleError(AgaviRequestDataHolder $rd)
    {
        $report = $this->getContainer()->getValidationManager()->getErrorMessages();
        $errors = array();
        foreach ($report as $rep) {
            $errors[] = $rep['message'];
        }
        $this->setAttribute('error', array ('errors' => $errors));
        return parent::handleError($rd);        
    }

    /**
     * Initialize renderer
     * A function to initialize and register custom handler in renderer 
     * mostly mustache
     *
     * @param AgaviRenderer $renderer current renderer
     *
     * @return void
     */
    protected function initializeRenderer($renderer)
    {
        //There is a need to use minify functions here
        if ($renderer instanceof AgaviMustacheRenderer) {
            // register your helpers here, like:
            //$renderer->getEngine()->addHelper(
            //    'test',
            //    function($text)
            //    {
            //        return strrev($text);
            //    }
            //);
        }

    }

    /**
     * Execute mail action and create message body
     *
     * @param string $module    Mailer module name
     * @param string $action    Action to execute
     * @param array  $arguments Action arguments
     *
     * @return     string 
     *
     * @author     fzerorubigd <fzerorubigd@gmail.com>
     * @since      1.1.0
     */
    private function _getActionBody($module , $action , array $arguments) 
    {
        $requestData = new AgaviRequestDataHolder();
        $requestData->setParametersByRef($arguments);
        $body = $this->getContainer()->createExecutionContainer(
            $module, 
            $action, 
            $requestData, 
            AgaviConfig::get('mailer.output_type', 'html'), 
            AgaviConfig::get('mailer.method', 'Read')
        );
        return $body->execute()->getContent();
    }

    /**
     * Helper method to send mail 
     *
     * @param string|array $to        Address to send to
     * @param string       $subject   Subject
     * @param string       $action    Action name in Mailer module to create message body
     * @param array        $arguments Arguments to pass to action
     *
     * @return bool
     * @author     fzerorubigd <fzerorubigd@gmail.com>
     * @since      1.1.0
     */
    protected function sendMail($to, $subject, $action, $arguments)
    {
        $module = AgaviConfig::get('mailer.module', 'Mailer');
        $model = $this->getContext()->getModel('Main', $module, array());
        $messageHtml = $this->_getActionBody($module, $action, $arguments);
        if (is_string($to)) {
            $to = array($to);
        }

        return $model->send($to, $subject, $messageHtml);
    }
}
