<?php

class Appliance_TagAction extends MarketApplianceBaseAction
{
	/**
     * exeuctes action regardless of method
	 *
     * @param AgaviRequestDataHolder $rd request data
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
        $appliance = $this->getContext()->getModel('Appliance', 'Appliance');
        $tag = $rd->getParameter('name');
        $this->setAttribute('appliances', $appliance->appliancesByTag($tag));
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
}

?>