<?php

class Appliance_IndexSuccessView extends MarketApplianceBaseView
{
	

	/**
	 * Handles the Html output type.
	 *
	 * @parameter  AgaviRequestDataHolder the (validated) request data
	 *
	 * @return     mixed <ul>
	 *                     <li>An AgaviExecutionContainer to forward the execution to or</li>
	 *                     <li>Any other type will be set as the response content.</li>
	 *                   </ul>
	 */
	public function executeHtml(AgaviRequestDataHolder $rd)
	{
		$this->setupHtml($rd);

		$this->setAttribute('_title', 'Index');
        $this->getLayer('content')->setSlot('tags', $this->createSlotContainer(
            'Appliance', // name of module to use
            'Tags', // name of action to execute
            array(), // parameters to pass to the slot
            'html', // output type to use
            'read' // request method to use
        ));
	}
}

?>