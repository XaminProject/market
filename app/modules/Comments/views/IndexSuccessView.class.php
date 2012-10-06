<?php

/**
 * View class
 * 
 * PHP versions 5.2
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
 * View class
 * 
 * @category  Xamin
 * @package   Market
 * @author    fzerorubigd <fzerorubigd@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class Comments_IndexSuccessView extends MarketCommentsBaseView
{
	

    /**
     * Handles the Html output type.
     *
     * @param AgaviRequestDataHolder $rd the request data
     *
     * @return     mixed <ul>
     *                     <li>An AgaviExecutionContainer to forward the execution to or</li>
     *                     <li>Any other type will be set as the response content.</li>
     *                   </ul>
     */
    public function executeHtml(AgaviRequestDataHolder $rd)
    {
        $this->getOneShotMessage('comment_');
        $this->setupHtml($rd);

        $this->setAttribute('_title', 'Index');
        $this->registerPaginatorSlot(
            'pager', //Designer can customize this in mustache template for paginator
            $this->getAttribute('count'),
            $this->getAttribute('current'),
            'p', //TODO : {fzerorubigd} Change this to comment parameter is good idea
            $this->getAttribute('redirect')
        );
    }
}

