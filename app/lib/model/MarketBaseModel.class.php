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
     * @var SolrClient solr client
     */
    protected $solr = null;

    /**
     * returns redis instance
     *
     * @return Redis
     */
    public function getRedis()
    {
        if (is_null($this->redis)) {
            $this->redis = $this->getContext()->getDatabaseManager()->getDatabase()->getConnection();
        }
        return $this->redis;
    }

    /**
     * returns an instance of Redis
     *
     * @author Behrooz Shabani <everplays@gmail.com>
     * @copyright 2012 (c) ParsPooyesh co
     * @return Redis
     */
    public function getSolr()
    {
        if (is_null($this->solr)) {
            $this->solr = $this->getContext()->getDatabaseManager()->getDatabase('solr')->getConnection();
        }
        return $this->solr;
    }
}
