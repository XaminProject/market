<?php

/**
 * Provides Solr connectivity through solr extension
 * 
 * PHP version 5.3
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
 * Database class
 * 
 * @category  Xamin
 * @package   Market
 * @author    Behrooz Shabani <everplays@gmail.com>
 * @copyright 2012 (c) ParsPooyesh Co
 * @license   Custom <http://xamin.ir>
 * @version   Release: @package_version@
 * @link      http://xamin.ir
 * @see       References to other sections (if any)...
 */
class AgaviSolrDatabase extends AgaviDatabase
{
	/**
	 * Connect to the database.
	 *
     * @return void
	 * @throws	 <b>AgaviDatabaseException</b> If a connection could not be 
	 *										   created.
	 *
	 * @author	 Behrooz Shabani <everplays@gmail.com>
	 * @since	  1.0.8
	 */
	protected function connect()
	{
		try {
			$solr = new SolrClient($this->getParameters());
			$this->resource = $this->connection = $solr;
		} catch(PDOException $e) {
			throw new AgaviDatabaseException($e->getMessage(), 0, $e);
		}
	}

	/**
	 * Execute the shutdown procedure.
	 *
     * @return void
	 * @throws	 <b>AgaviDatabaseException</b> If an error occurs while shutting
	 *										   down this database.
	 *
	 * @author	 Behrooz Shabani <everplays@gmail.com>
	 * @since	  1.0.8
	 */
	public function shutdown()
	{
        $this->connection = $this->resource = null;
	}
}
