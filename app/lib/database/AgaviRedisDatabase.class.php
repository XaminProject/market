<?php

/**
 * Provides Redis connectivity through phpredis extension
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
 * redis database class
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
class AgaviRedisDatabase extends AgaviDatabase
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
			$redis = new Redis();

			// make persistent connection if persistent is set to true
			$method = $this->getParameter('persistent', false) ? 'pconnect'
				: 'connect';

			// make connection
			$redis->{$method}($this->getParameter('host', 'localhost'),
				$this->getParameter('port', 6379),
				$this->getParameter('timeout'));

			// set options
			if ($this->hasParameter('options')) {
				foreach ((array)$this->getParameter('options') as $key => $value) {
					$redis->setOption($key, $value);
				}
			}

			$this->resource = $this->connection = $redis;
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
		//Shutdown is called even when ther is no connection
		if (isset($this->connection)) {
			$this->connection->close();
		}
	}
}
