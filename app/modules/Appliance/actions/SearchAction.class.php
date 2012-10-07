<?php

/**
 * Search action
 *
 * PHP version 5.2
 *
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */

/**
 * Search action
 *
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Appliance_SearchAction extends MarketApplianceBaseAction
{
    /**
     * @var Form_Form form instance
     */
    protected $form = null;

	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
     * @param AgaviRequestDataHolder $rd request data
     *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */
	public function execute(AgaviRequestDataHolder $rd)
	{
        $query = $rd->getParameter('query');
        $start = $rd->getParameter('start', '0');
        $this->setAttribute('form', $this->getForm());
        if (is_null($query)) {
            return 'Input';
        }
        $appliance = $this->getContext()->getModel('Appliance', 'Appliance');
        $response = $appliance->query('*'.$query.'*', $start);
        $this->setAttribute('appliances', $response);
		return 'Success';
	}

    /**
     * should return true, if action needs permission check
     *
     * @return bool
     */
    public function isSecure()
    {
        return false;
    }

    /**
     * creates search form
     *
     * @return Form_Form form instance
     */
    protected function getForm()
    {
        if (!is_null($this->form)) {
            return $this->form;
        }
        $this->form = new Form_Form(
            array(
                'method' => 'get',
                'id' => 0,
                'renderer' => $this->getContainer()->getOutputType()->getRenderer(),
                'submit' => 'Search',
                'action' => $this->getContext()->getRouting()->gen('search')
                )
        );
        $query = new Form_Elements_TextField(
            array(
                'name' => 'query',
                'title' => '',
                'required' => false,
                'id' => 1
                ), 
            $this->form
        );
        $this->form->addChild($query);
        return $this->form;
    }
    
    /**
     * registers validator
     *
     * @return void
     */
	public function registerValidators()
    {
        Form_Validator::registerValidators(
            $this->getForm(),
            $this->getContainer()->getValidationManager(),
            array()
        );
    }
}
