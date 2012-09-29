<?php

class MarketFPFHelper
{
	/**
	 * creates markup of error element that will be used in FPF
	 *
	 * @param DOMElement $element element that its validation failed
	 * @param string $errorMessage hit string that must be shown to user
	 * @return DOMElement
	 */
	public static function markup(DOMElement $element, $errorMessage)
	{
		$decoded = json_decode($errorMessage);
		if(!is_null($decoded) and isset($decoded->msg))
		{
			if(!isset($decoded->arguments))
				$decoded->arguments = array();
			$td = AgaviConfig::get('Form.TranslationDomain');
			if(!is_string($td))
			{
				$context = AgaviContext::getInstance();
				$errorMessage = $context->getTranslationManager()->_($decoded->msg, $td, null, $decoded->arguments);
			}
			else
				$errorMessage = vsprintf($decoded->msg, $decoded->arguments);
		}
		$doc = new DOMDocument();
		$el = $doc->createElement('div');
		$el->setAttribute('class', 'alert-message error');
		$doc->appendChild($el);
		$close = $doc->createElement('a', '×');
		$close->setAttribute('class', 'close');
		$close->setAttribute('href', '#');
		$el->appendChild($close);
		$message = $doc->createElement('p', $errorMessage);
		$el->appendChild($message);
		return $el;
	}
}
