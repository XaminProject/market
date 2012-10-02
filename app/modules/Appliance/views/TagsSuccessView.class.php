<?php

class Appliance_TagsSuccessView extends MarketApplianceBaseView
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

        $ro = $this->getContext()->getRouting();
        $tags = $this->getAttribute('tags', array());
        foreach($tags as &$tag)
        {
            $tag = array(
                "name" => $tag,
                "url" => $ro->gen('tags.tag', array('name'=>$tag))
            );
        }
        $this->setAttribute('tags', $tags);
		$this->setAttribute('_title', 'Tags');
	}
}

?>