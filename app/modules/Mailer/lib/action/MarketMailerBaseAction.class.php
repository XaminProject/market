<?php
/**
 * Market mailer base action
 *
 * PHP version 5.2
 *
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $
 * @link      http://xamin.ir
 */
/**
 * Base class for all actions in this module
 *
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 fzerorubigd
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */

class MarketMailerBaseAction extends MarketBaseAction
{
    /**
     * Create a validator
     *
     * @param string $class      validator class
     * @param array  $arg        Arguments
     * @param array  $parameters parameters
     * @param array  $error      error messages
     *
     * @return AgaviValidator
     */
    private function _createValidator($class, $arg, $parameters, $error) 
    {
        $arguments = array(
            $arg
        );
        $errors = array(
            '' => $error
        );
        $validator = new $class();
        $validator->initialize($this->getContext(), $parameters, $arguments, $errors);
        
        return $validator;
    }
    /**
     * Get array of every thing valid for this message
     *
     * @return array
     */
    protected function getParameters() 
    {
        
        return array();
    }
    /**
     * Register validator at runtime
     *
     * @return void
     * @access public
     */
    public function registerValidators() 
    {
        $validators = array();
        
        foreach ($this->getParameters() as $param) {
            $validators[] = $this->_createValidator(
				'AgaviStringValidator', 
				$param, 
				array(
					'required' => false
				), 
				$param . ' is invalid.'
            );
        }
        $this->getContainer()->getValidationManager()->registerValidators($validators);
    }
    /**
     * execute action
     *
     * @param object $rd Parameter description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
    public function execute(AgaviRequestDataHolder $rd)
    {
        
        foreach ($this->getParameters() as $param) {
			$this->setAttribute($param, $rd->getParameter($param));
		}
        
        return "Success";
    }
}
