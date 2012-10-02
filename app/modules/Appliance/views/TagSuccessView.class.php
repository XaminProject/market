<?php

class Appliance_TagSuccessView extends MarketApplianceBaseView
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
        $appliances = [];
        foreach($this->getAttribute('appliances', array()) as $name => $version)
        {
            $appliances[] = array(
                "name" => $name,
                "version" => $version
            );
        }
        $this->setAttribute('appliances', $appliances);
	}
}

?>