<?php

/**
 * The base model from which all project models inherit.
 *
 * php version 5.2
 *
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   GIT: $Id$
 * @link      http://xamin.ir
 */

/**
 * appliance model
 *
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 */
class MarketBaseModel extends AgaviModel
{
    /**
     * @var Redis redis database connection
     */
    protected $redis = null;

    /**
     * initializes model
     *
     * @param AgaviContext $context    the context we're in
     * @param array        $parameters init parameters
     *
     * @return void
     * @author Behrooz Shabani <everplays@gmail.com>fzerorubigd 
     * @copyright 2012 (c) ParsPooyesh co
     */
    public function initialize(AgaviContext $context, array $parameters = array())
    {
        parent::initialize($context, $parameters);
        $this->redis = $this->getContext()->getDatabaseManager()->getDatabase()->getConnection();
    }

}
