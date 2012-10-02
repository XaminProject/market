<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.								   |
// | Copyright (c) 2012 Parspooyesh co.								|
// |																		   |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the	|
// | LICENSE file online at http://www.agavi.org/LICENSE.txt				   |
// |   vi: set noexpandtab:													|
// |   Local Variables:														|
// |   indent-tabs-mode: t													 |
// |   End:																	|
// +---------------------------------------------------------------------------+

/**
 * Provides Solr connectivity through solr extension
 *
 * @package	agavi
 * @subpackage database
 *
 * @author	 Behrooz Shabani
 * @copyright  Authors
 * @copyright  ParsPooyesh co
 * @copyright  The Agavi Project
 *
 * @since	  1.0.8
 *
 * @version	$Id$
 */
class AgaviSolrDatabase extends AgaviDatabase
{
	/**
	 * Connect to the database.
	 *
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

?>